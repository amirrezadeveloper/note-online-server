<?php

class app {
    
    
    
    public static $Settings = array(
        "user_id"   => -1 ,
        "nightMode"  => 0 , 
        "font"        => "roboto.ttf" , 
        "bgColor"    => "F1F1F1" , 
        "textColor"  => "212121",
        "fontSize"   => 12 ,
        "avatar"      => 1 
        );

 
     
 
 
 
    public static function get($key) {
        
        if(isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }
        
        return "-1";
    }
    
    
    
    
    
}