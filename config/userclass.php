<?php
require_once ("dbConn.php");


class User{

    


    /*checks if user is registered*/
    public function isReg($userName){
        try{
            $stmt = $this->query("SELECT 1 FROM users WHERE username = :username");
            $stmt->bindParam(":username", $userName, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount() == 1)
                return true;
            else
                return false;
        }
        catch(PDOException $exception){
            echo ("is registered ERROR: " . $exception->getMessage());
        }
    }

    /*register a user*/
    public function regUser($firstname, $lastname, $userName, $email, $password){
        try{
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->query("INSERT INTO users(firstname, lastname, username, email, `password`)
                Values(:firstname, :lastname, :username, :email, :password)");
            $stmt->bindParam(":firstname", $firstname, PDO::PARAM_STR);
            $stmt->bindParam(":lastname", $lastname, PDO::PARAM_STR);
            $stmt->bindParam(":username", $userName, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt;
        }
        catch(PDOException $exception){
            echo("Error registering user: " . $exception->getMessage());
        }
    }
    /*verify user email*/
    public function verifyEmail($userName, $email){
        try{
            $stmt = $this->query("UPDATE users SET verified = 'Yes' WHERE username=:username");
            $stmt->execute(array('username'=>$userName));
            return true;
        }
        catch(PDOException $exception){
            echo("Error: " . $exception->getMessage());
            return false;
        }
    }

    /*check if email verified*/
    public function isVerified($userName){
        $stmt = $this->query("SELECT * FROM users WHERE username=:userName");
        $stmt->bindParam(":userName", $userName, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() == 1){
            if ($user['verified'] == "Yes")
                return true;
            else
                return false;
        }
    }
    /*attempt to log a user in*/
    public function login($userName, $password){


        try{
            $stmt = $this->query("SELECT `user_id`, firstname, lastname, email, `password` FROM users WHERE username=:username");
            $stmt->execute(array(':username'=>$userName));
            if($stmt->rowCount() == 1){
                $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $userRow['password'])){
                    $_SESSION['user_session'] = $userRow['username'];
                    return true;
                }
                else{
                    return false;
                }
            }
        }
        catch(PDOException $exception){
            echo("login ERROR: " . $exception->getMessage());
        }
    }

    /* change password */
    public function changePass($userName, $newPassword){
        try{
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->query("UPDATE users SET `password`=:newPassword WHERE username=:userName");
            $stmt->bindParam(":newPassword", $passwordHash, PDO::PARAM_STR);
            $stmt->bindParam(":userName", $userName, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        }
        catch(PDOException $exception){
            return false;
        }
    }

    /* check if user is logged in*/
    public function loggedIn(){
        if (isset($_SESSION['user_session']))
            return true;
        else
            return false;
    }

    /*log user out*/
    public function logOut(){
        unset($_SESSION['user_session']);
        session_destroy();
        return true;
    }

    public function query($sql_query){
        
        $conn = getConn();
        $stmt = $conn->prepare($sql_query);
        return $stmt;
    }

    /*redirect to another url*/
    public function redirect($url){
        header("location:$url");
    }

    /*upload a image*/
    public function uploadImage($userName, $imageName){
        try{
            $stmt = $this->query("INSERT INTO images (username, image_name) Values(:username, :image_name)");
            $stmt->bindParam(":username", $userName, PDO::PARAM_STR);
            $stmt->bindParam("image_name", $imageName, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        }
        catch(PDOException $exception){
            return false;
        }
    }

    /*delete image for logged in user*/
    public function deleteImage($userName, $imageId, $imageName){
        try{
            $stmt = $this->query("DELETE FROM images WHERE image_name=:imageName AND image_id=:imageId AND username=:userName");
            $stmt->bindParam(":imageName", $imageName, PDP::PARAM_STR);
            $stmt->bindParam(":imageId", $imageId, PDO::PARAM_STR);
            $stmt->bindParam(":userName", $userName, PDO::PARAM_STR);
            $stmt->execute();

            $stmt = $this->query("DELETE FROM likes WHERE image_id=:imageId");
            $stmt->bindParam(":imageId", $imageId, PDO::PARAM_STR);
            $stmt->execute();

            $stmt = $this->query("DELETE FROM comments WHERE image_id=:imageId");
            $stmt->bindParam(":imageId", $imageId, PDO::PARAM_STR);
            $stmt->execute();

            return true;
        }
        catch(PDOException $exception){
            return false;
        }
    }

	function test_input($data) {
		$data = strip_tags($data);
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
}

?>