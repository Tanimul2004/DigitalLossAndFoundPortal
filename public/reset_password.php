<?php
$pageTitle = "Set New Password";
require_once __DIR__ . '/../includes/functions.php';

$token = sanitize($_GET['token'] ?? '');
if ($token === '') {
  flash_set('error', 'Invalid reset link.');
  redirect('public/login.php');
}

include __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto">
  <div class="bg-card border border-gray-800 rounded-2xl p-7">
    <h1 class="text-2xl font-bold">Set a new password</h1>
    <p class="text-gray-400 mt-1">Choose a strong password.</p>

    <form class="mt-6 space-y-4" method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
      <div>
        <label class="text-sm text-gray-300">New password</label>
        <input name="password" type="password" required class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
      </div>
      <div>
        <label class="text-sm text-gray-300">Confirm password</label>
        <input name="confirm_password" type="password" required class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
      </div>
      <button class="w-full bg-primary hover:bg-blue-700 font-semibold py-3 rounded-xl">
        Update password
      </button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
