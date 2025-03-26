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
                }}}}

            