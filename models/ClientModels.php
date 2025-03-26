<?php 

class ClientModels 
{
    public $conn;

    public function __construct() { // Hàm khởi tạo kết nối đối tượng
        $this->conn = connectDB();
    }

    // Đăng kí tài khoản
    public function addAccount($username, $email, $password, $sdt){
        $sql = 'INSERT INTO accounts (username, email, password, sdt) VALUES (:username, :email, :password, :sdt)';
        $stmt = $this->conn->prepare($sql);
        $stmt -> execute(['username' => $username, 'email' => $email, 'password' => $password, 'sdt' => $sdt]);
        return true;
    }

    // Cập nhật tài khoản
    public function getAccountById($id){
        try {
            $sql = 'SELECT * FROM accounts WHERE id ='.$id;
    
            $stmt = $this->conn->prepare($sql);
        
            $stmt->execute();

            return $stmt->fetch();
            
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
    public function updateAccout($id, $username, $email, $avatar, $address, $sdt){
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
    

}

