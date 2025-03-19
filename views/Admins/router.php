<?php 
ob_start();
include './home/index.php';

// Require necessary files
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
  

};

    

include './home/footer.php';
ob_end_flush();
