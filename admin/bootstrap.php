<?php
declare(strict_types=1);

$sessionSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $sessionSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function admin_security_headers(): void {
    if (headers_sent()) return;
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://unpkg.com https://cdn.jsdelivr.net https://cdn.plyr.io https://www.youtube.com https://www.youtube-nocookie.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdn.plyr.io; img-src 'self' data: blob: https://images.unsplash.com https://*.unsplash.com https://i.ytimg.com; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self'; frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com");
}

admin_security_headers();

define('DATA_DIR', __DIR__ . '/../data');
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads');
define('DB_PATH', __DIR__ . '/../data/aromas.sqlite');

$adminScript = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$detectedBase = rtrim(str_replace('\\', '/', dirname(dirname($adminScript))), '/');
if ($detectedBase === '' || $detectedBase === '/' || $detectedBase === '.') $detectedBase = '';
define('FRONT_BASE', rtrim((string)(getenv('APP_BASE') ?: $detectedBase), '/'));
define('ADMIN_BASE', FRONT_BASE . '/admin');

if (!is_dir(DATA_DIR))   @mkdir(DATA_DIR, 0775, true);
if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0775, true);

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $config = db_config();
    if ($config['driver'] === 'mysql') {
        $pdo = new PDO($config['dsn'], $config['user'], $config['pass']);
    } else {
        $pdo = new PDO('sqlite:' . DB_PATH);
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    if ($config['driver'] === 'sqlite') $pdo->exec('PRAGMA foreign_keys = ON');
    return $pdo;
}

function db_config(): array {
    static $config = null;
    if ($config !== null) return $config;

    $url = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: '';
    if ($url && str_starts_with(strtolower($url), 'mysql')) {
        $parts = parse_url($url);
        $host = $parts['host'] ?? '127.0.0.1';
        $port = $parts['port'] ?? 3306;
        $db   = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
        $user = isset($parts['user']) ? rawurldecode($parts['user']) : '';
        $pass = isset($parts['pass']) ? rawurldecode($parts['pass']) : '';
        return $config = ['driver'=>'mysql','dsn'=>"mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",'user'=>$user,'pass'=>$pass];
    }

    $host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST');
    $db   = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE');
    $user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER');
    $pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '';
    if ($host && $db && $user) {
        $port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: 3306;
        return $config = ['driver'=>'mysql','dsn'=>"mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",'user'=>$user,'pass'=>$pass];
    }

    return $config = ['driver'=>'sqlite','dsn'=>'sqlite:' . DB_PATH,'user'=>null,'pass'=>null];
}

function db_driver(): string { return db_config()['driver']; }

function ee(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function admin_url(string $p = ''): string { return ADMIN_BASE . '/' . ltrim($p, '/'); }
function front_url(string $p = ''): string { return FRONT_BASE . '/' . ltrim($p, '/'); }

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">'; }
function csrf_check(): bool { return hash_equals($_SESSION['csrf'] ?? '', $_POST['_csrf'] ?? ''); }

function flash(?string $msg = null, string $type = 'success'): ?array {
    if ($msg !== null) { $_SESSION['flash'] = ['msg'=>$msg,'type'=>$type]; return null; }
    if (!empty($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}

function is_admin_logged(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void {
    if (!is_admin_logged()) {
        header('Location: ' . admin_url('login.php'));
        exit;
    }
}
function admin_user(): ?array {
    if (!is_admin_logged()) return null;
    $stmt = db()->prepare('SELECT * FROM admins WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
}

// Bootstrap schema + default admin
function bootstrap_db(): void {
    $pdo = db();
    $isMysql = db_driver() === 'mysql';
    $pk = $isMysql ? 'INT AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $short = $isMysql ? 'VARCHAR(191)' : 'TEXT';
    $date = $isMysql ? 'DATETIME DEFAULT CURRENT_TIMESTAMP' : 'TEXT DEFAULT CURRENT_TIMESTAMP';
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id $pk,
        email $short UNIQUE NOT NULL,
        name TEXT NOT NULL,
        password_hash TEXT NOT NULL,
        created_at $date
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS chalets (
        id $pk,
        slug $short UNIQUE NOT NULL,
        name TEXT NOT NULL,
        category $short,
        view $short,
        description TEXT,
        cover TEXT,
        gallery TEXT,
        is_active INTEGER DEFAULT 1,
        sort_order INTEGER DEFAULT 0,
        updated_at $date
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS experiences (
        id $pk,
        slug $short UNIQUE NOT NULL,
        title TEXT NOT NULL,
        icon $short,
        description TEXT,
        cover TEXT,
        gallery TEXT,
        is_active INTEGER DEFAULT 1,
        sort_order INTEGER DEFAULT 0,
        updated_at $date
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id $pk,
        name TEXT NOT NULL,
        email $short,
        phone $short,
        checkin $short,
        checkout $short,
        guests INTEGER,
        message TEXT,
        chalet_slug $short,
        status $short DEFAULT 'new',
        created_at $date
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id $pk,
        name TEXT NOT NULL,
        email $short,
        phone $short,
        subject $short,
        body TEXT,
        is_read INTEGER DEFAULT 0,
        created_at $date
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS gallery (
        id $pk,
        title TEXT,
        category $short,
        path TEXT NOT NULL,
        sort_order INTEGER DEFAULT 0,
        created_at $date
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
        id $pk,
        author TEXT NOT NULL,
        quote TEXT NOT NULL,
        context $short,
        rating INTEGER DEFAULT 5,
        is_active INTEGER DEFAULT 1,
        sort_order INTEGER DEFAULT 0,
        updated_at $date
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id $pk,
        slug $short UNIQUE NOT NULL,
        title TEXT NOT NULL,
        category $short,
        description TEXT,
        flavors TEXT,
        cover TEXT,
        is_active INTEGER DEFAULT 1,
        sort_order INTEGER DEFAULT 0,
        updated_at $date
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        `key` $short PRIMARY KEY,
        value TEXT
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS page_blocks (
        id $pk,
        page $short NOT NULL,
        block_key $short NOT NULL,
        type $short NOT NULL DEFAULT 'text',
        label TEXT,
        value TEXT,
        sort_order INTEGER DEFAULT 0,
        UNIQUE(page, block_key)
    )");
    ensure_column($pdo, 'chalets', 'video_url', 'TEXT');
    ensure_column($pdo, 'chalets', 'video_label', 'TEXT');

    // Seed default admin if empty
    $count = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('INSERT INTO admins (email,name,password_hash) VALUES (?,?,?)');
        $stmt->execute(['admin@aromas.local', 'Administradora', password_hash('aromas2025', PASSWORD_BCRYPT)]);
    }

    // Seed default settings
    $defaults = [
        'site_name'        => 'Pousada Aromas da Serra',
        'site_phone'       => '(82) 99326-0415',
        'site_email'       => 'atendimento@pousadaaromasdaserra.com.br',
        'site_whatsapp'    => '5582993260415',
        'site_instagram'   => 'https://www.instagram.com/pousadaaromasdaserra/',
        'site_facebook'    => 'https://www.facebook.com/pousadaaromasdaserra',
        'site_location'    => 'Mar Vermelho, Alagoas',
        'hero_headline'    => 'Onde o silêncio da serra acolhe e transforma.',
        'about_intro'      => 'Localizada entre as serras de Alagoas, com culinária sofisticada de influência europeia.',
    ];
    $check = $pdo->prepare('SELECT 1 FROM settings WHERE `key` = ?');
    $ins = $pdo->prepare('INSERT INTO settings (`key`,value) VALUES (?,?)');
    foreach ($defaults as $k=>$v) {
        $check->execute([$k]);
        if (!$check->fetchColumn()) $ins->execute([$k, $v]);
    }

    // Seed default page blocks (idempotent)
    $blocks = [
    ['home','hero_eyebrow','text','Hero · etiqueta','Seja bem-vindo'],
      ['home','hero_title','html','Hero · título','Onde o silêncio da serra<br><em>acolhe e transforma.</em>'],
    ['home','hero_subtitle','html','Hero · subtítulo','Um refúgio exclusivo para adultos em meio à <strong>Suíça Alagoana</strong>, com gastronomia mediterrânea, contemplação e tempo para si.'],
      ['home','hero_image','image','Hero · imagem de fundo','https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=2000&q=80'],
      ['home','manifesto_eyebrow','text','Manifesto · etiqueta','Sobre a pousada'],
      ['home','manifesto_title','html','Manifesto · título','Um destino para quem busca <em>tranquilidade e conforto.</em>'],
      ['home','manifesto_body','html','Manifesto · texto','Localizada entre as serras de Alagoas, com culinária sofisticada de influência europeia, a Aromas da Serra oferece uma viagem de <em>sabores, cores e bem-estar</em>.'],
    ['home','manifesto_note','text','Manifesto · linha menor','Exclusivamente para adultos. Sem pressa. Apenas presença.'],
      ['home','gastro_title','html','Gastronomia · título','Cozinha <em>Mediterrânea.</em>'],
      ['home','gastro_body','html','Gastronomia · texto','As receitas são fruto de uma jornada culinária pela Suíça, sul da França, Itália e Mediterrâneo. Cada prato é uma alquimia de sabores e aromas.'],

      ['a-pousada','hero_eyebrow','text','Hero · etiqueta','Sobre nós'],
      ['a-pousada','hero_title','html','Hero · título','A pousada <em>boutique</em> da Suíça Alagoana.'],
      ['a-pousada','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1455587734955-081b22074882?auto=format&fit=crop&w=2000&q=80'],
    ['a-pousada','intro_body','html','Introdução','Refúgio boutique em Mar Vermelho, com hospitalidade, gastronomia e contemplação.'],

      ['chales','hero_eyebrow','text','Hero · etiqueta','As nossas acomodações'],
      ['chales','hero_title','html','Hero · título','Chalés <em>com alma.</em>'],
    ['chales','hero_subtitle','html','Hero · subtítulo','Cada detalhe pensado com carinho, aconchego, conforto e gentilezas em contato constante com a natureza.'],
      ['chales','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=2000&q=80'],
      ['chales','intro_standard','html','Introdução · Aromáticos','Com decoração charmosa e <strong>vista para o nosso lindo e perfumado jardim</strong>, são refúgios perfeitos para momentos relaxantes. Cada chalé acomoda apenas 2 pessoas.'],

      ['gastronomia','hero_eyebrow','text','Hero · etiqueta','Cozinha Mediterrânea'],
      ['gastronomia','hero_title','html','Hero · título','Sabores que <em>acolhem.</em>'],
      ['gastronomia','hero_subtitle','html','Hero · subtítulo','Receitas autorais inspiradas em uma jornada culinária pela Suíça, sul da França, Itália e Mediterrâneo.'],
      ['gastronomia','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=2000&q=80'],
      ['gastronomia','philosophy_title','html','Filosofia · título','A mesa é o <em>coração</em> da pousada.'],
      ['gastronomia','philosophy_body','html','Filosofia · texto','Combinando ingredientes frescos e temperos colhidos diretamente da nossa Mandala, cada prato é uma alquimia de sabores e aromas. Mesa farta, vinhos especiais e tempo desacelerado.'],

      ['taberna','hero_eyebrow','text','Hero · etiqueta','Restaurante boutique'],
      ['taberna','hero_title','html','Hero · título','Taberna <em>do Monge.</em>'],
    ['taberna','hero_subtitle','html','Hero · subtítulo','Um convite à mesa farta, mediterrânea, generosa, harmonizada com vinhos especiais e a vista da serra.'],
      ['taberna','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1559717865-a99cac1c95d8?auto=format&fit=crop&w=2000&q=80'],
      ['taberna','about_title','html','Sobre · título','Aberta ao <em>público.</em>'],
    ['taberna','about_body','html','Sobre · texto','A Taberna do Monge é o restaurante boutique da pousada, aberto também a visitantes externos. Nasceu como um projeto afetivo dos diretores para reunir boa mesa, memória, conversa sem pressa e o clima acolhedor da serra. Receitas autorais, vinhos selecionados e uma lareira central fazem do espaço um dos encontros mais especiais da Aromas da Serra.'],

      ['experiencias','hero_eyebrow','text','Hero · etiqueta','Vivências na Serra'],
      ['experiencias','hero_title','html','Hero · título','Rituais que <em>tocam a alma.</em>'],
    ['experiencias','hero_subtitle','html','Hero · subtítulo','Experiências autorais desenhadas para o reencontro com o tempo, com a natureza e com você mesmo.'],
      ['experiencias','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=2000&q=80'],

    ['produtos','hero_eyebrow','text','Hero · etiqueta','Mostruário artesanal'],
    ['produtos','hero_title','html','Hero · título','Produtos que levam <em>aromas para casa.</em>'],
    ['produtos','hero_subtitle','html','Hero · subtítulo','Geleias, pães, temperos e delicadezas produzidas em pequenos lotes, apenas como catálogo de apresentação.'],
    ['produtos','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=2000&q=80'],
    ['produtos','intro_eyebrow','text','Introdução · etiqueta','Mostruário da casa'],
    ['produtos','intro_title','html','Introdução · título','Pequenos lotes, <em>muito cuidado.</em>'],
    ['produtos','intro_body','html','Introdução · texto','Os produtos artesanais da Aromas da Serra nascem da mesma cozinha afetiva que acolhe nossos hóspedes: ingredientes selecionados, ervas aromáticas e receitas preparadas com tempo. Consulte disponibilidade durante a sua estadia ou pelo WhatsApp.'],

    ['depoimentos','hero_eyebrow','text','Hero · etiqueta','Depoimentos'],
    ['depoimentos','hero_title','html','Hero · título','Histórias que ficam <em>na memória.</em>'],
    ['depoimentos','hero_subtitle','html','Hero · subtítulo','Relatos de quem viveu a pousada com calma, afeto, gastronomia e reconexão.'],
    ['depoimentos','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1499678329028-101435549a4e?auto=format&fit=crop&w=2000&q=80'],
    ['depoimentos','intro_eyebrow','text','Introdução · etiqueta','Vozes dos hóspedes'],
    ['depoimentos','intro_title','html','Introdução · título','A experiência contada por <em>quem esteve aqui.</em>'],

      ['localizacao','hero_eyebrow','text','Hero · etiqueta','Onde estamos'],
      ['localizacao','hero_title','html','Hero · título','Mar Vermelho, <em>Alagoas.</em>'],
      ['localizacao','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=2000&q=80'],

      ['itinerario','hero_eyebrow','text','Hero · etiqueta','Itinerário'],
      ['itinerario','hero_title','html','Hero · título','A viagem até a serra <em>já é parte da experiência.</em>'],
      ['itinerario','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=2000&q=80'],

            ['global','cta_eyebrow','text','Rodapé CTA · etiqueta','Reserve sua estadia'],
            ['global','cta_title','html','Rodapé CTA · título','Frio da serra, gastronomia, requinte e <em>hospitalidade.</em>'],
            ['global','cta_body','html','Rodapé CTA · texto','Faça sua reserva e venha vivenciar momentos especiais em meio ao silêncio da natureza, acompanhado de uma cozinha mediterrânea cheia de aromas e sabores.'],
            ['global','footer_about','html','Rodapé · texto institucional','Refúgio boutique em Mar Vermelho, Alagoas, na Suíça Alagoana. Hospedagem exclusiva para adultos, gastronomia mediterrânea e contemplação em meio à serra.'],
            ['global','footer_signature','text','Rodapé · assinatura','Suíça Alagoana · Mar Vermelho, AL'],

            ['home','hero_kicker','text','Hero · linha superior','Pousada · Mar Vermelho, Alagoas'],
            ['home','features_eyebrow','text','Diferenciais · etiqueta','Diferenciais'],
            ['home','features_title','html','Diferenciais · título','O que nos faz <em>únicos.</em>'],
            ['home','feature_1_title','text','Diferencial 1 · título','Cozinha Mediterrânea'],
            ['home','feature_1_body','text','Diferencial 1 · texto','Receitas autorais inspiradas no sul da França, Itália e Mediterrâneo, harmonizadas com vinhos especiais.'],
            ['home','feature_2_title','text','Diferencial 2 · título','Ritual da Fogueira'],
            ['home','feature_2_body','text','Diferencial 2 · texto','Noites de celebração à beira do fogo, com música, conversas e gastronomia em um ambiente íntimo e ancestral.'],
            ['home','feature_3_title','text','Diferencial 3 · título','Contemplação'],
            ['home','feature_3_body','text','Diferencial 3 · texto','Espaços de leitura, redário, mandala e o Caminho das Pedras, feitos para o reencontro consigo.'],
            ['home','chalets_eyebrow','text','Chalés · etiqueta','Acomodações'],
            ['home','chalets_title','html','Chalés · título','Chalés que <em>abraçam o jardim.</em>'],
            ['home','chalets_body','html','Chalés · texto','Cada detalhe foi pensado para proporcionar tranquilidade, aconchego, conforto, gentilezas e o contato constante com a natureza.'],
            ['home','gastro_body_2','html','Gastronomia · texto 2','Mesa farta, tempo desacelerado e harmonização com vinhos muito especiais. A gastronomia é o coração da experiência, não um adicional.'],
            ['home','itinerary_image','image','Itinerário teaser · imagem','https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1100&q=80'],
            ['home','itinerary_eyebrow','text','Itinerário teaser · etiqueta','Itinerário'],
            ['home','itinerary_title','html','Itinerário teaser · título','A viagem até a serra <em>já é parte da experiência.</em>'],
            ['home','itinerary_body','html','Itinerário teaser · texto','Preparamos um roteiro aconchegante pela serra, com paradas em Capela, Cajueiro e Viçosa antes da chegada a <strong>Mar Vermelho</strong>. Artesanato, cerâmica, caldinho e café transformam o caminho em parte da experiência.'],

            ['chales','standard_eyebrow','text','Aromáticos · etiqueta','Standard'],
            ['chales','standard_title','html','Aromáticos · título','Chalés <em>Aromáticos.</em>'],
            ['chales','standard_note','text','Aromáticos · observação','Cada chalé acomoda apenas 2 pessoas, exclusivamente para adultos.'],
            ['chales','amenity_min_stay','text','Comodidade · mínimo','Mínimo 2 noites'],
            ['chales','amenity_breakfast','text','Comodidade · café','Café da manhã'],
            ['chales','amenity_capacity','text','Comodidade · capacidade','Acomoda 2 pessoas'],

            ['experiencias','rituals_eyebrow','text','Rituais · etiqueta','Os 4 rituais'],
            ['experiencias','rituals_title','html','Rituais · título','Cada experiência, um <em>presente.</em>'],
            ['experiencias','rituals_hint','text','Rituais · dica mobile','Nas fotos, toque para ampliar e navegar pelo lightbox.'],
            ['experiencias','benefits_eyebrow','text','Benefícios · etiqueta','Bem-estar incluso'],
            ['experiencias','benefits_title','html','Benefícios · título','Cuidado em cada <em>detalhe.</em>'],
            ['experiencias','benefit_1_title','text','Benefício 1 · título','Espaço Redário'],
            ['experiencias','benefit_1_body','text','Benefício 1 · texto','Para a leitura e a contemplação no jardim aromático.'],
            ['experiencias','benefit_2_title','text','Benefício 2 · título','Biblioteca'],
            ['experiencias','benefit_2_body','text','Benefício 2 · texto','Curadoria de livros para acompanhar o silêncio da serra.'],
            ['experiencias','benefit_3_title','text','Benefício 3 · título','Carta de Vinhos'],
            ['experiencias','benefit_3_body','text','Benefício 3 · texto','Rótulos selecionados para harmonizar cada momento.'],

            ['a-pousada','essence_eyebrow','text','Essência · etiqueta','Nossa essência'],
            ['a-pousada','essence_title','html','Essência · título','Sabores, cores e bem-estar entre as serras de Alagoas.'],
            ['a-pousada','intro_body_2','html','Introdução · parágrafo 2','Localizada entre as serras de Alagoas e com uma culinária sofisticada de influência europeia, a pousada oferece uma verdadeira viagem de sabores, cores e bem-estar.'],
            ['a-pousada','intro_body_3','html','Introdução · parágrafo 3','A paisagem exuberante e a simplicidade do local criam um ambiente acolhedor, onde é possível sentir uma paz única e verdadeira. É um convite para quem deseja se conectar com amigos e consigo em um lugar de cheiros, brisa do campo, vinho e música boa.'],
            ['a-pousada','intro_note','html','Introdução · observação','Importante: traga um bom calçado e agasalho para aproveitar o clima frio da nossa <span>"Suíça Alagoana"</span>.'],
            ['a-pousada','adults_image','image','Adultos · imagem','https://images.unsplash.com/photo-1499678329028-101435549a4e?auto=format&fit=crop&w=1100&q=80'],
            ['a-pousada','adults_eyebrow','text','Adultos · etiqueta','Para adultos'],
            ['a-pousada','adults_title','html','Adultos · título','Uma proposta de <em>relaxamento</em> em meio ao silêncio.'],
            ['a-pousada','adults_body_1','html','Adultos · texto 1','Exclusivamente para adultos, não dispomos de atrativos infantis. Nossa proposta privilegia silêncio, contemplação, descanso e uma culinária exótica e experimental, sempre em harmonização com vinhos especiais.'],
            ['a-pousada','adults_body_2','html','Adultos · texto 2','Digamos que nossos hóspedes fazem uma linda viagem para outros países sem precisar sair de nossa querida Alagoas.'],

            ['gastronomia','philosophy_eyebrow','text','Filosofia · etiqueta','A nossa filosofia'],
            ['gastronomia','gallery_eyebrow','text','Galeria · etiqueta','Galeria'],
            ['gastronomia','gallery_title','html','Galeria · título','Aromas, cores e <em>texturas.</em>'],

            ['taberna','about_eyebrow','text','Sobre · etiqueta','Sobre a casa'],
            ['taberna','about_image_1','image','Galeria · imagem 1','https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85'],
            ['taberna','about_image_2','image','Galeria · imagem 2','https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85'],
            ['taberna','about_image_3','image','Galeria · imagem 3','https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=1200&q=85'],

            ['localizacao','hero_subtitle','html','Hero · subtítulo','Região serrana de Alagoas, a chamada <em>Suíça Alagoana</em>.'],
            ['localizacao','address_eyebrow','text','Endereço · etiqueta','Endereço'],
            ['localizacao','address_title','html','Endereço · título','Mar Vermelho<br><span>Alagoas, Brasil</span>'],
            ['localizacao','region_eyebrow','text','Região · etiqueta','A região'],
            ['localizacao','region_title','html','Região · título','Um lugar de <em>beleza incomparável.</em>'],
            ['localizacao','region_body_1','html','Região · texto 1','Localizado na região serrana de Alagoas, no município de Mar Vermelho. Durante o nascer ou pôr do sol, os amantes da contemplação podem apreciar uma paisagem espetacular, com tons de verde e floral que se misturam em um céu deslumbrante.'],
            ['localizacao','region_body_2','html','Região · texto 2','Mar Vermelho é um destino perfeito para quem busca momentos aconchegantes, chocolates quentes, fondues de queijo e bons vinhos em volta da lareira.'],

            ['itinerario','hero_subtitle','html','Hero · subtítulo','O trajeto até a serra é parte da hospedagem.'],
            ['itinerario','intro_eyebrow','text','Roteiro · etiqueta','Roteiro aconchegante'],
            ['itinerario','intro_title','html','Roteiro · título','Paradas especiais <em>pela serra.</em>'],
            ['itinerario','intro_body','html','Roteiro · texto','O caminho até a pousada parte de Maceió e segue por Capela, Cajueiro e Viçosa antes da chegada a Mar Vermelho. A proposta é aproveitar o percurso com calma, fazendo pausas especiais indicadas pela Aromas da Serra.'],
            ['itinerario','quote','html','Frase final','"Qualquer orientação, estamos à disposição."'],

            ['global','marquee_1','text','Marquee 1','Mar Vermelho · AL'],
            ['global','marquee_2','text','Marquee 2','Cozinha Mediterrânea'],
            ['global','marquee_3','text','Marquee 3','Ritual da Fogueira'],
            ['global','marquee_4','text','Marquee 4','Chá da Tarde'],
            ['global','marquee_5','text','Marquee 5','Temporada de Fondue'],
            ['global','marquee_6','text','Marquee 6','Suíça Alagoana'],

            ['a-pousada','feature_1_title','text','Card 1 · título','Clima serrano'],
            ['a-pousada','feature_1_body','text','Card 1 · texto','Temperaturas amenas e brisa do campo durante todo o ano.'],
            ['a-pousada','feature_2_title','text','Card 2 · título','Adults only'],
            ['a-pousada','feature_2_body','text','Card 2 · texto','Atmosfera contemplativa, projetada para o descanso profundo.'],
            ['a-pousada','feature_3_title','text','Card 3 · título','Hospitalidade'],
            ['a-pousada','feature_3_body','text','Card 3 · texto','Atendimento atento e personalizado para cada hóspede.'],

            ['gastronomia','bullet_1','text','Bullet 1','Pães rústicos artesanais'],
            ['gastronomia','bullet_2','text','Bullet 2','Carta de vinhos curada'],
            ['gastronomia','bullet_3','text','Bullet 3','Temporada de fondues'],
            ['gastronomia','bullet_4','text','Bullet 4','Ervas da Mandala'],
            ['gastronomia','carousel_image_1','image','Carrossel · imagem 1','https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85'],
            ['gastronomia','carousel_image_2','image','Carrossel · imagem 2','https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85'],
            ['gastronomia','carousel_image_3','image','Carrossel · imagem 3','https://images.unsplash.com/photo-1473093226795-af9932fe5856?auto=format&fit=crop&w=1200&q=85'],
            ['gastronomia','carousel_image_4','image','Carrossel · imagem 4','https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=1200&q=85'],

            ['taberna','bullet_1','text','Bullet 1','Aberto para almoços e jantares · reservas recomendadas'],
            ['taberna','bullet_2','text','Bullet 2','Espaço íntimo · capacidade limitada'],
            ['taberna','bullet_3','text','Bullet 3','Temporada de fondues no inverno'],

            ['itinerario','stop_1_title','text','Parada 1 · título','Capela · Artesanato e Caldinho'],
            ['itinerario','stop_1_body','html','Parada 1 · texto','Ao chegar em Capela, façam duas paradas especiais: o artesanato do Sr. João de Barro e o tradicional Caldinho de Capela, que funciona até às 12h, quase uma pausa obrigatória para quem passa por lá.'],
            ['itinerario','stop_1_icon','text','Parada 1 · ícone','hand-heart'],
            ['itinerario','stop_1_image','image','Parada 1 · imagem','https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=800&q=80'],
            ['itinerario','stop_2_title','text','Parada 2 · título','Cajueiro · Cerâmica Caju Queimado'],
            ['itinerario','stop_2_body','html','Parada 2 · texto','Seguindo para Cajueiro, aproveitem a Cerâmica Caju Queimado, a entrada é sinalizada.'],
            ['itinerario','stop_2_icon','text','Parada 2 · ícone','palette'],
            ['itinerario','stop_2_image','image','Parada 2 · imagem','https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=800&q=80'],
            ['itinerario','stop_3_title','text','Parada 3 · título','Viçosa · Padaria do Creso'],
            ['itinerario','stop_3_body','html','Parada 3 · texto','Chegando em Viçosa, façam uma pausa na Padaria do Creso para um café acolhedor.'],
            ['itinerario','stop_3_icon','text','Parada 3 · ícone','coffee'],
            ['itinerario','stop_3_image','image','Parada 3 · imagem','https://images.unsplash.com/photo-1485921325833-c519f76c4927?auto=format&fit=crop&w=800&q=80'],
            ['itinerario','stop_4_title','text','Parada 4 · título','Mar Vermelho · Chegada à Pousada'],
            ['itinerario','stop_4_body','html','Parada 4 · texto','Depois, sigam para Mar Vermelho para viver o descanso e aconchego conosco.'],
            ['itinerario','stop_4_icon','text','Parada 4 · ícone','home'],
            ['itinerario','stop_4_image','image','Parada 4 · imagem','https://images.unsplash.com/photo-1499678329028-101435549a4e?auto=format&fit=crop&w=800&q=80'],
    ];
    $bcheck = $pdo->prepare('SELECT 1 FROM page_blocks WHERE page=? AND block_key=?');
    $bins   = $pdo->prepare('INSERT INTO page_blocks (page,block_key,type,label,value,sort_order) VALUES (?,?,?,?,?,?)');
    foreach ($blocks as $i => $b) {
        [$pg,$k,$t,$lbl,$v] = $b;
        $bcheck->execute([$pg,$k]);
        if (!$bcheck->fetchColumn()) $bins->execute([$pg,$k,$t,$lbl,$v,$i]);
    }

    // Seed chalets and experiences from the public site content.
    $chalets = [
        ['lavanda','Chalé Lavanda','Luxo · Vista panorâmica','Vista panorâmica para a serra','Único com vista panorâmica para a serra. Varanda privativa com rede, decoração charmosa e conforto para uma hospedagem inesquecível.',[
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1200&q=85',
        ],10],
        ['manjericao','Chalé Manjericão','Luxo VIP · Vista jardim','Vista para o jardim','Refúgio sofisticado para descanso, reconexão e permanências mais prolongadas, com estrutura completa para momentos de encontro consigo.',[
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1567016432779-094069958ea5?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=85',
        ],20],
        ['alecrim','Chalé Alecrim','Standard · Vista jardim','Vista jardim','Chalé aromático para casal, com aconchego, café da manhã incluído e atmosfera tranquila para adultos.',[
            'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=900&q=80',
        ],30],
        ['capim-cidreira','Chalé Capim Cidreira','Standard · Vista jardim','Vista jardim','Chalé aromático para casal, com vista para o jardim perfumado e descanso em contato com a natureza.',[
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80',
        ],40],
        ['calendula','Chalé Calêndula','Standard · Vista jardim','Vista jardim','Chalé aromático com decoração charmosa, café da manhã incluído e clima íntimo para duas pessoas.',[
            'https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1567016432779-094069958ea5?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=900&q=80',
        ],50],
        ['erva-doce','Chalé Erva Doce','Standard · Vista jardim','Vista jardim','Chalé aromático para adultos, com conforto, silêncio e visual do jardim da pousada.',[
            'https://images.unsplash.com/photo-1471666875520-c75081f42081?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=80',
        ],60],
        ['melissa','Chalé Melissa','Standard · Vista jardim','Vista jardim','Chalé aromático para quem busca pausa, café da manhã incluso e uma estadia simples e acolhedora.',[
            'https://images.unsplash.com/photo-1527842891421-42eec6e703ea?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=900&q=80',
        ],70],
        ['jasmim','Chalé Jasmim','Standard · Vista jardim','Vista jardim','Chalé aromático com aconchego para duas pessoas, jardim perfumado e mínimo de duas noites.',[
            'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
        ],80],
    ];
    $chaletCheck = $pdo->prepare('SELECT 1 FROM chalets WHERE slug=?');
    $chaletInsert = $pdo->prepare('INSERT INTO chalets (slug,name,category,view,description,cover,gallery,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?,?)');
    foreach ($chalets as [$slug,$name,$category,$view,$description,$gallery,$order]) {
        $chaletCheck->execute([$slug]);
        if (!$chaletCheck->fetchColumn()) $chaletInsert->execute([$slug,$name,$category,$view,$description,$gallery[0] ?? '',sanitize_public_image_items($gallery),1,$order]);
    }

    $experiences = [
        ['ritual-da-fogueira','Ritual da Fogueira','flame','Encontros à beira do fogo, com música, conversas e gastronomia em ambiente íntimo e ancestral.',[
            'https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1455218873509-8097305ee378?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1455218873509-8097305ee378?auto=format&fit=crop&w=1200&q=85',
        ],10],
        ['cha-da-tarde','Chá da Tarde','coffee','Bolos artesanais, pães rústicos e chás especiais ao final do dia, em frente à serra.',[
            'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1545665277-5937489579f2?auto=format&fit=crop&w=1200&q=85',
        ],20],
        ['mandala-horta-organica','Mandala — horta orgânica','sprout','Ervas, flores comestíveis e temperos colhidos diretamente para a sua mesa.',[
            'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1492496913980-501348b61469?auto=format&fit=crop&w=1200&q=85',
        ],30],
        ['caminho-das-pedras','Caminho das Pedras','mountain','Trajeto contemplativo pelo bosque da pousada, com silêncio, presença e a serra à vista.',[
            'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=85',
            'https://images.unsplash.com/photo-1473773508845-188df298d2d1?auto=format&fit=crop&w=1200&q=85',
        ],40],
    ];
    $experienceCheck = $pdo->prepare('SELECT 1 FROM experiences WHERE slug=?');
    $experienceInsert = $pdo->prepare('INSERT INTO experiences (slug,title,icon,description,cover,gallery,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?)');
    foreach ([] as [$slug,$title,$icon,$description,$gallery,$order]) {
        $experienceCheck->execute([$slug]);
        if (!$experienceCheck->fetchColumn()) $experienceInsert->execute([$slug,$title,$icon,$description,$gallery[0] ?? '',sanitize_public_image_items($gallery),1,$order]);
    }

    seed_catalog_revision_20260508($pdo);
    seed_catalog_revision_20260508_label_cleanup($pdo);
    seed_content_revision_20260511_refinements($pdo);
}

function block(string $page, string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        try {
            $cache = [];
            $rows = db()->query('SELECT page, block_key, value FROM page_blocks')->fetchAll();
            foreach ($rows as $r) $cache[$r['page'].'|'.$r['block_key']] = $r['value'] ?? '';
        } catch (Throwable $e) { $cache = []; }
    }
    $v = $cache[$page.'|'.$key] ?? null;
    return ($v !== null && $v !== '') ? $v : $default;
}

function get_setting(string $key, string $default = ''): string {
    $stmt = db()->prepare('SELECT value FROM settings WHERE `key` = ?');
    $stmt->execute([$key]);
    $v = $stmt->fetchColumn();
    return $v !== false ? (string)$v : $default;
}
function set_setting(string $key, string $value): void {
    if (db_driver() === 'mysql') {
        $stmt = db()->prepare('INSERT INTO settings (`key`,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)');
    } else {
        $stmt = db()->prepare('INSERT INTO settings (`key`,value) VALUES (?,?) ON CONFLICT(`key`) DO UPDATE SET value=excluded.value');
    }
    $stmt->execute([$key, $value]);
}

function slugify(string $s): string {
    $s = strtolower(strtr($s, ['ç'=>'c','ã'=>'a','á'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','â'=>'a','à'=>'a']));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

function sanitize_block_html(string $html): string {
    $html = strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li><a><blockquote><h2><h3>');
    $html = preg_replace('/\s(?:on\w+|style|src|srcset|formaction|xlink:href)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
    $html = preg_replace_callback('/\shref\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', function(array $match): string {
        $url = trim($match[1], "\"' \t\n\r\0\x0B");
        $decoded = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $scheme = strtolower((string)parse_url($decoded, PHP_URL_SCHEME));
        if ($scheme === '' || in_array($scheme, ['http', 'https', 'mailto', 'tel'], true)) {
            return ' href="' . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
        }
        return ' href="#"';
    }, $html) ?? $html;
    return trim($html);
}

function sanitize_public_image_url(string $url): string {
    $url = trim($url);
    if ($url === '') return '';
    if (str_starts_with($url, '/') || str_starts_with($url, 'assets/') || str_starts_with($url, FRONT_BASE . '/assets/')) return $url;
    $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true) ? $url : '';
}

function sanitize_public_video_url(string $url): string {
    $url = trim($url);
    if ($url === '') return '';
    $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
    $host = strtolower((string)parse_url($url, PHP_URL_HOST));
    $allowedHosts = ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be', 'www.youtu.be', 'youtube-nocookie.com', 'www.youtube-nocookie.com'];
    return in_array($scheme, ['http', 'https'], true) && in_array($host, $allowedHosts, true) ? $url : '';
}

function sanitize_public_image_list(string $urls): string {
    $safe = [];
    foreach (preg_split('/\R+/', $urls) ?: [] as $url) {
        $clean = sanitize_public_image_url($url);
        if ($clean !== '') $safe[] = $clean;
    }
    return implode("\n", $safe);
}

function image_list_to_array(string $urls): array {
    $items = [];
    foreach (preg_split('/\R+/', $urls) ?: [] as $url) {
        $clean = sanitize_public_image_url($url);
        if ($clean !== '') $items[] = $clean;
    }
    return array_values(array_unique($items));
}

function sanitize_public_image_items(array $urls): string {
    $safe = [];
    foreach ($urls as $url) {
        $clean = sanitize_public_image_url((string)$url);
        if ($clean !== '') $safe[] = $clean;
    }
    return implode("\n", array_values(array_unique($safe)));
}

function upload_gallery_files(string $field, string $prefix): array {
    if (empty($_FILES[$field]['name']) || !is_array($_FILES[$field]['name'])) return [];

    $paths = [];
    $files = $_FILES[$field];
    $total = count($files['name']);
    for ($i = 0; $i < $total; $i++) {
        if (empty($files['name'][$i])) continue;
        $file = [
            'name' => $files['name'][$i],
            'tmp_name' => $files['tmp_name'][$i] ?? '',
            'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
            'size' => $files['size'][$i] ?? 0,
        ];
        if ($path = upload_file($file, $prefix)) $paths[] = $path;
    }
    return $paths;
}

function upload_file(array $file, string $prefix = 'img'): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
    $allowed = ['jpg'=>['image/jpeg','image/pjpeg'],'jpeg'=>['image/jpeg','image/pjpeg'],'png'=>['image/png'],'webp'=>['image/webp']];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!isset($allowed[$ext])) return null;
    if ($file['size'] > 8 * 1024 * 1024) return null;
    $tmp = $file['tmp_name'] ?? '';
    if (!is_uploaded_file($tmp)) return null;
    $mime = function_exists('finfo_open') ? (function() use ($tmp) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) return '';
        $detected = finfo_file($finfo, $tmp) ?: '';
        finfo_close($finfo);
        return $detected;
    })() : (mime_content_type($tmp) ?: '');
    if (!in_array($mime, $allowed[$ext], true)) return null;
    $name = $prefix . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
    $dest = UPLOAD_DIR . DIRECTORY_SEPARATOR . $name;
    if (!move_uploaded_file($tmp, $dest)) return null;
    return front_url('assets/uploads/' . $name);
}

function safe_identifier(string $name): string {
    if (!preg_match('/^[a-z_][a-z0-9_]*$/i', $name)) throw new InvalidArgumentException('Invalid identifier');
    return $name;
}

function has_column(PDO $pdo, string $table, string $column): bool {
    $table = safe_identifier($table);
    $column = safe_identifier($column);
    if (db_driver() === 'mysql') {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        return (bool)$stmt->fetch();
    }
    $rows = $pdo->query("PRAGMA table_info({$table})")->fetchAll();
    foreach ($rows as $row) {
        if (strcasecmp((string)($row['name'] ?? ''), $column) === 0) return true;
    }
    return false;
}

function ensure_column(PDO $pdo, string $table, string $column, string $definition): void {
    $table = safe_identifier($table);
    $column = safe_identifier($column);
    if (!has_column($pdo, $table, $column)) $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
}

function seed_upsert_block(PDO $pdo, string $page, string $key, string $type, string $label, string $value, int $sortOrder): void {
    $stmt = $pdo->prepare('SELECT id FROM page_blocks WHERE page=? AND block_key=?');
    $stmt->execute([$page, $key]);
    $id = $stmt->fetchColumn();
    if ($id) {
        $upd = $pdo->prepare('UPDATE page_blocks SET type=?, label=?, value=?, sort_order=? WHERE id=?');
        $upd->execute([$type, $label, $value, $sortOrder, (int)$id]);
        return;
    }
    $ins = $pdo->prepare('INSERT INTO page_blocks (page,block_key,type,label,value,sort_order) VALUES (?,?,?,?,?,?)');
    $ins->execute([$page, $key, $type, $label, $value, $sortOrder]);
}

function seed_upsert_chalet(PDO $pdo, array $data): void {
    $stmt = $pdo->prepare('SELECT * FROM chalets WHERE slug=?');
    $stmt->execute([$data['slug']]);
    $row = $stmt->fetch();
    $cover = $row['cover'] ?? ($data['cover'] ?? '');
    $gallery = $row['gallery'] ?? ($data['gallery'] ?? '');
    if ($row) {
        $upd = $pdo->prepare('UPDATE chalets SET name=?, category=?, view=?, description=?, cover=?, gallery=?, video_url=?, video_label=?, is_active=1, sort_order=?, updated_at=CURRENT_TIMESTAMP WHERE id=?');
        $upd->execute([$data['name'], $data['category'], $data['view'], $data['description'], $cover, $gallery, $data['video_url'] ?? '', $data['video_label'] ?? '', $data['sort_order'], (int)$row['id']]);
        return;
    }
    $ins = $pdo->prepare('INSERT INTO chalets (slug,name,category,view,description,cover,gallery,video_url,video_label,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
    $ins->execute([$data['slug'], $data['name'], $data['category'], $data['view'], $data['description'], $cover, $gallery, $data['video_url'] ?? '', $data['video_label'] ?? '', 1, $data['sort_order']]);
}

function seed_upsert_experience(PDO $pdo, array $data, array $legacySlugs = []): void {
    $slugs = array_values(array_unique(array_merge([$data['slug']], $legacySlugs)));
    $row = null;
    foreach ($slugs as $slug) {
        $stmt = $pdo->prepare('SELECT * FROM experiences WHERE slug=?');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        if ($row) break;
    }
    $cover = $row['cover'] ?? ($data['cover'] ?? '');
    $gallery = $row['gallery'] ?? ($data['gallery'] ?? '');
    if ($row) {
        $upd = $pdo->prepare('UPDATE experiences SET slug=?, title=?, icon=?, description=?, cover=?, gallery=?, is_active=1, sort_order=?, updated_at=CURRENT_TIMESTAMP WHERE id=?');
        $upd->execute([$data['slug'], $data['title'], $data['icon'], $data['description'], $cover, $gallery, $data['sort_order'], (int)$row['id']]);
        foreach ($legacySlugs as $legacySlug) {
            if ($legacySlug !== $data['slug']) $pdo->prepare('DELETE FROM experiences WHERE slug=?')->execute([$legacySlug]);
        }
        return;
    }
    $ins = $pdo->prepare('INSERT INTO experiences (slug,title,icon,description,cover,gallery,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?)');
    $ins->execute([$data['slug'], $data['title'], $data['icon'], $data['description'], $cover, $gallery, 1, $data['sort_order']]);
}

function seed_upsert_product(PDO $pdo, array $data): void {
    $stmt = $pdo->prepare('SELECT id FROM products WHERE slug=?');
    $stmt->execute([$data['slug']]);
    $id = $stmt->fetchColumn();
    if ($id) {
        $upd = $pdo->prepare('UPDATE products SET title=?, category=?, description=?, flavors=?, cover=?, is_active=1, sort_order=?, updated_at=CURRENT_TIMESTAMP WHERE id=?');
        $upd->execute([$data['title'], $data['category'], $data['description'], $data['flavors'], $data['cover'], $data['sort_order'], (int)$id]);
        return;
    }
    $ins = $pdo->prepare('INSERT INTO products (slug,title,category,description,flavors,cover,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?)');
    $ins->execute([$data['slug'], $data['title'], $data['category'], $data['description'], $data['flavors'], $data['cover'], 1, $data['sort_order']]);
}

function seed_upsert_testimonial(PDO $pdo, array $data): void {
    $stmt = $pdo->prepare('SELECT id FROM testimonials WHERE author=?');
    $stmt->execute([$data['author']]);
    $id = $stmt->fetchColumn();
    if ($id) {
        $upd = $pdo->prepare('UPDATE testimonials SET quote=?, context=?, rating=?, is_active=1, sort_order=?, updated_at=CURRENT_TIMESTAMP WHERE id=?');
        $upd->execute([$data['quote'], $data['context'], $data['rating'], $data['sort_order'], (int)$id]);
        return;
    }
    $ins = $pdo->prepare('INSERT INTO testimonials (author,quote,context,rating,is_active,sort_order) VALUES (?,?,?,?,?,?)');
    $ins->execute([$data['author'], $data['quote'], $data['context'], $data['rating'], 1, $data['sort_order']]);
}

function seed_catalog_revision_20260508(PDO $pdo): void {
    foreach (['drink-de-boas-vindas' => 'cha-de-boas-vindas', 'cha-da-tarde' => 'ritual-do-cha-da-tarde', 'mandala' => 'mandala-horta-organica'] as $currentSlug => $legacySlug) {
        $stmt = $pdo->prepare('SELECT id FROM experiences WHERE slug=?');
        $stmt->execute([$currentSlug]);
        if ($stmt->fetchColumn()) $pdo->prepare('DELETE FROM experiences WHERE slug=?')->execute([$legacySlug]);
    }

    $revisionKey = 'content_revision_20260508_catalog_v2';
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE `key`=?');
    $stmt->execute([$revisionKey]);
    if ($stmt->fetchColumn() === '1') return;

    $standardDescription = 'O Chalé Standard oferece uma estada confortável e aconchegante em meio à natureza. Com estrutura completa, decoração charmosa e vista para o nosso jardim perfumado, é o refúgio perfeito para momentos relaxantes e especiais. Seja bem-vindo e sinta-se em casa.';
    $standardVideo = 'https://www.youtube.com/shorts/P-aOeHb1gNE';
    $standardGallery = sanitize_public_image_items([
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=900&q=80',
    ]);
    seed_upsert_chalet($pdo, [
        'slug' => 'lavanda',
        'name' => 'Chalé Lavanda',
        'category' => 'Luxo Varanda · Vista panorâmica',
        'view' => 'Varanda privativa com vista para a natureza',
        'description' => 'O Chalé Luxo Varanda da Pousada Aromas da Serra é a escolha perfeita para quem deseja uma hospedagem inesquecível. Com vista ampla para a natureza ao redor, decoração charmosa e aconchegante, oferece conforto, privacidade e uma varanda que convida ao descanso. É um refúgio para contemplar a paisagem, respirar o clima da serra e criar memórias especiais em meio à beleza natural.',
        'cover' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85',
        'gallery' => '',
        'video_url' => 'https://www.youtube.com/shorts/N0FYYh8OmMo?feature=share',
        'video_label' => 'Ver vídeo do Chalé Lavanda',
        'sort_order' => 10,
    ]);
    seed_upsert_chalet($pdo, [
        'slug' => 'manjericao',
        'name' => 'Chalé Manjericão',
        'category' => 'Luxo VIP · Vivência Sabática',
        'view' => 'Vista para o jardim e estrutura para permanências prolongadas',
        'description' => 'O Chalé Luxo VIP Manjericão é um refúgio charmoso e sofisticado para quem busca descanso, silêncio e reconexão. Com estrutura completa e copa própria, foi pensado para uma Vivência Sabática: um período de pausa, realização pessoal, novas experiências e amadurecimento espiritual ou profissional. É um convite ao bem-estar, à presença e à felicidade.',
        'cover' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=85',
        'gallery' => '',
        'video_url' => 'https://www.youtube.com/shorts/XJ6yeT99HYc',
        'video_label' => 'Ver vídeo do Chalé Manjericão',
        'sort_order' => 20,
    ]);
    $standardChalets = ['alecrim' => 'Alecrim', 'capim-cidreira' => 'Capim Cidreira', 'calendula' => 'Calêndula', 'erva-doce' => 'Erva Doce', 'melissa' => 'Melissa', 'jasmim' => 'Jasmim'];
    $standardIndex = 0;
    foreach ($standardChalets as $slug => $name) {
        seed_upsert_chalet($pdo, [
            'slug' => $slug,
            'name' => 'Chalé ' . $name,
            'category' => 'Standard · Vista jardim',
            'view' => 'Vista para o jardim perfumado',
            'description' => $standardDescription,
            'cover' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80',
            'gallery' => $standardGallery,
            'video_url' => $standardVideo,
            'video_label' => 'Ver vídeo do Chalé Standard',
            'sort_order' => 30 + ($standardIndex * 10),
        ]);
        $standardIndex++;
    }

    $experienceGallery = sanitize_public_image_items([
        'https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=1200&q=85',
        'https://images.unsplash.com/photo-1455218873509-8097305ee378?auto=format&fit=crop&w=1200&q=85',
    ]);
    $experiences = [
        ['drink-de-boas-vindas', ['cha-de-boas-vindas'], 'Drink de Boas-Vindas', 'glass-water', 'O drink de boas-vindas marca a chegada com frescor, delicadeza e presença. Preparado para receber cada hóspede com cuidado, ele abre a estadia de forma leve e acolhedora, como um primeiro brinde ao descanso na serra.', 'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=1200&q=85', 10],
        ['cha-da-tarde', ['ritual-do-cha-da-tarde'], 'Chá da Tarde', 'coffee', 'O chá da tarde é uma experiência própria da hospedagem, pensada para desacelerar o dia com bolos artesanais, pães, infusões e conversas tranquilas. É uma pausa afetuosa, sem pressa, para saborear a serra em outro ritmo.', 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=85', 20],
        ['ritual-da-fogueira', [], 'Ritual da Fogueira · Magia do Fogo', 'flame', 'O fogo é nosso aliado sagrado: ilumina, aquece, acolhe e transmuta. Ao redor da fogueira celebramos fé, renovação e encontro, reconhecendo os muitos significados que esse elemento carrega para diferentes povos. Bem-vindo ao nosso encontro místico da fogueira.', 'https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=1200&q=85', 30],
        ['experiencia-gastronomica', [], 'Experiência Gastronômica', 'utensils', 'As receitas da pousada nascem de uma vivência cultural pela Suíça, pelo sul da França, pela Itália e pela região mediterrânea. Ingredientes frescos, ervas colhidas na horta e técnicas adaptadas ao estilo brasileiro resultam em pratos autorais, delicados e cheios de aromas.', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85', 40],
        ['fondue-de-queijo', ['fondue-de-queijo-moitie-moitie'], 'Fondue de Queijo', 'cooking-pot', 'Inspirado no clima dos Alpes Suíços, o fondue de queijo é preparado artesanalmente e servido à mesa como protagonista da temporada de inverno. Cremoso, generoso e perfeito para compartilhar, ele traduz o aconchego de Mar Vermelho em uma experiência gastronômica especial.', 'https://images.unsplash.com/photo-1485921325833-c519f76c4927?auto=format&fit=crop&w=1200&q=85', 50],
        ['taberna-do-monge', [], 'Taberna do Monge', 'wine', 'A Taberna do Monge nasceu como um projeto afetivo dos diretores, inspirado no imaginário das antigas tabernas e na vontade de criar um lugar de encontro verdadeiro. A decoração rústica, a lareira central, os vinhos selecionados e a cozinha autoral constroem uma atmosfera íntima, feita para comer bem, conversar sem pressa e celebrar os sabores da serra.', 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85', 60],
        ['espaco-contemplacao', [], 'Espaço Contemplação', 'mountain', 'Um ambiente pensado para meditação, silêncio e respiração profunda, com vista privilegiada para as montanhas e para a vegetação ao redor. É o lugar ideal para desacelerar do ritmo da vida moderna e reencontrar presença.', 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=85', 70],
        ['espaco-gourmet', [], 'Espaço Gourmet', 'chef-hat', 'Palco de bons encontros e experiências gastronômicas compartilhadas, o Espaço Gourmet convida hóspedes a criar, inovar e apresentar suas vivências culinárias com novos temperos, aromas e harmonizações especiais. Venha desfrutar desse momento e traga seus amigos.', 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1200&q=85', 80],
        ['caminho-das-pedras', [], 'Caminho das Pedras', 'footprints', 'O Caminho das Pedras é um percurso de cuidado criado para conduzir o hóspede a uma presença mais profunda. Inspirado nos cinco pilares de Sebastian Kneipp, água, movimento, alimentação, plantas medicinais e estilo de vida, ele une contato com a natureza, silêncio e intenção. Mais do que um trajeto pelo bosque, é um convite para sentir o corpo, acalmar a mente e reconhecer pequenos rituais de equilíbrio no cotidiano.', 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=85', 90],
        ['nossa-piscina', [], 'Nossa Piscina', 'waves', 'Um pequeno oásis de tranquilidade e beleza natural. Com águas cristalinas e vista deslumbrante, a piscina é perfeita para relaxar, refrescar-se e contemplar a natureza exuberante ao redor.', 'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=85', 100],
        ['mandala', ['mandala-horta-organica'], 'Mandala', 'flower-2', 'A Mandala representa o universo, a essência e a jornada espiritual de cada pessoa. Seus círculos simbolizam continuidade, conexão e harmonia. Na pousada, ela também expressa a arte de plantar, cultivar e colher aromas para a cozinha, além de inspirar tranquilidade, serenidade e concentração plena.', 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1200&q=85', 110],
        ['espaco-leitura', [], 'Espaço Leitura', 'book-open', 'Ler estimula o raciocínio, expande a imaginação e cria outros mundos dentro de nós. O Espaço Leitura é um convite para pausar, silenciar e se conectar com novas ideias em meio ao clima sereno da pousada.', 'https://images.unsplash.com/photo-1519682337058-a94d519337bc?auto=format&fit=crop&w=1200&q=85', 120],
    ];
    foreach ($experiences as [$slug,$legacy,$title,$icon,$description,$cover,$order]) {
        seed_upsert_experience($pdo, ['slug'=>$slug,'title'=>$title,'icon'=>$icon,'description'=>$description,'cover'=>$cover,'gallery'=>$experienceGallery,'sort_order'=>$order], $legacy);
    }

    seed_upsert_product($pdo, ['slug'=>'geleias-especiais','title'=>'Geleias Especiais','category'=>'Geleias artesanais','description'=>'Geleias preparadas em pequenos lotes, com frutas selecionadas e combinações que transitam entre o doce, o cítrico e o levemente picante.','flavors'=>"Jaboticaba\nAcerola com hibisco\nLaranja\nAmora\nManga com maracujá, cachaça e pimenta",'cover'=>'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?auto=format&fit=crop&w=1000&q=85','sort_order'=>10]);
    seed_upsert_product($pdo, ['slug'=>'paes-artesanais','title'=>'Pães Artesanais','category'=>'Pães da casa','description'=>'Pães de fermentação cuidadosa, pensados para acompanhar cafés, tábuas, entradas e momentos de partilha à mesa.','flavors'=>"Pão de ervas frescas\nPão sem glúten\nPão de multigrãos\nPão de azeitona\nPão ciabatta",'cover'=>'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=1000&q=85','sort_order'=>20]);
    seed_upsert_product($pdo, ['slug'=>'vinagre-aromatizado','title'=>'Vinagre Aromatizado','category'=>'Temperos autorais','description'=>'Vinagre aromático para finalizar saladas, legumes e preparos especiais com um toque fresco da casa.','flavors'=>'Mostruário sujeito à disponibilidade da produção artesanal.','cover'=>'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=1000&q=85','sort_order'=>30]);
    seed_upsert_product($pdo, ['slug'=>'sal-de-salvia-e-laranja','title'=>'Sal de Sálvia e Laranja','category'=>'Temperos autorais','description'=>'Sal aromatizado com sálvia e notas cítricas de laranja, ideal para realçar carnes, legumes, pães e finalizações.','flavors'=>'Sálvia e laranja','cover'=>'https://images.unsplash.com/photo-1506368249639-73a05d6f6488?auto=format&fit=crop&w=1000&q=85','sort_order'=>40]);
    seed_upsert_product($pdo, ['slug'=>'biscoito-quero-quero','title'=>'Biscoito Quero-Quero','category'=>'Delicadezas da casa','description'=>'Biscoito artesanal para acompanhar cafés, chás e pausas doces durante a estadia.','flavors'=>'Receita da casa','cover'=>'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=1000&q=85','sort_order'=>50]);

    seed_upsert_testimonial($pdo, ['author'=>'Maíra Almeida','context'=>'Estadia na pousada','quote'=>'Ficamos muito felizes com a nossa estadia na pousada. Tudo feito com muito bom gosto e carinho, atendimento impecável, acomodação excelente e uma culinária maravilhosa. A proposta social e ambiental da pousada também é um ponto de destaque. Recomendamos.','rating'=>5,'sort_order'=>10]);
    seed_upsert_testimonial($pdo, ['author'=>'Fabíolla Mello','context'=>'Hospedagem e gastronomia','quote'=>'Uma hospedagem surpreendente. Esperávamos uma boa experiência, mas a pousada, capitaneada por Jürg e Cristina, arrebatou nossos corações pela acolhida amorosa, comida delicada e saborosa, conversas animadas, paz, sossego e uma paisagem exuberante. Uma combinação perfeita para acalmar corpo e mente e voltar já com saudade. Também destaco a receptividade carinhosa da ágil Andressa. Super recomendo.','rating'=>5,'sort_order'=>20]);
    seed_upsert_testimonial($pdo, ['author'=>'Marina Fiuza','context'=>'Experiência completa','quote'=>'A experiência como um todo foi uma grata surpresa: os cuidados conosco, a gastronomia, o chá e a fogueira. Obrigada pela recepção e disponibilidade.','rating'=>5,'sort_order'=>30]);

    seed_upsert_block($pdo, 'chales', 'intro_standard', 'html', 'Introdução · Standard', 'O Chalé Standard oferece uma estada confortável e aconchegante em meio à natureza. Com estrutura completa, decoração charmosa e vista para o nosso lindo e perfumado jardim, é o refúgio perfeito para momentos relaxantes e especiais.<br><br>Chalés Alecrim, Capim Cidreira, Calêndula, Erva Doce, Melissa e Jasmim.', 204);
    seed_upsert_block($pdo, 'chales', 'standard_note', 'text', 'Aromáticos · observação', 'Mínimo 2 noites · Café da manhã · Acomoda 2 pessoas · Exclusivo para adultos.', 205);
    seed_upsert_block($pdo, 'experiencias', 'rituals_eyebrow', 'text', 'Experiências · etiqueta', 'Vivências da pousada', 210);
    seed_upsert_block($pdo, 'experiencias', 'rituals_title', 'html', 'Experiências · título', 'Rituais, sabores e espaços para <em>desacelerar.</em>', 211);
    seed_upsert_block($pdo, 'experiencias', 'rituals_hint', 'text', 'Experiências · dica mobile', 'Nas fotos, toque para ampliar e navegar pelo lightbox.', 212);

    set_setting($revisionKey, '1');
}

function seed_content_revision_20260511_refinements(PDO $pdo): void {
    $revisionKey = 'content_revision_20260511_refinements_v1';
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE `key`=?');
    $stmt->execute([$revisionKey]);
    if ($stmt->fetchColumn() === '1') return;

    set_setting('site_location', 'Mar Vermelho, Alagoas');

    seed_upsert_block($pdo, 'home', 'hero_eyebrow', 'text', 'Hero · etiqueta', 'Seja bem-vindo', 1);
    seed_upsert_block($pdo, 'home', 'hero_kicker', 'text', 'Hero · linha superior', 'Pousada · Mar Vermelho, Alagoas', 100);
    seed_upsert_block($pdo, 'home', 'hero_subtitle', 'html', 'Hero · subtítulo', 'Um refúgio exclusivo para adultos em meio à <strong>Suíça Alagoana</strong>, com gastronomia mediterrânea, contemplação e tempo para si.', 3);
    seed_upsert_block($pdo, 'home', 'manifesto_note', 'text', 'Manifesto · linha menor', 'Exclusivamente para adultos. Sem pressa. Apenas presença.', 7);
    seed_upsert_block($pdo, 'home', 'feature_2_body', 'text', 'Diferencial 2 · texto', 'Noites de celebração à beira do fogo, com música, conversas e gastronomia em um ambiente íntimo e ancestral.', 111);
    seed_upsert_block($pdo, 'home', 'feature_3_body', 'text', 'Diferencial 3 · texto', 'Espaços de leitura, redário, mandala e o Caminho das Pedras, feitos para o reencontro consigo.', 113);
    seed_upsert_block($pdo, 'home', 'chalets_body', 'html', 'Chalés · texto', 'Cada detalhe foi pensado para proporcionar tranquilidade, aconchego, conforto, gentilezas e o contato constante com a natureza.', 116);
    seed_upsert_block($pdo, 'home', 'itinerary_body', 'html', 'Itinerário teaser · texto', 'Preparamos um roteiro aconchegante pela serra, com paradas em Capela, Cajueiro e Viçosa antes da chegada a <strong>Mar Vermelho</strong>. Artesanato, cerâmica, caldinho e café transformam o caminho em parte da experiência.', 120);

    seed_upsert_block($pdo, 'a-pousada', 'intro_body', 'html', 'Introdução', 'Refúgio boutique em Mar Vermelho, com hospitalidade, gastronomia e contemplação.', 14);
    seed_upsert_block($pdo, 'a-pousada', 'intro_body_3', 'html', 'Introdução · parágrafo 3', 'A paisagem exuberante e a simplicidade do local criam um ambiente acolhedor, onde é possível sentir uma paz única e verdadeira. É um convite para quem deseja se conectar com amigos e consigo em um lugar de cheiros, brisa do campo, vinho e música boa.', 143);
    seed_upsert_block($pdo, 'a-pousada', 'adults_body_1', 'html', 'Adultos · texto 1', 'Exclusivamente para adultos, não dispomos de atrativos infantis. Nossa proposta privilegia silêncio, contemplação, descanso e uma culinária exótica e experimental, sempre em harmonização com vinhos especiais.', 148);

    seed_upsert_block($pdo, 'chales', 'hero_subtitle', 'html', 'Hero · subtítulo', 'Cada detalhe pensado com carinho, aconchego, conforto e gentilezas em contato constante com a natureza.', 18);
    seed_upsert_block($pdo, 'chales', 'standard_note', 'text', 'Aromáticos · observação', 'Mínimo 2 noites · Café da manhã · Acomoda 2 pessoas · Exclusivo para adultos.', 205);

    seed_upsert_block($pdo, 'experiencias', 'hero_subtitle', 'html', 'Hero · subtítulo', 'Experiências autorais desenhadas para o reencontro com o tempo, com a natureza e com você mesmo.', 29);
    seed_upsert_block($pdo, 'experiencias', 'rituals_title', 'html', 'Experiências · título', 'Rituais, sabores e espaços para <em>desacelerar.</em>', 211);

    seed_upsert_block($pdo, 'taberna', 'hero_subtitle', 'html', 'Hero · subtítulo', 'Um convite à mesa farta, mediterrânea, generosa, harmonizada com vinhos especiais e a vista da serra.', 26);
    seed_upsert_block($pdo, 'taberna', 'about_body', 'html', 'Sobre · texto', 'A Taberna do Monge é o restaurante boutique da pousada, aberto também a visitantes externos. Nasceu como um projeto afetivo dos diretores para reunir boa mesa, memória, conversa sem pressa e o clima acolhedor da serra. Receitas autorais, vinhos selecionados e uma lareira central fazem do espaço um dos encontros mais especiais da Aromas da Serra.', 28);

    seed_upsert_block($pdo, 'localizacao', 'hero_subtitle', 'html', 'Hero · subtítulo', 'Região serrana de Alagoas, a chamada <em>Suíça Alagoana</em>.', 154);
    seed_upsert_block($pdo, 'localizacao', 'address_title', 'html', 'Endereço · título', 'Mar Vermelho<br><span>Alagoas, Brasil</span>', 156);
    seed_upsert_block($pdo, 'localizacao', 'region_body_2', 'html', 'Região · texto 2', 'Mar Vermelho é um destino perfeito para quem busca momentos aconchegantes, chocolates quentes, fondues de queijo e bons vinhos em volta da lareira.', 160);

    seed_upsert_block($pdo, 'itinerario', 'hero_eyebrow', 'text', 'Hero · etiqueta', 'Roteiro aconchegante', 33);
    seed_upsert_block($pdo, 'itinerario', 'hero_title', 'html', 'Hero · título', 'Roteiro aconchegante <em>pela serra.</em>', 34);
    seed_upsert_block($pdo, 'itinerario', 'hero_subtitle', 'html', 'Hero · subtítulo', 'Da saída de Maceió à chegada em Mar Vermelho, o caminho também acolhe.', 161);
    seed_upsert_block($pdo, 'itinerario', 'intro_eyebrow', 'text', 'Roteiro · etiqueta', 'Roteiro aconchegante', 162);
    seed_upsert_block($pdo, 'itinerario', 'intro_title', 'html', 'Roteiro · título', 'Paradas especiais <em>pela serra.</em>', 163);
    seed_upsert_block($pdo, 'itinerario', 'intro_body', 'html', 'Roteiro · texto', 'O caminho até a pousada parte de Maceió e segue por Capela, Cajueiro e Viçosa antes da chegada a Mar Vermelho. A proposta é aproveitar o percurso com calma, fazendo pausas especiais indicadas pela Aromas da Serra.', 164);
    seed_upsert_block($pdo, 'itinerario', 'quote', 'html', 'Frase final', '"Qualquer orientação, estamos à disposição."', 165);
    seed_upsert_block($pdo, 'itinerario', 'stop_1_title', 'text', 'Parada 1 · título', 'Capela · Artesanato e Caldinho', 180);
    seed_upsert_block($pdo, 'itinerario', 'stop_1_body', 'html', 'Parada 1 · texto', 'Ao chegar em Capela, façam duas paradas especiais: o artesanato do Sr. João de Barro e o tradicional Caldinho de Capela, que funciona até às 12h, quase uma pausa obrigatória para quem passa por lá.', 181);
    seed_upsert_block($pdo, 'itinerario', 'stop_1_icon', 'text', 'Parada 1 · ícone', 'hand-heart', 182);
    seed_upsert_block($pdo, 'itinerario', 'stop_2_title', 'text', 'Parada 2 · título', 'Cajueiro · Cerâmica Caju Queimado', 184);
    seed_upsert_block($pdo, 'itinerario', 'stop_2_body', 'html', 'Parada 2 · texto', 'Seguindo para Cajueiro, aproveitem a Cerâmica Caju Queimado, a entrada é sinalizada.', 185);
    seed_upsert_block($pdo, 'itinerario', 'stop_2_icon', 'text', 'Parada 2 · ícone', 'palette', 186);
    seed_upsert_block($pdo, 'itinerario', 'stop_3_title', 'text', 'Parada 3 · título', 'Viçosa · Padaria do Creso', 188);
    seed_upsert_block($pdo, 'itinerario', 'stop_3_body', 'html', 'Parada 3 · texto', 'Chegando em Viçosa, façam uma pausa na Padaria do Creso para um café acolhedor.', 189);
    seed_upsert_block($pdo, 'itinerario', 'stop_3_icon', 'text', 'Parada 3 · ícone', 'coffee', 190);
    seed_upsert_block($pdo, 'itinerario', 'stop_4_title', 'text', 'Parada 4 · título', 'Mar Vermelho · Chegada à Pousada', 192);
    seed_upsert_block($pdo, 'itinerario', 'stop_4_body', 'html', 'Parada 4 · texto', 'Depois, sigam para Mar Vermelho para viver o descanso e aconchego conosco.', 193);
    seed_upsert_block($pdo, 'itinerario', 'stop_4_icon', 'text', 'Parada 4 · ícone', 'home', 194);
    seed_upsert_block($pdo, 'itinerario', 'stop_4_image', 'image', 'Parada 4 · imagem', 'https://images.unsplash.com/photo-1499678329028-101435549a4e?auto=format&fit=crop&w=800&q=80', 195);

    $deleteBlock = $pdo->prepare('DELETE FROM page_blocks WHERE page=? AND block_key=?');
    foreach ([5, 6] as $oldStop) {
        foreach (['title', 'body', 'icon', 'image'] as $suffix) $deleteBlock->execute(['itinerario', "stop_{$oldStop}_{$suffix}"]);
    }

    seed_upsert_block($pdo, 'global', 'cta_eyebrow', 'text', 'Rodapé CTA · etiqueta', 'Reserve sua estadia', 90);
    seed_upsert_block($pdo, 'global', 'footer_about', 'html', 'Rodapé · texto institucional', 'Refúgio boutique em Mar Vermelho, Alagoas, na Suíça Alagoana. Hospedagem exclusiva para adultos, gastronomia mediterrânea e contemplação em meio à serra.', 93);
    seed_upsert_block($pdo, 'global', 'footer_signature', 'text', 'Rodapé · assinatura', 'Suíça Alagoana · Mar Vermelho, AL', 94);
    seed_upsert_block($pdo, 'global', 'marquee_4', 'text', 'Marquee 4', 'Chá da Tarde', 104);

    $experienceGallery = sanitize_public_image_items([
        'https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=1200&q=85',
        'https://images.unsplash.com/photo-1455218873509-8097305ee378?auto=format&fit=crop&w=1200&q=85',
    ]);
    seed_upsert_experience($pdo, ['slug'=>'drink-de-boas-vindas','title'=>'Drink de Boas-Vindas','icon'=>'glass-water','description'=>'O drink de boas-vindas marca a chegada com frescor, delicadeza e presença. Preparado para receber cada hóspede com cuidado, ele abre a estadia de forma leve e acolhedora, como um primeiro brinde ao descanso na serra.','cover'=>'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>10], ['cha-de-boas-vindas']);
    seed_upsert_experience($pdo, ['slug'=>'cha-da-tarde','title'=>'Chá da Tarde','icon'=>'coffee','description'=>'O chá da tarde é uma experiência própria da hospedagem, pensada para desacelerar o dia com bolos artesanais, pães, infusões e conversas tranquilas. É uma pausa afetuosa, sem pressa, para saborear a serra em outro ritmo.','cover'=>'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>20], ['ritual-do-cha-da-tarde']);
    seed_upsert_experience($pdo, ['slug'=>'ritual-da-fogueira','title'=>'Ritual da Fogueira · Magia do Fogo','icon'=>'flame','description'=>'O fogo é nosso aliado sagrado: ilumina, aquece, acolhe e transmuta. Ao redor da fogueira celebramos fé, renovação e encontro, reconhecendo os muitos significados que esse elemento carrega para diferentes povos. Bem-vindo ao nosso encontro místico da fogueira.','cover'=>'https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>30], []);
    seed_upsert_experience($pdo, ['slug'=>'experiencia-gastronomica','title'=>'Experiência Gastronômica','icon'=>'utensils','description'=>'As receitas da pousada nascem de uma vivência cultural pela Suíça, pelo sul da França, pela Itália e pela região mediterrânea. Ingredientes frescos, ervas colhidas na horta e técnicas adaptadas ao estilo brasileiro resultam em pratos autorais, delicados e cheios de aromas.','cover'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>40], []);
    seed_upsert_experience($pdo, ['slug'=>'fondue-de-queijo','title'=>'Fondue de Queijo','icon'=>'cooking-pot','description'=>'Inspirado no clima dos Alpes Suíços, o fondue de queijo é preparado artesanalmente e servido à mesa como protagonista da temporada de inverno. Cremoso, generoso e perfeito para compartilhar, ele traduz o aconchego de Mar Vermelho em uma experiência gastronômica especial.','cover'=>'https://images.unsplash.com/photo-1485921325833-c519f76c4927?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>50], ['fondue-de-queijo-moitie-moitie']);
    seed_upsert_experience($pdo, ['slug'=>'taberna-do-monge','title'=>'Taberna do Monge','icon'=>'wine','description'=>'A Taberna do Monge nasceu como um projeto afetivo dos diretores, inspirado no imaginário das antigas tabernas e na vontade de criar um lugar de encontro verdadeiro. A decoração rústica, a lareira central, os vinhos selecionados e a cozinha autoral constroem uma atmosfera íntima, feita para comer bem, conversar sem pressa e celebrar os sabores da serra.','cover'=>'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>60], []);
    seed_upsert_experience($pdo, ['slug'=>'espaco-contemplacao','title'=>'Espaço Contemplação','icon'=>'mountain','description'=>'Um ambiente pensado para meditação, silêncio e respiração profunda, com vista privilegiada para as montanhas e para a vegetação ao redor. É o lugar ideal para desacelerar do ritmo da vida moderna e reencontrar presença.','cover'=>'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>70], []);
    seed_upsert_experience($pdo, ['slug'=>'espaco-gourmet','title'=>'Espaço Gourmet','icon'=>'chef-hat','description'=>'Palco de bons encontros e experiências gastronômicas compartilhadas, o Espaço Gourmet convida hóspedes a criar, inovar e apresentar suas vivências culinárias com novos temperos, aromas e harmonizações especiais. Venha desfrutar desse momento e traga seus amigos.','cover'=>'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>80], []);
    seed_upsert_experience($pdo, ['slug'=>'caminho-das-pedras','title'=>'Caminho das Pedras','icon'=>'footprints','description'=>'O Caminho das Pedras é um percurso de cuidado criado para conduzir o hóspede a uma presença mais profunda. Inspirado nos cinco pilares de Sebastian Kneipp, água, movimento, alimentação, plantas medicinais e estilo de vida, ele une contato com a natureza, silêncio e intenção. Mais do que um trajeto pelo bosque, é um convite para sentir o corpo, acalmar a mente e reconhecer pequenos rituais de equilíbrio no cotidiano.','cover'=>'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>90], []);
    seed_upsert_experience($pdo, ['slug'=>'nossa-piscina','title'=>'Nossa Piscina','icon'=>'waves','description'=>'Um pequeno oásis de tranquilidade e beleza natural. Com águas cristalinas e vista deslumbrante, a piscina é perfeita para relaxar, refrescar-se e contemplar a natureza exuberante ao redor.','cover'=>'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>100], []);
    seed_upsert_experience($pdo, ['slug'=>'mandala','title'=>'Mandala','icon'=>'flower-2','description'=>'A Mandala representa o universo, a essência e a jornada espiritual de cada pessoa. Seus círculos simbolizam continuidade, conexão e harmonia. Na pousada, ela também expressa a arte de plantar, cultivar e colher aromas para a cozinha, além de inspirar tranquilidade, serenidade e concentração plena.','cover'=>'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>110], ['mandala-horta-organica']);
    seed_upsert_experience($pdo, ['slug'=>'espaco-leitura','title'=>'Espaço Leitura','icon'=>'book-open','description'=>'Ler estimula o raciocínio, expande a imaginação e cria outros mundos dentro de nós. O Espaço Leitura é um convite para pausar, silenciar e se conectar com novas ideias em meio ao clima sereno da pousada.','cover'=>'https://images.unsplash.com/photo-1519682337058-a94d519337bc?auto=format&fit=crop&w=1200&q=85','gallery'=>$experienceGallery,'sort_order'=>120], []);

    seed_upsert_product($pdo, ['slug'=>'tempero-da-casa','title'=>'Tempero da Casa','category'=>'Temperos autorais','description'=>'Mistura autoral de ervas e especiarias preparada pela cozinha da pousada para dar acabamento a pratos, entradas e receitas afetivas. Um produto da casa para levar um pouco dos aromas da serra para a sua mesa.','flavors'=>'Receita da casa','cover'=>'https://images.unsplash.com/photo-1506368249639-73a05d6f6488?auto=format&fit=crop&w=1000&q=85','sort_order'=>50]);
    seed_upsert_product($pdo, ['slug'=>'biscoito-quero-quero','title'=>'Biscoito Quero-Quero','category'=>'Delicadezas da casa','description'=>'Biscoito artesanal para acompanhar cafés, chás e pausas doces durante a estadia.','flavors'=>'Receita da casa','cover'=>'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=1000&q=85','sort_order'=>60]);

    set_setting($revisionKey, '1');
}

function seed_catalog_revision_20260508_label_cleanup(PDO $pdo): void {
    $revisionKey = 'content_revision_20260508_label_cleanup_v1';
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE `key`=?');
    $stmt->execute([$revisionKey]);
    if ($stmt->fetchColumn() === '1') return;

    seed_upsert_block($pdo, 'produtos', 'intro_eyebrow', 'text', 'Introdução · etiqueta', 'Mostruário da casa', 206);
    set_setting($revisionKey, '1');
}

bootstrap_db();
