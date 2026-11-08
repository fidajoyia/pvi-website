/* Prime Vocational Institute — small front-end helpers. */
(function () {
  'use strict';

  // Mobile menu
  var btn = document.querySelector('.menu-btn');
  var links = document.querySelector('.nav-links');
  if (btn && links) {
    btn.addEventListener('click', function () {
      var open = links.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Poster lightbox (click an image to see it large)
  function openLightbox(src, title) {
    var box = document.getElementById('lightbox');
    if (!box) {
      box = document.createElement('div');
      box.id = 'lightbox';
      box.addEventListener('click', function () { box.classList.remove('open'); });
      document.body.appendChild(box);
    }
    box.innerHTML = '';
    var img = document.createElement('img');
    img.src = src;
    img.alt = title;
    var cap = document.createElement('p');
    cap.textContent = title + ' · band karne ke liye click karein';
    box.appendChild(img);
    box.appendChild(cap);
    box.classList.add('open');
  }
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-lightbox]');
    if (el) {
      openLightbox(el.getAttribute('data-lightbox'), el.getAttribute('data-title') || '');
    }
    if (e.target.closest('[data-print]')) {
      window.print();
    }
  });
  document.addEventListener('keydown', function (e) {
    var box = document.getElementById('lightbox');
    if (e.key === 'Escape' && box) { box.classList.remove('open'); }
  });

  // Course search
  var search = document.getElementById('course-search');
  if (search) {
    search.addEventListener('input', function () {
      var q = search.value.trim().toLowerCase();
      var any = false;
      document.querySelectorAll('.course-group').forEach(function (group) {
        var shown = 0;
        group.querySelectorAll('.course-card').forEach(function (card) {
          var match = !q || card.getAttribute('data-search').indexOf(q) !== -1;
          card.style.display = match ? '' : 'none';
          if (match) { shown++; }
        });
        group.style.display = shown ? '' : 'none';
        if (shown) { any = true; }
      });
      var empty = document.getElementById('course-empty');
      if (empty) { empty.classList.toggle('hidden', any); }
    });
  }

  // After submitting the apply form, bring the message into view.
  var result = document.getElementById('apply-result');
  if (result) { result.scrollIntoView({ block: 'center' }); }
})();
