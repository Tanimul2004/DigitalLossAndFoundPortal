<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$user = getUserById((int) $_SESSION['user_id']);
$myItems = getItems(['user_id' => (int) $_SESSION['user_id']], null, 0, true);
$myClaims = getUserClaims((int) $_SESSION['user_id']);
$pendingReports = array_filter($myItems, static fn ($item) => $item['status'] === 'pending');

include __DIR__ . '/../includes/header.php';
?>
<div class="mb-8">
    <h1 class="text-3xl font-bold">Welcome, <?= e($user['name']) ?>!</h1>
    <p class="text-slate-400">Track your reports and claims from one place.</p>
</div>

<div class="grid md:grid-cols-3 gap-6 mb-8">
    <div class="stat-card">
        <div class="text-3xl font-bold text-blue-400"><?= count($myItems) ?></div>
        <div class="text-slate-300 mt-2">Your Items</div>
    </div>
    <div class="stat-card">
        <div class="text-3xl font-bold text-amber-400"><?= count($pendingReports) ?></div>
        <div class="text-slate-300 mt-2">Pending Review</div>
    </div>
    <div class="stat-card">
        <div class="text-3xl font-bold text-emerald-400"><?= count($myClaims) ?></div>
        <div class="text-slate-300 mt-2">Claims Submitted</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="section-card p-6">
        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-bold">Your Reports</h2>
            <a class="text-blue-400" href="<?= base_url('items/report.php') ?>">New Report</a>
        </div>

        <?php if (!$myItems): ?>
            <div class="empty-state">You have not reported any item yet.</div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach (array_slice($myItems, 0, 6) as $item): ?>
                    <div class="listing-card">
                        <div class="flex justify-between gap-3">
                            <div>
                                <div class="font-semibold"><?= e($item['title']) ?></div>
                                <div class="text-sm text-slate-400">
                                    <?= e(ucfirst($item['type'])) ?> • <?= e($item['location']) ?>
                                </div>
                            </div>
                            <span class="badge <?= statusBadge($item['status']) ?>">
                                <?= e(ucfirst($item['status'])) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="section-card p-6">
        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-bold">Your Claims</h2>
            <a class="text-blue-400" href="<?= base_url('claims/track.php') ?>">Track All</a>
        </div>

        <?php if (!$myClaims): ?>
            <div class="empty-state">You have not submitted any claim yet.</div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach (array_slice($myClaims, 0, 6) as $claim): ?>
                    <div class="listing-card">
                        <div class="flex justify-between gap-3">
                            <div>
                                <div class="font-semibold"><?= e($claim['item_title']) ?></div>
                                <div class="text-sm text-slate-400">
                                    Score: <?= e((string) $claim['match_score']) ?>% • <?= e($claim['match_summary']) ?>
                                </div>
                            </div>
                            <span class="badge <?= statusBadge($claim['status']) ?>">
                                <?= e(ucfirst($claim['status'])) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
