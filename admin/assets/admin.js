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

  // ===== Gallery Picker (multi-image cards) =====
  function initGalleryPick(){
    document.querySelectorAll('[data-gallery-picker]').forEach(picker => {
      if (picker.__init) return; picker.__init = true;
      const list = picker.querySelector('[data-gallery-list]');
      const fileInput = picker.querySelector('[data-gallery-files]');
      const urlInput = picker.querySelector('[data-gallery-url]');
      const addBtn = picker.querySelector('[data-gallery-add]');
      const inputName = picker.dataset.inputName || 'gallery_urls[]';

      const makeItem = (src, hiddenValue, fileRef) => {
        const item = document.createElement('div');
        item.className = 'gallery-picker__item';
        item.dataset.galleryItem = 'true';
        if (fileRef) item.dataset.filePreview = 'true';
        item.innerHTML = `<img src="${src}" alt=""><button type="button" data-gallery-remove aria-label="Remover imagem"><i data-lucide="x"></i></button>`;
        if (hiddenValue) {
          const hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = inputName;
          hidden.value = hiddenValue;
          item.appendChild(hidden);
        }
        item.querySelector('[data-gallery-remove]').addEventListener('click', () => {
          if (fileRef && fileInput) {
            const dt = new DataTransfer();
            Array.from(fileInput.files).forEach(file => { if (file !== fileRef) dt.items.add(file); });
            fileInput.files = dt.files;
          }
          item.remove();
        });
        list.appendChild(item);
        refreshIcons();
      };

      const renderFiles = () => {
        list.querySelectorAll('[data-file-preview]').forEach(item => item.remove());
        Array.from(fileInput?.files || []).forEach(file => makeItem(URL.createObjectURL(file), '', file));
      };

      fileInput && fileInput.addEventListener('change', renderFiles);
      addBtn && addBtn.addEventListener('click', () => {
        const value = (urlInput?.value || '').trim();
        if (!value) return;
        makeItem(value, value, null);
        urlInput.value = '';
      });
      urlInput && urlInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); addBtn?.click(); }
      });

      ['dragenter','dragover'].forEach(ev => picker.addEventListener(ev, e => {
        e.preventDefault(); picker.classList.add('is-drag');
      }));
      ['dragleave','drop'].forEach(ev => picker.addEventListener(ev, e => {
        e.preventDefault(); picker.classList.remove('is-drag');
      }));
      picker.addEventListener('drop', e => {
        if (!fileInput) return;
        const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
        if (!files.length) return;
        const dt = new DataTransfer();
        Array.from(fileInput.files).forEach(file => dt.items.add(file));
        files.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        renderFiles();
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
    initGalleryPick();
    initEditors();
  });
  document.addEventListener('alpine:initialized', refreshIcons);
})();
