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

//______________________________________________//
// FADE IN ON FIRST LOAD
//______________________________________________//
$showFadeIn = !isset($_SESSION['fade_shown_main']);
$_SESSION['fade_shown_main'] = true;

$user_id = $_SESSION['user']['id'];

//______________________________________________//
// FETCH USER INFO
//______________________________________________//
$stmt = $conn->prepare("SELECT username, role, profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

//______________________________________________//
// IF NO USER FOUND
//______________________________________________//
if (!$user) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

//______________________________________________//
// HANDLE PROFILE IMAGE
//______________________________________________//
$profile_image = $user['profile_image'] ?? 'blank.png';
$profile_image_path = "profile_images/" . $profile_image;

if (!file_exists(__DIR__ . "/profile_images/" . $profile_image)) {
    $profile_image_path = "profile_images/blank.png";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LGU4 Dashboard - Admin</title>
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
  <aside class="w-64 bg-gradient-to-b from-stone-950 to-green-800 text-white flex flex-col h-screen">
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

    <!---============================== Navigation ==============================--->
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
          <a href="settings.php" class="flex items-center p-2 hover:bg-red-800 rounded">
            <i class='bx bx-cog text-xl'></i>
            <span class="ml-2">Settings</span>
          </a>
        </li>
      </ul>
    </nav>

    <!---============================== Logout ==============================--->
    <div class="p-4 border-t border-red-800">
      <a href="../config/logout.php" class="flex items-center p-2 hover:bg-red-800 rounded">
        <i class='bx bx-log-out text-xl'></i>
        <span class="ml-2">Logout</span>
      </a>
    </div>
  </aside>

  <!---============================== Main Content ==============================--->
  <main class="flex-1 p-6 overflow-y-auto">
    
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

      <!---============================== Total Users ==============================--->
      <div class="bg-white p-4 shadow rounded-lg">
        <h2 class="text-gray-600 text-sm">Total Users</h2>
        <?php
          $stmt = $conn->query("SELECT COUNT(*) FROM users");
          $total_users = $stmt->fetchColumn();
        ?>
        <p class="text-2xl font-bold"><?= $total_users ?></p>
      </div>

      <!---============================== Total Emergency Calls ==============================--->
      <div class="bg-white p-4 shadow rounded-lg">
        <h2 class="text-gray-600 text-sm">Total Emergency Calls</h2>
        <?php
          $stmt = $conn->query("SELECT COUNT(*) FROM emergency_calls");
          $total_calls = $stmt->fetchColumn();
        ?>
        <p class="text-2xl font-bold"><?= $total_calls ?></p>
      </div>

      <!---============================== Total Resources ==============================--->
      <div class="bg-white p-4 shadow rounded-lg">
        <h2 class="text-gray-600 text-sm">Total Resources</h2>
        <?php
          $stmt = $conn->query("SELECT COUNT(*) FROM resources");
          $total_resources = $stmt->fetchColumn();
        ?>
        <p class="text-2xl font-bold"><?= $total_resources ?></p>
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
