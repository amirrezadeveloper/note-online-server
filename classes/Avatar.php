<?php

class Avatar {
    
    
    public function __construct($system = false) {
        if($system)return;
        
        $action = app::get(ACTION);
        
        
        switch ($action) {
            case ACTION_GET: {
                    $this->getAvatars();
                break;
            }
        }
        
        
        
        
        
    }
    
    
    public function getAvatars($system = false) {
        
        $conn = MyPDO::getInstance();
        $query = "SELECT * FROM avatars ";
        $stmt = $conn->prepare($query);
        
        try {
            $stmt->execute();
            if($system)                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $exc) {
            $error = new MyError();
            $error->display("Server Error", "", MyError::$ERROR_MYPDO_SQL);
        } 
        
        
    }
    
    
    public function getAvatar($id) {
        $conn = MyPDO::getInstance();
        $query = "SELECT * FROM avatars WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id" , $id , PDO::PARAM_INT);
        
        try {
                $stmt->execute();
                while($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    return $res;
                }
        } catch (PDOException $ex) {

            $error = new MyError();
            $error->display("System Error", "", MyError::$ERROR_MYPDO_SQL);
        }
    
        
    }
    
    
}