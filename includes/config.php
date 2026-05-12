<?php
// ===== Pousada Aromas da Serra =====
// Configuração central do site
declare(strict_types=1);

date_default_timezone_set('America/Maceio');

// Base path detection — works on XAMPP at localhost/aromasdaserra
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($basePath === '' || $basePath === '/') $basePath = '';

define('SITE_BASE', $basePath);
define('SITE_NAME', 'Pousada Aromas da Serra');
define('SITE_TAGLINE', 'Refúgio na Suíça Alagoana');
define('SITE_LOCATION', 'Mar Vermelho, Alagoas');
define('SITE_PHONE_DISPLAY', '(82) 99326-0415');
define('SITE_PHONE_RAW', '5582993260415');
define('SITE_WHATSAPP', 'https://api.whatsapp.com/send?phone=5582993260415&text=' . rawurlencode('Olá! Gostaria de fazer uma reserva na Pousada Aromas da Serra.'));
define('SITE_EMAIL', 'atendimento@pousadaaromasdaserra.com.br');
define('SITE_INSTAGRAM', 'https://www.instagram.com/pousadaaromasdaserra/');
define('SITE_FACEBOOK', 'https://www.facebook.com/pousadaaromasdaserra');

function asset(string $path): string {
    $cleanPath = ltrim($path, '/');
    $url = SITE_BASE . '/assets/' . $cleanPath;
    $file = dirname(__DIR__) . '/assets/' . $cleanPath;

    if (is_file($file)) {
        $url .= '?v=' . filemtime($file);
    }

    return $url;
}
function url(string $path = ''): string {
    return SITE_BASE . '/' . ltrim($path, '/');
}
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function repair_image_url(string $url): string {
    $url = trim($url);
    if ($url === '') return '';

    return strtr($url, [
        'photo-1559717865-a99cac1c95d8' => 'photo-1499636136210-6f4ee915583e',
        'photo-1474482546248-690a01702af3' => 'photo-1455218873509-8097305ee378',
    ]);
}

function send_security_headers(): void {
    if (headers_sent()) return;
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://unpkg.com https://cdn.jsdelivr.net https://cdn.plyr.io https://www.youtube.com https://www.youtube-nocookie.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdn.plyr.io; img-src 'self' data: blob: https://images.unsplash.com https://*.unsplash.com https://i.ytimg.com; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self'; frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com");
}

send_security_headers();

function site_db(): ?PDO {
    static $pdo = null;
    static $loaded = false;
    if ($loaded) return $pdo;
    $loaded = true;

    $url = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: '';
    if ($url && str_starts_with(strtolower($url), 'mysql')) {
        $parts = parse_url($url);
        $host = $parts['host'] ?? '127.0.0.1';
        $port = $parts['port'] ?? 3306;
        $db   = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
        $user = isset($parts['user']) ? rawurldecode($parts['user']) : '';
        $pass = isset($parts['pass']) ? rawurldecode($parts['pass']) : '';
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass);
    } else {
        $host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST');
        $db   = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE');
        $user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER');
        $pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '';
        if ($host && $db && $user) {
            $port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: 3306;
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass);
        } else {
            $dbPath = __DIR__ . '/../data/aromas.sqlite';
            if (is_file($dbPath)) $pdo = new PDO('sqlite:' . $dbPath);
        }
    }

    if ($pdo) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    return $pdo;
}

// Read editable page block from admin SQLite database
function block(string $page, string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $pdo = site_db();
        if ($pdo) {
            try {
                $rows = $pdo->query('SELECT page, block_key, value FROM page_blocks');
                if ($rows) foreach ($rows as $r) {
                    $cache[$r['page'].'|'.$r['block_key']] = $r['value'] ?? '';
                }
            } catch (Throwable $e) {}
        }
    }
    $v = $cache[$page.'|'.$key] ?? null;
    return ($v !== null && $v !== '') ? $v : $default;
}

function public_gallery_caption(string $caption): string {
    $caption = trim(strip_tags($caption));
    $caption = preg_replace('/\s+/', ' ', $caption) ?? $caption;
    if (function_exists('mb_substr')) return mb_substr($caption, 0, 120, 'UTF-8');
    return substr($caption, 0, 120);
}

function public_image_items(string $value): array {
    $value = trim($value);
    if ($value === '') return [];

    $rawItems = [];
    $decoded = json_decode($value, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $rawItems = array_is_list($decoded) ? $decoded : [$decoded];
    } else {
        $rawItems = preg_split('/\R+/', $value) ?: [];
    }

    $items = [];
    $seen = [];
    foreach ($rawItems as $rawItem) {
        if (is_array($rawItem)) {
            $src = repair_image_url((string)($rawItem['src'] ?? $rawItem['url'] ?? $rawItem['path'] ?? ''));
            $caption = public_gallery_caption((string)($rawItem['caption'] ?? $rawItem['label'] ?? $rawItem['title'] ?? ''));
        } else {
            $src = repair_image_url((string)$rawItem);
            $caption = '';
        }
        $src = trim($src);
        if ($src === '' || isset($seen[$src])) continue;
        $seen[$src] = true;
        $items[] = ['src' => $src, 'caption' => $caption];
    }
    return $items;
}

function public_image_list(string $urls): array {
    return array_map(fn(array $item): string => $item['src'], public_image_items($urls));
}

function gallery_slides(string $gallery, string $alt): array {
    return array_map(fn(array $item): array => ['src' => $item['src'], 'alt' => $alt, 'caption' => $item['caption']], public_image_items($gallery));
}

function youtube_embed_url(string $url): string {
    $url = trim($url);
    if ($url === '') return '';
    $parts = parse_url($url);
    $host = strtolower((string)($parts['host'] ?? ''));
    $path = trim((string)($parts['path'] ?? ''), '/');
    $id = '';
    if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
        $id = explode('/', $path)[0] ?? '';
    } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
        parse_str((string)($parts['query'] ?? ''), $query);
        if (!empty($query['v'])) $id = (string)$query['v'];
        elseif (str_starts_with($path, 'shorts/')) $id = explode('/', substr($path, 7))[0] ?? '';
        elseif (str_starts_with($path, 'embed/')) $id = explode('/', substr($path, 6))[0] ?? '';
    }
    $id = preg_replace('/[^A-Za-z0-9_-]/', '', $id) ?? '';
    return $id !== '' ? 'https://www.youtube.com/embed/' . $id : '';
}

function catalog_chalets(array $fallback = []): array {
    $pdo = site_db();
    if ($pdo) {
        try {
            $rows = $pdo->query('SELECT * FROM chalets WHERE is_active=1 ORDER BY sort_order ASC, name ASC');
            if ($rows) {
                $items = $rows->fetchAll();
                if ($items) return $items;
            }
        } catch (Throwable $e) {}
    }
    return $fallback;
}

function catalog_experiences(array $fallback = []): array {
    $pdo = site_db();
    if ($pdo) {
        try {
            $rows = $pdo->query('SELECT * FROM experiences WHERE is_active=1 ORDER BY sort_order ASC, title ASC');
            if ($rows) {
                $items = $rows->fetchAll();
                if ($items) return $items;
            }
        } catch (Throwable $e) {}
    }
    return $fallback;
}

function catalog_products(array $fallback = []): array {
    $pdo = site_db();
    if ($pdo) {
        try {
            $rows = $pdo->query('SELECT * FROM products WHERE is_active=1 ORDER BY sort_order ASC, title ASC');
            if ($rows) {
                $items = $rows->fetchAll();
                if ($items) return $items;
            }
        } catch (Throwable $e) {}
    }
    return $fallback;
}

function catalog_testimonials(array $fallback = []): array {
    $pdo = site_db();
    if ($pdo) {
        try {
            $rows = $pdo->query('SELECT * FROM testimonials WHERE is_active=1 ORDER BY sort_order ASC, author ASC');
            if ($rows) {
                $items = $rows->fetchAll();
                if ($items) return $items;
            }
        } catch (Throwable $e) {}
    }
    return $fallback;
}

function product_flavor_items(string $flavors): array {
    $items = [];
    foreach (preg_split('/\R+/', $flavors) ?: [] as $item) {
        $item = trim($item);
        if ($item !== '') $items[] = $item;
    }
    return $items;
}

// Navigation tree — clean, after revision
$NAV = [
    ['label' => 'Início',         'href' => url('')],
    ['label' => 'A Pousada',      'href' => url('a-pousada.php'), 'children' => [
        ['label' => 'Nossa história', 'href' => url('a-pousada.php')],
        ['label' => 'Depoimentos',    'href' => url('depoimentos.php')],
    ]],
    ['label' => 'Chalés',         'href' => url('chales.php')],
    ['label' => 'Gastronomia',    'href' => url('gastronomia.php'),    'children' => [
        ['label' => 'Cozinha Mediterrânea', 'href' => url('gastronomia.php')],
        ['label' => 'Taberna do Monge',     'href' => url('taberna.php')],
        ['label' => 'Produtos Artesanais',  'href' => url('produtos.php')],
    ]],
    ['label' => 'Experiências',   'href' => url('experiencias.php')],
    ['label' => 'Localização',    'href' => url('localizacao.php'),    'children' => [
        ['label' => 'Onde estamos', 'href' => url('localizacao.php')],
        ['label' => 'Itinerário até a Serra', 'href' => url('itinerario.php')],
    ]],
];
