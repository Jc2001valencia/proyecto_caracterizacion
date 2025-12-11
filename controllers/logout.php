<?php
// controllers/logout.php
session_start();
session_destroy();
header('Location: ../index.php?action=landing');
exit;
?>