<?php

class Notes {
    /*
     * readNote
     * addNote
     * editNote
     * deleteNote
     * changeState
     */

    public function __construct($system = false) {

        if ($system)
            return;
        $action = app::get(ACTION);
        $userID = app::get(USER_ID);

        switch ($action) {
            case ACTION_ADD : {

                    $title = app::get(INPUT_TITLE);
                    $message = app::get(INPUT_MESSAGE);

                    $this->addNote($userID, $title, $message);
                    break;
                }
            case ACTION_READ : {

                    $start = app::get(INPUT_START);
                    $this->readNote($userID, $start);
                    break;
                }
            case ACTION_DELETE : {
                    $noteID = app::get(INPUT_NOTE_ID);
                    $this->deleteNote($userID, $noteID);
                    break;
                }
            case ACTION_CHANGE_STATE : {
                    $noteID = app::get(INPUT_NOTE_ID);
                    $state = app::get(INPUT_STATE);
                    $this->changeState($userID, $noteID, $state);
                    break;
                }

            case ACTION_EDIT : {

                    $noteID = app::get(INPUT_NOTE_ID);
                    $title = app::get(INPUT_TITLE);
                    $message = app::get(INPUT_MESSAGE);

                    $this->editNote($userID, $noteID, $title, $message);

                    break;
                }
        }
    }

    public function readNote($userID, $start = 0) {
        
        $conn = MyPDO::getInstance();
        $query = "SELECT * FROM notes WHERE user_id = :user_id ORDER BY date DESC LIMIT :start , 20";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":user_id" , $userID , PDO::PARAM_INT);
        $stmt->bindParam(":start" , $start , PDO::PARAM_INT);
       
        try {
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $exc) {
            echo $exc->getMessage();
            $error = new MyError();
            $error->display("Server Error", "", MyError::$ERROR_MYPDO_SQL);
        }
            
        
        
        
        
    }

    public function addNote($userID, $title, $message) {
        $conn = MyPDO::getInstance();
        $query = "INSERT INTO notes (user_id , title , message) VALUES (:user_id , :title , :message)";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(":user_id", $userID, PDO::PARAM_INT);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":message", $message);

        try {
            $stmt->execute();
            $id = MyPDO::getLastID($conn);
            $response = array("status" => SUCCESS, "id" => $id);
            echo json_encode($response);
        } catch (PDOException $ex) {
            $error = new MyError();
            $error->display("Server Error ", "", MyError::$ERROR_MYPDO_SQL);
        }
    }

    public function deleteNote($userID, $noteID) {
        $conn = MyPDO::getInstance();
        $query = "DELETE FROM notes WHERE user_id = :user_id AND id = :note_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":user_id", $userID);
        $stmt->bindParam(":note_id", $noteID);
        try {
            $stmt->execute();
            echo SUCCESS;
        } catch (PDOException $ex) {
            $error = new MyError();
            $error->display("Server Error ", "", MyError::$ERROR_MYPDO_SQL);
        }
    }

    public function changeState($userID, $noteID, $state) {
        
            
        $conn = MyPDO::getInstance();
        $query = "UPDATE notes SET seen = :state WHERE user_id = :user_id AND id = :note_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":state", $state, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $userID, PDO::PARAM_INT);
        $stmt->bindParam(":note_id", $noteID, PDO::PARAM_INT);

        try {
            $stmt->execute();
            echo SUCCESS;
        } catch (PDOException $ex) {
            $error = new MyError();
            $error->display("Server Error", "", MyError::$ERROR_MYPDO_SQL);
        }
    }

    public function editNote($userID, $noteID, $title, $message) {
        $conn = MyPDO::getInstance();
        $query = "UPDATE notes SET title = :title , message = :message WHERE user_id = :user_id AND id = :note_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":message", $message);
        $stmt->bindParam(":user_id", $userID, PDO::PARAM_INT);
        $stmt->bindParam(":note_id", $noteID, PDO::PARAM_INT);

        try {
            $stmt->execute();
            echo SUCCESS;
        } catch (PDOException $exc) {
            $error = new MyError();
            $error->display("Server Error", "", MyError::$ERROR_MYPDO_SQL);
        }
    }

}
