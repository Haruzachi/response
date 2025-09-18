<?php
require_once __DIR__ . '/../config/db.php';
session_start();

//______________________________________________//
// SESSION CHECK
//______________________________________________//
if (!isset($_SESSION['user']['id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SESSION['user']['role'] !== 'admin') {
    die("Access Denied: Admins only.");
}

//______________________________________________//
// FETCH LOGGED-IN ADMIN DETAILS
//______________________________________________//
$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT username, profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("Admin account not found.");
}

//______________________________________________//
// UPDATE PROFILE (USERNAME & IMAGE)
//______________________________________________//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $profile_image = $admin['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = __DIR__ . "/profile_images/";
        $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $file_name;
        }
    }

    $update_stmt = $conn->prepare("UPDATE users SET username = ?, profile_image = ? WHERE id = ?");
    $update_stmt->execute([$username, $profile_image, $user_id]);

    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: settings.php");
    exit;
}

//______________________________________________//
// CHANGE PASSWORD FUNCTIONALITY
//______________________________________________//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = "Account not found.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match.";
    } elseif ($user['password'] !== $current_password) {
        $_SESSION['error'] = "Current password is incorrect.";
    } else {
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->execute([$new_password, $user_id]);
        $_SESSION['success'] = "Password changed successfully!";
    }

    header("Location: settings.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Settings - Emergency Response System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<style>
  /* Responsive adjustments */
@media (max-width: 768px) {
  aside {
    position: fixed;
    left: -100%;
    top: 0;
    height: 100%;
    z-index: 50;
    transition: left 0.3s ease;
  }
  aside.active {
    left: 0;
  }
  main {
    margin-left: 0 !important;
  }
  #sidebarOverlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 40;
  }
  #sidebarOverlay.active {
    display: block;
  }
}
</style>
<body class="flex bg-gray-100 h-screen">
  
<!-- Top Bar (Mobile) -->
<header class="md:hidden flex items-center justify-between bg-stone-950 text-white p-3 fixed top-0 left-0 right-0 z-50 shadow">
  <!-- Hamburger menu -->
  <button id="menuBtn" class="text-white text-2xl focus:outline-none">
    <i class='bx bx-menu'></i>
  </button>

</header>

  <!---============================== SIDE BAR ==============================--->
  <aside class="w-64 bg-gradient-to-b from-stone-950 to-red-800 text-white flex flex-col h-screen">
    <div class="p-4 flex flex-col items-start space-y-2">
      <div class="flex items-center space-x-3">
        <img src="../img/Logo.png" alt="Logo" class="w-10 h-10 rounded-full">
        <div>
          <span class="font-bold text-lg">Admin Panel</span>
          <span class="block text-xs text-gray-300">Emergency Response</span>
        </div>
      </div>
      <div class="w-full border-b border-white/50 mt-2"></div>
    </div>

    <!---============================== NAVIGATION ==============================--->
    <nav class="flex-1 px-2 overflow-y-auto">
      <p class="mt-2 text-gray-300 uppercase text-xs px-2">Management</p>
      <ul class="space-y-1 mt-1">
        <li>
          <a href="dashboard.php" class="flex items-center p-2 hover:bg-red-800 rounded">
            <i class='bx bxs-dashboard text-xl'></i>
            <span class="ml-2">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="manage.php" class="flex items-center p-2 hover:bg-red-800 rounded">
            <i class='bx bx-user text-xl'></i>
            <span class="ml-2">Manage Users</span>
          </a>
        </li>
        <li>
          <a href="manage_resources.php" class="flex items-center p-2 hover:bg-red-800 rounded">
            <i class='bx bx-car text-xl'></i>
            <span class="ml-2">Manage Resources</span>
          </a>
        </li>
        <li>
          <a href="view_reports.php" class="flex items-center p-2 hover:bg-red-800 rounded">
            <i class='bx bx-bar-chart text-xl'></i>
            <span class="ml-2">Reports</span>
          </a>
        </li>
        <li>
          <a href="settings.php" class="flex items-center p-2 bg-red-700 rounded">
            <i class='bx bx-cog text-xl'></i>
            <span class="ml-2">Settings</span>
          </a>
        </li>
      </ul>
    </nav>

    <!---============================== LOG OUT ==============================--->
    <div class="p-4 border-t border-red-800">
      <a href="../config/logout.php" class="flex items-center p-2 hover:bg-red-800 rounded">
        <i class='bx bx-log-out text-xl'></i>
        <span class="ml-2">Logout</span>
      </a>
    </div>
  </aside>

  <!---============================== MAIN CONTENT ==============================--->
  <main class="flex-1 p-6 overflow-y-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Settings</h1>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!---============================== PROFILE SETTINGS ==============================--->
      <div class="bg-white p-4 shadow rounded-lg">
        <h2 class="text-lg font-bold mb-4">Profile Settings</h2>
        <form method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" class="w-full p-2 border rounded" required>
          </div>

          <!--<div class="mb-3">
            <label class="block text-sm font-medium mb-1">Profile Image</label>
            <input type="file" name="profile_image" class="w-full p-2 border rounded">
            <?php if (!empty($admin['profile_image'])): ?>
              <img src="profile_images/<?= htmlspecialchars($admin['profile_image']) ?>" alt="Profile Image" class="w-16 h-16 mt-2 rounded-full">
            <?php endif; ?>
          </div>
          <button type="submit" name="update_profile" class="bg-red-800 text-white px-4 py-2 rounded hover:bg-red-900">Update Profile</button>-->
        </form>
      </div>

      <!---============================== CHANGE PASSWORD ==============================--->
      <div class="bg-white p-4 shadow rounded-lg">
        <h2 class="text-lg font-bold mb-4">Change Password</h2>
        <form method="POST">
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Current Password</label>
            <input type="password" name="current_password" class="w-full p-2 border rounded" required>
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">New Password</label>
            <input type="password" name="new_password" class="w-full p-2 border rounded" required>
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Confirm Password</label>
            <input type="password" name="confirm_password" class="w-full p-2 border rounded" required>
          </div>
          <button type="submit" name="change_password" class="bg-red-800 text-white px-4 py-2 rounded hover:bg-red-900">Change Password</button>
        </form>
      </div>

    </div>
  </main>
  <script>
  // Mobile sidebar toggle
const menuBtn = document.getElementById('menuBtn');
const sidebar = document.querySelector('aside');
const overlay = document.createElement('div');
overlay.id = 'sidebarOverlay';
document.body.appendChild(overlay);

menuBtn.addEventListener('click', () => {
  sidebar.classList.toggle('active');
  overlay.classList.toggle('active');
});

overlay.addEventListener('click', () => {
  sidebar.classList.remove('active');
  overlay.classList.remove('active');
});
</script>
</body>
</html>
