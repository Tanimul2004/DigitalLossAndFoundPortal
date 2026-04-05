<?php
$pageTitle = 'Item Details';
require_once __DIR__ . '/../includes/functions.php';

$itemId = (int) ($_GET['id'] ?? 0);
$item = getItemById($itemId);

if (!$item || !in_array($item['status'], ['active', 'resolved'], true)) {
    redirect('items/search.php');
}

$claims = getItemClaims((int) $item['id']);
$canViewReporter = canViewReporterInfo($item, $claims);
$userAlreadyClaimed = false;

if (isLoggedIn()) {
    foreach ($claims as $claim) {
        if ((int) $claim['user_id'] === (int) $_SESSION['user_id']) {
            $userAlreadyClaimed = true;
            break;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 section-card p-6">
        <div class="flex flex-wrap justify-between gap-3 mb-4">
            <div class="flex gap-2">
                <span class="badge <?= itemTypeBadge($item['type']) ?>"><?= e(strtoupper($item['type'])) ?></span>
                <span class="badge <?= statusBadge($item['status']) ?>"><?= e(strtoupper($item['status'])) ?></span>
            </div>
            <span class="text-sm text-slate-500">Reported <?= e(date('M d, Y', strtotime($item['created_at']))) ?></span>
        </div>

        <h1 class="text-3xl font-bold mb-4"><?= e($item['title']) ?></h1>

        <?php if ($item['image']): ?>
            <img src="<?= base_url('assets/uploads/' . $item['image']) ?>" class="w-full h-96 object-cover rounded-3xl border border-white/10 mb-6" alt="<?= e($item['title']) ?>">
        <?php endif; ?>

        <div class="grid md:grid-cols-2 gap-5 mb-6 text-sm">
            <div class="detail-box"><div class="text-slate-400">Category</div><div class="font-semibold mt-1"><?= e($item['category'] ?: 'Not specified') ?></div></div>
            <div class="detail-box"><div class="text-slate-400">Location</div><div class="font-semibold mt-1"><?= e($item['location']) ?></div></div>
            <div class="detail-box"><div class="text-slate-400">Brand / Color</div><div class="font-semibold mt-1"><?= e(trim(($item['brand'] ?: 'Unknown') . ' • ' . ($item['color'] ?: 'Unknown'), ' •')) ?></div></div>
            <div class="detail-box"><div class="text-slate-400">Date</div><div class="font-semibold mt-1"><?= e(date('F j, Y', strtotime($item['date_lost_found']))) ?></div></div>
        </div>

        <div class="detail-box mb-6">
            <div class="text-slate-400 mb-2">Description</div>
            <p class="text-slate-200 whitespace-pre-line"><?= e($item['description']) ?></p>
        </div>

        <?php if ($item['unique_marks']): ?>
            <div class="detail-box">
                <div class="text-slate-400 mb-2">Unique Marks</div>
                <p class="text-slate-200 whitespace-pre-line"><?= e($item['unique_marks']) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="space-y-6">
        <div class="section-card p-6">
            <h2 class="text-xl font-bold mb-3">Claim This Item</h2>
            <?php if (!isLoggedIn()): ?>
                <p class="text-slate-400 mb-4">Login to submit a claim.</p>
                <a href="<?= base_url('public/login.php') ?>" class="btn btn-primary w-full">Login</a>
            <?php elseif ((int) $item['user_id'] === (int) $_SESSION['user_id']): ?>
                <p class="text-slate-400">This is your own report.</p>
            <?php elseif ($userAlreadyClaimed): ?>
                <p class="text-slate-400">You have already submitted a claim for this item.</p>
            <?php elseif ($item['status'] === 'resolved'): ?>
                <p class="text-slate-400">This item has already been resolved.</p>
            <?php else: ?>
                <a href="<?= base_url('claims/create.php?item_id=' . $item['id']) ?>" class="btn btn-primary w-full">Submit Claim</a>
            <?php endif; ?>
        </div>

        <div class="section-card p-6">
            <h2 class="text-xl font-bold mb-3">Reporter Information</h2>
            <?php if ($canViewReporter): ?>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-slate-400">Name</span>
                        <div class="font-semibold"><?= e($item['reporter_name']) ?></div>
                    </div>
                    <div>
                        <span class="text-slate-400">Phone</span>
                        <div class="font-semibold"><?= e($item['reporter_phone'] ?: 'N/A') ?></div>
                    </div>
                    <div>
                        <span class="text-slate-400">Email</span>
                        <div class="font-semibold"><?= e($item['reporter_email']) ?></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4 text-amber-200 text-sm">
                    Reporter information is hidden until admin approves a valid claim for a claimant.
                </div>
            <?php endif; ?>
        </div>

        <?php if (isAdmin() && $claims): ?>
            <div class="section-card p-6">
                <h2 class="text-xl font-bold mb-3">Claims Ranked by Match</h2>
                <div class="space-y-3">
                    <?php foreach ($claims as $claim): ?>
                        <div class="listing-card">
                            <div class="flex justify-between gap-3">
                                <div>
                                    <div class="font-semibold"><?= e($claim['claimant_name']) ?></div>
                                    <div class="text-xs text-slate-400"><?= e($claim['match_summary']) ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-blue-300"><?= e((string) $claim['match_score']) ?>%</div>
                                    <div class="text-xs text-slate-500"><?= e($claim['status']) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
