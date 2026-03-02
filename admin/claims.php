<?php
$pageTitle = "Manage Claims";
require_once __DIR__ . '/../includes/functions.php';

/*
|--------------------------------------------------------------------------
| Main Controller
|--------------------------------------------------------------------------
*/

function manage_claims() {

    if (!isLoggedIn() || !isAdmin()) {
        redirect('public/login.php');
    }

    handle_claim_decision();
    show_pending_claims();
}

manage_claims();


/*
|--------------------------------------------------------------------------
| Handle Approve / Reject Decision
|--------------------------------------------------------------------------
*/

function handle_claim_decision() {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (!csrf_validate($_POST['csrf'] ?? null)) {
        flash_set('error', 'Security check failed.');
        redirect('admin/claims.php');
    }

    $claimId = (int)($_POST['claim_id'] ?? 0);
    $decision = sanitize($_POST['decision'] ?? '');
    $notes = sanitize($_POST['admin_notes'] ?? '');

    try {

        if ($decision === 'approve') {

            admin_claim_approve(
                $claimId,
                (int)$_SESSION['user_id'],
                $notes
            );

            flash_set('success', 'Claim approved successfully.');

        } elseif ($decision === 'reject') {

            admin_claim_reject(
                $claimId,
                (int)$_SESSION['user_id'],
                $notes
            );

            flash_set('success', 'Claim rejected successfully.');
        }

    } catch (Throwable $e) {
        flash_set('error', 'Failed to process claim.');
    }

    redirect('admin/claims.php');
}


/*
|--------------------------------------------------------------------------
| Show Pending Claims List
|--------------------------------------------------------------------------
*/

function show_pending_claims() {

    $pending = admin_pending_claims();

    include __DIR__ . '/../includes/header.php';
    ?>

    <div class="container">
        <h1>Pending Claims</h1>

        <?php if (empty($pending)): ?>
            <p>No pending claims available.</p>
        <?php else: ?>

            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <tr>
                    <th>Item</th>
                    <th>Reporter</th>
                    <th>Claimant</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>

                <?php foreach ($pending as $c): ?>
                    <tr>
                        <td><?= e($c['item_title']) ?></td>
                        <td><?= e($c['reporter_name']) ?></td>
                        <td><?= e($c['claimant_name']) ?></td>
                        <td><?= e(date('M d, Y', strtotime($c['created_at']))) ?></td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                <input type="hidden" name="claim_id" value="<?= (int)$c['id'] ?>">

                                <button type="submit" name="decision" value="approve">
                                    Approve
                                </button>

                                <button type="submit" name="decision" value="reject">
                                    Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>

        <?php endif; ?>
    </div>

    <?php
    include __DIR__ . '/../includes/footer.php';
}