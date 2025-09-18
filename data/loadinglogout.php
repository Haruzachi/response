<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="../image/x-icon" href="../img/logocircle.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logging Out...</title>

  <!-- Boxicons for bell icon -->
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
      color: #fff;
    }

    /* ===== Logout Container ===== */
    .logout-screen {
      text-align: center;
    }

    /* Bell Icon */
    .logout-bell {
      font-size: 100px;
      color: #ff1e1e;
      display: inline-block;
      animation: shake-bell 1.5s ease-in-out infinite, glow 1.5s ease-in-out infinite;
      margin-bottom: 20px;
    }

    /* Bell Shake Animation */
    @keyframes shake-bell {
      0% { transform: rotate(0deg); }
      20% { transform: rotate(-20deg); }
      40% { transform: rotate(20deg); }
      60% { transform: rotate(-15deg); }
      80% { transform: rotate(15deg); }
      100% { transform: rotate(0deg); }
    }

    /* Glow Effect */
    @keyframes glow {
      0% { text-shadow: 0 0 10px #ff1e1e; }
      50% { text-shadow: 0 0 25px #ff3333, 0 0 50px #ff0000; }
      100% { text-shadow: 0 0 10px #ff1e1e; }
    }

    /* Logging Out Text */
    .logout-text {
      font-size: 1.8rem;
      font-weight: bold;
      letter-spacing: 2px;
      margin-top: 10px;
      animation: fade-text 1.2s ease-in-out infinite;
    }

    @keyframes fade-text {
      0%, 100% { opacity: 0.6; }
      50% { opacity: 1; }
    }

    /* Subtext */
    .logout-sub {
      margin-top: 8px;
      font-size: 1rem;
      color: #ccc;
    }
  </style>
</head>
<body>

  <!-- ===== Logout Screen ===== -->
  <div class="logout-screen">
    <div class="logout-bell">
      <i class='bx bxs-bell'></i>
    </div>
    <div class="logout-text">Logging Out...</div>
    <div class="logout-sub">You will be redirected shortly</div>
  </div>

  <!-- ===== JavaScript Redirect ===== -->
  <script>
    // Wait for 3 seconds, then redirect to login page
    setTimeout(() => {
      window.location.href = "../index/dashboard.php"; // Change to your login page
    }, 3000);
  </script>

</body>
</html>