<?php
/**
 * Auth API
 * Asisten Akademik Harian
 */

require_once '../config/database.php';
require_once '../config/session.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'logout':
        destroySession();
        header('Location: ../index.php');
        exit;
        break;

    default:
        header('Location: ../index.php');
        exit;
}
