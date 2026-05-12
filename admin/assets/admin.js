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

      const clearCoverField = () => {
        const form = zone.closest('form');
        const coverField = form?.querySelector('input[name="cover"]');
        if (coverField) coverField.value = '';
      };

      const renderPreview = (file) => {
        const wrap = document.createElement('div');
        wrap.className = 'upz__preview';
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        const x = document.createElement('button');
        x.type = 'button';
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
        if (!multiple && input.files.length) previews.querySelectorAll('[data-current]').forEach(p => p.remove());
        Array.from(input.files).forEach(renderPreview);
      };

      zone.addEventListener('click', e => {
        const removeCurrent = e.target.closest('[data-upz-current-remove]');
        if (!removeCurrent || !zone.contains(removeCurrent)) return;
        e.preventDefault(); e.stopPropagation();
        input.value = '';
        clearCoverField();
        removeCurrent.closest('[data-current]')?.remove();
      });

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
      const triggers = box.querySelectorAll('.img-pick__btn, .img-pick__thumb');
      const removeBtn = box.querySelector('[data-img-pick-remove]');
      const urlField = box.querySelector('input[type=text]');
      const statusText = box.querySelector('.img-pick__txt small');

      triggers.forEach(trigger => {
        trigger.addEventListener('click', e => {
          if (e.target.closest('[data-img-pick-remove]')) return;
          e.preventDefault(); input.click();
        });
      });
      removeBtn && removeBtn.addEventListener('click', e => {
        e.preventDefault(); e.stopPropagation();
        input.value = '';
        if (urlField) urlField.value = '';
        if (thumb) thumb.removeAttribute('src');
        if (statusText) statusText.textContent = 'Nenhuma imagem';
        box.classList.add('is-empty');
      });
      input.addEventListener('change', () => {
        const f = input.files[0]; if (!f) return;
        if (thumb) thumb.src = URL.createObjectURL(f);
        if (statusText) statusText.textContent = f.name;
        box.classList.remove('is-empty');
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
        if (statusText) statusText.textContent = f.name;
        box.classList.remove('is-empty');
      });
    });
  }

  // ===== Visual Lucide Icon Picker =====
  function initIconPicker(){
    document.querySelectorAll('[data-icon-picker]').forEach(picker => {
      if (picker.__init) return; picker.__init = true;
      const input = picker.querySelector('[data-icon-value]');
      const preview = picker.querySelector('.icon-picker__preview span');
      const setIcon = (value) => {
        if (!input || !value) return;
        input.value = value;
        picker.querySelectorAll('[data-icon-choice]').forEach(btn => {
          const selected = btn.dataset.iconChoice === value;
          btn.classList.toggle('is-selected', selected);
          btn.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });
        if (preview) preview.innerHTML = `<i data-lucide="${value}"></i>`;
        refreshIcons();
      };
      picker.addEventListener('click', e => {
        const btn = e.target.closest('[data-icon-choice]');
        if (!btn || !picker.contains(btn)) return;
        e.preventDefault();
        setIcon(btn.dataset.iconChoice);
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
      const fileCaptionName = picker.dataset.fileCaptionName || 'gallery_file_captions[]';

      const encodeItem = (src, caption = '') => JSON.stringify({ src, caption });
      const decodeItem = (value, fallbackSrc = '') => {
        try {
          const parsed = JSON.parse(value || '');
          return { src: parsed.src || parsed.url || fallbackSrc, caption: parsed.caption || parsed.label || parsed.title || '' };
        } catch (error) {
          return { src: value || fallbackSrc, caption: '' };
        }
      };

      const updateOrderBadges = () => {
        list.querySelectorAll('[data-gallery-item]').forEach((item, index) => {
          const badge = item.querySelector('[data-gallery-order]');
          if (badge) badge.textContent = String(index + 1);
        });
      };

      const syncFileOrder = () => {
        if (!fileInput || typeof DataTransfer === 'undefined') return;
        const dt = new DataTransfer();
        list.querySelectorAll('[data-file-preview]').forEach(item => {
          if (item.__fileRef) dt.items.add(item.__fileRef);
        });
        fileInput.files = dt.files;
      };

      const syncHiddenValue = (item) => {
        const hidden = item.querySelector('[data-gallery-value]');
        if (!hidden) return;
        const caption = item.querySelector('[data-gallery-caption]')?.value || '';
        if (hidden.dataset.galleryUpload === 'true') {
          hidden.value = JSON.stringify({ upload: true, caption });
          return;
        }
        const data = decodeItem(hidden.value, item.querySelector('img')?.src || '');
        hidden.value = encodeItem(data.src, caption);
      };

      const removeItem = (item) => {
        if (!item) return;
        if (item.__fileRef && fileInput) {
          const dt = new DataTransfer();
          Array.from(fileInput.files).forEach(file => { if (file !== item.__fileRef) dt.items.add(file); });
          fileInput.files = dt.files;
        }
        item.remove();
        updateOrderBadges();
      };

      const makeItem = (src, hiddenValue, fileRef) => {
        const data = hiddenValue && !fileRef ? decodeItem(hiddenValue, src) : { src, caption: '' };
        const item = document.createElement('div');
        item.className = 'gallery-picker__item';
        item.dataset.galleryItem = 'true';
        item.draggable = true;
        if (fileRef) item.dataset.filePreview = 'true';
        item.__fileRef = fileRef || null;
        const media = document.createElement('div');
        media.className = 'gallery-picker__media';
        const img = document.createElement('img');
        img.src = data.src || src;
        img.alt = '';
        const drag = document.createElement('button');
        drag.type = 'button';
        drag.className = 'gallery-picker__drag';
        drag.setAttribute('data-gallery-drag', '');
        drag.setAttribute('aria-label', 'Arrastar imagem');
        drag.innerHTML = '<i data-lucide="grip-vertical"></i>';
        const order = document.createElement('span');
        order.className = 'gallery-picker__order';
        order.setAttribute('data-gallery-order', '');
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'gallery-picker__remove';
        remove.setAttribute('data-gallery-remove', '');
        remove.setAttribute('aria-label', 'Remover imagem');
        remove.innerHTML = '<i data-lucide="x"></i>';
        media.appendChild(img);
        media.appendChild(drag);
        media.appendChild(order);
        media.appendChild(remove);
        item.appendChild(media);
        if (hiddenValue || fileRef) {
          const hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = inputName;
          hidden.value = fileRef ? JSON.stringify({ upload: true, caption: '' }) : encodeItem(data.src, data.caption || '');
          hidden.setAttribute('data-gallery-value', '');
          if (fileRef) hidden.dataset.galleryUpload = 'true';
          item.appendChild(hidden);
        }
        const caption = document.createElement('input');
        caption.type = 'text';
        caption.className = 'gallery-picker__caption';
        caption.placeholder = 'Nome da comida / legenda';
        caption.setAttribute('data-gallery-caption', '');
        caption.value = data.caption || '';
        if (fileRef) caption.name = fileCaptionName;
        caption.addEventListener('input', () => syncHiddenValue(item));
        item.appendChild(caption);
        list.appendChild(item);
        updateOrderBadges();
        refreshIcons();
      };

      const renderFiles = () => {
        list.querySelectorAll('[data-file-preview]').forEach(item => item.remove());
        Array.from(fileInput?.files || []).forEach(file => makeItem(URL.createObjectURL(file), '', file));
      };

      fileInput && fileInput.addEventListener('change', renderFiles);
      picker.addEventListener('click', e => {
        const btn = e.target.closest('[data-gallery-remove]');
        if (!btn || !picker.contains(btn)) return;
        e.preventDefault(); e.stopPropagation();
        removeItem(btn.closest('[data-gallery-item]'));
      });
      addBtn && addBtn.addEventListener('click', () => {
        const value = (urlInput?.value || '').trim();
        if (!value) return;
        makeItem(value, encodeItem(value, ''), null);
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

      let draggedItem = null;
      list.addEventListener('dragstart', e => {
        const item = e.target.closest('[data-gallery-item]');
        if (!item || !list.contains(item)) return;
        draggedItem = item;
        item.classList.add('is-dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', 'gallery-item');
      });
      list.addEventListener('dragend', () => {
        if (draggedItem) draggedItem.classList.remove('is-dragging');
        draggedItem = null;
        list.querySelectorAll('[data-gallery-item]').forEach(syncHiddenValue);
        syncFileOrder();
        updateOrderBadges();
      });
      list.addEventListener('dragover', e => {
        if (!draggedItem) return;
        e.preventDefault();
        const target = e.target.closest('[data-gallery-item]');
        if (!target || target === draggedItem || !list.contains(target)) return;
        const rect = target.getBoundingClientRect();
        const columns = getComputedStyle(list).gridTemplateColumns.split(' ').filter(Boolean).length;
        const before = columns > 1 ? e.clientX < rect.left + rect.width / 2 : e.clientY < rect.top + rect.height / 2;
        list.insertBefore(draggedItem, before ? target : target.nextSibling);
        updateOrderBadges();
      });
      list.querySelectorAll('[data-gallery-caption]').forEach(input => {
        input.addEventListener('input', () => syncHiddenValue(input.closest('[data-gallery-item]')));
      });
      updateOrderBadges();
    });
  }

  // ===== Media library sortable cards =====
  function initGalleryLibrarySort(){
    document.querySelectorAll('[data-sortable-gallery-library]').forEach(form => {
      if (form.__init) return; form.__init = true;
      const list = form.querySelector('[data-gallery-library-list]');
      if (!list) return;
      let dragged = null;
      list.addEventListener('dragstart', e => {
        const item = e.target.closest('[data-gallery-library-item]');
        if (!item || !list.contains(item)) return;
        dragged = item;
        item.classList.add('is-dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', 'gallery-library-item');
      });
      list.addEventListener('dragend', () => {
        if (dragged) dragged.classList.remove('is-dragging');
        dragged = null;
      });
      list.addEventListener('dragover', e => {
        if (!dragged) return;
        e.preventDefault();
        const target = e.target.closest('[data-gallery-library-item]');
        if (!target || target === dragged || !list.contains(target)) return;
        const rect = target.getBoundingClientRect();
        const columns = getComputedStyle(list).gridTemplateColumns.split(' ').filter(Boolean).length;
        const before = columns > 1 ? e.clientX < rect.left + rect.width / 2 : e.clientY < rect.top + rect.height / 2;
        list.insertBefore(dragged, before ? target : target.nextSibling);
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
    initIconPicker();
    initGalleryPick();
    initGalleryLibrarySort();
    initEditors();
  });
  document.addEventListener('alpine:initialized', refreshIcons);
})();
