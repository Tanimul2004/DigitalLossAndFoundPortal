<?php
$pageTitle = 'Report Item';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$error = '';
$selectedType = $_POST['type'] ?? ($_GET['type'] ?? 'lost');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid request.';
    } else {
        $data = [
            'type' => sanitize($_POST['type'] ?? ''),
            'title' => sanitize($_POST['title'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'category' => sanitize($_POST['category'] ?? ''),
            'brand' => sanitize($_POST['brand'] ?? ''),
            'color' => sanitize($_POST['color'] ?? ''),
            'serial_number' => sanitize($_POST['serial_number'] ?? ''),
            'unique_marks' => sanitize($_POST['unique_marks'] ?? ''),
            'location' => sanitize($_POST['location'] ?? ''),
            'latitude' => sanitize($_POST['latitude'] ?? ''),
            'longitude' => sanitize($_POST['longitude'] ?? ''),
            'date_lost_found' => sanitize($_POST['date_lost_found'] ?? ''),
            'user_id' => (int) $_SESSION['user_id'],
            'status' => 'pending',
        ];

        if (
            !in_array($data['type'], ['lost', 'found'], true)
            || $data['title'] === ''
            || $data['description'] === ''
            || $data['location'] === ''
            || $data['date_lost_found'] === ''
        ) {
            $error = 'Please fill all required fields.';
        } else {
            try {
                $data['image'] = uploadImage($_FILES['image'] ?? []);
                createItem($data);
                flash('success', 'Item reported successfully. It is waiting for admin approval.');
                redirect('public/dashboard.php');
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
    <form id="report-form" method="post" enctype="multipart/form-data" class="xl:col-span-3 section-card p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 reveal">
        <div class="md:col-span-2 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-3xl font-bold mb-2">Report an Item</h1>
                <p class="text-slate-400">Structured form with Google-style location picker and image preview.</p>
            </div>
            <span class="badge bg-blue-500/15 text-blue-300 border border-blue-500/20">Admin approval required</span>
        </div>

        <?php if ($error): ?>
            <div class="md:col-span-2 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" id="latitude" name="latitude" value="<?= e($_POST['latitude'] ?? '') ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?= e($_POST['longitude'] ?? '') ?>">

        <div class="md:col-span-2">
            <label class="block mb-3 text-sm font-medium">Item Type *</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="radio-card <?= $selectedType === 'lost' ? 'active' : '' ?>" data-radio-card>
                    <input type="radio" name="type" value="lost" <?= $selectedType === 'lost' ? 'checked' : '' ?>>
                    <div class="font-semibold text-lg text-red-300 mb-1">
                        <i class="fa-solid fa-circle-minus mr-2"></i>Lost Item
                    </div>
                    <div class="text-sm text-slate-400">Post an item you lost so others can help identify it.</div>
                </label>

                <label class="radio-card <?= $selectedType === 'found' ? 'active' : '' ?>" data-radio-card>
                    <input type="radio" name="type" value="found" <?= $selectedType === 'found' ? 'checked' : '' ?>>
                    <div class="font-semibold text-lg text-emerald-300 mb-1">
                        <i class="fa-solid fa-circle-plus mr-2"></i>Found Item
                    </div>
                    <div class="text-sm text-slate-400">Post an item you discovered so the rightful owner can claim it.</div>
                </label>
            </div>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Item Title *</label>
            <input class="input" name="title" value="<?= e($_POST['title'] ?? '') ?>" required>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Category</label>
            <select class="select" name="category">
                <option value="">Select category</option>
                <?php foreach (categories() as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= ($_POST['category'] ?? '') === $key ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Brand</label>
            <input class="input" name="brand" value="<?= e($_POST['brand'] ?? '') ?>">
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Color</label>
            <input class="input" name="color" value="<?= e($_POST['color'] ?? '') ?>">
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Serial / ID Number</label>
            <input class="input" name="serial_number" value="<?= e($_POST['serial_number'] ?? '') ?>">
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Date *</label>
            <input class="input" type="date" name="date_lost_found" value="<?= e($_POST['date_lost_found'] ?? date('Y-m-d')) ?>" required>
        </div>

        <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-medium">Location Label *</label>
            <input class="input" id="location" name="location" value="<?= e($_POST['location'] ?? '') ?>" required>
        </div>

        <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-medium">Description *</label>
            <textarea class="textarea" name="description" rows="5" required><?= e($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-medium">Unique Marks</label>
            <textarea class="textarea" name="unique_marks" rows="3"><?= e($_POST['unique_marks'] ?? '') ?></textarea>
        </div>

        <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-medium">Selected Coordinates</label>
            <div class="flex gap-3 flex-wrap">
                <input class="input flex-1 min-w-[220px]" id="selected-coordinates" type="text" readonly placeholder="Pick a point from the map">
                <a id="google-maps-link" href="https://www.google.com/maps" target="_blank" rel="noopener" class="btn btn-glass whitespace-nowrap">Open in Google Maps</a>
            </div>
        </div>

        <div class="md:col-span-2 flex justify-between gap-4 flex-wrap border-t border-slate-800 pt-6">
            <a href="<?= base_url('public/dashboard.php') ?>" class="btn btn-secondary">Back to Dashboard</a>
            <button class="btn btn-primary btn-lg">Submit Report</button>
        </div>
    </form>

    <div class="xl:col-span-2 reveal">
        <div class="map-card p-4 md:p-5 h-full flex flex-col gap-5">
            <div>
                <h2 class="font-semibold text-lg">Location &amp; Image Tools</h2>
                <p class="text-sm text-slate-400 mt-1">
                    On desktop and laptop, the location picker and image upload preview stay together in one side column.
                </p>
            </div>

            <div>
                <div class="flex items-center justify-between gap-4 mb-3 flex-wrap">
                    <div>
                        <h3 class="font-semibold text-base">Google-style Location Picker</h3>
                        <p class="text-sm text-slate-400">Click map to select coordinates and create a Google Maps shortcut.</p>
                    </div>
                    <button type="button" id="use-current-location" class="btn btn-secondary btn-sm">Use My Location</button>
                </div>
                <div id="item-map" class="map-canvas"></div>
            </div>

            <div>
                <h3 class="font-semibold text-base mb-3">Image Upload with Preview</h3>
                <label class="upload-dropzone" for="image">
                    <input id="image" class="sr-only" type="file" name="image" accept="image/*" form="report-form">
                    <div class="text-center pointer-events-none">
                        <div class="upload-icon">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="font-semibold text-white mb-1">Drop image here or click to browse</div>
                        <div class="text-sm text-slate-400">JPG, PNG, GIF or WEBP · Max 5MB</div>
                    </div>
                </label>
                <div id="preview-wrap" class="hidden mt-4">
                    <img id="preview" class="w-full h-72 object-cover rounded-2xl border border-slate-800" alt="Preview">
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
