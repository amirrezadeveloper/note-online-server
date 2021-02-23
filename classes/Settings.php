<?php

class Settings {
    
    
    
    public function __construct($system = false) {
        
        
        if($system)            return;
        
        $action = app::get(ACTION);
        
        switch ($action) {
            
            case ACTION_GET : {
                $userID = app::get(USER_ID);
                $this->getSettings($userID , $system);
             break;   
            }
            
            case ACTION_SET : {
                
                $userID    = app::get(USER_ID);
                $nightMode = app::get(NIGHT_MODE);
                $font      = app::get(FONT);
                $bgColor   = app::get(BG_COLOR);
                $textColor = app::get(TEXT_COLOR);
                $fontSize  = app::get(FONT_SIZE);
                $avatar    = app::get(AVATAR_ID); 
                
                $this->setSettings($userID, $nightMode, $font, $bgColor, $textColor, $fontSize, $avatar , $system);
             break;   
            } 
            
            
        }
        
        
    }
    
    
    
    
    public function setSettings($userID , $nightMode , $font , $bgColor , $textColor , $fontSize  , $avatar , $system = false) {
        
        
        if($nightMode == -1) $nightMode = 0 ;
        if($font == -1) $font = "roboto.ttf";
        if($bgColor == -1) $bgColor = "F1F1F1";
        if($textColor == -1) $textColor = "212121";
        if($fontSize == -1) $fontSize =12 ;
        if($avatar == -1) $avatar = 2;
        
        
        
        
        
        
        
        
        $conn = MyPDO::getInstance();
        $error = new MyError();
        $query = "UPDATE settings SET nightMode = :nightMode , font = :font , bgColor = :bgColor , textColor = :textColor , fontSize = :fontSize , avatar = :avatar WHERE user_id = :user_id	";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":nightMode" ,$nightMode , PDO::PARAM_INT);
        $stmt->bindParam(":font" , $font);
        $stmt->bindParam(":bgColor" , $bgColor);
        $stmt->bindParam(":textColor" , $textColor );
        $stmt->bindParam(":fontSize" , $fontSize , PDO::PARAM_INT);
        $stmt->bindParam(":avatar" , $avatar , PDO::PARAM_INT);
        $stmt->bindParam(":user_id" , $userID , PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            echo SUCCESS;
        } catch (PDOException $exc) {
           // echo $exc->getMessage();
           $error->display("Server Error", "", MyError::$ERROR_MYPDO_SQL);
        }
            
        
    }
    
    public function getSettings($userID, $system = false) {
        $conn = MyPDO::getInstance();
        $error = new MyError();
        $query = "SELECT nightMode , font , bgColor , textColor , fontSize	 , avatars.id as avatarID , avatars.name , avatars.image FROM settings  
                    LEFT JOIN avatars ON settings.avatar = avatars.id 
                    WHERE user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":user_id", $userID);
        try {
            $stmt->execute();
            while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                
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
                
                
                if($system)                    return app::$Settings ;
                  
                  else echo json_encode (app::$Settings);
                  exit;
                
                
                
                
                
            }
        } catch (PDOException $exc) {
            $error->display("Server Error", "", MyError::$ERROR_MYPDO_SQL);
        }
    }

    public function addSettings($userID , $sex) {
        
          $conn = MyPDO::getInstance();
           $error = new MyError();
            $query = "INSERT INTO settings (user_id , nightMode , font , bgColor , textColor , fontsize , avatar) "
                    . "VALUES(:user_id , :nightMode , :font , :bgColor , :textColor , :fontSize , :avatar) ";
            
            /*
             * sex 0 = female => avatar  1
             * sex 1 = male   => avatar  2
             * 
             */
            
            if($sex == 1) app::$Settings['avatar'] = 2 ;
            
            $settingStmt = $conn->prepare($query);
            $settingStmt->bindParam(":user_id"     , $userID);
            $settingStmt->bindParam(":nightMode"   , app::$Settings['nightMode'] , PDO::PARAM_INT);
            $settingStmt->bindParam(":font"        , app::$Settings['font']);
            $settingStmt->bindParam(":bgColor"     , app::$Settings['bgColor']);
            $settingStmt->bindParam(":textColor"   , app::$Settings['textColor']);
            $settingStmt->bindParam(":fontSize"    , app::$Settings['fontSize']);
            $settingStmt->bindParam(":avatar"      , app::$Settings['avatar']);
            
            
            
             try {
                   $settingStmt->execute();
                             
            } catch (PDOException $ex) { 
            
                 $error->display("System Error", "", MyError::$ERROR_MYPDO_SQL);  
            }
            
            
            
            
            
    }
    
}
