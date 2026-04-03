<?php
$pageTitle = 'Track Claims';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$claims = getUserClaims((int) $_SESSION['user_id']);

include __DIR__ . '/../includes/header.php';
?>
<div class="section-card p-6">
    <h1 class="text-3xl font-bold mb-2">Track Your Claims</h1>
    <p class="text-slate-400 mb-6">Claims are sorted by latest submission. Match score helps admin prioritize review.</p>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Score</th>
                    <th>Status</th>
                    <th>Summary</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($claims as $claim): ?>
                    <tr>
                        <td><?= e($claim['item_title']) ?></td>
                        <td><?= e((string) $claim['match_score']) ?>%</td>
                        <td>
                            <span class="badge <?= statusBadge($claim['status']) ?>">
                                <?= e(ucfirst($claim['status'])) ?>
                            </span>
                        </td>
                        <td><?= e($claim['match_summary']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
