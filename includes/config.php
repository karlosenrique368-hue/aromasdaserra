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
define('SITE_LOCATION', 'Mar Vermelho — Alagoas');
define('SITE_PHONE_DISPLAY', '(82) 99326-0415');
define('SITE_PHONE_RAW', '5582993260415');
define('SITE_WHATSAPP', 'https://api.whatsapp.com/send?phone=5582993260415&text=' . rawurlencode('Olá! Gostaria de fazer uma reserva na Pousada Aromas da Serra.'));
define('SITE_EMAIL', 'atendimento@pousadaaromasdaserra.com.br');
define('SITE_INSTAGRAM', 'https://www.instagram.com/pousadaaromasdaserra/');
define('SITE_FACEBOOK', 'https://www.facebook.com/pousadaaromasdaserra');

function asset(string $path): string {
    return SITE_BASE . '/assets/' . ltrim($path, '/');
}
function url(string $path = ''): string {
    return SITE_BASE . '/' . ltrim($path, '/');
}
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

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

// Navigation tree — clean, after revision
$NAV = [
    ['label' => 'Início',         'href' => url('')],
    ['label' => 'A Pousada',      'href' => url('a-pousada.php')],
    ['label' => 'Chalés',         'href' => url('chales.php')],
    ['label' => 'Gastronomia',    'href' => url('gastronomia.php'),    'children' => [
        ['label' => 'Cozinha Mediterrânea', 'href' => url('gastronomia.php')],
        ['label' => 'Taberna do Monge',     'href' => url('taberna.php')],
    ]],
    ['label' => 'Experiências',   'href' => url('experiencias.php')],
    ['label' => 'Localização',    'href' => url('localizacao.php'),    'children' => [
        ['label' => 'Onde estamos', 'href' => url('localizacao.php')],
        ['label' => 'Itinerário até a Serra', 'href' => url('itinerario.php')],
    ]],
];
