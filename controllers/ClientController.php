<?php
class ClientController
{
    public $modelClients;

    public function __construct()
    {
        $this->modelClients = new ClientModels();
    }


    public function home()
    {
        $listDanhMuc = $this->modelClients->getAllDanhMuc();
        $datas = $this->modelClients->getAllProductsByCategory();
        $top10 = $this->modelClients->getTop10Sp();


        if (isset($_SESSION['buy'])) {
            unset($_SESSION['buy']);
        }
        $newestProducts = $this->modelClients->getNewestProducts(4); // Mặc định lấy 4 sản phẩm mới nhất
        // var_dump($top10);
        // var_dump($listDanhMuc);
        require_once '../views/Clients/home.php';
        // require_once '../views/Clients/header.php';
        // require_once '../views/Clients/footer.php';
    }
    // Cập nhật tài khoản
    public function updateAcount()
    {
        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
            extract($_SESSION['user']);
            $imgPath = './../' . $avatar;
            $avt = $imgPath ? $imgPath : './img/userNo.jpg';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                $address = $_POST['address'];
                $sdt = $_POST['sdt'];

                // Xử lý ảnh đại diện
                $avatar = $avt;
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
                    $file_save = uploadFile($_FILES['avatar'], 'uploads/users');
                    if ($file_save) {
                        $avatar = $file_save; // Gán đường dẫn ảnh mới
                    } else {
                        echo "Lỗi khi lưu tệp ảnh.";
                        return;
                    }
                }
            
                   // Cập nhật thông tin
                   $current_img = $_POST['current_img'];
                   if ($this->modelClients->updateAccout($id, $username, $email, $avatar ?: $current_img, $address, $sdt)) {
                       // Cập nhật thành công
                       // Lấy lại thông tin người dùng mới
                       $_SESSION['user'] = $this->modelClients->getAccountById($id);
                       header('Location: http://localhost/base_test_DA1/public/');
                       exit;
                   } else {
                       echo "Lỗi khi cập nhật tài khoản.";
                   }
               } else {
                   require '../views/Clients/UpdateTaiKhoan/updateTk.php';
                   exit;
               }
           }
       
           // Gọi view cập nhật tài khoản
           require_once '../views/Clients/UpdateTaiKhoan/updateTk.php';
       } 
         
       
       public function login() {
           require_once '../views/Clients/accounts/login.php';
       }
       public function postLogin() {
           if ($_SERVER['REQUEST_METHOD'] === 'POST') {
               $userOrEmail = $_POST['username']; 
               $pass = $_POST['password'];
               $check = $this->modelClients->checkAcc($userOrEmail, $pass);
               if (is_array($check)) {
                   if($check['active'] === 1){
                       echo '<script>
                       // Khắc phục lỗi mất thanh cuộn
                       document.body.style.overflowX = "auto"; 
                       document.body.style.overflowY = "auto";  
                       Swal.fire({
                           text: "Tài khoản của bạn đã bị khoá",
                           icon: "error",
                           confirmButtonColor: "#C62E2E"
                           });
                     </script>';
                     require_once '../views/Clients/accounts/login.php';
                   }else{
                       $_SESSION['user'] = $check;
                       echo '<script>
                       // Khắc phục lỗi mất thanh cuộn
                       document.body.style.overflowX = "auto"; 
                       document.body.style.overflowY = "auto";  
                       Swal.fire({
                           text: "Đăng nhập thành công",
                           icon: "success",
                           confirmButtonColor: "#C62E2E"
                           });
                     </script>';
                       header('Location: http://localhost/base_test_DA1/public/');
                       exit;
                   }
               } else {
                   $thongbao = 'Tài khoản hoặc mật khẩu không đúng';
               }
           } else {
               echo 'Lỗi';
           }
           require_once '../views/Clients/accounts/login.php';
       }
   
       public function logOut(){
           session_unset();
           session_destroy();
           header('Location: http://localhost/base_test_DA1/public/');
       }
       public function signUp() {
           require_once '../views/Clients/accounts/signUp.php';
       }
       // Đăng kí tài khoản
       public function addAccount(){
           if($_SERVER['REQUEST_METHOD'] === 'POST'){
               $username = $_POST['username'];
               $email = $_POST['email'];
               $sdt = $_POST['sdt'];
               $password = $_POST['password'];
               if($this->modelClients->addAccount($username, $email, $password, $sdt)){
                   header('location: http://localhost/base_test_DA1/public/');
               }
           }
       }
            