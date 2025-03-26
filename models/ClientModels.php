<?php

class ClientModels
{
    public $conn;

    public function __construct()
    { // Hàm khởi tạo kết nối đối tượng
        $this->conn = connectDB();
    }

    // Đăng kí tài khoản
    public function addAccount($username, $email, $password, $sdt)
    {
        $sql = 'INSERT INTO accounts (username, email, password, sdt) VALUES (:username, :email, :password, :sdt)';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password, 'sdt' => $sdt]);
        return true;
    }

    // Cập nhật tài khoản
    public function getAccountById($id)
    {
        try {
            $sql = 'SELECT * FROM accounts WHERE id =' . $id;

            $stmt = $this->conn->prepare($sql);

            $stmt->execute();

            return $stmt->fetch();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
    public function updateAccout($id, $username, $email, $avatar, $address, $sdt)
    {
        try {
            $sql = "UPDATE accounts 
                    SET 
                        username = :username, 
                        email = :email, 
                        avatar = :avatar, 
                        address = :address, 
                        sdt = :sdt
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'avatar' => $avatar,
                'address' => $address,
                'sdt' => $sdt,
                'id' => $id
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
    // Login
    public function checkAcc($userOrEmail, $pass)
    {
        $sql = 'SELECT * FROM accounts WHERE (username = :userOrEmail OR email = :userOrEmail) AND password = :pass';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':userOrEmail' => $userOrEmail,
            ':pass' => $pass
        ]);
        return $stmt->fetch();
    }

    // đổi mật khẩu


    // Danh mục
    public function getAllDanhMuc()
    {
        try {
            $sql = 'SELECT * FROM categories ORDER BY id DESC';

            $stmt = $this->conn->prepare($sql);

            $stmt->execute();

            return $stmt->fetchAll();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    // Lấy sản phẩm theo từng nhóm danh mục
    public function getAllProductsByCategory()
    {
        $sql = "SELECT 
                        products.id, 
                        products.namesp, 
                        products.price, 
                        products.img,
                        products.mota,
                        products.quantity,
                        categories.name AS category_name
                    FROM categories
                    LEFT JOIN products ON categories.id = products.iddm
                    ORDER BY categories.name, products.price ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTop10Sp()
    {
        try {
            // Câu lệnh SQL để lấy 10 sản phẩm có lượt xem cao nhất
            $sql = "SELECT 
                        p.id AS id, 
                        p.namesp AS namesp, 
                        p.img,
                        p.price,
                        SUM(bi.quantity) AS total_quantity_sold
                    FROM bill_items bi
                    JOIN bills b ON bi.bill_id = b.id
                    JOIN products p ON bi.product_id = p.id
                    WHERE b.bill_status = 3 
                    GROUP BY bi.product_id
                    ORDER BY total_quantity_sold DESC
                    LIMIT 10";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    // Tìm kiếm theo sản phẩm
    public function getAllSP($search)
    {
        try {
            $sql = "SELECT * FROM products WHERE namesp LIKE '%$search%'";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    public function getSPById($id)
    {
        try {
            $sql = 'SELECT * FROM products WHERE id =' . $id;

            $stmt = $this->conn->prepare($sql);

            $stmt->execute();

            return $stmt->fetch();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
    public function load_sanpham_cungloai($id, $iddm)
    {
        try {
            $sql = "SELECT * FROM products WHERE iddm = " . $iddm . " AND id <> " . $id;
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    public function getNewestProducts($limit = 4)
    {
        try {
            $sql = "SELECT * FROM products ORDER BY id DESC LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Trả về mảng rỗng nếu không có kết quả
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }
    ///comment
    public function addComment($idpro, $idUser, $noidung, $time)
    {
        try {
            $sql = "INSERT INTO comments (idpro, idUser, noidung, time) 
                VALUES (:idpro, :idUser, :noidung, :time)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':idpro' => $idpro,
                ':idUser' => $idUser,
                ':noidung' => $noidung,
                ':time' => $time,
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Ghi log lỗi
            file_put_contents('error_log.txt', $e->getMessage(), FILE_APPEND);
            return false;
        }
    }

    // Giỏ hàng
    public function getProductById($id)
    {
        try {
            $sql = 'SELECT * FROM products WHERE id =' . $id;

            $stmt = $this->conn->prepare($sql);

            $stmt->execute();

            return $stmt->fetch();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Kiểm tra xem giỏ hàng có tồn tại sản phẩm
    public function checkCarts($idUser, $idProduct)
    {
        try {
            $sql = 'SELECT * FROM carts WHERE idUser = :idUser AND idpro = :idProduct';

            $stmt = $this->conn->prepare($sql);

            $stmt->execute(['idUser' => $idUser, 'idProduct' => $idProduct]);

            return $stmt->fetch();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
    // Lưu sản phẩm trong giỏ hàng vào cart
    public function addCarts($idUser, $idpro, $img, $name, $price, $soluong, $thanhtien, $mota, $remaining_quantity)
    {
        // SQL để thêm sản phẩm vào giỏ hàng
        $sql = 'INSERT INTO carts (idUser, idpro, img, name, price, soluong, thanhtien, mota, created_at, remaining_quantity) 
            VALUES (:idUser, :idpro, :img, :name, :price, :soluong, :thanhtien, :mota, NOW(), :remaining_quantity)';

        // Chuẩn bị câu lệnh SQL
        $stmt = $this->conn->prepare($sql);

        // Thực thi câu lệnh với các tham số
        $stmt->execute([
            'idUser' => $idUser,
            'idpro' => $idpro,
            'img' => $img,
            'name' => $name,
            'price' => $price,
            'soluong' => $soluong,
            'thanhtien' => $thanhtien,
            'mota' => $mota,
            'remaining_quantity' => $remaining_quantity
        ]);

        return true;
    }
    // Lấy số lượng trong bảng carts để kiểm tra còn tăng lên chứ
    public function getCartQuantity($id)
    {
        try {
            $sql = 'SELECT quantity FROM products WHERE id = :id';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'id' => $id
            ]);

            $result = $stmt->fetch();
            return $result ? $result['quantity'] : 0; // Nếu không có sản phẩm
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
}
