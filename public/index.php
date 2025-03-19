<?php 
session_start();
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = null; // Hoặc giá trị mặc định
}
ob_start();
include '../views/Clients/header.php';


// Require file Common
require_once '../commons/env.php'; // Khai báo biến môi trường
require_once '../commons/function.php'; // Hàm hỗ trợ

// Require toàn bộ file Controllers
require_once '../controllers/AdminController.php';

// Require toàn bộ file Models
require_once '../models/AdminModels.php';
// Route
$act = $_GET['act'] ?? '/';


include '../views/Clients/footer.php';
ob_end_flush();

