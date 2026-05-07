<?php
require __DIR__ . '/bootstrap.php';
require_admin();
$current='gallery.php'; $pageTitle='Galeria'; $pageEyebrow='Gestão';

$pdo = db();

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check()) {
    $op = $_POST['op'] ?? '';
    if ($op==='upload' && !empty($_FILES['files']['name'][0])) {
        $cat = trim($_POST['category'] ?? 'geral');
        $count = 0;
        $files = $_FILES['files'];
        for ($i=0; $i<count($files['name']); $i++) {
            $f = ['name'=>$files['name'][$i],'tmp_name'=>$files['tmp_name'][$i],'error'=>$files['error'][$i],'size'=>$files['size'][$i]];
            if ($p = upload_file($f, 'gal')) {
                $stmt = $pdo->prepare('INSERT INTO gallery (title,category,path) VALUES (?,?,?)');
                $stmt->execute([$f['name'], $cat, $p]); $count++;
            }
        }
        if ($count > 0) {
          flash("$count imagens enviadas.");
        } else {
          flash('Nenhuma imagem válida foi enviada. Use JPG, PNG ou WEBP até 8MB.', 'error');
        }
        header('Location: ' . admin_url('gallery.php')); exit;
    }
    if ($op==='delete') {
        $stmt = $pdo->prepare('SELECT path FROM gallery WHERE id=?'); $stmt->execute([(int)$_POST['id']]);
        $p = $stmt->fetchColumn();
        if ($p) { $real = __DIR__ . '/../' . ltrim(str_replace('/aromasdaserra/','', $p), '/'); if (is_file($real)) @unlink($real); }
        $pdo->prepare('DELETE FROM gallery WHERE id=?')->execute([(int)$_POST['id']]);
        flash('Imagem removida.');
        header('Location: ' . admin_url('gallery.php')); exit;
    }
}

$rows = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll();
require __DIR__ . '/partials/layout_top.php';
?>
<div class="page-head">
  <div><span class="eyebrow">Mídia</span><h2>Biblioteca de <em>imagens</em>.</h2></div>
</div>

<form method="post" enctype="multipart/form-data" class="adm-card adm-form" style="margin-bottom:1.5rem;">
  <?= csrf_field() ?><input type="hidden" name="op" value="upload">
  <label><span class="lbl">Categoria</span>
    <select name="category" style="max-width:300px;">
      <?php foreach (['geral','chales','gastronomia','experiencias','localizacao'] as $c): ?>
        <option><?= ee($c) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label style="margin-top:1rem;"><span class="lbl">Imagens</span></label>
  <?php $name='files[]'; $multiple=true; $current=''; $hint='Arraste várias imagens · JPG, PNG ou WEBP · até 8MB cada'; require __DIR__ . '/partials/upload_zone.php'; ?>
  <div style="margin-top:1rem;"><button class="btn btn-primary" type="submit"><i data-lucide="upload"></i> Enviar imagens</button></div>
</form>

<?php if (!$rows): ?>
  <div class="adm-card"><div class="empty"><i data-lucide="image"></i><h4>Galeria vazia</h4><p>Comece enviando imagens acima.</p></div></div>
<?php else: ?>
  <div style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fill, minmax(200px,1fr));">
    <?php foreach ($rows as $r): ?>
      <div class="adm-card" style="padding:.6rem; position:relative;">
        <img src="<?= ee($r['path']) ?>" alt="" style="width:100%; aspect-ratio:1/1; object-fit:cover; border-radius:6px;">
        <div style="margin-top:.5rem; display:flex; justify-content:space-between; align-items:center; gap:.4rem;">
          <span class="badge muted"><?= ee($r['category']) ?></span>
          <form method="post" onsubmit="return confirm('Remover?');" style="margin:0;">
            <?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>">
            <button class="btn-icon" type="submit" style="color:#9a3a3a;" title="Remover"><i data-lucide="trash-2"></i></button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/layout_bot.php'; ?>
