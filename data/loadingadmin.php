<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loading...</title>

  <!-- Boxicons for siren icon -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <style>
    /* ===== Reset & Layout ===== */
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      font-family: Arial, sans-serif;
      background: #0d0d0d;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ===== Loading Container ===== */
    #loading-screen {
      text-align: center;
      color: #fff;
    }

    /* Siren Icon Styling */
    .siren-icon {
      font-size: 90px;
      color: #ff1e1e;
      animation: spin 2s linear infinite, glow 1.5s ease-in-out infinite;
      display: inline-block;
      margin-bottom: 20px;
    }

    /* Siren Rotation */
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Glowing Effect */
    @keyframes glow {
      0% { text-shadow: 0 0 10px #ff1e1e; }
      50% { text-shadow: 0 0 25px #ff3333, 0 0 50px #ff0000; }
      100% { text-shadow: 0 0 10px #ff1e1e; }
    }

    /* Loading Text */
    .loading-text {
      font-size: 1.8rem;
      font-weight: bold;
      color: #fff;
      letter-spacing: 2px;
      animation: fade-text 1.2s ease-in-out infinite;
    }

    @keyframes fade-text {
      0%, 100% { opacity: 0.5; }
      50% { opacity: 1; }
    }

    /* Sub Text */
    .loading-sub {
      margin-top: 10px;
      font-size: 1rem;
      color: #bbb;
    }
  </style>
</head>
<body>

  <!-- ===== Loading Screen ===== -->
  <div id="loading-screen">
    <div class="siren-icon">
      <i class='bx bxs-bell'></i>
    </div>
    <div class="loading-text">INITIALIZING SYSTEM...</div>
    <div class="loading-sub">Please wait while we prepare your dashboard</div>
  </div>

  <!-- ===== JavaScript Redirect ===== -->
  <script>
    window.addEventListener("load", function() {
      setTimeout(() => {
        window.location.href = "../admin/dashboard.php"; // Redirect to the main dashboard
      }, 3000); // 3-second delay
    });
  </script>

</body>
</html>