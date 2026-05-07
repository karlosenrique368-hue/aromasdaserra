<?php require __DIR__ . '/bootstrap.php';

if (is_admin_logged()) { header('Location: ' . admin_url('index.php')); exit; }

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $err = 'Sessão expirada — recarregue a página.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $stmt = db()->prepare('SELECT * FROM admins WHERE LOWER(email) = LOWER(?)');
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        if ($u && password_verify($pass, $u['password_hash'])) {
            $_SESSION['admin_id'] = (int)$u['id'];
            session_regenerate_id(true);
            header('Location: ' . admin_url('index.php')); exit;
        }
        $err = 'Credenciais inválidas.';
    }
}
?><!DOCTYPE html>
<html lang="pt-BR"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Acesso · Painel Aromas da Serra</title>
<link rel="icon" type="image/jpeg" href="<?= ee(front_url('assets/img/logoserra.jpg')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,400;1,500&family=Inter:wght@300;400;500;600&family=Italiana&display=swap" rel="stylesheet">
<script>tailwind.config={theme:{extend:{colors:{forest:{700:'#3a5b30',800:'#2f4a2a',900:'#1f3019'},cream:{50:'#faf6ef',100:'#f4ece0',200:'#ebdcc4'},gold:{500:'#c4a46c',600:'#a98955'},ink:{700:'#3b342c',800:'#2a2520',900:'#1a1612'},terracota:{500:'#b8754a'}},fontFamily:{editorial:['"Italiana"','serif'],display:['"Cormorant Garamond"','serif']}}}}</script>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?= ee(admin_url('assets/admin.css')) ?>">
<script defer src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
</head>
<body class="min-h-screen bg-cream-50 font-sans text-ink-800 antialiased">
<div class="min-h-screen grid lg:grid-cols-2">
  <div class="hidden lg:block relative overflow-hidden">
    <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1400&q=80" class="absolute inset-0 w-full h-full object-cover" alt="">
    <div class="absolute inset-0 bg-gradient-to-br from-forest-900/85 via-forest-900/60 to-forest-800/85"></div>
    <div class="relative h-full flex flex-col justify-between p-12 text-cream-50">
      <span class="text-[11px] tracking-[.32em] uppercase text-cream-50/80">Aromas da Serra · Admin</span>
      <div>
        <h1 class="font-editorial text-5xl xl:text-6xl leading-[1.05] max-w-md">Painel de gestão da pousada.</h1>
        <p class="mt-5 max-w-sm text-cream-100/80 italic font-display text-lg">Tudo que acontece no refúgio — em um só lugar.</p>
      </div>
      <span class="text-[11px] tracking-[.32em] uppercase text-cream-50/60">Mar Vermelho — Alagoas</span>
    </div>
  </div>
  <div class="flex items-center justify-center p-8">
    <div class="w-full max-w-md">
      <div class="text-center lg:text-left mb-10">
        <img src="<?= ee(front_url('assets/img/logoserra.jpg')) ?>" alt="Pousada Aromas da Serra" class="w-24 h-24 object-contain rounded-full mx-auto lg:mx-0 mb-5 shadow-lg">
        <span class="text-[11px] tracking-[.32em] uppercase text-terracota-500">Acesso restrito</span>
        <h2 class="font-editorial text-4xl text-forest-900 mt-3">Bem-vinda de volta.</h2>
        <p class="text-ink-700/70 mt-2 text-[15px]">Entre com suas credenciais para gerenciar o site.</p>
      </div>
      <?php if ($err): ?>
        <div class="mb-5 px-4 py-3 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
          <i data-lucide="alert-circle" class="w-4 h-4"></i> <?= ee($err) ?>
        </div>
      <?php endif; ?>
      <form method="post" class="space-y-4">
        <?= csrf_field() ?>
        <label class="block">
          <span class="text-[11px] tracking-[.2em] uppercase text-ink-700/70">E-mail</span>
          <input type="email" name="email" required autofocus class="mt-1 w-full px-4 py-3 bg-white border border-cream-200 rounded-md focus:outline-none focus:border-forest-700 transition" value="admin@aromas.local">
        </label>
        <label class="block">
          <span class="text-[11px] tracking-[.2em] uppercase text-ink-700/70">Senha</span>
          <input type="password" name="password" required class="mt-1 w-full px-4 py-3 bg-white border border-cream-200 rounded-md focus:outline-none focus:border-forest-700 transition">
        </label>
        <button type="submit" class="w-full mt-4 inline-flex items-center justify-center gap-2 px-5 py-3 bg-forest-800 hover:bg-forest-900 text-cream-50 rounded-md uppercase tracking-[.18em] text-[12px] font-medium transition">
          <i data-lucide="log-in" class="w-4 h-4"></i> Entrar
        </button>
      </form>
      <p class="mt-8 text-center text-[12px] text-ink-700/60">Padrão: <code class="font-mono">admin@aromas.local</code> · <code class="font-mono">aromas2025</code></p>
      <p class="mt-2 text-center"><a href="<?= ee(front_url()) ?>" class="text-[12px] text-terracota-500 hover:underline">← Voltar ao site</a></p>
    </div>
  </div>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>lucide.createIcons());</script>
</body></html>
