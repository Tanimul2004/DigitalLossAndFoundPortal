<?php
$pageTitle = 'Submit Claim';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$itemId = (int) ($_GET['item_id'] ?? 0);
$item = getItemById($itemId);

if (!$item || $item['status'] !== 'active' || (int) $item['user_id'] === (int) $_SESSION['user_id']) {
    redirect('items/search.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        try {
            createClaim([
                'item_id' => (int) $item['id'],
                'user_id' => (int) $_SESSION['user_id'],
                'claim_details' => sanitize($_POST['claim_details'] ?? ''),
                'proof_docs' => sanitize($_POST['proof_docs'] ?? ''),
                'claimed_brand' => sanitize($_POST['claimed_brand'] ?? ''),
                'claimed_color' => sanitize($_POST['claimed_color'] ?? ''),
                'claimed_serial' => sanitize($_POST['claimed_serial'] ?? ''),
                'claimed_location' => sanitize($_POST['claimed_location'] ?? ''),
                'claimed_date' => sanitize($_POST['claimed_date'] ?? ''),
                'identifying_marks' => sanitize($_POST['identifying_marks'] ?? ''),
            ]);

            flash('success', 'Claim submitted. Admin will review your details and match score.');
            redirect('claims/track.php');
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 section-card p-6">
        <h1 class="text-3xl font-bold mb-2">Submit Claim</h1>
        <p class="text-slate-400 mb-6">Add precise details so the match score is higher for the admin.</p>

        <?php if ($error): ?>
            <div class="mb-5 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="grid md:grid-cols-2 gap-4">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <div>
                <label class="block mb-2 text-sm font-medium">Claimed Brand</label>
                <input class="input" name="claimed_brand">
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium">Claimed Color</label>
                <input class="input" name="claimed_color">
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium">Claimed Serial / ID</label>
                <input class="input" name="claimed_serial">
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium">Claimed Date</label>
                <input class="input" type="date" name="claimed_date">
            </div>
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium">Claimed Location</label>
                <input class="input" name="claimed_location">
            </div>
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium">Identifying Marks</label>
                <textarea class="textarea" name="identifying_marks" rows="3"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium">Claim Details *</label>
                <textarea class="textarea" name="claim_details" rows="5" required></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium">Additional Proof</label>
                <textarea class="textarea" name="proof_docs" rows="3"></textarea>
            </div>
            <div class="md:col-span-2">
                <button class="btn btn-primary">Submit Claim</button>
            </div>
        </form>
    </div>

    <div class="section-card p-6">
        <h2 class="text-xl font-bold mb-3">Item Summary</h2>
        <div class="font-semibold text-lg"><?= e($item['title']) ?></div>
        <div class="text-slate-400 text-sm mt-2">
            <?= e($item['location']) ?> • <?= e(date('M d, Y', strtotime($item['date_lost_found']))) ?>
        </div>
        <div class="detail-box mt-4">
            <div class="text-slate-400 text-sm mb-2">Stored description</div>
            <p class="text-sm text-slate-200"><?= e($item['description']) ?></p>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
