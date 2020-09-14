<?php
namespace pechenki\Telsender\clasess;

use pechenki\Telsender\clasess\TelegramSend as Telegransender;
use pechenki\Telsender\clasess\TscfwcSetting;
// use pechenki\Telsender\clasess\TelsenderWc;
/**
 * Core
 */
class TelsenderCore
{

    public $telegram;
    public $tscfwc;
    static $instance;

    function __construct(){
      if ( ! empty( self::$instance ) ) return new WP_Error( 'duplicate_object','error');


              $this->tscfwc = new TscfwcSetting(get_option( TSCFWC_SETTING ));
              $this->telegram = new Telegransender;

              $this->telegram->Pechenki_key = $this->tscfwc->Option('tscfwc_setting_newtoken');
              $this->telegram->Chat_id = $this->tscfwc->Option('tscfwc_setting_chatid');
              $this->telegram->Token = $this->tscfwc->Option('tscfwc_setting_token');




          add_action('admin_menu', array($this,'tscfwc_dynamic_button'));
          add_action('woocommerce_checkout_order_processed',array($this,'tscfwc_woocommerce_new_order'));
          add_action('wp_ajax_tscfwc_form_reqest',array($this,'tscfwc_form_ajax_reqest'));
          add_action( 'wpforms_process_entry_save', array( $this, 'tscfwc_wp_form'),10, 4 );
          // add_action('Telsender_pechenki_self', array($this,'testFu'));
          // add_action('woocommerce_new_order', array($this,'tscfwc_woocommerce_new_order'));
           add_action("wpcf7_mail_sent", array($this,'wpcf7_tscfwc'),99, 1 );

          // add_filter( 'wpcf7_form_elements', 'do_shortcode' );// register hook
    }


    public static function get_instance(){
  		if ( empty( self::$instance ) ) :
  			self::$instance = new self;
  		endif;

  		return self::$instance;
  	}

    public  function tscfwc_dynamic_button () {
        add_menu_page ('TelSender', 'TelSender', 'manage_options', 'telsender', array($this,'tscfwc_setting_page'),  plugin_dir_url( __FILE__ ).'../assets/icon-plugin.png');
     }


     /**
     * setting
     * @return
     */
    public  function tscfwc_setting_page(){
      load_plugin_textdomain( 'telsender', false, TELSENDER_DIR_NAME.'/languages/' );

      wp_enqueue_style('multi-select',plugin_dir_url( __FILE__ ) . '../css/multiselect.css');
      wp_enqueue_script('multi-select.',plugin_dir_url( __FILE__ ) . '../js/multiselect.js');
      wp_enqueue_script('ajax', plugin_dir_url( __FILE__ ) . '../js/ajax.js');
      wp_enqueue_style('telsender', plugin_dir_url( __FILE__ ) . '../css/telsender.css');

         if(isset($_POST['curssent'])) {
            $reply = 'Send';
            $this->telegram->SendMesage($reply);
           }

           if ($this->tscfwc->Option('tscfwc_setting_setcheck')) {
             $is_check_pechenki = $this->tscfwc->Option('tscfwc_setting_setcheck')['tscfwc_key'];
           }else{
             $is_check_pechenki = '';
           }

           if ( $this->tscfwc->Option('tscfwc_setting_setcheck')) {
             $is_check_wc = $this->tscfwc->Option('tscfwc_setting_setcheck')['wooc_check'] ;
           }else{
             $is_check_wc = '';
           }
           if (function_exists('wc_get_order_statuses')) {
             $list_statuse_wc = wc_get_order_statuses();
           }else{
              $list_statuse_wc = [];
           }

         require_once( TELSENDER_DIR . 'template/view.php' );
      }


      /*
      * action wp_forms
      * @return SendMesage
      */

        public function tscfwc_wp_form($fields, $entry, $form_id, $form_data) {
			if(is_array($this->tscfwc->Option('tscfwc_setting_acseswpforms'))){
                if(in_array($form_id,$this->tscfwc->Option('tscfwc_setting_acseswpforms'))) {
                   $ss  =  wpforms()->smart_tags->process($form_data['settings']['notifications'][1]['message'], $form_data, $fields);
                    if ($fields && (strrpos($ss,'{all_fields}')!== false)) {
                      $message = '<b>'.$form_data['settings']['form_title'].'</b>'.chr(10);
                        foreach ($fields as $fieldskey => $fieldsvalue) {
                            $message .=$fieldsvalue["name"].' : '.$fieldsvalue['value'].chr(10);
                        }

                       $ss = str_replace('{all_fields}', $message, $ss);
                    }



                  $this->telegram->SendMesage($ss);
                }
		}

          }

        /**
        * action contact-form7
        *  @return SendMesage
        */
        public  function wpcf7_tscfwc($ccg) {


             if(in_array($ccg->id,$this->tscfwc->Option('tscfwc_setting_acsesform'))) {

               $output = wpcf7_mail_replace_tags ($ccg->mail["body"]);
                 $this->telegram->SendMesage($output);

              }//end if

         }

         /**
         * action new order woocommerce
         *  @return SendMesage
         */
         public function tscfwc_woocommerce_new_order($order_id){
          $wc_chek = $this->tscfwc->Option('tscfwc_setting_setcheck');
          $wc = new TelsenderWc($order_id);

           if ($wc_chek['wooc_check']) {
             $teml = $this->tscfwc->Option('tscfwc_setting_wooc_template');
             $message = $wc->getBillingDetails($teml);
             $this->telegram->SendMesage($message);
           }


         }



         /**
         * ajax action
         *  @return save to db
         */
      public  function tscfwc_form_ajax_reqest(){

           $validatePost = array(
         	'tscfwc_setting_token' => (!preg_match( '/[^0-9.A-Za-z:\-_=]/m', $_POST['tscfwc_setting_token'])? $_POST['tscfwc_setting_token']:''),
         	'tscfwc_setting_chatid' => (int)$_POST['tscfwc_setting_chatid'],
         	'tscfwc_setting_wooc_template' => preg_replace( '/[#~?%&*@!^]/m','',$_POST['tscfwc_setting_wooc_template']),
         	'tscfwc_setting_newtoken' => (!preg_match( '/[^0-9.A-Za-z\-:]/m', $_POST['tscfwc_setting_newtoken'])? $_POST['tscfwc_setting_newtoken']:''),
         	'tscfwc_setting_setcheck' => array('wooc_check'=> (int)$_POST['tscfwc_setting_setcheck']['wooc_check'],
                                              'tscfwc_key'=> (int)$_POST['tscfwc_setting_setcheck']['tscfwc_key']
                                            ),
         	'tscfwc_setting_acsesform' => array_map(function($key){ return (int)$key;}, $_POST["tscfwc_setting_acsesform"]),
         	'tscfwc_setting_acseswpforms' => array_map(function($key){ return (int)$key;}, $_POST["tscfwc_setting_acseswpforms"]),
         	'tscfwc_setting_status_wc' => array_map(function($key){ return (string)$key;}, $_POST["tscfwc_setting_status_wc"]),
         );

           if ($validatePost) {
             update_option(TSCFWC_SETTING,serialize($validatePost));
           }
           wp_die();

         }


}
