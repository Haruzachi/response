<?php
session_start();
session_unset();
session_destroy();
header("Location: ../data/loadinglogout.php");
exit;
?>