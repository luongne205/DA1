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
// Match to route the request to the corresponding controller action  
match ($act) {

    '/' => (new HomeController())->home(),

    // Categories (Danh Mục)  
    'addDm' => (new HomeController())->formAddDm(),
    'listDm' => (new HomeController())->listDm(),
    'postDm' => (new HomeController())->postDm(),
    'xoadm' => (new HomeController())->deleteDm(),
    'formSuaDm' => (new HomeController())->formSuaDm(),
    'postSuaDm' => (new HomeController())->updateDm(),

    // Products (Sản Phẩm)  
    'addSP' => (new HomeController())->formAddSP(),
    'listSP' => (new HomeController())->listSP(),
    'postSP' => (new HomeController())->postSP(),
    'xoasp' => (new HomeController())->deleteSP(),
    'formSuaSP' => (new HomeController())->formSuaSP(),
    'suasp' => (new HomeController())->formSuaSP(),
    'updateSP' => (new HomeController())->updateSP(),
};



include './home/footer.php';
ob_end_flush();
