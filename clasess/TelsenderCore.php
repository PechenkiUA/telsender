<?php

namespace pechenki\Telsender\clasess;

use pechenki\Telsender\clasess\TelegramSend as Telegransender;
use pechenki\Telsender\clasess\TscfwcSetting;
use function Symfony\Component\Translation\t;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class TelsenderCore
 * @property $settings
 * @package pechenki\Telsender\clasess
 */
class TelsenderCore extends TscfwcSetting
{

    public $version = '1.14.6';

    /**
     * @var TelegramSend $telegram
     */

    public $telegram;

    /**
     * @var TelegramSend $tscfwc
     */
    public $tscfwc;


    /**
     * @var $instance
     */
    static $instance;

    /**
     * TelsenderCore constructor.
     */
    function __construct()
    {
        if (!empty(self::$instance)) return new WP_Error('duplicate_object', 'error');

        parent::__construct();
        $this->telegram = new Telegransender;

        $this->telegram->Pechenki_key = $this->tscfwc_setting_newtoken;
        $this->telegram->Chat_id = $this->tscfwc_setting_chatid;
        $this->telegram->Token = $this->tscfwc_setting_token;


        add_action('admin_menu', array($this, 'tscfwc_dynamic_button'));
        add_action('admin_enqueue_scripts', array($this, 'wc_code_templated'));
        add_action('wp_ajax_tscfwc_form_reqest', array($this, 'tscfwc_form_ajax_reqest'));

        if (!$this->tscfwc_enabled) return;

        add_action('wpforms_process_complete', array($this, 'tscfwc_wp_form'), 10, 4);
        add_action('woocommerce_after_order_object_save', array($this, 'tscfwc_woocommerce_new_order_status'), 99, 2);
        if ($this->tscfwc_setting_acsesform) {
            add_action("wpcf7_mail_sent", array($this, 'wpcf7_tscfwc'), 99, 1);
        }

    }

    /**
     * @return TelsenderCore
     */
    public static function get_instance()
    {
        if (empty(self::$instance)) :
            self::$instance = new self;
        endif;

        return self::$instance;
    }

    /**
     * tscfwc_dynamic_button
     */
    public function tscfwc_dynamic_button()
    {
        add_menu_page('TelSender', 'TelSender 🇺🇦', 'manage_options', 'telsender', array($this, 'tscfwc_setting_page'), plugin_dir_url(__FILE__) . '../assets/icon-plugin.png');
        add_submenu_page('telsender', 'Help', 'Help 🆘', 'manage_options', 'telsender-help', array($this, 'help'),10);
    }


    /**
     * setting
     * @return html
     */

    public function tscfwc_setting_page()
    {
        load_plugin_textdomain('telsender', false, TELSENDER_DIR_NAME . '/languages/');
        wp_enqueue_style('multi-select',TELSENDER_DIR_URL . 'css/multiselect.css',false,$this->version);
        wp_enqueue_script('multi-select.',TELSENDER_DIR_URL . 'js/multiselect.js');
        wp_enqueue_script('ajax', TELSENDER_DIR_URL . 'js/ajax.js',false,false,$this->version);
        wp_enqueue_style('telsender-css', TELSENDER_DIR_URL . 'css/telsender.css',false,$this->version);

        if (isset($_POST['curssent'])) {
            $reply = 'Send';
            $this->telegram->SendMesage($reply);
        }

        if ($this->tscfwc_setting_setcheck && isset($this->tscfwc_setting_setcheck['wooc_check'])) {
            $data['is_check_wc'] = $this->tscfwc_setting_setcheck['wooc_check'];
        } else {
            $data['is_check_wc'] = '';

        }



        if ($this->tscfwc_setting_setcheck && isset($this->tscfwc_setting_setcheck['wooc_chat_id'])) {
            $data['wooc_chat_id'] = $this->tscfwc_setting_setcheck['wooc_chat_id'];
        } else {
            $data['wooc_chat_id'] = '';

        }


        if ($this->tscfwc_setting_setcheck && isset($this->tscfwc_setting_setcheck['wooc_all_order'])) {
            $data['is_wooc_all_order'] = $this->tscfwc_setting_setcheck['wooc_all_order'];
        } else {
            $data['is_wooc_all_order'] = '';
        }

        if (function_exists('wc_get_order_statuses')) {
            $data['list_statuse_wc'] = wc_get_order_statuses();
        } else {
            $data['list_statuse_wc'] = [];
        }
        /**
         * contact form list
         */
        $args = array(
            'post_type' => 'wpcf7_contact_form',
            'posts_per_page' => -1,
        );
        $query = new \WP_Query;
        $data['cf7List'] = $query->query($args);
        /**
         * wpform list
         */
        $args = array(
            'post_type' => 'wpforms',
            'posts_per_page' => -1,
        );
        $query = new \WP_Query;
        $data['wpfList'] = $query->query($args);


        $this->render('template/view',$data);

    }

    /**
     * @return void
     */
    public function help()
    {
        load_plugin_textdomain('telsender', false, TELSENDER_DIR_NAME . '/languages/');
        wp_enqueue_style('multi-select',TELSENDER_DIR_URL . 'css/multiselect.css',false,$this->version);
        wp_enqueue_script('multi-select.',TELSENDER_DIR_URL . 'js/multiselect.js');
        wp_enqueue_script('ajax', TELSENDER_DIR_URL . 'js/ajax.js',false,false,$this->version);
        wp_enqueue_style('telsender-css', TELSENDER_DIR_URL . 'css/telsender.css',false,$this->version);



        $this->render('template/help-page',[]);

    }



    /**
     * action wp_forms
     * @param $fields
     * @param $entry
     * @param $form_id
     * @param $form_data
     */
    public function tscfwc_wp_form($fields, $entry,$form_data,$entry_id)
    {
        if ($this->tscfwc_setting_acseswpforms &&  is_array($this->tscfwc_setting_acseswpforms)) {
            if (in_array($form_data['id'], $this->tscfwc_setting_acseswpforms)) {

                $m = $form_data['settings']['notifications'][1]['message'];

                $entry_id = intval($_POST['wpforms']['entry_id']);
                if (isset($entry_id)){
                    $m = str_replace('{entry_id}',$entry_id,$m);
                }

                $ss = wpforms()->smart_tags->process($m, $form_data, $fields);
                if ($fields && (strrpos($ss, '{all_fields}') !== false)) {
                    $message = '<b>' . $form_data['settings']['form_title'] . '</b>' . chr(10);
                    foreach ($fields as $fieldskey => $fieldsvalue) {
                        if ($fieldsvalue['value']){
                            $message .= $fieldsvalue["name"] . ' : ' . $fieldsvalue['value'] . chr(10);
                        }

                    }
                    $ss = str_replace('{all_fields}', $message, $ss);
                }
                $this->telegram->SendMesage($ss);
            }
        }
    }

    /**
     * action contact-form7
     * @param object $ccg
     * @return SendMesage
     */

    public function wpcf7_tscfwc($ccg)
    {

       if (in_array($ccg->id, $this->tscfwc_setting_acsesform)) {

            $output = wpcf7_mail_replace_tags($ccg->mail["body"]);
            $this->telegram->SendMesage($output);
        }
        return ;

    }

    /**
     * action new order woocommerce status
     * @param object $order
     * @return SendMesage
     */
    public function tscfwc_woocommerce_new_order_status($order)
    {


        $wc_chek = $this->tscfwc_setting_setcheck;
        $wc_access_status = $this->tscfwc_setting_status_wc;

        if (!is_array($wc_access_status)) return;

        if (in_array('wc-' . $order->get_status(), $wc_access_status) || !$wc_access_status) {
            $isSendn = get_post_meta($order->get_id(), 'telsIsm', true);
            if (!$wc_chek['wooc_check'])  return;
            if (!empty($wc_chek['wooc_chat_id'])) {
                $this->telegram->Chat_id = $wc_chek['wooc_chat_id'];
            }

            if ($isSendn && $isSendn != '-1') {
                $this->updateOrderToTelegram($order->get_id(),$isSendn);
            } else {
                $send = $this->sendNewOrderToTelegram($order->get_id());
                if (isset($send['result']['message_id'])){
                    update_post_meta($order->get_id(), 'telsIsm', $send['result']['message_id']);
                }else{
                    update_post_meta($order->get_id(), 'telsIsm', -1);
                }

            }

        }
        return;
    }

    /**
     * @param $OrderId
     */
    private function sendNewOrderToTelegram($OrderId)
    {
        $wc = new TelsenderWc($OrderId);
        $teml = $this->tscfwc_setting_wooc_template;
        $message = $wc->getBillingDetails($teml);

        return $this->telegram->SendMesage($message);

    }

    /**
     * @param $OrderId
     */
    private function updateOrderToTelegram($OrderId,$message_id)
    {
        $wc = new TelsenderWc($OrderId);
        $teml = $this->tscfwc_setting_wooc_template;
        $message = $wc->getBillingDetails($teml);
        return $this->telegram->UpdateMessage($message,$message_id);

    }


    /**
     * ajax action
     * @return save to db
     */
    public function tscfwc_form_ajax_reqest()
    {



        $validatePost = array(
            'tscfwc_setting_token' => $this->post('tscfwc_setting_token'),
            'tscfwc_setting_chatid' => htmlentities($this->post('tscfwc_setting_chatid')),
            'tscfwc_setting_wooc_template' => htmlentities($this->post('tscfwc_setting_wooc_template')),
            'tscfwc_setting_setcheck' => array(
                'wooc_check' => (int)htmlentities($this->post('tscfwc_setting_setcheck')['wooc_check']),
                'wooc_chat_id' => htmlentities($this->post('tscfwc_setting_setcheck')['wooc_chat_id']),
                'wooc_all_order' => (int)htmlentities($this->post('tscfwc_setting_setcheck')['wooc_all_order']),
                'tscfwc_key' => (int)htmlentities($this->post('tscfwc_setting_setcheck')['tscfwc_key'])
            ),
        );
        /**
         * status woocommerse save
         */
        if ($this->post('tscfwc_setting_status_wc')){

            $validatePost['tscfwc_setting_status_wc'] =  $this->post("tscfwc_setting_status_wc");
        }

        /**
         * cf-7 save
         */
        if ( $this->post("tscfwc_setting_acsesform")){
            $validatePost['tscfwc_setting_acsesform'] = array_map(function ($key) {
                return (int)$key;
            }, $this->post("tscfwc_setting_acsesform"));
        }


        /**
         * wp-forms-save
         */
        if ($this->post("tscfwc_setting_acseswpforms")){

            $validatePost['tscfwc_setting_acseswpforms'] = array_map(function ($key) {
                return (int)$key;
            }, $this->post("tscfwc_setting_acseswpforms"));
        }
        /**
         * wp-forms-save
         */
        if ($this->post('tscfwc_enabled')){

            $validatePost['tscfwc_enabled'] = (int)htmlentities($this->post('tscfwc_enabled'));
        }



        if ($validatePost) {
            update_option(TSCFWC_SETTING, serialize($validatePost));
        }



    }

    /**
     * codeEditor.initialize
     */
    public function wc_code_templated()
    {

        if ('toplevel_page_telsender' !== get_current_screen()->id) {
            return;
        }
        $settings = wp_enqueue_code_editor(array('type' => 'text/html'));
        if (false === $settings) {
            return;
        }
        wp_add_inline_script(
            'code-editor',
            sprintf('jQuery( function() { ts_wc =  wp.codeEditor.initialize( "tscfwc_setting_wooc_template_editor", %s );setInterval(()=>{
                  ts_wc.codemirror.refresh()
                  ts_wc.codemirror.save()

                  },500); } );', wp_json_encode($settings))
        );
    }


}
