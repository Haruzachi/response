<?php
require_once "../config/db.php";
session_start();

if (!isset($_SESSION['pending_user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['pending_user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = trim($_POST['otp']);

    $stmt = $conn->prepare("SELECT otp_code, otp_expiration FROM users WHERE id=?");
    $stmt->execute([$user['id']]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record) {
        $now = date("Y-m-d H:i:s");
        if ($otp === $record['otp_code'] && $now <= $record['otp_expiration']) {
            $clear = $conn->prepare("UPDATE users SET otp_code=NULL, otp_expiration=NULL WHERE id=?");
            $clear->execute([$user['id']]);

            $_SESSION['user'] = $user;
            unset($_SESSION['pending_user']);

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
            echo "<script>alert('Invalid or expired OTP');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
</head>
<body>
    <h2>Two-Factor Authentication</h2>
    <p>Enter the 6-digit code sent to your email.</p>
    <form method="POST">
        <input type="text" name="otp" maxlength="6" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>