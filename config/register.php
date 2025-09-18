<?php
require_once "./db.php";
session_start();

//______________________________________________//
// Handle registration form submission
//______________________________________________//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    $role = 'user'; // Default role

    //______________________________________________//
    // Validate form inputs
    //______________________________________________//
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->fetch()) {
                $error = "Username already exists!";
            } else {
                // Insert new user into the database
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $password, $role])) {
                    $success = "Account created successfully!";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Form</title>
  <link rel="icon" type="image/x-icon" href="../img/Logocircle.png">
  <link rel="stylesheet" href="../css/responsive.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">


  <style>
    .logo-font {
      font-family: 'Poppins', sans-serif;
      letter-spacing: 1px;
    }
    
  </style>

  <script>
    //______________________________________________//
    // Toggle mobile menu
    //______________________________________________//
    function toggleMenu() {
      const menu = document.getElementById('mobile-menu');
      menu.classList.toggle('hidden');
    }
  </script>
</head>
<body class="h-screen bg-stone-800 text-white flex flex-col">

<!---============================== MAIN WRAPPER ==============================--->
<div id="dashboardWrapper" class="flex-1 flex flex-col">

  <!---============================== TOP BAR ==============================--->
  <header class="w-full bg-gradient-to-b from-green-800 to-red-800 backdrop-blur-md shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-7 flex justify-between items-center">

      <div class="flex items-center space-x-3">
        <img src="../img/Logo.png" alt="My Logo" class="h-10 w-10 rounded-full">
        <span class="logo-font text-white">Emergency Response</span>
      </div>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex space-x-8 text-lg font-medium">
        <a href="../index/dashboard.php#home" class="hover:text-blue-400 transition">Home</a>
        <a href="../index/dashboard.php#services" class="hover:text-blue-400 transition">Services</a>
        <a href="../index/dashboard.php#contact" class="hover:text-blue-400 transition">Contact Us</a>
        <a href="../index/dashboard.php#about" class="hover:text-blue-400 transition">About Us</a>
        <a href="../index/setting.php" class="hover:text-blue-400 transition">Settings</a>
      </nav>

      <!---============================== LOGIN / REGISTER / E-CALL ==============================--->
      <div class="flex space-x-6 hidden md:flex space-x-4">
        <a href="../config/login.php" class="flex items-center space-x-2 text-white font-medium transition duration-300 hover:text-blue-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
          <span class="relative group">
            LOGIN
            <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-blue-400 transition-all duration-300 group-hover:w-full"></span>
          </span>
        </a>

        <a href="../config/register.php" class="flex items-center space-x-2 text-white font-medium transition duration-300 hover:text-orange-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span class="relative group">
            REGISTER
            <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-orange-400 transition-all duration-300 group-hover:w-full"></span>
          </span>
        </a>

        <a href="#" onclick="openCallModal()" 
           class="flex items-center space-x-2 text-white font-medium transition duration-300 hover:text-red-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3 7-2 2a11.042 11.042 0 005.586 5.586l2-2 7 3v2a2 2 0 01-2 2 19 19 0 01-16-16 2 2 0 012-2z" />
          </svg>
          <span class="relative group">
            E-Call
            <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-red-400 transition-all duration-300 group-hover:w-full"></span>
          </span>
        </a>
      </div>

      <!---============================== MOBILE MENU BUTTONS ==============================--->
      <div class="flex items-center space-x-3 md:hidden">
        <button onclick="openCallModal()" class="text-white focus:outline-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3 7-2 2a11.042 11.042 0 005.586 5.586l2-2 7 3v2a2 2 0 01-2 2 19 19 0 01-16-16 2 2 0 012-2z" />
          </svg>
        </button>

        <button onclick="toggleMenu()" class="focus:outline-none">
          <svg class="w-7 h-7" fill="none" stroke="white" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>
    </div>

    <!---============================== MOBILE MENU ==============================--->
    <div id="mobile-menu" class="hidden md:hidden bg-gradient-to-b from-stone-800 to-blue-800 backdrop-blur-md px-4 py-4 space-y-4">
      <a href="../index/dashboard.php#home" class="block hover:text-blue-400 transition">Home</a>
      <a href="../index/dashboard.php#services" class="block hover:text-blue-400 transition">Services</a>
      <a href="../index/dashboard.php#contact" class="block hover:text-blue-400 transition">Contact Us</a>
      <a href="../index/dashboard.php#about" class="block hover:text-blue-400 transition">About Us</a>
      <a href="../index/dashboard.php#settings" class="block hover:text-blue-400 transition">Settings</a>
      <hr class="border-gray-600">
      <a href="login.php" class="block px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 transition text-center">Login</a>
      <a href="register.php" class="block px-4 py-2 rounded-lg bg-orange-600 hover:bg-orange-500 transition text-center">Register</a>
    </div>
  </header>

  <!---============================== REGISTRATION FORM ==============================--->
  <main class="flex-1 flex items-center justify-center px-4">
    <div class="bg-gradient-to-b from-stone-800 to-sky-800 rounded-2xl text-black shadow-lg w-full max-w-md p-8">

      <div class="flex justify-center mb-6">
        <img src="../img/Logo.png" alt="Logo" class="w-20 h-20 rounded-full shadow-md bg-stone-800 p-2">
      </div>

      <h2 class="text-2xl text-white font-bold text-center mb-2">Create an Account</h2>
      <p class="text-center text-gray-400 mb-6">Fill in the details below</p>

      <?php if (!empty($success)): ?>
        <p class="bg-green-100 text-green-700 p-2 rounded mb-4 text-center"><?= $success ?></p>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <p class="bg-red-100 text-red-600 p-2 rounded mb-4 text-center"><?= $error ?></p>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <div>
          <label class="block text-white mb-1 text-sm">Username</label>
          <input type="text" name="username" placeholder="Choose a username" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
        </div>

        <div>
          <label class="block text-white mb-1 text-sm">Password</label>
          <input type="password" name="password" placeholder="Enter your password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
        </div>

        <div>
          <label class="block text-white mb-1 text-sm">Confirm Password</label>
          <input type="password" name="confirm_password" placeholder="Confirm your password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
        </div>

        <button class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">Register</button>
      </form>

      <p class="text-center text-white text-sm mt-6">
        Already have an account?
        <a href="./login.php" class="text-blue-400 hover:underline">Login here</a>
      </p>
    </div>
  </main>

  <!---============================== EMERGENCY CALL MODAL ==============================--->
  <div id="callModal" class="fixed inset-0 hidden items-start justify-center bg-black/70 backdrop-blur-md z-50 pt-20">
    <div class="relative w-full max-w-sm bg-stone-900 rounded-3xl shadow-2xl overflow-hidden">

      <style>
        @keyframes ping {
          0% { transform: scale(1); opacity: 0.6; }
          75%, 100% { transform: scale(2); opacity: 0; }
        }
        .animate-ping {
          animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
      </style>

      <div class="flex flex-col items-center justify-center p-6 space-y-4">
        <div class="relative">
          <div class="absolute inset-0 rounded-full bg-red-600 opacity-30 animate-ping"></div>
          <div class="absolute inset-0 rounded-full bg-red-500 opacity-20 animate-ping delay-150"></div>
          <img src="../img/Logo.png" alt="Emergency Logo" class="relative w-24 h-24 rounded-full border-4 border-red-600 shadow-lg">
        </div>

        <p id="callStatus" class="text-gray-300 text-lg mt-4">Connecting to Dispatcher...</p>
        <p class="text-gray-400 text-sm">Please wait while we connect your call</p>

        <div class="flex justify-around w-full mt-6">
          <button onclick="toggleMute()" id="muteBtn" class="bg-gray-700 hover:bg-gray-600 p-4 rounded-full text-white transition">
            <i class="bx bx-microphone text-xl"></i>
          </button>

          <button onclick="endCall()" class="bg-red-600 hover:bg-red-500 p-4 rounded-full text-white shadow-lg transition">
            <i class="bx bx-phone-off text-xl"></i>
          </button>

          <button onclick="toggleSpeaker()" id="speakerBtn" class="bg-gray-700 hover:bg-gray-600 p-4 rounded-full text-white transition">
            <i class="bx bx-volume-full text-xl"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  //______________________________________________//
  // Emergency Call JS Logic
  //______________________________________________//
  let localStream;
  let peerConnection;
  let isMuted = false;
  let isSpeakerOn = false;
  const servers = { iceServers: [{ urls: "stun:stun.l.google.com:19302" }] };

  async function openCallModal() {
    const modal = document.getElementById('callModal');
    const dashboard = document.getElementById('dashboardWrapper');
    const callStatus = document.getElementById('callStatus');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    dashboard.classList.add('blurred');

    try {
      callStatus.textContent = "Calling...";
      localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
      peerConnection = new RTCPeerConnection(servers);
      localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

      setTimeout(() => {
        callStatus.textContent = "Connected with Emergency Response";
      }, 2000);

      const offer = await peerConnection.createOffer();
      await peerConnection.setLocalDescription(offer);
    } catch (error) {
      console.error("Error starting call:", error);
      alert("Unable to access microphone. Please allow microphone permissions.");
      callStatus.textContent = "Error: Microphone access denied";
    }
  }

  function endCall() {
    const callStatus = document.getElementById('callStatus');
    const dashboard = document.getElementById('dashboardWrapper');

    if (localStream) {
      localStream.getTracks().forEach(track => track.stop());
    }

    if (peerConnection) {
      peerConnection.close();
      peerConnection = null;
    }

    callStatus.textContent = "Call Ended";

    setTimeout(() => {
      document.getElementById('callModal').classList.add('hidden');
      document.getElementById('callModal').classList.remove('flex');
      dashboard.classList.remove('blurred');
      callStatus.textContent = "Connecting to Dispatcher...";
    }, 1200);
  }

  function toggleMute() {
    if (!localStream) return;
    isMuted = !isMuted;
    localStream.getAudioTracks().forEach(track => track.enabled = !isMuted);
    document.getElementById('muteBtn').classList.toggle('bg-gray-500');
  }

  function toggleSpeaker() {
    isSpeakerOn = !isSpeakerOn;
    document.getElementById('speakerBtn').classList.toggle('bg-gray-500');
  }
</script>

</body>
</html>
