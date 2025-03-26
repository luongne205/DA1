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
       public function chitietSP(){
        require_once '../views/Clients/productDetails/chitietSP.php';
    }
   
    

    public function sanphamchitiet() {
        $id = $_GET['id']; 
        $sanPhamChiTiet = $this->modelClients->getSPById($id);
        extract($sanPhamChiTiet); 
        $soLuongConLai = $sanPhamChiTiet['quantity'] > 0 ?  $sanPhamChiTiet['quantity'] -1 : 0;
        $sanPhamChung = $this->modelClients->load_sanpham_cungloai($id, $iddm); 
        $sanPhamNoiBat = $this->modelClients->getTop10Sp(); 
        $comments = $this->modelClients->getCommentsByProductId($id);
        $soluongMua = $sanPhamChiTiet['quantity'] > 0 ?  1 : 0;
        $tongtien = $sanPhamChiTiet['price'];
        
        require_once '../views/Clients/productDetails/chitietSP.php'; 
    }
    public function tangGiamMuaNgay(){
        $id = $_GET['id'];
    
        // Kiểm tra nếu có flag reset_session, reset thông tin sản phẩm trong session
        if (isset($_SESSION['reset_session']) && $_SESSION['reset_session'] == true) {
            unset($_SESSION['buy'][$id]);  // Xóa thông tin sản phẩm trong session
            $_SESSION['reset_session'] = false;  // Reset flag sau khi đã xử lý
        }
        
        // Lấy thông tin sản phẩm chi tiết
        $sanPhamChiTiet = $this->modelClients->getSPById($id);
        extract($sanPhamChiTiet);
        
        // Kiểm tra số lượng sản phẩm còn trong kho
        $quantityProduct = $this->modelClients->getCartQuantity($id);
        $soLuongConLai = isset($_POST['soluongconlai']) ? $_POST['soluongconlai'] : $quantityProduct;
        
        // Kiểm tra số lượng trong session cho sản phẩm cụ thể
        $soluongMua = isset($_POST['soluongMua']) ?  $_POST['soluongMua']: 1;
        $tongtien = isset($_POST['tongtien']) ?  $_POST['tongtien'] : $sanPhamChiTiet['price']; 
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Kiểm tra nút "tăng"
            if (isset($_POST['tang'])) {
                if ($quantityProduct > $soluongMua) { 
                    $soluongMua++;
                    $soLuongConLai--; // Giảm đi 1 khi tăng số lượng
                    $tongtien = $sanPhamChiTiet['price'] * $soluongMua;  // Tính lại tổng tiền
                    $_SESSION['buy'][$id] = [
                        'id' => $id,
                        'quantity' => $soluongMua,
                        'thanhtien' => $tongtien,
                        'soluongconlai' => $soLuongConLai 
                    ];
                } else {
                    echo '<script>
                        Swal.fire({
                            text: "Số lượng trong kho đã hết!",
                            icon: "warning",
                            confirmButtonColor: "#C62E2E"
                        });
                    </script>';
                }
            }
    
            // Kiểm tra nút "giảm"
            if (isset($_POST['giam'])) {
                if ($soluongMua > 1) {
                    $soluongMua--;
                    $soLuongConLai++; // Tăng lên 1 khi giảm số lượng
                    $tongtien = $sanPhamChiTiet['price'] * $soluongMua;  // Tính lại tổng tiền
                    $_SESSION['buy'][$id] = [
                        'id' => $id,
                        'quantity' => $soluongMua,
                        'thanhtien' => $tongtien,
                        'soluongconlai' => $soLuongConLai 
                    ];
                } else {
                    echo '<script>
                        Swal.fire({
                            text: "Giới hạn giảm là 1!",
                            icon: "error",
                            confirmButtonColor: "#C62E2E"
                        });
                    </script>';
                }
            }
        }
    
        // Tính lại tổng tiền sau khi thay đổi số lượng và lưu vào session
        $price = $this->modelClients->getPriceByPro($id);
        $tongtien = $price * $soluongMua;  // Cập nhật lại tổng tiền khi số lượng thay đổi
    
        // Lưu lại vào session nếu không có thao tác tăng/giảm (tức là lần đầu truy cập)
        if (!isset($_SESSION['buy'][$id])) {
            $_SESSION['buy'][$id] = [
                'id' => $id,
                'quantity' => $soluongMua,
                'thanhtien' => $tongtien,
                'soluongconlai' => $soLuongConLai
            ];
        }
        // Lấy các sản phẩm liên quan
        $sanPhamChung = $this->modelClients->load_sanpham_cungloai($id, $iddm);
        $sanPhamNoiBat = $this->modelClients->getTop10Sp();
        $comments = $this->modelClients->getCommentsByProductId($id);
    
        require_once '../views/Clients/productDetails/chitietSP.php';
    }
    

    
    public function search() {
        if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['search'])){
            $search = $_POST['search'];
            $datasSearch = $this->modelClients->getAllSP($search);

            // var_dump($datasSearch);
        }else{
            header('location: http://localhost/base_test_DA1/public/');
        }
        require_once '../views/Clients/products/products.php';
    }

    // Giỏ hàng
    public function carts() {
        // Kiểm tra nếu giỏ hàng chưa tồn tại, khởi tạo mảng giỏ hàng
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $userRole = $_SESSION['user']['role'] ?? null;
        if( $userRole == 1){
            
            echo '<script>
            // Khắc phục lỗi mất thanh cuộn
            document.body.style.overflowX = "auto"; 
            document.body.style.overflowY = "auto";  
            Swal.fire({
                text: "Admin không thể mua hàng",
                icon: "warning",
                confirmButtonColor: "#C62E2E"
                });
        </script>';
        $listCarts= $this->modelClients->listCartByUser($_SESSION['user']['id']);
        // var_dump($listCarts);
        // header('location: http://localhost/base_test_DA1/public/');
        $this->home();
        exit();
        }

        if(!isset($_SESSION['user'])){
            echo '<script>
            // Khắc phục lỗi mất thanh cuộn
                    document.body.style.overflowX = "auto"; 
                    document.body.style.overflowY = "auto";  
                    Swal.fire({
                        text: "Bạn cần đăng nhập để mua hàng",
                        icon: "error",
                        confirmButtonColor: "#C62E2E"
                    });
                </script>';
                $this->login();
                exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
            $id = $_POST['id'];
            // Xem số lượng trong bảng products là bao nhiêu
            $currentQuantity = $this->modelClients->getCartQuantity( $id);
            if($currentQuantity  > 0){
                $namesp = $_POST['namesp'];
                $img = $_POST['img'];
                $price = (float) $_POST['price'];
                $mota = $_POST['mota'];
                $sumQuantityMuaNgay =  $_SESSION['buy'][$id]['quantity'] ?? 1;
                // var_dump($_SESSION['buy'][$id]);
                // var_dump($sumQuantityMuaNgay); die();
                // $soluong = isset($_POST['soluong']) ? $_POST['soluong'] : 1;
                $soluong = $sumQuantityMuaNgay;
                // var_dump($soluong); die();
                $getSoLuongCarts = $this->modelClients->getSoLuongCarts($id, $_SESSION['user']['id']);
                // $tongTien = ($getSoLuongCarts + 1 ) * $price;
                $tongTien = ($getSoLuongCarts + $sumQuantityMuaNgay ) * $price;
                 // Xem số lượng trong bảng products là bao nhiêu
                 $newPro = $currentQuantity - 1;
            
                // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
                $exists = $this->modelClients->checkCarts($_SESSION['user']['id'], $id);
                
                if ($exists) {
                    $this->modelClients->updateQuantity($_SESSION['user']['id'], $id, $getSoLuongCarts + $sumQuantityMuaNgay, $tongTien);
                
                    // Cập nhật số lượng kho nếu còn hàng
                    if ($currentQuantity > 0) {
                         $this->modelClients->updateQuantityProducts($id, $currentQuantity - $sumQuantityMuaNgay );
                        $this->modelClients->updateRemainingQuantity($id, $currentQuantity - $sumQuantityMuaNgay ?? $getSoLuongCarts  );
                    } else {
                        echo '<script>
                                     // Khắc phục lỗi mất thanh cuộn
                                    document.body.style.overflowX = "auto"; 
                                    document.body.style.overflowY = "auto";  
                                    Swal.fire({
                                        text: "Sản phẩm đã hết hàng!",
                                        icon: "error",
                                        confirmButtonColor: "#C62E2E"
                                    });
                                </script>';
                                exit(); 
                    }
                    $listCarts= $this->modelClients->listCartByUser($_SESSION['user']['id']);
                    require_once '../views/Clients/carts/cart.php';
                }
            
                // Nếu sản phẩm chưa tồn tại, thêm sản phẩm mới vào giỏ hàng
                if (!$exists) {
                    $newProduct = [
                        'id' => $id,
                        'namesp' => $namesp,
                        'img' => $img,
                        'price' => $price,
                        'soluong' => $soluong,
                        'tongTien' => $tongTien,
                        'mota' => $mota,
                        'remaining_quantity' => $currentQuantity,
                    ];
                    $this->modelClients->updateQuantityProducts($id, $currentQuantity - $soluong);
                    array_push($_SESSION['cart'], $newProduct);
                    $this->modelClients->addCarts($_SESSION['user']['id'], $id, $img, $namesp, $price, $soluong , $tongTien, $mota, $currentQuantity - $soluong);
                }
                // Lấy danh sách giỏ hàng của người dùng
                $listCarts = $this->modelClients->listCartByUser($_SESSION['user']['id']);
                require_once '../views/Clients/carts/cart.php';
                // Chuyển hướng về trang hiện tại để tránh gửi lại POST
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }else{
                $previousPage = $_SERVER['HTTP_REFERER'];
                 // Hiển thị thông báo và chuyển hướng
                    echo "<script>
                    alert('Sản phẩm trong kho đã hết!');
                    window.location.href = '$previousPage';
                </script>";
            }
            
            
            if(!isset($_SESSION['user'])){
            echo '<script>
                    // Khắc phục lỗi mất thanh cuộn
                    document.body.style.overflowX = "auto"; 
                    document.body.style.overflowY = "auto";  
                    Swal.fire({
                        text: "Bạn cần đăng nhập để truy cập giỏ hàng",
                        icon: "warning",
                        confirmButtonColor: "#C62E2E"
                        });
                  </script>';
                  require_once '../views/Clients/accounts/login.php';
                //   $listCarts= $this->modelClients->listCartByUser($_SESSION['user']['id']);
                  // var_dump($listCarts);
            }
        }
       
        $listCarts= $this->modelClients->listCartByUser($_SESSION['user']['id']);
        require_once '../views/Clients/carts/cart.php';
    }


    
    // Xử lí tăng thông số
    public function tangGiam(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])){
            if(isset($_POST['tang'])){
                $id = $_GET['id'];
                $idpro = $this->modelClients->getIdProduct($id);
                $price = $this->modelClients->getPriceByIdCart($id);
                // Gọi số lượng sản phẩm hiện có trong đơn hàng ra
                $QuantityCart = $this->modelClients->getSoLuongById($id);
                $thanhTien = $price * ($QuantityCart + 1);
                // Lấy số lượng của sản phẩm đó trong bảng products;
                $quantityPro = $this->modelClients->getCartQuantity($idpro);
                // Trừ đi số lượng trong products và số lượng còn lại trong carts
               if($quantityPro > 0){
                    $this->modelClients->updateQuantityProducts($idpro, $quantityPro - 1 );
                    $this->modelClients->updateRemainingQuantity($idpro, $quantityPro - 1);

                    // Update các chỉ số xoay quanh số lượng
                    $this->modelClients->updateQuantity($_SESSION['user']['id'], $idpro, $QuantityCart + 1, $thanhTien);
               }else{
                        echo '<script>
                        // Khắc phục lỗi mất thanh cuộn
                            document.body.style.overflowX = "auto"; 
                            document.body.style.overflowY = "auto";  
                            Swal.fire({
                                text: "Sản phẩm đã hết hàng!",
                                icon: "error",
                                confirmButtonColor: "#C62E2E"
                            });
                        </script>';
               } 
            }elseif(isset($_POST['giam'])){
                $id = $_GET['id'];
                $idpro = $this->modelClients->getIdProduct($id);
                $price = $this->modelClients->getPriceByIdCart($id);
                // Gọi số lượng sản phẩm hiện có trong đơn hàng ra
                $QuantityCart = $this->modelClients->getSoLuongById($id);
                $thanhTien = $price * ($QuantityCart - 1);
                // Lấy số lượng của sản phẩm đó trong bảng products;
                $quantityPro = $this->modelClients->getCartQuantity($idpro);
                // Trừ đi số lượng trong products và số lượng còn lại trong carts
               if($QuantityCart <= 1){
                echo '<script>
                // Khắc phục lỗi mất thanh cuộn
                    document.body.style.overflowX = "auto"; 
                    document.body.style.overflowY = "auto";  
                    Swal.fire({
                        text: "Giới hạn giảm là 1!",
                        icon: "error",
                        confirmButtonColor: "#C62E2E"
                    });
                </script>';
               }else{
                $this->modelClients->updateQuantityProducts($idpro, $quantityPro + 1 );
                $this->modelClients->updateRemainingQuantity($idpro, $quantityPro + 1);

                // Update các chỉ số xoay quanh số lượng
                $this->modelClients->updateQuantity($_SESSION['user']['id'], $idpro, $QuantityCart - 1, $thanhTien);
    
               } 
            }
        }
        $listCarts = $this->modelClients->listCartByUser($_SESSION['user']['id']);
        require_once '../views/Clients/carts/cart.php';
    }