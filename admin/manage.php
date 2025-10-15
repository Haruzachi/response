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
// FETCH LOGGED-IN USER DETAILS
//______________________________________________//
$stmt = $conn->prepare("
    SELECT username, role, profile_image 
    FROM users 
    WHERE id = ?
");
$stmt->execute([$user_id]);
$logged_in_user = $stmt->fetch(PDO::FETCH_ASSOC);

//______________________________________________//
// IF NO USER FOUND
//______________________________________________//
if (!$logged_in_user) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

//______________________________________________//
// HANDLE PROFILE IMAGE PATH
//______________________________________________//
$profile_image = !empty($logged_in_user['profile_image']) ? $logged_in_user['profile_image'] : 'blank.png';
$profile_image_path = "profile_images/" . $profile_image;

if (!file_exists(__DIR__ . "/profile_images/" . $profile_image)) {
    $profile_image_path = "profile_images/blank.png";
}

//______________________________________________//
// FETCH ALL USERS FROM DATABASE
//______________________________________________//
try {
    $stmt = $conn->query("
        SELECT id, username, role, profile_image, created_at
        FROM users
        ORDER BY id DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users</title>
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
          <a href="dashboard.php" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bxs-dashboard text-xl'></i>
            <span class="ml-2">Dashboard</span>
          </a>
        </li>

        <li>
          <a href="#" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bx-phone text-xl'></i>
            <span class="ml-2">Receiving & Logging</span>
          </a>
        </li>

        <li>
          <a href="#" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bx-map-pin text-xl'></i>
            <span class="ml-2">Incident Prioritization</span>
          </a>
        </li>

        <li>
          <a href="manage_resources.php" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bx-car text-xl'></i>
            <span class="ml-2">Manage Resources</span>
          </a>
        </li>

        <li>
          <a href="#" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bx-compass text-xl'></i>
            <span class="ml-2">Tracking of Responders</span>
          </a>
        </li>

        <li>
          <a href="view_reports.php" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bx-bar-chart text-xl'></i>
            <span class="ml-2">Reports</span>
          </a>
        </li>

        <li>
          <a href="#" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bx-network-chart text-xl'></i>
            <span class="ml-2">Coordination Portal</span>
          </a>
        </li>

        <li>
          <a href="#" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bx-chat text-xl'></i>
            <span class="ml-2">Review & Feedback</span>
          </a>
        </li>
        
        <li>
          <a href="manage.php" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bx-user text-xl'></i>
            <span class="ml-2">Manage Users</span>
          </a>
        </li>

        <li>
          <a href="settings.php" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bx-cog text-xl'></i>
            <span class="ml-2">Settings</span>
          </a>
        </li>
      </ul>
    </nav>

    <!---============================== Logout ==============================--->
    <div class="p-4 border-t border-green-800">
      <a href="../config/logout.php" class="flex items-center p-2 hover:bg-blue-800 rounded">
        <i class='bx bx-log-out text-xl'></i>
        <span class="ml-2">Logout</span>
      </a>
    </div>
  </aside>

    <!---============================== MAIN CONTENT ==============================--->
    <main class="flex-1 p-6">
      <h1 class="text-2xl font-bold mb-4">Manage Users</h1>

      <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full text-left">
          <thead>
            <tr class="bg-gray-200 text-gray-700">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Username</th>
              <th class="px-4 py-2">Role</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($users)): ?>
              <?php foreach ($users as $user): ?>
              <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2"><?= htmlspecialchars($user['id']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($user['username']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($user['role']) ?></td>
                <td class="px-4 py-2 text-center">
                  <a href="./data/delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure?')" class="text-red-500 hover:underline">Delete</a>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center py-4 text-gray-500">No users found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
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
