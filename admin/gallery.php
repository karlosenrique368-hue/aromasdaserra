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
      $nextOrder = (int)$pdo->query('SELECT COALESCE(MAX(sort_order), 0) + 10 FROM gallery')->fetchColumn();
        for ($i=0; $i<count($files['name']); $i++) {
            $f = ['name'=>$files['name'][$i],'tmp_name'=>$files['tmp_name'][$i],'error'=>$files['error'][$i],'size'=>$files['size'][$i]];
            if ($p = upload_file($f, 'gal')) {
          $stmt = $pdo->prepare('INSERT INTO gallery (title,category,path,sort_order) VALUES (?,?,?,?)');
          $stmt->execute([$f['name'], $cat, $p, $nextOrder]); $count++; $nextOrder += 10;
            }
        }
        if ($count > 0) {
          flash("$count imagens enviadas.");
        } else {
          flash('Nenhuma imagem válida foi enviada. Use JPG, PNG ou WEBP até 8MB.', 'error');
        }
        header('Location: ' . admin_url('gallery.php')); exit;
    }
      if ($op==='save_library') {
        $titles = (array)($_POST['title'] ?? []);
        $categories = (array)($_POST['category_item'] ?? []);
        $orderIds = array_map('intval', (array)($_POST['order_ids'] ?? []));
        $upd = $pdo->prepare('UPDATE gallery SET title=?, category=?, sort_order=? WHERE id=?');
        foreach ($orderIds as $index => $galleryId) {
          if ($galleryId <= 0) continue;
          $upd->execute([
            sanitize_gallery_caption((string)($titles[$galleryId] ?? '')),
            trim((string)($categories[$galleryId] ?? 'geral')),
            ($index + 1) * 10,
            $galleryId,
          ]);
        }
        flash('Galeria atualizada.');
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

$rows = $pdo->query('SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC')->fetchAll();
$categories = ['geral','chales','gastronomia','experiencias','localizacao'];
require __DIR__ . '/partials/layout_top.php';
?>
<div class="page-head">
  <div><span class="eyebrow">Mídia</span><h2>Biblioteca de <em>imagens</em>.</h2></div>
</div>

<form method="post" enctype="multipart/form-data" class="adm-card adm-form" style="margin-bottom:1.5rem;">
  <?= csrf_field() ?><input type="hidden" name="op" value="upload">
  <label><span class="lbl">Categoria</span>
    <select name="category" style="max-width:300px;">
      <?php foreach ($categories as $c): ?>
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
  <form method="post" class="adm-card adm-form" data-sortable-gallery-library>
    <?= csrf_field() ?><input type="hidden" name="op" value="save_library">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
      <div><span class="lbl">Biblioteca</span><p style="margin:.2rem 0 0; color:var(--a-muted); font-size:13px;">Arraste os cards para definir a ordem. Edite o título/legenda de cada imagem quando precisar.</p></div>
      <button class="btn btn-primary" type="submit"><i data-lucide="save"></i> Salvar ordem</button>
    </div>
    <div style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fill, minmax(220px,1fr));" data-gallery-library-list>
    <?php foreach ($rows as $r): ?>
      <div class="adm-card gallery-library-item" data-gallery-library-item draggable="true" style="padding:.7rem; position:relative; display:grid; gap:.65rem;">
        <input type="hidden" name="order_ids[]" value="<?= (int)$r['id'] ?>">
        <button type="button" class="btn-icon" data-gallery-library-drag title="Arrastar" style="position:absolute; left:.9rem; top:.9rem; z-index:2; background:rgba(31,48,25,.84); color:#fff;"><i data-lucide="grip-vertical"></i></button>
        <img src="<?= ee($r['path']) ?>" alt="" style="width:100%; aspect-ratio:1/1; object-fit:cover; border-radius:6px;">
        <label><span class="lbl">Título / legenda</span><input type="text" name="title[<?= (int)$r['id'] ?>]" value="<?= ee($r['title'] ?? '') ?>"></label>
        <div style="display:flex; justify-content:space-between; align-items:end; gap:.6rem;">
          <label style="flex:1;"><span class="lbl">Categoria</span>
            <select name="category_item[<?= (int)$r['id'] ?>]">
              <?php foreach ($categories as $c): ?><option <?= ($r['category'] ?? '')===$c?'selected':'' ?>><?= ee($c) ?></option><?php endforeach; ?>
            </select>
          </label>
          <button class="btn-icon" type="submit" form="delete-gallery-<?= (int)$r['id'] ?>" style="color:#9a3a3a;" title="Remover"><i data-lucide="trash-2"></i></button>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </form>
  <?php foreach ($rows as $r): ?>
    <form id="delete-gallery-<?= (int)$r['id'] ?>" method="post" onsubmit="return confirm('Remover?');" style="display:none;">
      <?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
    </form>
  <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/partials/layout_bot.php'; ?>
