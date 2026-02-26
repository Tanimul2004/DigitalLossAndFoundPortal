<?php


function manage_claims() {

    if (!isLoggedIn() || !isAdmin()) {
        redirect('public/login.php');
    }


    // Default View (Pending Claims List Only)
    $pending = admin_pending_claims();

    include __DIR__ . '/../includes/header.php';
    ?>

    <h1>Pending Claims</h1>

    <?php if (empty($pending)): ?>
        <p>No pending claims.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Item</th>
                <th>Claimant</th>
                <th>Action</th>
            </tr>

            <?php foreach ($pending as $c): ?>
                <tr>
                    <td><?= e($c['item_title']) ?></td>
                    <td><?= e($c['claimant_name']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <input type="hidden" name="claim_id" value="<?= (int)$c['id'] ?>">
                            <button name="decision" value="approve">Approve</button>
                            <button name="decision" value="reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

        </table>
    <?php endif; ?>

    <?php
   
}
