<?php
require_once "../config/db.php";
session_start();

//______________________________________________//
// CREATE DEFAULT ADMIN, STAFF and SupAd ACCOUNTS
//______________________________________________//
try {
    // Create default admin if not exists
    $checkAdmin = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $checkAdmin->execute();
    if ($checkAdmin->fetchColumn() == 0) {
        $insertAdmin = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insertAdmin->execute(['admin', '123', 'admin']); // default admin account
    }

    // Create default staff if not exists
    $checkStaff = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = 'staff'");
    $checkStaff->execute();
    if ($checkStaff->fetchColumn() == 0) {
        $insertStaff = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insertStaff->execute(['staff', '123', 'staff']); // default staff account
    }

    // Create default SupAd if not exists
    $checkSupAd = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = 'supad'");
    $checkSupAd->execute();
    if ($checkSupAd->fetchColumn() == 0) {
        $insertSupAd = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insertSupAd->execute(['supad', '123', 'supad']); // default supad account
    }
} catch (PDOException $e) {
    die("Error creating default accounts: " . $e->getMessage());
}

//______________________________________________//
// LOGIN ATTEMPTS SYSTEM (2-minute lockout)
//______________________________________________//
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = time();
}

$lockout_duration = 120; // 2 minutes
$max_attempts = 3;       // Max allowed failed attempts

// Reset attempts if 2 minutes passed
if (time() - $_SESSION['last_attempt_time'] > $lockout_duration) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

$lockout = $_SESSION['login_attempts'] >= $max_attempts;

//______________________________________________//
// LOGIN HANDLER
//______________________________________________//
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['forgot_password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($lockout) {
        // Show remaining lockout time
        $remaining_time = $lockout_duration - (time() - $_SESSION['last_attempt_time']);
        $error = "Too many failed attempts. Please wait {$remaining_time} seconds before trying again.";
    } else {
        // Fetch user by username
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // If user password is plain text (admin/staff default accounts)
            // or hashed (users who changed password via forgot password)
            $isValidPassword = false;
            if (password_verify($password, $user['password'])) {
                $isValidPassword = true;
            } elseif ($password === $user['password']) {
                $isValidPassword = true;
            }

            if ($isValidPassword) {
                // Successful login
                $_SESSION['login_attempts'] = 0; // Reset failed attempts
                $_SESSION['last_attempt_time'] = time();

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'profile_image' => $user['profile_image'] ?? 'default.png'
                ];

                $_SESSION['just_logged_in'] = true;

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: ../data/loadingadmin.php");
                        break;
                    case 'staff':
                        header("Location: ../data/loadingstaff.php");
                        break;
                    case 'supad':
                        header("Location: ../data/loadingsupad.php");
                        break;
                    default:
                        header("Location: ../data/loadinguser.php");
                        break;
                }
                exit;
            } else {
                // Wrong password
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                $error = "Invalid username or password!";
            }
        } else {
            // Username not found
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            $error = "Invalid username or password!";
        }
    }
}

//______________________________________________//
// FORGOT PASSWORD HANDLER
//______________________________________________//
$fp_error = '';
$fp_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $username = trim($_POST['username']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($new_password) || empty($confirm_password)) {
        $fp_error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $fp_error = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Hash the new password before updating
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            if ($update->execute([$hashed_password, $username])) {
                $fp_success = "Password successfully updated!";
            } else {
                $fp_error = "Something went wrong.";
            }
        } else {
            $fp_error = "Username not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
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
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</head>
<body class="min-h-screen bg-stone-800 text-white flex flex-col">
    <div id="dashboardWrapper" class="flex-1 flex flex-col">
        <header class="w-full bg-gradient-to-b from-green-800 to-stone-800 backdrop-blur-md shadow-md">
            <div class="max-w-7xl mx-auto px-4 py-7 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="../img/Logo.png" alt="My Logo" class="h-10 w-10 rounded-full">
                    <span class="logo-font text-white">Emergency Response</span>
                </div>

                <nav class="hidden md:flex space-x-8 text-lg font-medium">
                    <a href="../index/dashboard.php#home" class="hover:text-blue-400 transition">Home</a>
                    <a href="../index/dashboard.php#services" class="hover:text-blue-400 transition">Services</a>
                    <a href="../index/dashboard.php#contact" class="hover:text-blue-400 transition">Contact Us</a>
                    <a href="../index/dashboard.php#about" class="hover:text-blue-400 transition">About Us</a>
                    <a href="../index/setting.php" class="hover:text-blue-400 transition">Settings</a>
                </nav>

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

                    <!--<a href="../config/register.php" class="flex items-center space-x-2 text-white font-medium transition duration-300 hover:text-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="relative group">
                            REGISTER
                            <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-orange-400 transition-all duration-300 group-hover:w-full"></span>
                        </span>
                    </a>-->

                </div>

                <div class="flex items-center space-x-3 md:hidden">

                    <button onclick="toggleMenu()" class="focus:outline-none">
                        <svg class="w-7 h-7" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
                     
            <div id="mobile-menu" class="hidden md:hidden bg-gradient-to-b from-stone-800 to-blue-800 backdrop-blur-md px-4 py-4 space-y-4">
                <a href="../index/dashboard.php#home" class="block hover:text-blue-400 transition">Home</a>
                <a href="../index/dashboard.php#services" class="block hover:text-blue-400 transition">Services</a>
                <a href="../index/dashboard.php#contact" class="block hover:text-blue-400 transition">Contact Us</a>
                <a href="../index/dashboard.php#about" class="block hover:text-blue-400 transition">About Us</a>
                <a href="../index/dashboard.php#settings" class="block hover:text-blue-400 transition">Settings</a>
                <hr class="border-gray-600">
                <a href="login.php" class="block px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 transition text-center">Login</a>
                <!--<a href="register.php" class="block px-4 py-2 rounded-lg bg-orange-600 hover:bg-orange-500 transition text-center">Register</a>-->
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center px-4">
            <div class="bg-gradient-to-b from-stone-800 to-sky-800 rounded-2xl text-black shadow-lg w-full max-w-md p-8">
                <div class="flex justify-center mb-6">
                    <img src="../img/Logo.png" alt="Logo" class="w-20 h-20 rounded-full shadow-md bg-stone-800 p-2">
                </div>

                <h2 class="text-2xl text-white font-bold text-center mb-2">Emergency Response System</h2>
                <p class="text-center text-gray-400 mb-6">Login to your account</p>

                <?php if (!empty($error)): ?>
                    <p class="bg-red-100 text-red-600 p-2 rounded mb-4 text-center"><?= $error ?></p>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div>
        <label class="block text-white mb-1 text-sm">Username</label>
        <input type="text" name="username" placeholder="Enter your username" 
            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
    </div>

    <div>
        <label class="block text-white mb-1 text-sm">Password</label>
        <input type="password" name="password" placeholder="Enter your password" 
            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
    </div>

    <button type="submit" name="login" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
        Login
    </button>
</form>

                <div class="flex justify-between mt-4 text-sm">
                    <!--<p class="text-white">Don't have an account? <a href="./register.php" class="text-blue-400 hover:underline">Register here</a></p>-->
                    <p><a href="javascript:void(0)" onclick="openForgotModal()" class="text-yellow-400 hover:underline">Forgot Password?</a></p>
                </div>
            </div>
        </main>

        <!-- FORGOT PASSWORD MODAL -->
        <div id="forgotPasswordModal" class="fixed inset-0 hidden items-center justify-center bg-black/70 backdrop-blur-md z-50">
            <div class="relative text-black w-full max-w-md bg-gradient-to-b from-stone-800 to-sky-800 rounded-3xl shadow-2xl overflow-hidden p-6">
                <h2 class="text-xl text-white font-bold mb-4 text-center">Reset Password</h2>
                
                <?php if (!empty($fp_error)): ?>
                    <p class="bg-red-100 text-red-600 p-2 rounded mb-4 text-center"><?= $fp_error ?></p>
                <?php endif; ?>
                <?php if (!empty($fp_success)): ?>
                    <p class="bg-green-100 text-green-600 p-2 rounded mb-4 text-center"><?= $fp_success ?></p>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="forgot_password" value="1">
                    <div>
                        <input type="text" name="username" placeholder="Username" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <input type="password" name="new_password" placeholder="New Password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" onclick="closeForgotModal()" class="bg-gray-600 hover:bg-gray-500 text-white py-2 px-4 rounded-lg transition">Cancel</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition">Reset</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function openForgotModal() {
            document.getElementById('forgotPasswordModal').classList.remove('hidden');
            document.getElementById('forgotPasswordModal').classList.add('flex');
            document.getElementById('dashboardWrapper').classList.add('blurred');
        }

        function closeForgotModal() {
            document.getElementById('forgotPasswordModal').classList.add('hidden');
            document.getElementById('forgotPasswordModal').classList.remove('flex');
            document.getElementById('dashboardWrapper').classList.remove('blurred');
        }
    </script>

        <div id="callModal" class="fixed inset-0 hidden items-start justify-center bg-black/70 backdrop-blur-md z-50 pt-20">
            <div class="relative w-full max-w-sm bg-stone-900 rounded-3xl shadow-2xl overflow-hidden">
                <style>
                    @keyframes ping {
                        0% { transform: scale(1); opacity: 0.6; }
                        75%, 100% { transform: scale(2); opacity: 0; }
                    }
                    .animate-ping { animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; }
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

        <script>
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
    </div>
</body>
</html>
