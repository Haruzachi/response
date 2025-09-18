<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

$pageTitle = "Feedback Forms for Responders";

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT username, role, profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

$profile_image = $user['profile_image'] ?? 'default.png';

$image_path = __DIR__ . "/../profile_images/" . $profile_image;
if (!file_exists($image_path)) {
    $profile_image = 'default.png';
}
?>

<?php

$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['feedback'])) {
    $feedback = trim($_POST['feedback']);
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, feedback) VALUES (NULL, ?)");
    $stmt->execute([$feedback]);
    header("Location: feedback.php");
    exit;
}

// Fetch all feedback (no JOIN, since user_id is removed)
$query = $conn->prepare("
    SELECT id, feedback, created_at
    FROM feedback
    ORDER BY created_at DESC
");
$query->execute();
$feedbacks = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback Forms for Responders</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="../../image/x-icon" href="../../img/Logocircle.png">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<style>
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
<body class="flex bg-gray-100 h-screen font-sans">

  <!---============================== SIDE BAR ==============================--->
  <aside class="w-64 bg-gradient-to-b from-stone-950 to-blue-800 text-white flex flex-col h-screen">
    <!-- Brand -->
    <div class="p-4 flex flex-col items-start space-y-2">
      <div class="flex flex-col">
        <div class="flex items-center space-x-3">
          <img src="../../img/Logo.png" alt="Logo" class="w-10 h-10 rounded-full cursor-pointer" id="sidebarLogo">
          <div class="flex flex-col">
            <span class="font-bold text-lg">Quezon City</span>
            <span class="text-xs text-gray-300">Emergency Response System</span>
          </div>
        </div>
      </div>
      <!-- Separator line -->
      <div class="w-full border-b border-white/50"></div>
    </div>

    <!---============================== LOGO ==============================--->
    <div id="logoModal" class="fixed inset-0 bg-black bg-opacity-70 hidden justify-center items-center z-50">
      <span class="absolute top-4 right-6 text-white text-2xl cursor-pointer" id="closeLogoModal">&times;</span>
      <img id="logoModalImage" src="../../img/Logo.png" class="max-h-[80%] max-w-[80%] rounded shadow-lg">
    </div>

    <!---============================== NAVIGATION ==============================--->
    <nav class="flex-1 overflow-hidden px-2">
      <!---============================== MAIN DASHBOARD ==============================--->
      <p class="mt-2 text-gray-300 uppercase text-xs px-2">Main Dashboard</p>
      <ul class="space-y-1 mt-1">
        <li>
          <a href="../dashboard.php" class="flex items-center p-2 hover:bg-blue-800 rounded transition-colors duration-200">
            <i class='bx bxs-dashboard text-xl'></i>
            <span class="ml-2">Dashboard</span>
          </a>
        </li>
      </ul>

      <!---============================== MAIN MODULES ==============================--->
      <p class="mt-6 text-gray-300 uppercase text-xs px-2">Main Modules</p>
      <ul class="space-y-1 mt-1">
        <!-- 1. Receiving and Logging -->
        <li>
          <button class="flex items-center w-full p-2 hover:bg-blue-800 rounded justify-between module-btn transition-colors duration-200" data-target="module1">
            <span class="flex items-center"><i class='bx bx-bell text-xl'></i><span class="ml-2">Receiving and Logging</span></span>
            <i class='bx bx-chevron-down transition-transform duration-300'></i>
          </button>
          <ul class="max-h-0 overflow-hidden flex-col transition-all duration-500 ml-4 submodule" id="module1">
            <li><a href="../Sub1/logging.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-user'></i><span class="ml-1">Caller Information</span></a></li>
          </ul>
        </li>

        <!-- 2. Prioritization and Dispatch -->
        <li>
          <button class="flex items-center w-full p-2 hover:bg-blue-800 rounded justify-between module-btn transition-colors duration-200" data-target="module2">
            <span class="flex items-center"><i class='bx bx-map-pin text-xl'></i><span class="ml-2">Prioritization & Dispatch</span></span>
            <i class='bx bx-chevron-down transition-transform duration-300'></i>
          </button>
          <ul class="max-h-0 overflow-hidden flex-col transition-all duration-500 ml-4 submodule" id="module2">
            <li><a href="../Sub2/severity.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-time-five'></i><span class="ml-1">Severity assessment tool</span></a></li>
            <li><a href="../Sub2/dispatch.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-directions'></i><span class="ml-1">Dispatch assignment interface</span></a></li>
            <li><a href="../Sub2/monitoring.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-street-view'></i><span class="ml-1">Monitoring of incidents</span></a></li>
          </ul>
        </li>

        <!-- 3. Resource Allocation -->
        <li>
          <button class="flex items-center w-full p-2 hover:bg-blue-800 rounded justify-between module-btn transition-colors duration-200" data-target="module3">
            <span class="flex items-center"><i class='bx bx-first-aid text-xl'></i><span class="ml-2">Resource Allocation</span></span>
            <i class='bx bx-chevron-down transition-transform duration-300'></i>
          </button>
          <ul class="max-h-0 overflow-hidden flex-col transition-all duration-500 ml-4 submodule" id="module3">
            <li><a href="../Sub3/availables.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-car'></i><span class="ml-1">Availables</span></a></li>
            <li><a href="../Sub3/override.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-refresh'></i><span class="ml-1">Manual override for dispatchers</span></a></li>
            <li><a href="../Sub3/reports.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-bar-chart'></i><span class="ml-1">Reports on resource utilization</span></a></li>
          </ul>
        </li>

        <!-- 4. Real-Time GPS Tracking -->
        <li>
          <button class="flex items-center w-full p-2 hover:bg-blue-800 rounded justify-between module-btn transition-colors duration-200" data-target="module4">
            <span class="flex items-center"><i class='bx bx-map text-xl'></i><span class="ml-2">Tracking of Responders</span></span>
            <i class='bx bx-chevron-down transition-transform duration-300'></i>
          </button>
          <ul class="max-h-0 overflow-hidden flex-col transition-all duration-500 ml-4 submodule" id="module4">
            <li><a href="../Sub4/mapview.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-map-alt'></i><span class="ml-1">Map view of ongoing incidents</span></a></li>
            <li><a href="../Sub4/alerts.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-shield'></i><span class="ml-1">Geofence alerts for zones</span></a></li>
          </ul>
        </li>

        <!-- 5. Response Time Analytics -->
        <li>
          <button class="flex items-center w-full p-2 hover:bg-blue-800 rounded justify-between module-btn transition-colors duration-200" data-target="module5">
            <span class="flex items-center"><i class='bx bx-timer text-xl'></i><span class="ml-2">Response Time Analytics</span></span>
            <i class='bx bx-chevron-down transition-transform duration-300'></i>
          </button>
          <ul class="max-h-0 overflow-hidden flex-col transition-all duration-500 ml-4 submodule" id="module5">
            <li><a href="../Sub5/reports.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-bar-chart-alt'></i><span class="ml-1">Daily, weekly, monthly reports</span></a></li>
            <li><a href="../Sub5/heatmaps.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-map'></i><span class="ml-1">Heatmaps identifying delays</span></a></li>
          </ul>
        </li>

        <!-- 6. Inter-agency Coordination Portal -->
        <li>
          <button class="flex items-center w-full p-2 hover:bg-blue-800 rounded justify-between module-btn transition-colors duration-200" data-target="module6">
            <span class="flex items-center"><i class='bx bx-network-chart text-xl'></i><span class="ml-2">Coordination Portal</span></span>
            <i class='bx bx-chevron-down transition-transform duration-300'></i>
          </button>
          <ul class="max-h-0 overflow-hidden flex-col transition-all duration-500 ml-4 submodule" id="module6">
            <li><a href="../Sub6/coms.php" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-chat'></i><span class="ml-1">Shared communication</span></a></li>
          </ul>
        </li>

        <!-- 7. Review and Feedback Mechanism -->
        <li>
          <button class="flex items-center w-full p-2 hover:bg-blue-800 rounded justify-between module-btn transition-colors duration-200" data-target="module7">
            <span class="flex items-center"><i class='bx bx-chat text-xl'></i><span class="ml-2">Review & Feedback</span></span>
            <i class='bx bx-chevron-down transition-transform duration-300'></i>
          </button>
          <ul class="max-h-0 overflow-hidden flex-col transition-all duration-500 ml-4 submodule" id="module7">
            <li><a href="#" class="flex items-center text-sm p-1 hover:text-blue-200"><i class='bx bx-edit'></i><span class="ml-1">Feedback forms for responders</span></a></li>
          </ul>
        </li>
      </ul>
    </nav>

    <!-- Settings at Bottom -->
    <div class="p-4 border-t border-blue-800">
      <ul class="space-y-1">
        <li>
          <a href="Profile.html" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bxs-user-pin text-xl'></i><span class="ml-2">Profile</span>
          </a>
        </li>
        <li>
          <a href="Settings.html" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bxs-cog text-xl'></i><span class="ml-2">Settings</span>
          </a>
        </li>
        <li>
          <a href="../../config/logout.php" class="flex items-center p-2 hover:bg-blue-800 rounded">
            <i class='bx bxs-log-out-circle text-xl'></i><span class="ml-2">Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <!---============================== MAIN CONTENT ==============================--->
  <main class="flex-1 flex flex-col overflow-auto">


    <!---============================== TOP BAR ==============================--->
<div class="bg-gradient-to-r from-stone-950 to-blue-800 text-white shadow flex justify-between items-center px-6 py-3">
  <div class="flex items-center space-x-4">
    <!-- Add menu button in top bar -->
<button id="menuBtn" class="md:hidden p-2 text-white bg-blue-950 rounded">
  <i class='bx bx-menu text-2xl'></i>
</button>

    <!-- Dynamic Page Title -->
    <h1 class="font-bold text-base sm:text-lg md:text-xl lg:text-2xl text-white flex-1 truncate">
      --// <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard'; ?> //--
    </h1>
  </div>

  <div class="flex items-center space-x-4">
    <!---============================== SEARCH BAR ==============================--->
    <form class="hidden md:flex">
      <input 
        type="search" 
        placeholder="Search..." 
        class="px-2 py-1 rounded-l border border-gray-300 focus:outline-none focus:ring focus:ring-blue-500">
      <button 
        type="submit" 
        class="bg-blue-700 px-3 py-1 rounded-r text-white hover:bg-blue-800">
        <i class='bx bx-search'></i>
      </button>
    </form>

    <!---============================== AI ASSISTANT ==============================--->
<div class="relative">
  <button id="ai-btn" class="relative">
    <i class='bx bx-brain text-2xl'></i>
  </button>
</div>

    <!---============================== USER PROFILE ==============================--->
    <div class="relative">
      <img 
        src="../profile_images/<?php echo $profile_image; ?>?<?php echo time(); ?>"  
        id="profile-btn" 
        class="w-10 h-10 rounded-full cursor-pointer border border-gray-300">
      <div id="dropdown-menu" class="hidden absolute right-0 mt-2 w-40 bg-white text-black shadow-lg rounded"></div>
    </div>
  </div>
</div>

<!---============================== DASHBOARD ==============================--->


<div class="max-w-4xl mx-auto p-6">

  <!--  
  <form action="" method="POST" class="mb-6 bg-white p-4 rounded shadow">
    <textarea name="feedback" rows="3" required
      placeholder="Write your feedback here..." 
      class="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500"></textarea>
    <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      Submit Feedback
    </button>
  </form> -->

  <!-- Feedback List -->
  <?php if (empty($feedbacks)): ?>
    <div class="text-gray-500 text-center">No feedback submitted yet.</div>
<?php else: ?>
    <?php foreach ($feedbacks as $fb): ?>
        <div class="p-3 border-l-4 border-blue-600 bg-gray-50 rounded shadow-sm">
            <div class="flex justify-between text-sm text-gray-500 mb-1">
                <span>Anonymous</span> 
                <span><?= date('M d, H:i', strtotime($fb['created_at'])) ?></span>
            </div>
            <p class="text-gray-700"><?= htmlspecialchars($fb['feedback']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div>



  </main>
  <!---============================== MODALS ==============================--->
<!-- AI Chat Modal -->
<div id="aiModal" class="fixed bottom-16 right-6 w-80 bg-white shadow-2xl rounded-xl hidden flex-col z-50">
  <!-- Header -->
  <div class="bg-blue-800 text-white px-4 py-2 flex justify-between items-center rounded-t-xl">
    <span>AI Assistant</span>
    <button id="closeAiModal" class="text-white text-xl">&times;</button>
  </div>

  <!-- Chat Content -->
  <div id="aiChatContent" class="p-3 h-64 overflow-y-auto flex flex-col gap-2 bg-gray-50"></div>

  <!-- Input -->
  <div class="flex p-3 border-t border-gray-300">
    <input id="aiInput" type="text" placeholder="Type a message..." class="flex-1 px-2 py-1 border rounded-l focus:outline-none focus:ring focus:ring-blue-500">
    <button id="aiSend" class="bg-blue-600 text-white px-3 py-1 rounded-r hover:bg-blue-700 transition">Send</button>
  </div>
</div>

  <!-- Profile Modal -->
<div id="profileModal" class="fixed inset-0 bg-black bg-opacity-70 hidden justify-center items-center z-50">
  <div class="bg-white rounded-2xl w-[700px] p-0 relative shadow-2xl overflow-hidden">

    <!-- Modal Header -->
    <div class="bg-gradient-to-r from-stone-950 to-blue-800 text-white px-6 py-4 flex justify-between items-center">
      <h2 class="text-xl font-semibold">--// Your Profile //--</h2>
      <button id="closeProfileModal" class="text-white hover:text-gray-200 text-2xl">&times;</button>
    </div>

    <div class="flex">
      <!-- Left Side: User Information -->
      <div class="w-1/2 p-6 border-r border-gray-200">
        <h3 class="text-lg font-bold mb-6 text-gray-800 text-center">Account Information</h3>

        <div class="space-y-5">
          <div>
            <label class="block text-gray-600 font-semibold">Username</label>
            <p class="mt-1 text-gray-900 bg-gray-100 rounded px-3 py-2 shadow-inner">
              <?php echo htmlspecialchars($user['username']); ?>
            </p>
          </div>
          
          <div>
            <label class="block text-gray-600 font-semibold">Role</label>
            <p class="mt-1 text-gray-900 bg-gray-100 rounded px-3 py-2 shadow-inner capitalize">
              <?php echo htmlspecialchars($user['role']); ?>
            </p>
          </div>
        </div>
      </div>

      <!-- Right Side: Profile Picture & Upload -->
      <div class="w-1/2 p-6 flex flex-col items-center">
        <h3 class="text-lg font-bold mb-4 text-gray-800 text-center">Profile Picture</h3>
        
        <!-- Current Profile Picture -->
        <div class="relative">
          <img 
            id="profilePreview" 
            src="../profile_images/<?php echo $profile_image; ?>?<?php echo time(); ?>" 
            alt="Profile Image" 
            class="w-32 h-32 rounded-full border-4 border-gray-200 shadow-md object-cover"
          />
          <div class="absolute inset-0 rounded-full bg-black bg-opacity-30 opacity-0 hover:opacity-100 flex justify-center items-center cursor-pointer transition">
            <span class="text-white text-sm">Change</span>
          </div>
        </div>

        <!-- Upload Form -->
        <form action="../../data/upload_profile.php" method="POST" enctype="multipart/form-data" class="mt-6 w-full">
          <div class="space-y-3">
            <div>
              <label class="block text-gray-700 font-medium mb-1">Upload New Image</label>
              <input 
                type="file" 
                name="profile_image" 
                accept="image/*" 
                id="profileInput" 
                class="w-full text-sm text-gray-600 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              >
            </div>

            <button 
              type="submit" 
              class="w-full bg-blue-600 text-white py-2 rounded-lg shadow hover:bg-blue-700 transition duration-200"
            >
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  <script>
    // Sidebar accordion
    let openModule = null;
    document.querySelectorAll('.module-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const target = document.getElementById(btn.dataset.target);
        const icon = btn.querySelector('.bx-chevron-down');

        if (openModule && openModule !== target) {
          openModule.style.maxHeight = null;
          openModule.previousElementSibling.querySelector('.bx-chevron-down').style.transform = 'rotate(0deg)';
        }

        if (target.style.maxHeight) {
          target.style.maxHeight = null;
          icon.style.transform = 'rotate(0deg)';
          openModule = null;
        } else {
          target.style.maxHeight = target.scrollHeight + "px";
          icon.style.transform = 'rotate(180deg)';
          openModule = target;
        }
      });
    });

    // Logo modal
    const sidebarLogo = document.getElementById('sidebarLogo');
    const logoModal = document.getElementById('logoModal');
    const closeLogoModal = document.getElementById('closeLogoModal');

    sidebarLogo.addEventListener('click', () => {
      logoModal.classList.remove('hidden');
      logoModal.classList.add('flex');
    });

    closeLogoModal.addEventListener('click', () => {
      logoModal.classList.remove('flex');
      logoModal.classList.add('hidden');
    });

    logoModal.addEventListener('click', (e) => {
      if (e.target === logoModal) {
        logoModal.classList.remove('flex');
        logoModal.classList.add('hidden');
      }
    });
  </script>

  <script>
document.addEventListener('DOMContentLoaded', () => {
  const profileModal = document.getElementById('profileModal');
  const closeModal = document.getElementById('closeProfileModal');

  // Open modal when clicking "Profile" in sidebar
  document.querySelector('a[href="Profile.html"]').addEventListener('click', (e) => {
    e.preventDefault();
    profileModal.classList.remove('hidden');
    profileModal.classList.add('flex');
  });

  // Close modal
  closeModal.addEventListener('click', () => {
    profileModal.classList.add('hidden');
    profileModal.classList.remove('flex');
  });

  // Close modal when clicking outside
  profileModal.addEventListener('click', (e) => {
    if (e.target === profileModal) {
      profileModal.classList.add('hidden');
      profileModal.classList.remove('flex');
    }
  });
});
</script>

<script>
const aiBtn = document.getElementById('ai-btn');
const aiModal = document.getElementById('aiModal');
const closeAiModal = document.getElementById('closeAiModal');
const aiChatContent = document.getElementById('aiChatContent');
const aiInput = document.getElementById('aiInput');
const aiSend = document.getElementById('aiSend');

aiBtn.addEventListener('click', () => aiModal.classList.toggle('hidden'));
closeAiModal.addEventListener('click', () => aiModal.classList.add('hidden'));

// Append messages
function appendMessage(sender, text) {
  const div = document.createElement('div');
  div.textContent = text;
  div.className = sender === 'user' ? 
    'self-end bg-blue-500 text-white px-3 py-1 rounded-xl max-w-xs' : 
    'self-start bg-gray-200 text-gray-800 px-3 py-1 rounded-xl max-w-xs';
  aiChatContent.appendChild(div);
  aiChatContent.scrollTop = aiChatContent.scrollHeight;
}

// Large knowledge base
const knowledgeBase = [
  {q: "hello", a: "Hello! How are you today?"},
  {q: "hi", a: "Hi there! How can I help you?"},
  {q: "how are you", a: "I'm just a JS AI, but I'm doing great!"},
  {q: "what is your name", a: "I am your JavaScript AI assistant!"},
  {q: "what is javascript", a: "JavaScript is a programming language used for web development."},
  {q: "what is php", a: "PHP is a server-side scripting language for building dynamic websites."},
  {q: "what is html", a: "HTML stands for HyperText Markup Language. It structures web pages."},
  {q: "what is css", a: "CSS is used to style HTML elements on web pages."},
  {q: "who are you", a: "I am an AI created entirely with JavaScript!"},
  {q: "what can you do", a: "I can chat with you, answer predefined questions, and pretend to be smart!"},

    // Geography
  {q:"earth", a:"Earth is the third planet from the Sun and the only known planet to support life."},
  {q:"moon", a:"The Moon is Earth's natural satellite."},
  {q:"sun", a:"The Sun is the star at the center of our solar system."},
  {q:"mars", a:"Mars is the fourth planet from the Sun, also called the Red Planet."},
  {q:"mount everest", a:"Mount Everest is the Earth's highest mountain above sea level, located in the Himalayas."},
  {q:"amazon river", a:"The Amazon River is the largest river by discharge volume of water in the world."},
  {q:"sahara", a:"The Sahara is the largest hot desert in the world, located in North Africa."},
  {q:"pacific ocean", a:"The Pacific Ocean is the largest and deepest ocean on Earth."},

  // Earth Science
  {q:"water cycle", a:"The water cycle describes how water evaporates, condenses, and precipitates on Earth."},
  {q:"plate tectonics", a:"Plate tectonics explains the movement of Earth's lithospheric plates."},
  {q:"volcano", a:"A volcano is an opening in Earth's crust that allows molten rock and gases to escape."},
  {q:"earthquake", a:"An earthquake is the shaking of the Earth's surface caused by seismic waves."},
  {q:"gravity", a:"Gravity is a force that attracts objects toward the center of the Earth or any other mass."},

  // Physics
  {q:"light", a:"Light is electromagnetic radiation visible to the human eye."},
  {q:"electricity", a:"Electricity is the flow of electric charge, usually through a conductor."},
  {q:"magnetism", a:"Magnetism is a force produced by moving electric charges or intrinsic magnetic moments."},
  {q:"energy", a:"Energy is the capacity to do work."},
  {q:"force", a:"A force is an interaction that changes the motion of an object."},

  // Chemistry
  {q:"water", a:"Water is a chemical substance composed of H2O molecules."},
  {q:"oxygen", a:"Oxygen is a chemical element with symbol O and atomic number 8."},
  {q:"carbon", a:"Carbon is a chemical element with symbol C and is the basis of all known life."},
  {q:"nitrogen", a:"Nitrogen is a chemical element with symbol N and makes up 78% of Earth's atmosphere."},
  {q:"hydrogen", a:"Hydrogen is the lightest element and the most abundant in the universe."},

  // Biology
  {q:"human body", a:"The human body is the complete structure of a human being, consisting of organs, tissues, and cells."},
  {q:"cell", a:"A cell is the basic structural and functional unit of all living organisms."},
  {q:"dna", a:"DNA is a molecule that carries the genetic instructions for life."},
  {q:"photosynthesis", a:"Photosynthesis is the process by which plants convert sunlight into chemical energy."},
  {q:"evolution", a:"Evolution is the process by which species change over time through natural selection."},

  // Astronomy
  {q:"planet", a:"A planet is a celestial body orbiting a star, massive enough to be rounded by gravity."},
  {q:"star", a:"A star is a luminous ball of gas held together by gravity, producing light and heat by nuclear fusion."},
  {q:"galaxy", a:"A galaxy is a massive system of stars, gas, dust, and dark matter."},
  {q:"black hole", a:"A black hole is a region of space where gravity is so strong that nothing can escape from it."},
  {q:"milky way", a:"The Milky Way is the galaxy that contains our Solar System."},

  // Technology
  {q:"computer", a:"A computer is a device that processes data and executes programs."},
  {q:"internet", a:"The Internet is a global network connecting millions of computers."},
  {q:"artificial intelligence", a:"Artificial Intelligence is the simulation of human intelligence in machines."},
  {q:"robot", a:"A robot is a machine capable of carrying out complex tasks automatically."},
  {q:"blockchain", a:"Blockchain is a decentralized ledger used to record transactions securely."},

  // Programming
  {q:"javascript", a:"JavaScript is a programming language used for web development."},
  {q:"python", a:"Python is a high-level programming language for general-purpose programming."},
  {q:"html", a:"HTML is a markup language used to structure web pages."},
  {q:"css", a:"CSS is a style sheet language used to style web pages."},
  {q:"react", a:"React is a JavaScript library for building user interfaces."},

  // History
  {q:"world war 1", a:"World War I was a global war from 1914 to 1918."},
  {q:"world war 2", a:"World War II was a global war from 1939 to 1945."},
  {q:"industrial revolution", a:"The Industrial Revolution was a period of major industrialization from the 18th to 19th centuries."},
  {q:"ancient egypt", a:"Ancient Egypt was a civilization of ancient North Africa, concentrated along the lower Nile River."},
  {q:"renaissance", a:"The Renaissance was a period of cultural, artistic, and scientific revival in Europe, starting in the 14th century."},

  // Culture & Society
  {q:"music", a:"Music is organized sound that expresses emotion and creativity."},
  {q:"art", a:"Art is the expression of human creativity through visual, auditory, or performance forms."},
  {q:"language", a:"Language is a system of communication using words or symbols."},
  {q:"philosophy", a:"Philosophy is the study of fundamental questions about existence, knowledge, and ethics."},
  {q:"economics", a:"Economics is the study of production, distribution, and consumption of goods and services."},

  // Health
  {q:"vaccine", a:"A vaccine stimulates the immune system to protect against disease."},
  {q:"covid", a:"COVID-19 is a respiratory disease caused by the coronavirus SARS-CoV-2."},
  {q:"nutrition", a:"Nutrition is the process of consuming and utilizing food for health and growth."},
  {q:"exercise", a:"Exercise is physical activity that improves health and fitness."},
  {q:"mental health", a:"Mental health is the state of well-being in which a person can cope with stress and function effectively."},

  // Fun / Misc
  {q:"joke", a:"Why did the computer go to the doctor? Because it caught a virus!"},
  {q:"fact", a:"Did you know? Honey never spoils, archaeologists found edible honey in ancient tombs!"},
  {q:"weather", a:"Weather describes the atmospheric conditions at a specific time and place."},
  {q:"rainbow", a:"A rainbow is a spectrum of light appearing in the sky when sunlight shines through rain droplets."},
  {q:"ocean", a:"Oceans cover about 71% of Earth's surface and contain most of the planet's water."},

  {q: "hello", a: "Hello! How are you today?"},
  {q: "hi", a: "Hi there! How can I help you?"},
  {q: "how are you", a: "I'm just a JS AI, but I'm doing great!"},
  {q: "what is your name", a: "I am your JavaScript AI assistant!"},
  {q: "who are you", a: "I am an AI created entirely with JavaScript!"},
  {q: "what can you do", a: "I can chat with you, answer predefined questions, and simulate intelligence!"},
  {q: "what is javascript", a: "JavaScript is a programming language used for web development."},
  {q: "what is php", a: "PHP is a server-side scripting language for building dynamic websites."},
  {q: "what is html", a: "HTML stands for HyperText Markup Language. It structures web pages."},
  {q: "what is css", a: "CSS is used to style HTML elements on web pages."},
  {q: "what is sql", a: "SQL is a language used to manage and query databases."},
  {q: "what is mysql", a: "MySQL is an open-source relational database management system."},
  {q: "what is json", a: "JSON is a lightweight format for exchanging data between client and server."},
  {q: "what is api", a: "API stands for Application Programming Interface, which allows communication between software."},
  {q: "what is rest api", a: "REST API is an API that uses HTTP requests to access and manipulate data."},
  {q: "what is backend", a: "Backend refers to the server-side of a web application, handling logic and database interactions."},
  {q: "what is frontend", a: "Frontend is the client-side part of a web app, handling UI and user interactions."},
  {q: "what is programming", a: "Programming is the process of writing instructions for computers to perform tasks."},
  {q: "what is coding", a: "Coding is another word for writing computer programs."},
  {q: "what is git", a: "Git is a version control system used to track changes in code."},
  {q: "what is github", a: "GitHub is a platform for hosting and collaborating on Git repositories."},
  {q: "what is nodejs", a: "Node.js is a runtime environment that allows running JavaScript on the server."},
  {q: "what is npm", a: "NPM is the Node Package Manager, used to install JavaScript libraries."},
  {q: "what is typescript", a: "TypeScript is a superset of JavaScript that adds static types."},
  {q: "what is react", a: "React is a JavaScript library for building user interfaces."},
  {q: "what is vue", a: "Vue.js is a progressive JavaScript framework for building user interfaces."},
  {q: "what is angular", a: "Angular is a TypeScript-based framework for building web apps."},
  {q: "what is bootstrap", a: "Bootstrap is a CSS framework for designing responsive websites."},
  {q: "what is tailwind", a: "Tailwind CSS is a utility-first CSS framework for styling web apps."},
  {q: "what is linux", a: "Linux is an open-source operating system used on servers and desktops."},
  {q: "what is windows", a: "Windows is a popular operating system developed by Microsoft."},
  {q: "what is macos", a: "macOS is an operating system developed by Apple for Mac computers."},
  {q: "what is computer", a: "A computer is a device that processes data and executes programs."},
  {q: "what is internet", a: "The Internet is a global network connecting millions of computers."},
  {q: "what is website", a: "A website is a collection of web pages accessible via the internet."},
  {q: "what is web app", a: "A web app is a program that runs in a browser using web technologies."},
  {q: "what is server", a: "A server is a computer that provides services or data to other computers."},
  {q: "what is client", a: "A client is a computer or software that accesses services from a server."},
  {q: "what is database", a: "A database stores and organizes data for easy access and management."},
  {q: "what is cybersecurity", a: "Cybersecurity is the practice of protecting systems and data from attacks."},
  {q: "what is hacker", a: "A hacker is someone who explores or exploits computer systems."},
  {q: "what is ai", a: "AI stands for Artificial Intelligence, enabling machines to perform tasks that require human intelligence."},
  {q: "what is machine learning", a: "Machine learning is a subset of AI that allows systems to learn from data."},
  {q: "what is deep learning", a: "Deep learning is a type of machine learning using neural networks."},
  {q: "what is neural network", a: "A neural network is a computational model inspired by the human brain."},
  {q: "what is chatgpt", a: "ChatGPT is an AI language model developed by OpenAI for conversation."},
  {q: "who is elon musk", a: "Elon Musk is a tech entrepreneur, CEO of Tesla and SpaceX."},
  {q: "who is bill gates", a: "Bill Gates is the co-founder of Microsoft and philanthropist."},
  {q: "who is steve jobs", a: "Steve Jobs was the co-founder of Apple Inc."},
  {q: "what is math", a: "Math is the study of numbers, quantities, and shapes."},
  {q: "what is physics", a: "Physics is the study of matter, energy, and the universe."},
  {q: "what is chemistry", a: "Chemistry is the study of substances and how they interact."},
  {q: "what is biology", a: "Biology is the study of living organisms."},
  {q: "what is ai ethics", a: "AI ethics studies how to use AI responsibly and fairly."},
  {q: "what is climate change", a: "Climate change is the long-term alteration of Earth's climate."},
  {q: "what is covid", a: "COVID-19 is a respiratory disease caused by the coronavirus SARS-CoV-2."},
  {q: "what is vaccine", a: "A vaccine stimulates the immune system to protect against disease."},
  {q: "what is stock market", a: "The stock market is where people buy and sell shares of companies."},
  {q: "what is bitcoin", a: "Bitcoin is a decentralized digital currency."},
  {q: "what is cryptocurrency", a: "Cryptocurrency is a digital currency using cryptography for security."},
  {q: "what is blockchain", a: "Blockchain is a decentralized ledger used to record transactions."},
  {q: "what is nft", a: "NFT stands for Non-Fungible Token, a unique digital asset on blockchain."},
  {q: "what is music", a: "Music is organized sound that expresses emotion and creativity."},
  {q: "what is art", a: "Art is the expression of human creativity through visual, auditory, or performance forms."},
  {q: "what is love", a: "Love is a deep feeling of affection and care."},
  {q: "what is happiness", a: "Happiness is a state of well-being and contentment."},
  {q: "what is life", a: "Life is the existence of living beings."},
  {q: "what is universe", a: "The universe is all of space, time, matter, and energy."},
  {q: "what is sun", a: "The Sun is the star at the center of our solar system."},
  {q: "what is moon", a: "The Moon is Earth's natural satellite."},
  {q: "what is earth", a: "Earth is the third planet from the Sun."},
  {q: "what is mars", a: "Mars is the fourth planet from the Sun, also called the Red Planet."},
  {q: "what is water", a: "Water is a chemical substance essential for life, H2O."},
  {q: "what is air", a: "Air is the mixture of gases we breathe, mainly nitrogen and oxygen."},
  {q: "what is fire", a: "Fire is the rapid oxidation of material releasing heat and light."},
  {q: "what is technology", a: "Technology is the application of scientific knowledge for practical purposes."},
  {q: "what is computer science", a: "Computer science is the study of computation and programming."},
  {q: "what is programming language", a: "A programming language is a set of rules to write computer programs."},
  {q: "what is algorithm", a: "An algorithm is a step-by-step procedure for solving a problem."},
  {q:"hello", a:"Hello! How are you today?"},
  {q:"hi", a:"Hi there!"},
  {q:"hey", a:"Hey! Nice to see you."},
  {q:"how are you", a:"I'm just a JS AI, but I'm doing well!"},
  {q:"good morning", a:"Good morning! Hope you have a great day."},
  {q:"good night", a:"Good night! Sleep well."},
  {q:"bye", a:"Goodbye! Talk to you later."},
  {q:"thank you", a:"You're welcome!"},
  {q:"thanks", a:"No problem!"},
  {q:"sorry", a:"It's okay! No worries."},
  {q:"yes", a:"Yes."},
  {q:"no", a:"No."},
  {q:"please", a:"Sure, here you go."},
  {q:"food", a:"Food is what we eat to live."},
  {q:"water", a:"Water is essential for life."},
  {q:"drink", a:"Drink something to stay hydrated."},
  {q:"eat", a:"Eating keeps you healthy and energized."},
  {q:"book", a:"A book is a set of written or printed pages."},
  {q:"pen", a:"A pen is a tool used for writing."},
  {q:"pencil", a:"A pencil is used for writing or drawing."},
  {q:"chair", a:"A chair is furniture for sitting."},
  {q:"table", a:"A table is furniture for placing things on."},
  {q:"house", a:"A house is a building for people to live in."},
  {q:"school", a:"School is a place where people learn."},
  {q:"teacher", a:"A teacher helps students learn."},
  {q:"student", a:"A student is someone who learns."},
  {q:"computer", a:"A computer is a machine that processes data."},
  {q:"phone", a:"A phone is used to call or message people."},
  {q:"car", a:"A car is a vehicle for transportation."},
  {q:"bike", a:"A bike is a two-wheeled vehicle powered by pedaling."},
  {q:"bus", a:"A bus is a large vehicle for public transport."},
  {q:"train", a:"A train moves on rails to transport people or goods."},
  {q:"airport", a:"An airport is a place where airplanes take off and land."},
  {q:"station", a:"A station is a place where vehicles stop for passengers."},
  {q:"road", a:"A road is a path for vehicles or people."},
  {q:"street", a:"A street is a public road in a city or town."},
  {q:"city", a:"A city is a large town."},
  {q:"village", a:"A village is a small settlement in the countryside."},
  {q:"country", a:"A country is a nation with its own government."},
  {q:"planet", a:"A planet is a celestial body orbiting a star."},
  {q:"earth", a:"Earth is the planet we live on."},
  {q:"sun", a:"The Sun is the star that provides light and heat to Earth."},
  {q:"moon", a:"The Moon is Earth's natural satellite."},
  {q:"star", a:"A star is a luminous celestial body."},
  {q:"sky", a:"The sky is the atmosphere seen from Earth."},
  {q:"rain", a:"Rain is water falling from clouds."},
  {q:"snow", a:"Snow is frozen precipitation that falls from the sky."},
  {q:"wind", a:"Wind is moving air."},
  {q:"fire", a:"Fire is the rapid oxidation of material, producing heat and light."},
  {q:"tree", a:"A tree is a tall plant with a trunk, branches, and leaves."},
  {q:"flower", a:"A flower is the reproductive part of a plant, often colorful."},
  {q:"grass", a:"Grass is green vegetation that grows on the ground."},
  {q:"animal", a:"An animal is a living organism that moves and eats."},
  {q:"dog", a:"A dog is a domesticated animal often kept as a pet."},
  {q:"cat", a:"A cat is a small domesticated feline."},
  {q:"bird", a:"A bird is an animal with feathers and wings."},
  {q:"fish", a:"A fish is an aquatic animal with gills and fins."},
  {q:"cow", a:"A cow is a domesticated animal raised for milk and meat."},
  {q:"horse", a:"A horse is an animal used for riding or work."},
  {q:"chicken", a:"A chicken is a domesticated bird raised for eggs and meat."},
  {q:"fruit", a:"A fruit is the edible part of a plant containing seeds."},
  {q:"vegetable", a:"A vegetable is an edible plant or part of a plant."},
  {q:"apple", a:"An apple is a sweet, edible fruit."},
  {q:"banana", a:"A banana is a long, yellow fruit."},
  {q:"orange", a:"An orange is a citrus fruit with vitamin C."},
  {q:"school", a:"A school is a place where students learn."},
  {q:"book", a:"A book contains pages with written or printed information."},
  {q:"pen", a:"A pen is used for writing with ink."},
  {q:"computer", a:"A computer processes data electronically."},
  {q:"internet", a:"The Internet is a global network of connected computers."},
  {q:"game", a:"A game is an activity for fun or competition."},
  {q:"music", a:"Music is organized sound that expresses emotion or art."},
  {q:"movie", a:"A movie is a sequence of moving images telling a story."},
  {q:"food", a:"Food is what humans and animals eat to survive."},
  {q:"drink", a:"A drink is a liquid consumed for hydration or enjoyment."},
  {q:"sleep", a:"Sleep is a natural state of rest for the body and mind."},
  {q:"work", a:"Work is an activity done to achieve a goal or earn money."},
  {q:"play", a:"Play is an activity done for enjoyment or recreation."},
  {q:"love", a:"Love is a deep feeling of affection."},
  {q:"happy", a:"Happy means feeling joy or pleasure."},
  {q:"sad", a:"Sad means feeling unhappy or sorrowful."},
  {q:"angry", a:"Angry means feeling strong displeasure."},
  {q:"excited", a:"Excited means feeling enthusiastic or eager."},
  {q:"tired", a:"Tired means feeling a need to rest."},
  {q:"hungry", a:"Hungry means needing or wanting food."},
  {q:"thirsty", a:"Thirsty means needing or wanting water."},
  {q:"friend", a:"A friend is someone you like and trust."},
  {q:"family", a:"Family is a group of related people living together or connected by blood."},
];

// AI response logic
function aiResponse(message) {
  message = message.toLowerCase();
  
  for (const item of knowledgeBase) {
    if (message.includes(item.q)) return item.a;
  }
  
  // If no match, return a random fallback
  const fallback = [
    "Interesting question!",
    "I need more info to answer that.",
    "Can you clarify your question?",
    "Hmm, I’m not sure about that.",
    "I’m learning, please ask something else!"
  ];
  
  return fallback[Math.floor(Math.random() * fallback.length)];
}

// Send message
function sendMessage() {
  const message = aiInput.value.trim();
  if (!message) return;

  appendMessage('user', message);
  aiInput.value = '';

  // Simulate typing
  appendMessage('ai', 'Typing...');
  setTimeout(() => {
    aiChatContent.lastChild.remove(); // remove "Typing..."
    appendMessage('ai', aiResponse(message));
  }, 600);
}

aiSend.addEventListener('click', sendMessage);
aiInput.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') sendMessage();
});
</script>

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
