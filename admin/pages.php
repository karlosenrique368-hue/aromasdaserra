<?php
require __DIR__ . '/bootstrap.php';
require_admin();

$pages = [
    'home'         => 'Início',
    'a-pousada'    => 'A Pousada',
    'chales'       => 'Chalés',
    'gastronomia'  => 'Gastronomia',
    'taberna'      => 'Taberna do Monge',
    'produtos'     => 'Produtos Artesanais',
    'experiencias' => 'Experiências',
    'depoimentos'  => 'Depoimentos',
    'localizacao'  => 'Localização',
    'itinerario'   => 'Itinerário',
    'global'       => 'Global / Rodapé',
];
$page    = $_GET['page'] ?? 'home';
if (!isset($pages[$page])) $page = 'home';

function page_gallery_upload_items(int $id): array {
  if (empty($_FILES['gallery_block_files']['name'][$id]) || !is_array($_FILES['gallery_block_files']['name'][$id])) return [];

  $captions = (array)($_POST['gallery_block_file_captions'][$id] ?? []);
  $items = [];
  foreach ($_FILES['gallery_block_files']['name'][$id] as $index => $name) {
    if (empty($name)) continue;
    $file = [
      'name' => $name,
      'tmp_name' => $_FILES['gallery_block_files']['tmp_name'][$id][$index] ?? '',
      'error' => $_FILES['gallery_block_files']['error'][$id][$index] ?? UPLOAD_ERR_NO_FILE,
      'size' => $_FILES['gallery_block_files']['size'][$id][$index] ?? 0,
    ];
    if ($url = upload_file($file, 'block_gallery')) {
      $items[] = ['src' => $url, 'caption' => sanitize_gallery_caption((string)($captions[$index] ?? ''))];
    }
  }
  return $items;
}

// Save handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) { flash('Sessão expirada. Tente novamente.', 'error'); header('Location: ' . admin_url('pages.php?page=' . urlencode($page))); exit; }

  $metaRows = db()->query('SELECT id, type FROM page_blocks')->fetchAll();
  $blockTypes = [];
  foreach ($metaRows as $row) $blockTypes[(int)$row['id']] = $row['type'] ?? 'text';
    $upd = db()->prepare('UPDATE page_blocks SET value=? WHERE id=?');

    foreach ($_POST['block'] ?? [] as $id => $val) {
        $id = (int)$id; if ($id <= 0) continue;
    $type = $blockTypes[$id] ?? 'text';
    if ($type === 'html') $val = sanitize_block_html((string)$val);
    elseif ($type === 'image') $val = sanitize_public_image_url((string)$val);
    elseif ($type === 'gallery') $val = sanitize_public_image_items((array)$val);
        $upd->execute([(string)$val, $id]);
    }
    // Image uploads
    foreach ($_FILES['image_block'] ?? [] as $field => $infoMap) {
        if ($field !== 'tmp_name') continue;
    }
    if (!empty($_FILES['image_block']['name']) && is_array($_FILES['image_block']['name'])) {
        foreach ($_FILES['image_block']['name'] as $id => $name) {
            if (empty($name)) continue;
            $file = [
                'name'     => $name,
                'tmp_name' => $_FILES['image_block']['tmp_name'][$id],
                'error'    => $_FILES['image_block']['error'][$id],
                'size'     => $_FILES['image_block']['size'][$id],
            ];
            $url = upload_file($file, 'block');
            if ($url) $upd->execute([$url, (int)$id]);
        }
    }
        foreach ($blockTypes as $id => $type) {
          if ($type !== 'gallery') continue;
          $items = (array)($_POST['block_gallery'][$id] ?? []);
          $uploads = page_gallery_upload_items((int)$id);
          $upd->execute([sanitize_public_image_items(merge_gallery_post_items($items, $uploads)), (int)$id]);
        }
    flash('Página atualizada com sucesso.');
    header('Location: ' . admin_url('pages.php?page=' . urlencode($page)));
    exit;
}

$blocks = db()->prepare('SELECT * FROM page_blocks WHERE page=? ORDER BY sort_order ASC, id ASC');
$blocks->execute([$page]);
$blocks = $blocks->fetchAll();

$pageTitle = 'Páginas — ' . $pages[$page];
require __DIR__ . '/partials/layout_top.php';
?>

<div class="adm-card">
  <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
    <div>
      <h1 style="font-family:'Italiana', serif; font-size:2rem; color:var(--a-forest-deep); margin:0;">Editor de páginas</h1>
      <p style="color:var(--a-muted); font-size:13px; margin:.3rem 0 0;">Edite textos e imagens de qualquer página do site. Mudanças aparecem imediatamente.</p>
    </div>
    <a href="<?= ee(front_url(($page === 'home' ? '' : $page . '.php'))) ?>" target="_blank" class="btn btn-ghost"><i data-lucide="external-link"></i> Ver página</a>
  </div>

  <nav class="pg-tabs" style="margin-top:1.4rem;">
    <?php foreach ($pages as $slug => $label): ?>
      <a href="<?= ee(admin_url('pages.php?page=' . urlencode($slug))) ?>" class="<?= $slug === $page ? 'active' : '' ?>"><?= ee($label) ?></a>
    <?php endforeach; ?>
  </nav>
</div>

<form method="post" enctype="multipart/form-data" class="adm-card" style="margin-top:1.2rem;">
  <?= csrf_field() ?>

  <?php if (!$blocks): ?>
    <div class="empty">Nenhum bloco editável cadastrado para esta página.</div>
  <?php else: ?>
    <?php foreach ($blocks as $b): ?>
      <?php
        $type = $b['type'] ?? 'text';
        $id   = (int)$b['id'];
        $key  = $b['block_key'];
        $lbl  = $b['label'] ?: $key;
        $val  = $b['value'] ?? '';
      ?>
      <div class="adm-form" style="border-bottom:1px solid var(--a-border-soft); padding:1.4rem 0;">
        <label class="lbl">
          <span style="font-family:'Italiana',serif; font-size:1.1rem; color:var(--a-forest-deep);"><?= ee($lbl) ?></span>
          <small style="display:block; color:var(--a-muted); font-size:11px; letter-spacing:.1em; text-transform:uppercase; margin-top:.15rem;"><?= ee($key) ?> · <?= ee($type) ?></small>
        </label>

        <?php if ($type === 'text' && (str_ends_with((string)$key, '_icon') || stripos((string)$lbl, 'ícone') !== false || stripos((string)$lbl, 'icone') !== false)): ?>
          <?php $iconName='block[' . $id . ']'; $iconValue=$val ?: 'sparkles'; require __DIR__ . '/partials/icon_picker.php'; ?>

        <?php elseif ($type === 'text'): ?>
          <input type="text" name="block[<?= $id ?>]" value="<?= ee($val) ?>" class="input">

        <?php elseif ($type === 'html'): ?>
          <textarea name="block[<?= $id ?>]" id="ed_<?= $id ?>" style="display:none;"><?= ee($val) ?></textarea>
          <div class="editor-wrap">
            <div data-editor data-target="#ed_<?= $id ?>" data-placeholder="Escreva aqui…"></div>
          </div>

        <?php elseif ($type === 'image'): ?>
          <div class="img-pick" data-img-pick>
            <div class="img-pick__thumb">
              <?php if ($val): ?><img src="<?= ee($val) ?>" alt=""><?php else: ?><img src="" alt=""><?php endif; ?>
              <button type="button" class="img-pick__remove" data-img-pick-remove aria-label="Remover imagem"><i data-lucide="x"></i></button>
            </div>
            <div class="img-pick__txt">
              <strong>Imagem atual</strong>
              <small><?= $val ? ee(basename(parse_url($val, PHP_URL_PATH) ?: $val)) : 'Nenhuma imagem' ?></small>
              <input type="file" name="image_block[<?= $id ?>]" accept="image/*">
              <div style="margin-top:.6rem; display:flex; gap:.5rem; align-items:center;">
                <button type="button" class="img-pick__btn">Trocar imagem</button>
                <input type="text" name="block[<?= $id ?>]" value="<?= ee($val) ?>" placeholder="ou cole uma URL externa" class="input" style="flex:1;">
              </div>
            </div>
          </div>
        <?php elseif ($type === 'gallery'): ?>
          <?php
            $items = $val;
            $inputName = 'block_gallery[' . $id . '][]';
            $uploadName = 'gallery_block_files[' . $id . '][]';
            $fileCaptionName = 'gallery_block_file_captions[' . $id . '][]';
            $hint = 'Envie várias imagens em uma galeria única. Use a legenda para nome do prato, parada ou foto no lightbox.';
            require __DIR__ . '/partials/gallery_picker.php';
          ?>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <div style="display:flex; justify-content:flex-end; gap:.6rem; padding-top:1.4rem;">
      <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Salvar alterações</button>
    </div>
  <?php endif; ?>
</form>

<?php require __DIR__ . '/partials/layout_bot.php'; ?>
