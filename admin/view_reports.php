<?php
//______________________________________________//
// VIEW REPORTS
//______________________________________________//

require_once __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SESSION['user']['role'] !== 'admin') {
    die("Access Denied: Admins only.");
}

$search = $_GET['search'] ?? '';
$incident_type = $_GET['incident_type'] ?? '';
$severity = $_GET['severity'] ?? '';
$status = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$sql = "SELECT * FROM emergency_calls WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (caller_name LIKE ? OR location LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($incident_type)) {
    $sql .= " AND incident_type = ?";
    $params[] = $incident_type;
}

if (!empty($severity)) {
    $sql .= " AND severity = ?";
    $params[] = $severity;
}

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

if (!empty($date_from) && !empty($date_to)) {
    $sql .= " AND DATE(created_at) BETWEEN ? AND ?";
    $params[] = $date_from;
    $params[] = $date_to;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Reports - Emergency Response System</title>
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
          <a href="view_reports.php" class="flex items-center p-2 bg-red-700 rounded">
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
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Incident Reports</h1>

    <form method="GET" class="bg-white p-4 shadow rounded-lg mb-6 grid grid-cols-1 md:grid-cols-1 gap-4">
      <input type="text" name="search" placeholder="Search Caller or Location" value="<?= htmlspecialchars($search) ?>" class="p-2 border rounded">
      
      <select name="incident_type" class="p-2 border rounded">
        <option value="">Incident Type</option>
        <option value="Fire" <?= $incident_type == 'Fire' ? 'selected' : '' ?>>Fire</option>
        <option value="Medical" <?= $incident_type == 'Medical' ? 'selected' : '' ?>>Medical</option>
        <option value="Crime" <?= $incident_type == 'Crime' ? 'selected' : '' ?>>Crime</option>
      </select>

      <select name="severity" class="p-2 border rounded">
        <option value="">Severity</option>
        <option value="Low" <?= $severity == 'Low' ? 'selected' : '' ?>>Low</option>
        <option value="Medium" <?= $severity == 'Medium' ? 'selected' : '' ?>>Medium</option>
        <option value="High" <?= $severity == 'High' ? 'selected' : '' ?>>High</option>
        <option value="Critical" <?= $severity == 'Critical' ? 'selected' : '' ?>>Critical</option>
      </select>

      <select name="status" class="p-2 border rounded">
        <option value="">Status</option>
        <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="Ongoing" <?= $status == 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
        <option value="Completed" <?= $status == 'Completed' ? 'selected' : '' ?>>Completed</option>
      </select>

      <button type="submit" class="col-span-1 md:col-span-5 bg-red-800 text-white p-2 rounded hover:bg-red-900">Filter</button>
    </form>

    <!---============================== REPORTS TABLE ==============================--->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full text-left">
        <thead>
          <tr class="bg-gray-200 text-gray-700">
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Caller</th>
            <th class="px-4 py-2">Incident Type</th>
            <th class="px-4 py-2">Location</th>
            <th class="px-4 py-2">Severity</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($reports)): ?>
            <?php foreach ($reports as $report): ?>
            <tr class="border-t">
              <td class="px-4 py-2"><?= htmlspecialchars($report['id']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($report['caller_name']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($report['incident_type']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($report['location']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($report['severity']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($report['status']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($report['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-4 text-gray-500">No reports found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
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
