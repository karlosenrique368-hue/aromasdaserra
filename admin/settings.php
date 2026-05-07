<?php
require __DIR__ . '/bootstrap.php';
require_admin();
$current='settings.php'; $pageTitle='Configurações'; $pageEyebrow='Painel';

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check()) {
    $tab = $_POST['tab'] ?? 'site';
    if ($tab==='site') {
        foreach (['site_name','site_phone','site_email','site_whatsapp','site_instagram','site_facebook','site_location','hero_headline','about_intro'] as $k) {
            if (isset($_POST[$k])) set_setting($k, trim($_POST[$k]));
        }
        flash('Configurações salvas.');
    } elseif ($tab==='password') {
        $u = admin_user();
        $cur = $_POST['current'] ?? ''; $new = $_POST['new'] ?? ''; $confirm = $_POST['confirm'] ?? '';
        if (!password_verify($cur, $u['password_hash'])) { flash('Senha atual incorreta.','error'); }
        elseif (strlen($new) < 8) { flash('Nova senha precisa ter ao menos 8 caracteres.','error'); }
        elseif ($new !== $confirm) { flash('As senhas não conferem.','error'); }
        else { db()->prepare('UPDATE admins SET password_hash=? WHERE id=?')->execute([password_hash($new, PASSWORD_BCRYPT), $u['id']]); flash('Senha atualizada.'); }
    } elseif ($tab==='profile') {
        db()->prepare('UPDATE admins SET name=?, email=? WHERE id=?')->execute([trim($_POST['name']), trim($_POST['email']), admin_user()['id']]);
        flash('Perfil atualizado.');
    }
    header('Location: ' . admin_url('settings.php')); exit;
}

require __DIR__ . '/partials/layout_top.php';
$u = admin_user();
?>
<div class="page-head">
  <div><span class="eyebrow">Ajustes</span><h2>Configurações <em>gerais</em>.</h2></div>
</div>

<div style="display:grid; gap:1.25rem; grid-template-columns:1fr 1fr;" class="set-grid">
  <form method="post" class="adm-card adm-form">
    <h3>Site</h3>
    <?= csrf_field() ?><input type="hidden" name="tab" value="site">
    <label><span class="lbl">Nome</span><input type="text" name="site_name" value="<?= ee(get_setting('site_name')) ?>"></label>
    <div class="row-2">
      <label><span class="lbl">Telefone</span><input type="text" name="site_phone" value="<?= ee(get_setting('site_phone')) ?>"></label>
      <label><span class="lbl">WhatsApp (raw, ex 5582...)</span><input type="text" name="site_whatsapp" value="<?= ee(get_setting('site_whatsapp')) ?>"></label>
    </div>
    <label><span class="lbl">E-mail</span><input type="email" name="site_email" value="<?= ee(get_setting('site_email')) ?>"></label>
    <label><span class="lbl">Localização</span><input type="text" name="site_location" value="<?= ee(get_setting('site_location')) ?>"></label>
    <div class="row-2">
      <label><span class="lbl">Instagram URL</span><input type="text" name="site_instagram" value="<?= ee(get_setting('site_instagram')) ?>"></label>
      <label><span class="lbl">Facebook URL</span><input type="text" name="site_facebook" value="<?= ee(get_setting('site_facebook')) ?>"></label>
    </div>
    <label><span class="lbl">Headline do hero</span><textarea name="hero_headline" rows="2"><?= ee(get_setting('hero_headline')) ?></textarea></label>
    <label><span class="lbl">Introdução (manifesto)</span><textarea name="about_intro" rows="4"><?= ee(get_setting('about_intro')) ?></textarea></label>
    <div><button class="btn btn-primary" type="submit"><i data-lucide="save"></i> Salvar configurações</button></div>
  </form>

  <div style="display:flex; flex-direction:column; gap:1.25rem;">
    <form method="post" class="adm-card adm-form">
      <h3>Perfil</h3>
      <?= csrf_field() ?><input type="hidden" name="tab" value="profile">
      <label><span class="lbl">Nome</span><input type="text" name="name" value="<?= ee($u['name']) ?>"></label>
      <label><span class="lbl">E-mail</span><input type="email" name="email" value="<?= ee($u['email']) ?>"></label>
      <div><button class="btn btn-primary" type="submit"><i data-lucide="user"></i> Atualizar perfil</button></div>
    </form>
    <form method="post" class="adm-card adm-form">
      <h3>Trocar senha</h3>
      <?= csrf_field() ?><input type="hidden" name="tab" value="password">
      <label><span class="lbl">Senha atual</span><input type="password" name="current" required></label>
      <label><span class="lbl">Nova senha</span><input type="password" name="new" required minlength="8"></label>
      <label><span class="lbl">Confirmar nova senha</span><input type="password" name="confirm" required minlength="8"></label>
      <div><button class="btn btn-primary" type="submit"><i data-lucide="lock"></i> Alterar senha</button></div>
    </form>
  </div>
</div>
<style>@media(max-width:980px){.set-grid{grid-template-columns:1fr !important;}}</style>

<?php require __DIR__ . '/partials/layout_bot.php'; ?>
