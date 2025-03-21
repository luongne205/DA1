<?php 
ob_start();
include './home/index.php';

// Require necessary files
require_once('../../commons/env.php'); // Environmental variables
require_once '../../commons/function.php'; // Utility functions
require_once '../../controllers/AdminController.php';
require_once '../../models/AdminModels.php';

// Route  
$act = $_GET['act'] ?? '/';  

// Match to route the request to the corresponding controller action  
match ($act) {  

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
