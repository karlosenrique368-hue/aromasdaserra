// ============ Aromas Admin JS ============
(function(){
  'use strict';

  // Lucide refresh helper
  function refreshIcons(){ if (window.lucide) lucide.createIcons(); }

  // ===== Premium Upload Zones =====
  function initUpload(){
    document.querySelectorAll('[data-upz]').forEach(zone => {
      if (zone.__init) return; zone.__init = true;
      const input    = zone.querySelector('input[type=file]');
      const previews = zone.querySelector('.upz__previews');
      const multiple = zone.hasAttribute('data-multiple');

      const renderPreview = (file) => {
        const wrap = document.createElement('div');
        wrap.className = 'upz__preview';
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        const x = document.createElement('span');
        x.className = 'x'; x.title = 'Remover';
        x.innerHTML = '<i data-lucide="x"></i>';
        x.addEventListener('click', (e) => {
          e.stopPropagation(); e.preventDefault();
          wrap.remove();
          const dt = new DataTransfer();
          Array.from(input.files).forEach(f => { if (f !== file) dt.items.add(f); });
          input.files = dt.files;
        });
        wrap.appendChild(img); wrap.appendChild(x);
        previews.appendChild(wrap);
        refreshIcons();
      };

      const rebuild = () => {
        previews.querySelectorAll('.upz__preview:not([data-current])').forEach(p => p.remove());
        Array.from(input.files).forEach(renderPreview);
      };

      input.addEventListener('change', rebuild);

      // Drag & drop
      ['dragenter','dragover'].forEach(ev =>
        zone.addEventListener(ev, e => { e.preventDefault(); zone.classList.add('is-drag'); }));
      ['dragleave','drop'].forEach(ev =>
        zone.addEventListener(ev, e => { e.preventDefault(); zone.classList.remove('is-drag'); }));
      zone.addEventListener('drop', e => {
        const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
        if (!files.length) return;
        const dt = new DataTransfer();
        if (multiple) Array.from(input.files).forEach(f => dt.items.add(f));
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
        rebuild();
      });
    });
  }

  // ===== Image Picker (single) =====
  function initImgPick(){
    document.querySelectorAll('[data-img-pick]').forEach(box => {
      if (box.__init) return; box.__init = true;
      const input = box.querySelector('input[type=file]');
      const thumb = box.querySelector('.img-pick__thumb img');
      const btn   = box.querySelector('.img-pick__btn, .img-pick__thumb');

      btn && btn.addEventListener('click', e => { e.preventDefault(); input.click(); });
      input.addEventListener('change', () => {
        const f = input.files[0]; if (!f) return;
        if (thumb) thumb.src = URL.createObjectURL(f);
      });
      ['dragenter','dragover'].forEach(ev =>
        box.addEventListener(ev, e => { e.preventDefault(); box.classList.add('is-drag'); }));
      ['dragleave','drop'].forEach(ev =>
        box.addEventListener(ev, e => { e.preventDefault(); box.classList.remove('is-drag'); }));
      box.addEventListener('drop', e => {
        const f = Array.from(e.dataTransfer.files).find(x => x.type.startsWith('image/'));
        if (!f) return;
        const dt = new DataTransfer(); dt.items.add(f);
        input.files = dt.files;
        if (thumb) thumb.src = URL.createObjectURL(f);
      });
    });
  }

  // ===== Quill editors =====
  function initEditors(){
    if (!window.Quill) return;
    document.querySelectorAll('[data-editor]').forEach(el => {
      if (el.__init) return; el.__init = true;
      const target = document.querySelector(el.dataset.target);
      if (!target) return;
      const q = new Quill(el, {
        theme: 'snow',
        modules: { toolbar: [
          [{ header: [2, 3, false] }],
          ['bold','italic','underline'],
          [{ list:'ordered' }, { list:'bullet' }],
          ['link','blockquote'],
          ['clean']
        ]},
        placeholder: el.dataset.placeholder || 'Escreva aqui…'
      });
      if (target.value) q.root.innerHTML = target.value;
      q.on('text-change', () => { target.value = q.root.innerHTML; });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    refreshIcons();
    initUpload();
    initImgPick();
    initEditors();
  });
  document.addEventListener('alpine:initialized', refreshIcons);
})();
