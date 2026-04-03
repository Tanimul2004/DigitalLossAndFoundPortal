<?php
$pageTitle = 'Manage Claims';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$action = $_GET['action'] ?? '';
$claimId = (int) ($_GET['id'] ?? 0);

if ($claimId && $action === 'approve') {
    approveClaimAndDeleteItem($claimId, (int) $_SESSION['user_id']);
    flash('success', 'Claim approved. Matching information was reviewed and the item has been removed from the database.');
    redirect('admin/claims.php');
}

if ($claimId && $action === 'reject') {
    rejectClaim($claimId, (int) $_SESSION['user_id']);
    flash('success', 'Claim rejected.');
    redirect('admin/claims.php');
}

$statement = db()->query(
    "SELECT c.*, i.title AS item_title, u.name AS claimant_name
     FROM claims c
     LEFT JOIN items i ON i.id = c.item_id
     JOIN users u ON u.id = c.user_id
     ORDER BY c.status = 'pending' DESC, c.match_score DESC, c.created_at DESC"
);
$claims = $statement->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="section-card p-6">
    <h1 class="text-3xl font-bold mb-6">Manage Claims</h1>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Claimant</th>
                    <th>Item</th>
                    <th>Score</th>
                    <th>Status</th>
                    <th>Summary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($claims as $claim): ?>
                    <tr>
                        <td><?= e($claim['claimant_name']) ?></td>
                        <td><?= e($claim['item_title'] ?? 'Item removed') ?></td>
                        <td><?= e((string) $claim['match_score']) ?>%</td>
                        <td><span class="badge <?= statusBadge($claim['status']) ?>"><?= e($claim['status']) ?></span></td>
                        <td><?= e($claim['match_summary']) ?></td>
                        <td class="space-x-2">
                            <?php if ($claim['status'] === 'pending'): ?>
                                <a class="text-emerald-400" href="<?= base_url('admin/claims.php?action=approve&id=' . $claim['id']) ?>">Approve</a>
                                <a class="text-red-400" href="<?= base_url('admin/claims.php?action=reject&id=' . $claim['id']) ?>">Reject</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
