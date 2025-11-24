<?php
session_start();

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header('Location: index.php?controller=auth&action=login');
exit;

