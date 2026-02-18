<?php
$pageTitle = "Profile";
require_once __DIR__ . '/../includes/functions.php';
if (!isLoggedIn()) redirect('public/login.php');

$user = user_public_by_id((int)$_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf'] ?? null)) {
    flash_set('error', 'Security check failed.');
    redirect('profile/index.php');
  }

  $name = sanitize($_POST['name'] ?? '');
  $phone = sanitize($_POST['phone'] ?? '');

  // Upload profile photo (optional)
  $newAvatar = null;
  if (!empty($_FILES['profile_image']['name'])) {
    $up = upload_image($_FILES['profile_image']);
    if (!$up['ok']) {
      flash_set('error', $up['error']);
      redirect('profile/index.php');
    }
    $newAvatar = $up['filename'];
  }

  global $pdo;

  // Update profile info
  if ($name !== '') {
    $st = $pdo->prepare("UPDATE users SET name=?, phone=?, profile_image=COALESCE(?, profile_image) WHERE id=?");
    $st->execute([$name, $phone ?: null, $newAvatar, (int)$_SESSION['user_id']]);
    $_SESSION['user_name'] = $name;
    if ($newAvatar) { $_SESSION['user_profile_image'] = $newAvatar; }
    flash_set('success', 'Profile updated.');
  }

  // Change password (optional)
  $current = $_POST['current_password'] ?? '';
  $new = $_POST['new_password'] ?? '';
  $confirm = $_POST['confirm_password'] ?? '';

  if ($current !== '' || $new !== '' || $confirm !== '') {
    if (strlen($new) < 6) {
      flash_set('error', 'New password must be at least 6 characters.');
      redirect('profile/index.php');
    }
    if ($new !== $confirm) {
      flash_set('error', 'New passwords do not match.');
      redirect('profile/index.php');
    }
    $st = $pdo->prepare("SELECT password FROM users WHERE id=?");
    $st->execute([(int)$_SESSION['user_id']]);
    $row = $st->fetch();
    if (!$row || !password_verify($current, $row['password'])) {
      flash_set('error', 'Current password is incorrect.');
      redirect('profile/index.php');
    }
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $st = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
    $st->execute([$hash, (int)$_SESSION['user_id']]);
    flash_set('success', 'Password updated.');
  }

  redirect('profile/index.php');
}

include __DIR__ . '/../includes/header.php';
?>
<div class="max-w-4xl mx-auto">
  <div class="flex items-end justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-3xl font-extrabold">Profile</h1>
      <p class="text-gray-400 mt-1">Manage your account details.</p>
    </div>
    <a class="text-gray-400 hover:text-gray-200" href="<?= BASE_URL ?>public/dashboard.php"><i class="fa-solid fa-arrow-left mr-2"></i>Dashboard</a>
  </div>

  <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-card border border-gray-800 rounded-2xl p-7">
      <div class="font-bold text-lg mb-4">Account</div>
      <form method="post" class="space-y-4">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm text-gray-300">Name</label>
            <input name="name" value="<?= e($user['name'] ?? '') ?>" class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
          </div>
          <div>
            <label class="text-sm text-gray-300">Phone</label>
            <input name="phone" value="<?= e($user['phone'] ?? '') ?>" class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
          </div>
        </div>
      <div>
        <label class="text-sm text-gray-300">Profile photo (optional)</label>
        <input name="profile_image" type="file" accept="image/*"
               class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3" />
        <p class="text-xs text-gray-500 mt-2">Max 5MB • JPG/PNG/GIF/WEBP</p>
      </div>

        <div>
          <label class="text-sm text-gray-300">Email</label>
          <input disabled value="<?= e($user['email'] ?? '') ?>" class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 opacity-70" />
        </div>

        <div class="pt-4 border-t border-gray-800">
          <button class="bg-primary hover:bg-blue-700 font-semibold px-6 py-3 rounded-xl">
            <i class="fa-solid fa-floppy-disk mr-2"></i>Save
          </button>
        </div>

        <div class="pt-4 border-t border-gray-800">
          <div class="font-bold text-lg mb-3">Change password</div>
          <div class="space-y-3">
            <input name="current_password" type="password" placeholder="Current password"
                   class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <input name="new_password" type="password" placeholder="New password"
                     class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
              <input name="confirm_password" type="password" placeholder="Confirm new password"
                     class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="bg-card border border-gray-800 rounded-2xl p-6">
      <div class="font-bold">Account summary</div>
      <div class="mt-4 space-y-2 text-sm text-gray-400">
        <div class="flex justify-between"><span>Role</span><span class="text-gray-200"><?= e($user['role'] ?? 'user') ?></span></div>
        <div class="flex justify-between"><span>Member since</span><span class="text-gray-200"><?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?></span></div>
      </div>
      <?php if (isAdmin()): ?>
        <a class="mt-6 block text-center bg-gray-700 hover:bg-gray-600 font-semibold py-3 rounded-xl" href="<?= BASE_URL ?>admin/index.php">
          Go to Admin Panel
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
