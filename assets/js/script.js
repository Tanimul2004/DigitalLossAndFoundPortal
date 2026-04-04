document.addEventListener('DOMContentLoaded', () => {

  const btn = document.getElementById('mobileBtn');
  const menu = document.getElementById('mobileMenu');

  if (btn && menu) {
    btn.addEventListener('click', () => 
      menu.classList.toggle('hidden')
    );

    menu.addEventListener('click', (ev) => {
      const a = ev.target.closest('a');
      if (a) menu.classList.add('hidden');
    });
  }

  const els = Array.from(document.querySelectorAll('.reveal'));

  if (!('IntersectionObserver' in window) || els.length === 0) {
    els.forEach(el => el.classList.add('reveal-show'));
  } else {
    const io = new IntersectionObserver((entries) => {
      for (const e of entries) {
        if (e.isIntersecting) {
          e.target.classList.add('reveal-show');
          io.unobserve(e.target);
        }
      }
    }, {
      threshold: 0.12,
      rootMargin: '0px 0px -10% 0px'
    });

    els.forEach(el => io.observe(el));
  }

  const image = document.getElementById('image');
  const preview = document.getElementById('preview');
  const wrap = document.getElementById('preview-wrap');

  if (image && preview && wrap) {
    image.addEventListener('change', () => {
      const file = image.files && image.files[0];
      if (!file) return;

      const r = new FileReader();
      r.onload = e => {
        preview.src = e.target.result;
        wrap.classList.remove('hidden');
      };

      r.readAsDataURL(file);
    });
  }

  document.querySelectorAll('[data-radio-card]').forEach(card => {
    const input = card.querySelector('input[type="radio"]');

    const refresh = () => {
      document.querySelectorAll('[data-radio-card]')
        .forEach(c => c.classList.remove('active'));

      if (input && input.checked) {
        card.classList.add('active');
      }
    };

    card.addEventListener('click', refresh);
    refresh();
  });

  const mapEl = document.getElementById('item-map');

  if (mapEl && window.L) {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const txt = document.getElementById('selected-coordinates');
    const mapsLink = document.getElementById('google-maps-link');

    const lat = parseFloat(latInput?.value || '23.8103');
    const lng = parseFloat(lngInput?.value || '90.4125');

    const map = L.map('item-map').setView([lat, lng], 13);

    L.tileLayer(
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }
    ).addTo(map);

    let marker = L.marker([lat, lng]).addTo(map);

    const sync = (la, lo) => {
      if (latInput) latInput.value = la.toFixed(6);
      if (lngInput) lngInput.value = lo.toFixed(6);

      if (txt) {
        txt.value = `${la.toFixed(6)}, ${lo.toFixed(6)}`;
      }

      if (mapsLink) {
        mapsLink.href = `https://www.google.com/maps?q=${la},${lo}`;
      }
    };

    sync(lat, lng);

    map.on('click', (e) => {
      marker.setLatLng(e.latlng);
      sync(e.latlng.lat, e.latlng.lng);
    });

    const loc = document.getElementById('use-current-location');

    if (loc && navigator.geolocation) {
      loc.addEventListener('click', () => {
        navigator.geolocation.getCurrentPosition((pos) => {
          const la = pos.coords.latitude;
          const lo = pos.coords.longitude;

          map.setView([la, lo], 15);
          marker.setLatLng([la, lo]);
          sync(la, lo);
        });
      });
    }
  }

});