<?php
$pageTitle = 'Browse Items';
require_once __DIR__ . '/../includes/functions.php';

$filters = [];
if (!empty($_GET['search'])) {
    $filters['search'] = sanitize($_GET['search']);
}
if (!empty($_GET['type'])) {
    $filters['type'] = sanitize($_GET['type']);
}
if (!empty($_GET['category'])) {
    $filters['category'] = sanitize($_GET['category']);
}

$items = getItems($filters, 24, 0, false);

include __DIR__ . '/../includes/header.php';
?>
<div class="section-card p-6 mb-6">
    <h1 class="text-3xl font-bold mb-2">Browse Items</h1>
    <p class="text-slate-400 mb-5">Find lost or found items using search and category filters.</p>

    <form class="grid md:grid-cols-4 gap-4">
        <input class="input" name="search" value="<?= e($_GET['search'] ?? '') ?>" placeholder="Search items...">

        <select class="select" name="type">
            <option value="">All Types</option>
            <option value="lost" <?= ($_GET['type'] ?? '') === 'lost' ? 'selected' : '' ?>>Lost</option>
            <option value="found" <?= ($_GET['type'] ?? '') === 'found' ? 'selected' : '' ?>>Found</option>
        </select>

        <select class="select" name="category">
            <option value="">All Categories</option>
            <?php foreach (categories() as $key => $label): ?>
                <option value="<?= e($key) ?>" <?= ($_GET['category'] ?? '') === $key ? 'selected' : '' ?>>
                    <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn btn-primary">Search</button>
    </form>
</div>

<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($items as $item): ?>
        <article class="listing-card reveal">
            <div class="thumb-wrap mb-4">
                <?php if ($item['image']): ?>
                    <img src="<?= base_url('assets/uploads/' . $item['image']) ?>" alt="<?= e($item['title']) ?>" class="thumb-img">
                <?php else: ?>
                    <div class="thumb-placeholder <?= $item['type'] === 'lost' ? 'thumb-lost' : 'thumb-found' ?>">
                        <?= strtoupper($item['type'][0]) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex justify-between gap-3 mb-2">
                <span class="badge <?= itemTypeBadge($item['type']) ?>"><?= e(ucfirst($item['type'])) ?></span>
                <span class="text-xs text-slate-500"><?= e(date('M d, Y', strtotime($item['created_at']))) ?></span>
            </div>

            <h3 class="text-lg font-bold mb-1"><?= e($item['title']) ?></h3>
            <p class="text-slate-400 text-sm line-clamp-2 mb-3"><?= e($item['description']) ?></p>
            <div class="text-sm text-slate-500 mb-3">
                <i class="fa-solid fa-location-dot mr-1"></i><?= e($item['location']) ?>
            </div>

            <a href="<?= base_url('items/view.php?id=' . $item['id']) ?>" class="btn btn-glass w-full">View Details</a>
        </article>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
