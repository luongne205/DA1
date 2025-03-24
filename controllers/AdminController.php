<?php
class HomeController
{
    public $modelAdmin;

    public function __construct()
    {
        $this->modelAdmin = new AdminModels();
    }

    public function home()
    {
        // $listBillStatus = $this->modelAdmin->getAllBill_3();
        // $sumBills = $this->modelAdmin->getTotalOrders();
        // $sumTotalBill = $this->modelAdmin->sumTotalOrders();
        // $sumProducts = $this->modelAdmin->sumProducts();
        // $sumComments = $this->modelAdmin->sumComments();
        // $revenues = $this->modelAdmin->getDailyRevenue();
        // print_r($revenues);
        require_once '../../views/Admins/home/home.php';
    }


    // Danh Mục
    public function formAddDm()
    {
        require_once '../../views/Admins/DanhMuc/formAddDM.php';
    }

    public function listDm()
    {
        $listDanhMuc = $this->modelAdmin->getAllDanhMuc();
        require_once '../../views/Admins/DanhMuc/listDm.php';
    }
    public function postDm()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $name = $_POST['name'];
            if ($this->modelAdmin->postDm(name: $name)) {
                header( 'location: router.php?act=listDm');
                exit;
            }
        }
    }
    public function deleteDm()
    {
        $id = $_GET['id'];
        $record = $this->modelAdmin->getDmById($id);
        if ($record) { // Kiểm tra xem bản ghi có tồn tại không
            if ($this->modelAdmin->deleteDm($id)) {
                header('Location: router.php?act=listDm');
                exit;
            } else {
                echo "Không thể xóa danh mục.";
            }
        } else {
            echo "Danh mục không tồn tại.";
        }
    }

    public function formSuaDm()
    {
        $id = $_GET['id'];
        $danhMuc = $this->modelAdmin->getDmById($id);
        require_once '../../views/Admins/DanhMuc/updateDm.php';
    }

    public function updateDm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];

            if ($this->modelAdmin->updateDm($id, $name)) {
                header('Location: router.php?act=listDm');
                exit;
            }
        }
    }

    ///SanPham
    public function formAddSP()
    {
        $listDanhMuc = $this->modelAdmin->getAllDanhMuc();
        require_once '../../views/Admins/SanPham/formAddSP.php';
    }
    public function listSP()
    {
        $listDanhMuc = $this->modelAdmin->getAllDanhMuc();

        $categoryId = isset($_GET['category']) ? $_GET['category'] : null;
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : null;

        $listProducts = $this->modelAdmin->getAllProductsByCategory($categoryId, $searchTerm);
        // var_dump($listProducts);
        require_once '../../views/Admins/SanPham/listSP.php';
    }
    public function postSP()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $namesp = $_POST['namesp'];
            $price = $_POST['price'];
            $mota = $_POST['mota'];
            $iddm = $_POST['iddm'];
            $quantity = $_POST['id_soluong'];

            // Kiểm tra file ảnh  
            if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $file_save = uploadFile($_FILES['img'], 'uploads');
                if ($file_save) {
                    if ($this->modelAdmin->postSP($namesp, $price, $file_save, $mota, $iddm, $quantity)) {
                        header('Location: router.php?act=listSP');
                        exit();
                    } else {
                        echo "Lỗi khi thêm sản phẩm vào cơ sở dữ liệu.";
                    }
                } else {
                    echo "Lỗi khi lưu tệp ảnh.";
                }
            }
        } else {
            header('Location: /router.php?act=listSP');
            exit();
        }
    }

    public function deleteSP()
    {
        $id = $_GET['id'];

        $record = $this->modelAdmin->getSPById($id);

        if ($record) {
            if ($this->modelAdmin->deleteSP($id)) {
                header('Location: router.php?act=listSP');
                exit;
            } else {
                echo "Không thể xóa sản phẩm.";
            }
        } else {
            echo "Sản phẩm không tồn tại.";
        }
    }
    public function formSuaSP()
    {
        $id = $_GET['id'];
        $product = $this->modelAdmin->getSPById($id);
        $listDanhMuc = $this->modelAdmin->getAllDanhMuc();

        if ($product) {
            require_once '../../views/Admins/SanPham/formupdateSP.php';
        } else {
            echo "Sản phẩm không tồn tại.";
        }
    }


    public function updateSP()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $namesp = $_POST['namesp'];
            $price = $_POST['price'];
            $mota = $_POST['mota'];
            $iddm = $_POST['iddm'];
            $quantity = $_POST['quantity'];
            $img = ''; // Xử lý ảnh mới  

            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                // Xử lý upload ảnh mới  
                $file_save = uploadFile($_FILES['img'], 'uploads');
                if ($file_save) {
                    $img = $file_save; // Gán đường dẫn ảnh mới  
                } else {
                    echo "Lỗi khi lưu tệp ảnh.";
                    return;
                }
            }

            // Cập nhật sản phẩm
            $current_img = $_POST['current_img'];
            if ($this->modelAdmin->updateSP($id, $namesp, $price, $img ?: $current_img, $mota, $iddm, $quantity)) {
                header('Location: router.php?act=listSP');
                exit;
            } else {
                echo "Lỗi khi cập nhật sản phẩm.";
            }
        } else {
            header('Location: router.php?act=listSP');
            exit();
        }
    }
}
