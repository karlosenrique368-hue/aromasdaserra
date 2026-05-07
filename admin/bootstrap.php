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
    header("Content-Security-Policy: default-src 'self'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; img-src 'self' data: blob: https://images.unsplash.com https://*.unsplash.com; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self'");
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
        'site_location'    => 'Mar Vermelho — Alagoas',
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
      ['home','hero_eyebrow','text','Hero · etiqueta','— Seja bem-vindo —'],
      ['home','hero_title','html','Hero · título','Onde o silêncio da serra<br><em>acolhe e transforma.</em>'],
      ['home','hero_subtitle','html','Hero · subtítulo','Um refúgio exclusivo para adultos em meio à <strong>Suíça Alagoana</strong> — gastronomia mediterrânea, contemplação e tempo para si.'],
      ['home','hero_image','image','Hero · imagem de fundo','https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=2000&q=80'],
      ['home','manifesto_eyebrow','text','Manifesto · etiqueta','Sobre a pousada'],
      ['home','manifesto_title','html','Manifesto · título','Um destino para quem busca <em>tranquilidade e conforto.</em>'],
      ['home','manifesto_body','html','Manifesto · texto','Localizada entre as serras de Alagoas, com culinária sofisticada de influência europeia, a Aromas da Serra oferece uma viagem de <em>sabores, cores e bem-estar</em>.'],
      ['home','manifesto_note','text','Manifesto · linha menor','Exclusivamente para adultos. Sem TVs nos quartos. Sem pressa. Apenas presença.'],
      ['home','gastro_title','html','Gastronomia · título','Cozinha <em>Mediterrânea.</em>'],
      ['home','gastro_body','html','Gastronomia · texto','As receitas são fruto de uma jornada culinária pela Suíça, sul da França, Itália e Mediterrâneo. Cada prato é uma alquimia de sabores e aromas.'],

      ['a-pousada','hero_eyebrow','text','Hero · etiqueta','Sobre nós'],
      ['a-pousada','hero_title','html','Hero · título','A pousada <em>boutique</em> da Suíça Alagoana.'],
      ['a-pousada','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1455587734955-081b22074882?auto=format&fit=crop&w=2000&q=80'],
      ['a-pousada','intro_body','html','Introdução','Refúgio boutique em Mar Vermelho — hospitalidade, gastronomia e contemplação.'],

      ['chales','hero_eyebrow','text','Hero · etiqueta','As nossas acomodações'],
      ['chales','hero_title','html','Hero · título','Chalés <em>com alma.</em>'],
      ['chales','hero_subtitle','html','Hero · subtítulo','Cada detalhe pensado com carinho — aconchego, conforto e gentilezas em contato constante com a natureza.'],
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
      ['taberna','hero_subtitle','html','Hero · subtítulo','Um convite à mesa farta — mediterrânea, generosa, harmonizada com vinhos especiais e a vista da serra.'],
      ['taberna','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1559717865-a99cac1c95d8?auto=format&fit=crop&w=2000&q=80'],
      ['taberna','about_title','html','Sobre · título','Aberta ao <em>público.</em>'],
      ['taberna','about_body','html','Sobre · texto','A Taberna do Monge é o restaurante boutique da pousada — aberto também a visitantes externos. Receitas autorais, vinhos selecionados e ambiente acolhedor com vista para a serra.'],

      ['experiencias','hero_eyebrow','text','Hero · etiqueta','Vivências na Serra'],
      ['experiencias','hero_title','html','Hero · título','Rituais que <em>tocam a alma.</em>'],
      ['experiencias','hero_subtitle','html','Hero · subtítulo','Experiências autorais — desenhadas para o reencontro com o tempo, com a natureza e com você mesmo.'],
      ['experiencias','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=2000&q=80'],

      ['localizacao','hero_eyebrow','text','Hero · etiqueta','Onde estamos'],
      ['localizacao','hero_title','html','Hero · título','Mar Vermelho, <em>Alagoas.</em>'],
      ['localizacao','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=2000&q=80'],

      ['itinerario','hero_eyebrow','text','Hero · etiqueta','Itinerário'],
      ['itinerario','hero_title','html','Hero · título','A viagem até a serra <em>já é parte da experiência.</em>'],
      ['itinerario','hero_image','image','Hero · imagem','https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=2000&q=80'],
    ];
    $bcheck = $pdo->prepare('SELECT 1 FROM page_blocks WHERE page=? AND block_key=?');
    $bins   = $pdo->prepare('INSERT INTO page_blocks (page,block_key,type,label,value,sort_order) VALUES (?,?,?,?,?,?)');
    foreach ($blocks as $i => $b) {
        [$pg,$k,$t,$lbl,$v] = $b;
        $bcheck->execute([$pg,$k]);
        if (!$bcheck->fetchColumn()) $bins->execute([$pg,$k,$t,$lbl,$v,$i]);
    }
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

function sanitize_public_image_list(string $urls): string {
    $safe = [];
    foreach (preg_split('/\R+/', $urls) ?: [] as $url) {
        $clean = sanitize_public_image_url($url);
        if ($clean !== '') $safe[] = $clean;
    }
    return implode("\n", $safe);
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

bootstrap_db();
