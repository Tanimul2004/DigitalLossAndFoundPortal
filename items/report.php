<?php
$pageTitle = "Report Item";
require_once __DIR__ . '/../includes/functions.php';
if (!isLoggedIn()) redirect('public/login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf'] ?? null)) {
    flash_set('error', 'Security check failed.');
    redirect('items/report.php');
  }

  $type = sanitize($_POST['type'] ?? '');
  $title = sanitize($_POST['title'] ?? '');
  $description = sanitize($_POST['description'] ?? '');
  $category = sanitize($_POST['category'] ?? '');
  $location = sanitize($_POST['location'] ?? '');
  $date = sanitize($_POST['date_lost_found'] ?? '');

  if (!in_array($type, ['lost','found'], true)) {
    flash_set('error', 'Invalid item type.');
  } elseif ($title==='' || $description==='' || $location==='' || $date==='') {
    flash_set('error', 'Please fill all required fields.');
  } else {
    $image = null;
    if (!empty($_FILES['image']['name'])) {
      $up = upload_image($_FILES['image']);
      if (!$up['ok']) {
        flash_set('error', $up['error']);
        redirect('items/report.php');
      }
      $image = $up['filename'];
    }

    item_create([
      'type' => $type,
      'title' => $title,
      'description' => $description,
      'category' => $category,
      'location' => $location,
      'date_lost_found' => $date,
      'image' => $image,
      'user_id' => (int)$_SESSION['user_id'],
      'status' => 'pending'
    ]);

    flash_set('success', 'Item submitted. Admin will review before it becomes public.');
    redirect('public/dashboard.php');
  }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="max-w-4xl mx-auto">
  <div class="flex items-end justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-3xl font-extrabold">Report an Item</h1>
      <p class="text-gray-400 mt-1">Lost or found something? Submit details (optional photo).</p>
    </div>
    <a class="text-gray-400 hover:text-gray-200" href="<?= BASE_URL ?>public/dashboard.php"><i class="fa-solid fa-arrow-left mr-2"></i>Dashboard</a>
  </div>

  <div class="mt-6 bg-card border border-gray-800 rounded-2xl p-7">
    <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />

      <div class="space-y-4">
        <div>
          <div class="text-sm text-gray-300 mb-2">Type *</div>
          <div class="flex gap-3">
            <label class="flex items-center gap-2 bg-gray-800/50 border border-gray-800 rounded-xl px-4 py-3 cursor-pointer">
              <input type="radio" name="type" value="lost" checked />
              <span class="text-red-300"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Lost</span>
            </label>
            <label class="flex items-center gap-2 bg-gray-800/50 border border-gray-800 rounded-xl px-4 py-3 cursor-pointer">
              <input type="radio" name="type" value="found" />
              <span class="text-emerald-300"><i class="fa-solid fa-magnifying-glass mr-2"></i>Found</span>
            </label>
          </div>
        </div>

        <div>
          <label class="text-sm text-gray-300">Title *</label>
          <input name="title" required placeholder="e.g., iPhone 13, Wallet, ID Card"
                 class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
        </div>

        <div>
          <label class="text-sm text-gray-300">Category</label>
          <select name="category" class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary">
            <option value="">Select</option>
            <option value="electronics">Electronics</option>
            <option value="documents">Documents</option>
            <option value="jewelry">Jewelry</option>
            <option value="clothing">Clothing</option>
            <option value="bags">Bags & Wallets</option>
            <option value="keys">Keys</option>
            <option value="books">Books</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div>
          <label class="text-sm text-gray-300">Location *</label>
          <input id="locationInput" name="location" required placeholder="e.g., Library, Cafeteria, Block A"
                 class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
          <div class="mt-2 flex flex-wrap items-center gap-3">
            <button type="button" id="toggleMap" class="text-sm px-3 py-2 rounded-xl glass border border-white/10 hover:bg-white/5">
              <i class="fa-solid fa-location-crosshairs mr-2 text-primary"></i>Choose on map (optional)
            </button>
            <span class="text-xs text-gray-400">Tip: click on the map to pick a spot and auto-fill location.</span>
          </div>

          <div id="mapWrap" class="mt-4 hidden">
            <div class="glass border border-white/10 rounded-2xl p-3">
              <div class="flex items-center justify-between gap-3 flex-wrap mb-3">
                <div class="text-sm text-gray-300 font-semibold">Pick location</div>
                <button type="button" id="useMyLocation" class="text-sm px-3 py-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10">
                  <i class="fa-solid fa-crosshairs mr-2"></i>Use my current location
                </button>
              </div>
              <div id="map" class="w-full rounded-2xl overflow-hidden" style="height: 260px;"></div>
              <p class="text-xs text-gray-500 mt-3">Map uses OpenStreetMap (no API key). You can still type location manually.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-4">
        <div>
          <label class="text-sm text-gray-300">Date *</label>
          <input name="date_lost_found" type="date" required
                 class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
        </div>

        <div>
          <label class="text-sm text-gray-300">Description *</label>
          <textarea name="description" required rows="7"
                    placeholder="Color, brand, unique marks, serial number, contents…"
                    class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary"></textarea>
        </div>

        <div>
          <label class="text-sm text-gray-300">Photo (optional)</label>
          <input name="image" type="file" accept="image/*"
                 class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3" />
          <p class="text-xs text-gray-500 mt-2">Max 5MB • JPG/PNG/GIF/WEBP</p>
        </div>

        <button class="w-full bg-primary hover:bg-blue-700 font-semibold py-3 rounded-xl">
          <i class="fa-solid fa-paper-plane mr-2"></i>Submit for Review
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Leaflet (OpenStreetMap) for optional location picking -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
  (function(){
    const toggleBtn = document.getElementById('toggleMap');
    const mapWrap = document.getElementById('mapWrap');
    const locInput = document.getElementById('locationInput');
    const myLocBtn = document.getElementById('useMyLocation');

    let map, marker;

    function ensureMap(){
      if (map) return;
      map = L.map('map', { scrollWheelZoom: false }).setView([23.8103, 90.4125], 12); // Dhaka default
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      map.on('click', async (e) => {
        const { lat, lng } = e.latlng;
        if (!marker) marker = L.marker([lat, lng]).addTo(map);
        marker.setLatLng([lat, lng]);
        map.panTo([lat, lng]);

        // Try reverse geocoding (optional). If it fails, fall back to coordinates.
        try {
          const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
          const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
          if (!res.ok) throw new Error('Reverse geocode failed');
          const data = await res.json();
          locInput.value = data.display_name ? data.display_name : `Lat ${lat.toFixed(5)}, Lng ${lng.toFixed(5)}`;
        } catch (err) {
          locInput.value = `Lat ${lat.toFixed(5)}, Lng ${lng.toFixed(5)}`;
        }
      });
    }

    toggleBtn?.addEventListener('click', () => {
      mapWrap.classList.toggle('hidden');
      if (!mapWrap.classList.contains('hidden')) {
        ensureMap();
        setTimeout(() => map.invalidateSize(), 50);
      }
    });

    myLocBtn?.addEventListener('click', () => {
      if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser.');
        return;
      }
      mapWrap.classList.remove('hidden');
      ensureMap();
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const lat = pos.coords.latitude;
          const lng = pos.coords.longitude;
          map.setView([lat, lng], 16);
          if (!marker) marker = L.marker([lat, lng]).addTo(map);
          marker.setLatLng([lat, lng]);
          map.invalidateSize();
        },
        () => alert('Could not get your location. Please allow location permission or click on the map.')
      );
    });
  })();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
