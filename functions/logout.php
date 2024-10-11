<?php
require_once '../vendor/autoload.php';

session_start();
session_unset();
session_destroy();

header('Location: ../login');
exit;
