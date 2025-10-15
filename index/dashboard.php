<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Handle new feedback submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['feedback'])) {
    $feedback = trim($_POST['feedback']);
    $stmt = $conn->prepare("INSERT INTO feedback (feedback) VALUES (?)");
    $stmt->execute([$feedback]);
    header("Location: dashboard.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCprotektado - Emergency Response System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">

  <style>

    .logo-font {
      font-family: 'Poppins', sans-serif;
      letter-spacing: 1px;
    }

    html {
      scroll-behavior: smooth;
    }

    .hover-btn {
      transition: all 0.3s ease-in-out;
    }
    .hover-btn:hover {
      transform: scale(1.05);
    }

    #map {
      height: 300px;
      width: 100%;
      border-radius: 0.75rem;
    }

    .img-shadow {
      box-shadow: 0px 4px 20px rgba(0,0,0,0.4);
    }

    section {
      scroll-margin-top: 50px; /* Adjust based on navbar height */
    }

    .blurred {
      filter: blur(8px);
      pointer-events: none;
      user-select: none;
      transition: filter 0.3s ease-in-out;
    }
    
  </style>
</head>
<body class="h-screen text-white flex flex-col">

<!---============================== DASHBOARD WRAPPER ==============================--->
<div id="dashboardWrapper">

  <!---============================== TOP BAR ==============================--->
  <header class="w-full fixed top-0 left-0 z-50 bg-gradient-to-b from-green-800 to-stone-800 backdrop-blur-md shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-7 flex justify-between items-center">

      <!---============================== LOGO ==============================--->
      <div class="flex items-center space-x-3">
        <img src="../img/Logo.png" alt="My Logo" class="h-10 w-10 rounded-full">
        <span class="logo-font text-white">Emergency Response</span>
      </div>

      <!---============================== NAVIGATION ==============================--->
      <nav class="hidden md:flex space-x-8 text-lg font-medium">
        <a href="#home" class="hover:text-blue-400 transition">Home</a>
        <a href="#services" class="hover:text-blue-400 transition">Services</a>
        <a href="#contact" class="hover:text-blue-400 transition">Contact Us</a>
        <a href="#about" class="hover:text-blue-400 transition">About Us</a>
        <a href="#settings" class="hover:text-blue-400 transition">Settings</a>
      </nav>

   <!---============================== LOGIN, REGISTER, E-CALL BUTTON ==============================--->
      <div class="flex space-x-6 hidden md:flex">

        <a href="../config/login.php" 
          class="flex items-center space-x-2 text-white font-medium transition duration-300 hover:text-blue-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
          <span class="relative group">
            LOGIN
            <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-blue-400 transition-all duration-300 group-hover:w-full"></span>
          </span>
        </a>


        <a href="../config/register.php" 
          class="flex items-center space-x-2 text-white font-medium transition duration-300 hover:text-orange-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span class="relative group">
            REGISTER
            <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-orange-400 transition-all duration-300 group-hover:w-full"></span>
          </span>
        </a>


      </div>

      
     <!---============================== MOBILE BUTTON ==============================--->
<div class="flex items-center space-x-3 md:hidden">
 


  <!---============================== MENU BUTTON ==============================--->
  <button onclick="toggleMenu()" class="focus:outline-none">
    <svg class="w-7 h-7" fill="none" stroke="white" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
  </button>
</div>
      
    </div>

    <script>
  function toggleMenu() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
  }
</script>
    <!---============================== MOBILE MENU ==============================--->
    <div id="mobile-menu" class="hidden md:hidden bg-stone-900/90 bg-gradient-to-b from-green-800 to-stone-800 backdrop-blur-md px-4 py-4 space-y-4">
      <a href="#home" class="block hover:text-blue-400 transition">Home</a>
      <a href="#services" class="block hover:text-blue-400 transition">Services</a>
      <a href="#contact" class="block hover:text-blue-400 transition">Contact Us</a>
      <a href="#about" class="block hover:text-blue-400 transition">About Us</a>
      <a href="#settings" class="block hover:text-blue-400 transition">Settings</a>
      <hr class="border-gray-600">
      <a href="../config/login.php" class="block px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 transition text-center">
        Login
      </a>
      <a href="../config/register.php" class="block px-4 py-2 rounded-lg bg-orange-600 hover:bg-orange-500 transition text-center">
        Register
      </a>
    </div>
  </header>

  <!---============================== MAIN CONTENT ==============================--->
  <main class="flex-1 pt-24">

<!-- HERO SECTION (MAP BACKGROUND + CENTERED OVERLAY) -->
<section id="home" class="relative h-screen flex items-center justify-center overflow-hidden">

  <!-- Background Map -->
  <img src="../img/Map.png"
       alt="Hazard Map Background"
       class="absolute inset-0 w-full h-full object-cover object-center opacity-70">

  <!-- Overlay Container -->
  <div class="relative z-10 bg-white/90 rounded-2xl shadow-2xl p-10 w-[600px] max-w-[90%] text-center">
    <h1 class="text-4xl md:text-5xl font-bold mb-6 text-gray-800">Hazard Mapping</h1>
    <p class="text-gray-600 mb-6 leading-relaxed">
      Identify areas vulnerable to <span class="font-semibold">floods</span>, 
      <span class="font-semibold">landslides</span>, and 
      <span class="font-semibold">storm surges</span>. 
      Get the awareness you need to prepare early and respond quickly when hazards strike.
    </p>

    <!-- Search form -->
    <form action="map.php" method="get" class="flex items-center border border-gray-300 rounded-full overflow-hidden shadow-md">
      <input 
        type="text" 
        name="location" 
        placeholder="Search Location" 
        required
        class="flex-1 px-5 py-4 focus:outline-none text-gray-700 text-lg"
      >
      <button 
        type="submit" 
        class="bg-blue-600 hover:bg-blue-700 text-white px-7 py-4 text-lg font-semibold transition duration-300">
        🔍
      </button>
    </form>
  </div>
</section>

    <!---============================== SERVICES SECTION ==============================--->
<section id="services" class="min-h-screen bg-gradient-to-b from-stone-800 to-sky-800 py-16 px-6">

  <div class="max-w-6xl mx-auto text-center mb-12">
    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Our Services</h2>
    <p class="text-gray-300 max-w-2xl mx-auto">
      We provide integrated services to enhance emergency response efficiency and coordination across Quezon City.
    </p>
  </div>

  <!-- Services Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
    
    <!-- Service 1 -->
    <div class="bg-stone-800 rounded-xl p-6 text-center hover:scale-105 transition transform hover:shadow-lg">
      <div class="mb-4">
        <i class="bx bx-phone-call text-blue-400 text-5xl"></i>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">Emergency Call Logging</h3>
      <p class="text-gray-400 text-sm">
        Capture caller details, location, and incident type with accuracy and speed.
      </p>
    </div>

    <!-- Service 2 -->
    <div class="bg-stone-800 rounded-xl p-6 text-center hover:scale-105 transition transform hover:shadow-lg">
      <div class="mb-4">
        <i class="bx bx-alarm-exclamation text-red-400 text-5xl"></i>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">Incident Prioritization</h3>
      <p class="text-gray-400 text-sm">
        Assess severity and prioritize incidents for faster emergency response.
      </p>
    </div>

    <!-- Service 3 -->
    <div class="bg-stone-800 rounded-xl p-6 text-center hover:scale-105 transition transform hover:shadow-lg">
      <div class="mb-4">
        <i class="bx bx-first-aid text-green-400 text-5xl"></i>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">Resource Allocation</h3>
      <p class="text-gray-400 text-sm">
        Efficiently assign EMS, fire, and police teams to emergency locations.
      </p>
    </div>

    <!-- Service 4 -->
    <div class="bg-stone-800 rounded-xl p-6 text-center hover:scale-105 transition transform hover:shadow-lg">
      <div class="mb-4">
        <i class="bx bx-map-pin text-yellow-400 text-5xl"></i>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">Real-Time GPS Tracking</h3>
      <p class="text-gray-400 text-sm">
        Monitor responders' exact locations to improve response times.
      </p>
    </div>

    <!-- Service 5 -->
    <div class="bg-stone-800 rounded-xl p-6 text-center hover:scale-105 transition transform hover:shadow-lg">
      <div class="mb-4">
        <i class="bx bx-time-five text-orange-400 text-5xl"></i>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">Response Time Analytics</h3>
      <p class="text-gray-400 text-sm">
        Analyze performance metrics to enhance decision-making and efficiency.
      </p>
    </div>

    <!-- Service 6 -->
    <div class="bg-stone-800 rounded-xl p-6 text-center hover:scale-105 transition transform hover:shadow-lg">
      <div class="mb-4">
        <i class="bx bx-group text-purple-400 text-5xl"></i>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">Inter-Agency Coordination</h3>
      <p class="text-gray-400 text-sm">
        Streamline communication between emergency services and city agencies.
      </p>
    </div>

    <!-- Service 7 -->
    <div class="bg-stone-800 rounded-xl p-6 text-center hover:scale-105 transition transform hover:shadow-lg">
      <div class="mb-4">
        <i class="bx bx-notepad text-blue-300 text-5xl"></i>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">After-Action Reviews</h3>
      <p class="text-gray-400 text-sm">
        Document and review incident responses to improve future strategies.
      </p>
    </div>

    <!-- Service 8 -->
    <div class="bg-stone-800 rounded-xl p-6 text-center hover:scale-105 transition transform hover:shadow-lg">
      <div class="mb-4">
        <i class="bx bx-shield-quarter text-green-300 text-5xl"></i>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">Community Safety Programs</h3>
      <p class="text-gray-400 text-sm">
        Promote awareness and preparedness through training and drills.
      </p>
    </div>

  </div>
</section>


 <!---============================== CONTACT SECTION ==============================--->
<section id="contact" class="min-h-screen flex flex-col md:flex-row justify-center items-start bg-gradient-to-b from-sky-800 to-stone-800 py-20 px-6 relative overflow-hidden">

  <!-- Wider Map -->
  <div class="w-full md:w-1/2 flex justify-center max-w-[600px] relative z-10">
    <div id="map" 
         style="width: 100%; height: 500px; border-radius: 1rem; box-shadow: 0 20px 40px rgba(0,0,0,0.5); border: 2px solid rgba(255,255,255,0.1);">
    </div>
  </div>

  <!-- Contact Info -->
  <div class="space-y-6 mt-10 md:mt-0 md:ml-10 max-w-[400px] relative z-10">
    <div class="bg-stone-900/70 p-8 rounded-2xl shadow-2xl border border-white/10 backdrop-blur-md">
      <h2 class="text-3xl font-bold text-white mb-2">Contact Us</h2>
      <p class="text-gray-300 mb-4">
        Have questions or need help? Reach out to us for support or emergency assistance.
      </p>
      <div class="space-y-2 text-gray-300">
        <p><span class="font-semibold text-blue-400">Location:</span> Bestlink College of the Philippines, Quezon City</p>
        <p><span class="font-semibold text-green-400">Phone:</span> +63 912 345 6789</p>
        <p><span class="font-semibold text-purple-400">Email:</span> emergency@quezoncity.gov.ph</p>
        <p><span class="font-semibold text-orange-400">Hours:</span> 24/7 Emergency Support</p>
      </div>
    </div>
  </div>
</section>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  const map = L.map('map').setView([14.726674031300147, 121.03718577644092], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  L.marker([14.726564251365751, 121.03672870828876])
    .addTo(map)
    .bindPopup('<b>Bestlink College of the Philippines</b><br>Quezon City')
    .openPopup();
</script>

<style>
  /* Subtle floating animation */
  @keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.7; }
    50% { transform: scale(1.1); opacity: 0.4; }
  }
  .animate-pulse {
    animation: pulse 6s ease-in-out infinite;
  }

  @keyframes ping {
    0% { transform: scale(1); opacity: 0.6; }
    75%, 100% { transform: scale(2); opacity: 0; }
  }
  .animate-ping {
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
  }
</style>


    <!---============================== ABOUT SECTION ==============================--->
    <section id="about" class="min-h-screen bg-gradient-to-b from-stone-800 to-sky-800 py-20 px-6 border-stone-800">
      <div class="max-w-6xl mx-auto text-center mb-12">
        <h2 class="text-3xl font-bold">About Us</h2>
        <p class="text-gray-300 mt-2 max-w-3xl mx-auto">
          Our Emergency Response System project was created to further improve the rapid response of 
          responders and keep our citizens safe from calamities, accidents, fires, and other incidents 
          that may occur in Quezon City. Through our project, we will use modern technology to reduce and 
          prevent accidents and hazards through Hazard Mapping, to keep our community safe.
        </p>
      </div>

      <div class="max-w-6xl mx-auto grid grid-cols-3 md:grid-cols-5 gap-8">
        <!-- Team Member 1 -->
        <div class="text-center">
          <img src="./img/profile1.jpg" alt="Team Member 1" class="w-40 h-40 mx-auto rounded-lg img-shadow object-cover">
          <h3 class="mt-4 font-semibold">Santiago, Josh Angelo J.</h3>
          <p class="text-gray-400 text-sm">Project Manager</p>
        </div>

        <!-- Team Member 2 -->
        <div class="text-center">
          <img src="./img/profile3.jpg" alt="Team Member 2" class="w-40 h-40 mx-auto rounded-lg img-shadow object-cover">
          <h3 class="mt-4 font-semibold">Dulfo, Von Derick G.</h3>
          <p class="text-gray-400 text-sm">Full Stack Developer</p>
        </div>

        <!-- Team Member 3 -->
        <div class="text-center">
          <img src="./img/profile5.jpg" alt="Team Member 3" class="w-40 h-40 mx-auto rounded-lg img-shadow object-cover">
          <h3 class="mt-4 font-semibold">Dionson, Jonathan R.</h3>
          <p class="text-gray-400 text-sm">Researcher</p>
        </div>

        <!-- Team Member 4 -->
        <div class="text-center">
          <img src="./img/profile4.jpg" alt="Team Member 4" class="w-40 h-40 mx-auto rounded-lg img-shadow object-cover">
          <h3 class="mt-4 font-semibold">Gunda, Hyziel Y.</h3>
          <p class="text-gray-400 text-sm">Document Analyst</p>
        </div>

        <!-- Team Member 5 -->
        <div class="text-center">
          <img src="./img/profile2.jpg" alt="Team Member 5" class="w-40 h-40 mx-auto rounded-lg img-shadow object-cover">
          <h3 class="mt-4 font-semibold">Pacheco, Margarita L.</h3>
          <p class="text-gray-400 text-sm">Quality Assurance</p>
        </div>
      </div>
    </section>
  </main>
</div>

<!---============================== MODALS ==============================--->
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

    <!-- CALLING / ACTIVE CALL STATE -->
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


<script>
  // Custom smooth scroll with true centering
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId && targetId !== "#") {
        e.preventDefault();
        const target = document.querySelector(targetId);
        if (target) {
          const navbarHeight = document.querySelector('header').offsetHeight; // Fixed header height
          const viewportHeight = window.innerHeight;
          const elementHeight = target.offsetHeight;
          const elementTop = target.getBoundingClientRect().top + window.scrollY;

          // Compute target scroll position so section is centered
          const scrollTo = elementTop - (viewportHeight / 2) + (elementHeight / 2) - (navbarHeight / 2);

          window.scrollTo({
            top: scrollTo,
            behavior: "smooth"
          });
        }
      }
    });
  });
</script>

</body>
</html>
