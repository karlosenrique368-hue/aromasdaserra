<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();
$user = admin_user();
$current = $current ?? basename($_SERVER['PHP_SELF']);
$pageTitle = $pageTitle ?? 'Painel';
$pageEyebrow = $pageEyebrow ?? 'Aromas da Serra';
$flash = flash();

$NAV = [
  ['index.php',       'Visão geral',  'layout-dashboard'],
  ['pages.php',       'Páginas',      'file-text'],
  ['chalets.php',     'Chalés',       'home'],
  ['experiences.php', 'Experiências', 'sparkles'],
  ['products.php',    'Produtos',     'shopping-bag'],
  ['testimonials.php','Depoimentos',  'quote'],
  ['gallery.php',     'Galeria',      'image'],
  ['leads.php',       'Reservas',     'calendar-heart'],
  ['messages.php',    'Mensagens',    'mail'],
  ['settings.php',    'Configurações','settings'],
];
?><!DOCTYPE html>
<html lang="pt-BR"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= ee($pageTitle) ?> · Painel Aromas</title>
<link rel="icon" type="image/jpeg" href="<?= ee(front_url('assets/img/logoserra.jpg')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,400;1,500&family=Inter:wght@300;400;500;600&family=Italiana&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= ee(admin_url('assets/admin.css')) ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
<script defer src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.14.9/dist/cdn.min.js"></script>
<script defer src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
<script defer src="<?= ee(admin_url('assets/admin.js')) ?>"></script>
</head>
<body x-data="{ side:false }" @keydown.escape.window="side=false">
<div class="admin-shell">
  <button type="button" class="adm-side-scrim" :class="side && 'open'" @click="side=false" aria-label="Fechar menu"></button>
  <aside class="adm-side" :class="side && 'open'" @click.away="side=false">
    <div class="adm-brand">
      <img src="<?= ee(front_url('assets/img/logoserra.jpg')) ?>" alt="">
      <b>Aromas</b>
    </div>
    <nav class="adm-nav">
      <?php foreach ($NAV as [$file,$lbl,$icon]): ?>
        <a href="<?= ee(admin_url($file)) ?>" class="<?= $current===$file ? 'active' : '' ?>">
          <i data-lucide="<?= $icon ?>"></i> <?= ee($lbl) ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="foot">
      <div style="display:flex; align-items:center; gap:.5rem; margin-bottom:.6rem;">
        <span style="width:32px; height:32px; border-radius:999px; background:rgba(196,164,108,.2); display:grid; place-items:center; color:var(--a-gold); font-family:'Italiana',serif;"><?= ee(mb_substr($user['name'] ?? 'A', 0, 1)) ?></span>
        <div style="line-height:1.2;"><div style="color:#faf6ef; font-size:13px;"><?= ee($user['name']) ?></div><div style="font-size:11px;"><?= ee($user['email']) ?></div></div>
      </div>
      <div style="display:flex; gap:.5rem;">
        <a href="<?= ee(front_url()) ?>" target="_blank" style="flex:1; padding:.5rem; border:1px solid rgba(244,236,224,.15); border-radius:6px; text-align:center; font-size:11px; color:rgba(244,236,224,.7);">Ver site</a>
        <a href="<?= ee(admin_url('logout.php')) ?>" style="flex:1; padding:.5rem; border:1px solid rgba(244,236,224,.15); border-radius:6px; text-align:center; font-size:11px; color:rgba(244,236,224,.7);">Sair</a>
      </div>
    </div>
  </aside>

  <div class="adm-main">
    <div class="adm-top">
      <div style="display:flex; align-items:center; gap:.8rem;">
        <button @click.stop="side=!side" class="adm-burger" aria-label="Menu"><i data-lucide="menu"></i></button>
        <h1><?= ee($pageEyebrow) ?> · <span><?= ee($pageTitle) ?></span></h1>
      </div>
      <div style="font-size:12px; color:var(--a-muted);"><?= date('d/m/Y · H:i') ?></div>
    </div>
    <main class="adm-content">
      <?php if ($flash): ?>
        <div class="toast <?= $flash['type']==='error' ? 'error' : '' ?>" x-data="{s:true}" x-show="s" x-init="setTimeout(()=>s=false, 3500)">
          <i data-lucide="<?= $flash['type']==='error' ? 'alert-circle' : 'check-circle' ?>"></i>
          <?= ee($flash['msg']) ?>
        </div>
      <?php endif; ?>
