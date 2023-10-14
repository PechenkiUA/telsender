<?php
/**
 * @package HOT
 * @version 1.14.5
 */
/*
Plugin Name: TelSender
Description: Плагін відправляє заявки з форм у телеграм канал
Author: Pechenki
Version: 1.14.8
Author URI: https://coder.org.ua/dev/wordpress/telsender
*/
//////////////////////////////////
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
define( 'TELSENDER_DIR', plugin_dir_path( __FILE__ ) );
define( 'TELSENDER_DIR_URL',  plugin_dir_url(__FILE__) );
define( 'TELSENDER_DIR_NAME', dirname( plugin_basename( __FILE__ ) ) );
define( 'TSCFWC_SETTING', 'ts__globalSetind' );

require_once( TELSENDER_DIR . 'autoload.php' );

use pechenki\Telsender\clasess\TelsenderCore;
use pechenki\Telsender\clasess\TelsenderEvent;

$Telsender = TelsenderCore::get_instance();
$TelsenderEvent = new TelsenderEvent($Telsender);
do_action( 'telsender_init', $Telsender );


/**
 * @param $text
 * @param array $param
 * @return false|mixed|string|null
 */
function TelsenderSendMessages($text, array $param = array()){
    global $Telsender;

    if (isset($param['parse_mode']))
        $Telsender->telegram->parse_mode = $param['parse_mode'];

    if (isset($param['token']))
        $Telsender->telegram->Token = $param['token'];

    if (isset($param['chat_id']))
        $Telsender->telegram->Chat_id = $param['chat_id'];


   return $Telsender->telegram->SendMesage($text);
}