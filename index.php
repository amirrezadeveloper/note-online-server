<?php

include 'Config.php';
include 'ROUTER.php';
include 'app.php';
include 'MyPDO.php';
include 'classes/Avatar.php';
include 'classes/MyError.php';
include 'classes/Login.php';
include 'classes/Notes.php';
include 'classes/Settings.php';




$ROUTE   = app::get(ROUTE);
$SESSION = app::get(SESSION);
$USER_ID = app::get(USER_ID);


 
switch ($ROUTE) {

    case ROUTE_LOGIN: { 
        $login = new Login(); 
     break;
    }
    case ROUTE_NOTES : {
        $login = new Login(true);
        if($login->checkLogin($USER_ID, $SESSION)) {
            $note = new Notes();
        }
        
       break; 
    }
    
    case ROUTE_SETTINGS : {
        $login = new Login(true);
        if($login->checkLogin($USER_ID , $SESSION)) {
            $settting = new Settings();
        }
        
     break;   
    }
default : {
    
    $error = new MyError();
    $error->display("There is no valid ROUTE", "", MyError::$ERROR_NO_ROUTE);
    
    
}
        
        
}