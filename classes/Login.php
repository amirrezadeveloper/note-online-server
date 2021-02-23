<?php

class Login {
    
    
    public function __construct($system = false) {
        
        if($system)     return;
        
        $action = app::get(ACTION);
        
        switch ($action) {
            case ACTION_LOGIN : {
                $email = app::get(INPUT_EMAIL);
                $password = app::get(INPUT_PASSWORD);
                $this->login($email, $password );
                break;
            }
            
            case ACTION_REGISTERATION : { 
                 $email    = app::get(INPUT_EMAIL);
                 $password = app::get(INPUT_PASSWORD);
                 $fname    = app::get(INPUT_FNAME);
                 $lname    = app::get(INPUT_LNAME);
                 $sex      = app::get(INPUT_SEX);
                
                 $this->registration($email, $password, $fname, $lname, $sex);
            
               break;
            }
        }
        
        
        
    }
    
    
    
    public function login($email , $password , $system = false) {
        
        $conn = MyPDO::getInstance();
        $query = "SELECT 
                    users.id , users.email , users.fname, users.lname , users.sex ,
                    settings.nightMode , settings.bgColor , settings.font , settings.textColor , settings.fontSize , avatars.id as avatarID , avatars.image , avatars.name 
                    FROM `users` 
                    LEFT JOIN settings ON users.id = settings.user_id
                    LEFT JOIN avatars ON settings.avatar = avatars.id

                    WHERE users.email = :email AND users.password = SHA1(CONCAT(users.longer_pass , :password))";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":email" , $email);
        $stmt->bindParam(":password" , $password);
        
        try {
            $stmt->execute();
            
            
            while($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $response = array() ;
                app::$Settings['nightMode'] = $res['nightMode'];
                app::$Settings['font'] = $res['font'];
                app::$Settings['bgColor'] = $res['bgColor'];
                app::$Settings['textColor'] = $res['textColor'];
                app::$Settings['fontSize'] = $res['fontSize'];
                
                $avatar = array();
                $avatar['id']    = $res['avatarID'];
                $avatar['name']  = $res['name'];
                $avatar['image'] = $res['image'];
                app::$Settings['AvatarObject'] = $avatar;
                unset(app::$Settings['avatar']);
            
                $session = md5(sha1(microtime()));
                $query = "UPDATE users SET session = '$session' WHERE id = " . $res['id'];
                $stmt1 = $conn->prepare($query);
                $stmt1->execute();
                
                   $response['state'] = SUCCESS;
                $response['SettingsObject'] = app::$Settings ;
            
                $user = array(
                    USER_ID       => $res['id'] ,
                    INPUT_EMAIL   => $res['email'] ,
                    INPUT_FNAME   => $res['fname'] ,
                    INPUT_LNAME   => $res['lname'] , 
                    INPUT_SEX     => $res['sex']   ,
                    SESSION       => $session
                );
             
             
                $response['UserObject'] = $user;
               
                echo json_encode($response) ;
                
                exit;
                
            }
          
            $error = new MyError();
            $error->display("Wrong Login Data", "", MyError::$ERROR_WRONG_LOGIN_DATA);
            
            
        } catch (PDOException $exc) {
            echo $exc->getMessage();
          $error = new MyError();
          $error->display("Server Error ", "", MyError::$ERROR_MYPDO_SQL);
        }
            
        
        
        
        
    }
    
    public function registration($email , $password , $fname, $lname , $sex , $system = false) {
          
         $error = new MyError();
        if($email == -1 || $password == -1 || !filter_var($email , FILTER_VALIDATE_EMAIL)) {
           
            $error->display("Invalid Data", "", MyError::$ERROR_INVALID_DATA);
        }
        
        
        
        $longer_pass = sha1(microtime()) . md5(microtime()); 
        $session     = sha1(microtime()) . md5(microtime());
        
        
            
        $conn = MyPDO::getInstance();
        
        $query = " INSERT INTO users (email  , fname , lname ,   password , longer_pass , session ,  sex) "
                . " VALUES (:email , :fname , :lname  ,  SHA1(CONCAT(:longer_pass , :password))  , :longer_pass , :session , :sex)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":email" , $email);
        $stmt->bindParam(":fname" , $fname);
        $stmt->bindParam(":lname" , $lname);
        $stmt->bindParam(":password" , $password);
        $stmt->bindParam(":longer_pass" , $longer_pass);
        $stmt->bindParam(":session" , $session);
        $stmt->bindParam(":sex"  , $sex , PDO::PARAM_INT);
        try {
            
            $stmt->execute();
            $id =  MyPDO::getLastID($conn);
            

            $settings = new Settings(true);
            $settings->addSettings($id, $sex);
            
            
           
                   $avatar = new Avatar(true);
                   app::$Settings['AvatarObject'] = $avatar->getAvatar(app::$Settings['avatar']);
                   
                   
                   $response = array() ;
                   
                   $response['session'] = $session ;
                   $response['id'] = $id ;
                   $response['state']  = SUCCESS;
                   $response['SettingsObject'] = app::$Settings;
                   
                   echo json_encode($response);
                                     
            
            
        } catch (PDOException $exc) {
            
         
            if($exc->getCode() == 23000) {//duplicate email 
                $error->display("this is Email is already in use", "", MyError::$ERROR_DUPLICATE_EMAIL);  
            }
            
             $error->display("System Error", "", MyError::$ERROR_MYPDO_SQL);  
            
            
        }
            

        
        
    }
    
    public function checkLogin($id , $session) {

        $errorManager = new MyError();
        $conn = MyPDO::getInstance();
        $query = "SELECT id FROM users WHERE id = :id AND session = :session";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id" , $id , PDO::PARAM_INT);
        $stmt->bindParam(":session" , $session);
        
        
        try {
            $stmt->execute();
            if(MyPDO::getRowCount($stmt) == 1)                return true;
        } catch (PDOException $exc) {
          $errorManager->display("Server Error", "", MyError::$ERROR_MYPDO_SQL);
        }

   
        $errorManager->display("Wrong Login Data" , "logout" , MyError::$ERROR_WRONG_LOGIN_DATA);
    }
    
    
    
            
    
    
    
    
    
    
    
    
    
    
    
    
}