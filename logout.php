<?php
session_start();
session_unset();
session_destroy();
header("Location: https://portale.calvino.edu.it/api/auth/logout");
exit;
?>
