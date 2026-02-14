<?php
$pageTitle = "Login";
require_once __DIR__ . '/../includes/functions.php';
if (isLoggedIn()) redirect('public/dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf'] ?? null)) {
    flash_set('error', 'Security check failed. Please try again.');
    redirect('public/login.php');
  }

  $email = sanitize($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email==='' || $password==='') {
    flash_set('error', 'Please enter email and password.');
  } else {
    $u = user_by_email($email);
    if ($u && password_verify($password, $u['password'])) {
      $_SESSION['user_id'] = $u['id'];
      $_SESSION['user_name'] = $u['name'];
      $_SESSION['user_email'] = $u['email'];
      $_SESSION['user_role'] = $u['role'];
      $_SESSION['user_phone'] = $u['phone'] ?? '';
      $_SESSION['user_profile_image'] = $u['profile_image'] ?? '';
      flash_set('success', 'Welcome back, ' . $u['name'] . '!');
      redirect($u['role']==='admin' ? 'admin/index.php' : 'public/dashboard.php');
    } else {
      flash_set('error', 'Invalid email or password.');
    }
  }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto">
  <div class="bg-card border border-gray-800 rounded-2xl p-7">
    <h1 class="text-2xl font-bold">Welcome back</h1>
    <p class="text-gray-400 mt-1">Login to continue.</p>

    <form class="mt-6 space-y-4" method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
      <div>
        <label class="text-sm text-gray-300">Email</label>
        <input name="email" type="email" required class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
      </div>
      <div>
        <label class="text-sm text-gray-300">Password</label>
        <input name="password" type="password" required class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary" />
      </div>

      <div class="flex items-center justify-between text-sm">
        <a class="text-primary hover:text-blue-300" href="<?= BASE_URL ?>public/forgot_password.php">Forgot password?</a>
        <span class="text-gray-500">Admin: admin@nexus.com</span>
      </div>

      <button class="w-full bg-primary hover:bg-blue-700 font-semibold py-3 rounded-xl">
        Login
      </button>

      <p class="text-sm text-gray-400 text-center">
        No account?
        <a class="text-primary hover:text-blue-300" href="<?= BASE_URL ?>public/register.php">Register</a>
      </p>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
