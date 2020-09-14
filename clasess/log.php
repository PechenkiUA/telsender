<?php 
namespace pechenki\Telsender\clasess;

class log{
    
    static $logNameOption = 'telsenderLog';



    public static function setLog($text){

        if($text){
            $log = [];
            $real = json_decode(self::getLog(),true)?:[];           
            
            $log['date'] = time();  
            $log['data'] = $text;
            $real[]=$log;
            return update_option(self::$logNameOption,json_encode($real));
        }        
        return false;
    }

    public static function getLog(){
        return get_option(self::$logNameOption);
    }

    public static function clearLog(){
        return update_option(self::$logNameOption,'');
    }
}