<?php
/**
 * @package HOT
 * @version 1.0.8
 */
/*
Plugin Name: TelSender
Description: Плагин отправляет заявки из форм в телеграм канал
Author: Pechenki
Version: 1.12.3
Author URI: https://pechenki.top/telsender.html
*/
//////////////////////////////////
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
define( 'TELSENDER_DIR', plugin_dir_path( __FILE__ ) );
define( 'TELSENDER_DIR_NAME', dirname( plugin_basename( __FILE__ ) ) );
define( 'TSCFWC_SETTING', 'ts__globalSetind' );

require_once( TELSENDER_DIR . 'autoload.php' );

use pechenki\Telsender\clasess\TelsenderCore;

$Telsender = TelsenderCore::get_instance();
do_action( 'telsender_init', $Telsender );
