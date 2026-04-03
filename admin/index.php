<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$pendingItems = getItems(['status' => 'pending'], 8, 0, true);
$claimStatement = db()->query(
    "SELECT c.*, i.title AS item_title, u.name AS claimant_name
     FROM claims c
     JOIN items i ON i.id = c.item_id
     JOIN users u ON u.id = c.user_id
     WHERE c.status = 'pending'
     ORDER BY c.match_score DESC, c.created_at DESC
     LIMIT 8"
);
$pendingClaims = $claimStatement->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="mb-8">
    <h1 class="text-3xl font-bold">Admin Dashboard</h1>
    <p class="text-slate-400">Review pending items and rank claims by match score.</p>
</div>

<div class="grid md:grid-cols-4 gap-6 mb-8">
    <div class="stat-card">
        <div class="text-3xl font-bold text-amber-400"><?= countRows('items', "status='pending'") ?></div>
        <div class="text-slate-300 mt-2">Pending Items</div>
    </div>
    <div class="stat-card">
        <div class="text-3xl font-bold text-blue-400"><?= countRows('claims', "status='pending'") ?></div>
        <div class="text-slate-300 mt-2">Pending Claims</div>
    </div>
    <div class="stat-card">
        <div class="text-3xl font-bold text-emerald-400"><?= countRows('items', "status='active'") ?></div>
        <div class="text-slate-300 mt-2">Active Items</div>
    </div>
    <div class="stat-card">
        <div class="text-3xl font-bold text-fuchsia-400"><?= countRows('users', "role='user'") ?></div>
        <div class="text-slate-300 mt-2">Users</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="section-card p-6">
        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-bold">Pending Items</h2>
            <a class="text-blue-400" href="<?= base_url('admin/items.php') ?>">Manage</a>
        </div>

        <div class="space-y-3">
            <?php foreach ($pendingItems as $item): ?>
                <div class="listing-card">
                    <div class="flex justify-between gap-3">
                        <div>
                            <div class="font-semibold"><?= e($item['title']) ?></div>
                            <div class="text-sm text-slate-400"><?= e($item['location']) ?></div>
                        </div>
                        <div class="flex gap-2">
                            <a class="btn btn-sm btn-primary" href="<?= base_url('admin/items.php?action=approve&id=' . $item['id']) ?>">Approve</a>
                            <a class="btn btn-sm btn-secondary" href="<?= base_url('admin/items.php?action=reject&id=' . $item['id']) ?>">Reject</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section-card p-6">
        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-bold">Top Pending Claims</h2>
            <a class="text-blue-400" href="<?= base_url('admin/claims.php') ?>">Manage</a>
        </div>

        <div class="space-y-3">
            <?php foreach ($pendingClaims as $claim): ?>
                <div class="listing-card">
                    <div class="flex justify-between gap-3">
                        <div>
                            <div class="font-semibold"><?= e($claim['claimant_name']) ?> → <?= e($claim['item_title']) ?></div>
                            <div class="text-sm text-slate-400"><?= e($claim['match_summary']) ?></div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-blue-300"><?= e((string) $claim['match_score']) ?>%</div>
                            <a class="text-sm text-blue-400" href="<?= base_url('admin/claims.php') ?>">Review</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
