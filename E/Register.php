<?php



class Register
{

    private Database $db;
    /**
     * @var mixed|null
     */

    private Login $login;

    private Email $mailer;


    public function __construct() {
        $this->db = Database::getInstance();
        $this->login = new Login();
        $this->mailer = new Email();
    }

    /**
     * Register user
     * @param string $username User's username
     * @param string $email Email of the user
     * @param string $password Password of the user
     * @param string $confirm_password Retyped password of the user
     * @param int $captcha User's answer to the captcha

     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function userRegister(string $username, string $email, string $password, string $confirm_password, int $user_role){

        $username = trim($username);
        $email = trim($email);
        $password = trim($password);
        $confirm_password = trim($confirm_password);

        
         
            if($this->login->checkUsername($username)){
                echo json_encode(["success" => false, "message" => "Username is already exists."]);
                return;
            }

            if($this->login->checkEmail($email)){
                echo json_encode(["success" => false, "message" => "Email is already exists."]);
                return false;
            }

            if ($password !== $confirm_password){
                echo json_encode(["success" => false, "message" => "Password doesnt match."]);
                return false;
            }

    

         try {

            $algorithm = random_int(0, 1) === 0 ? PASSWORD_BCRYPT : PASSWORD_ARGON2ID;
            $hashed_password = password_hash($confirm_password, $algorithm);

             // generate confirmation code
             $confirmation_code = Others::generateKey();

             $status = (EMAIL_CONFIRMATION)  ? '0' : '1';

             $sql = "INSERT INTO `users` (`username`, `email`, `password`,  `user_role`, `status`, `confirmation_key`) VALUES (:username, :email, :password, :user_role, :status, :confirmation_key)";
             $stmt = $this->db->prepare($sql);
             $stmt->bindParam(":username", $username, PDO::PARAM_STR);
             $stmt->bindParam(":email", $email, PDO::PARAM_STR);
             $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
             $stmt->bindParam(":user_role", $user_role);
             $stmt->bindParam(":confirmation_key", $confirmation_code, PDO::PARAM_STR);
             $stmt->bindParam(":status", $status, PDO::PARAM_STR);
             if($stmt->execute()){

                 $uid = $this->db->lastInsertId();

                if ($this->setUserData($uid)){
                    echo json_encode(["success" => true, "message" => "Registered successfully."]);
                    return true;

                }
                return true;

             }

         } catch(Exception $e){
            echo json_encode(["success" => false, "message" => "Error:  ". $e->getMessage()]);
            return false;
         }

         return false;


    }

    /**
     * Insert other user info to database
     * @param $uid User id
     * @return bool|void
     */
    private function setUserData($uid){

        $date_time = date("Y-m-d h:i:s");

        try {

            $sql = "INSERT INTO `user_details` (`user_id`) VALUES (:uid)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":uid", $uid, PDO::PARAM_INT);
            if($stmt->execute()){

              return true;
            }

        } catch (Exception $e){
            echo json_encode(["success" => false, "message" => "Error:  ". $e->getMessage()]);
            return false;
        }
    }


    public function confirmAccount(string $key){

        try {

            $sql = "SELECT * FROM `users` WHERE `confirmation_key` = :key";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':key', $key, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $sql = "UPDATE `users` SET `status` = '1' WHERE `confirmation_key` = :key";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':key', $key, PDO::PARAM_STR);
                if($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "Thank you for registering, you account has been activated."]);
                    return true;

                } else {
                    echo json_encode(["success" => false, "message" => "There must be an error confirming your account!"]);
                    return false;
                }
            } else {
                echo json_encode(["success" => true, "message" => "Oops! Sorry, the key with this user does not exist."]);

                return false;
            }

        } catch (Exception $e){
            echo json_encode(["success" => false, "message" => "Error:  ". $e->getMessage()]);
            return false;
        }



    }

    /**
     * Generate register captcha
     * @return void
     */
    public function generateCaptcha(){

        Session::setSession('first_number', rand(1, 10));
        Session::setSession("second_number", rand(1, 10));
    }

}
