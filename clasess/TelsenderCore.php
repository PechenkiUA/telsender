<?php

namespace pechenki\Telsender\clasess;

use pechenki\Telsender\clasess\TelegramSend as Telegransender;

/**
 * Class TelsenderCore
 * @package pechenki\Telsender\clasess
 */
class TelsenderCore
{

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


        $this->tscfwc = new TscfwcSetting(get_option(TSCFWC_SETTING));
        $this->telegram = new Telegransender;

        $this->telegram->Pechenki_key = $this->tscfwc->Option('tscfwc_setting_newtoken');
        $this->telegram->Chat_id = $this->tscfwc->Option('tscfwc_setting_chatid');
        $this->telegram->Token = $this->tscfwc->Option('tscfwc_setting_token');


        add_action('admin_menu', array($this, 'tscfwc_dynamic_button'));

        add_action('wp_ajax_tscfwc_form_reqest', array($this, 'tscfwc_form_ajax_reqest'));
        add_action('wpforms_process_complete', array($this, 'tscfwc_wp_form'), 10, 4);
        add_action('admin_enqueue_scripts', array($this, 'wc_code_templated'));

        add_action('woocommerce_after_order_object_save', array($this, 'tscfwc_woocommerce_new_order_status'), 99, 2);


        add_action("wpcf7_mail_sent", array($this, 'wpcf7_tscfwc'), 99, 1);
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
        add_menu_page('TelSender', 'TelSender', 'manage_options', 'telsender', array($this, 'tscfwc_setting_page'), plugin_dir_url(__FILE__) . '../assets/icon-plugin.png');
    }


    /**
     * setting
     * @return html
     */

    public function tscfwc_setting_page()
    {
        load_plugin_textdomain('telsender', false, TELSENDER_DIR_NAME . '/languages/');

        wp_enqueue_style('multi-select', plugin_dir_url(__FILE__) . '../css/multiselect.css');
        wp_enqueue_script('multi-select.', plugin_dir_url(__FILE__) . '../js/multiselect.js');
        wp_enqueue_script('ajax', plugin_dir_url(__FILE__) . '../js/ajax.js');
        wp_enqueue_style('telsender', plugin_dir_url(__FILE__) . '../css/telsender.css');

        if (isset($_POST['curssent'])) {
            $reply = 'Send';
            $this->telegram->SendMesage($reply);
        }

        if ($this->tscfwc->Option('tscfwc_setting_setcheck') && isset($this->tscfwc->Option('tscfwc_setting_setcheck')['tscfwc_key'])) {
            $is_check_pechenki = $this->tscfwc->Option('tscfwc_setting_setcheck')['tscfwc_key'];
        } else {
            $is_check_pechenki = '';
        }

        if ($this->tscfwc->Option('tscfwc_setting_setcheck') && isset($this->tscfwc->Option('tscfwc_setting_setcheck')['wooc_check'])) {
            $is_check_wc = $this->tscfwc->Option('tscfwc_setting_setcheck')['wooc_check'];
        } else {
            $is_check_wc = '';
        }

        if ($this->tscfwc->Option('tscfwc_setting_setcheck') && isset($this->tscfwc->Option('tscfwc_setting_setcheck')['wooc_all_order'])) {

            $is_wooc_all_order = $this->tscfwc->Option('tscfwc_setting_setcheck')['wooc_all_order'];
        } else {
            $is_wooc_all_order = '';
        }
        if (function_exists('wc_get_order_statuses')) {
            $list_statuse_wc = wc_get_order_statuses();
        } else {
            $list_statuse_wc = [];
        }

        require_once(TELSENDER_DIR . 'template/view.php');
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
        if (is_array($this->tscfwc->Option('tscfwc_setting_acseswpforms'))) {
            if (in_array($form_data['id'], $this->tscfwc->Option('tscfwc_setting_acseswpforms'))) {

                $m = $form_data['settings']['notifications'][1]['message'];

                $entry_id = $_POST['wpforms']['entry_id'];
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
        if (in_array($ccg->id, $this->tscfwc->Option('tscfwc_setting_acsesform'))) {

            $output = wpcf7_mail_replace_tags($ccg->mail["body"]);
            $this->telegram->SendMesage($output);
        } //end if

    }

    /**
     * action new order woocommerce status
     * @param object $order
     * @return SendMesage
     */
    public function tscfwc_woocommerce_new_order_status($order)
    {


        $wc_chek = $this->tscfwc->Option('tscfwc_setting_setcheck');
        $wc_access_status = $this->tscfwc->Option('tscfwc_setting_status_wc');

        if (in_array('wc-' . $order->get_status(), $wc_access_status) || !$wc_access_status) {

            $isSendn = get_post_meta($order->get_id(), 'telsIsm', true);

            if (!$wc_chek['wooc_check'])  return;

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
        $teml = $this->tscfwc->Option('tscfwc_setting_wooc_template');
        $message = $wc->getBillingDetails($teml);

        return $this->telegram->SendMesage($message);

    }

    /**
     * @param $OrderId
     */
    private function updateOrderToTelegram($OrderId,$message_id)
    {
        $wc = new TelsenderWc($OrderId);
        $teml = $this->tscfwc->Option('tscfwc_setting_wooc_template');
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
            'tscfwc_setting_token' => (!preg_match('/[^0-9.A-Za-z:\-_=]/m', $_POST['tscfwc_setting_token']) ? $_POST['tscfwc_setting_token'] : ''),
            'tscfwc_setting_chatid' => (string)$_POST['tscfwc_setting_chatid'],
            'tscfwc_setting_wooc_template' => htmlentities($_POST['tscfwc_setting_wooc_template']),
            'tscfwc_setting_newtoken' => (!preg_match('/[^0-9.A-Za-z\-:]/m', $_POST['tscfwc_setting_newtoken']) ? $_POST['tscfwc_setting_newtoken'] : ''),
            'tscfwc_setting_setcheck' => array(
                'wooc_check' => (int)$_POST['tscfwc_setting_setcheck']['wooc_check'],
                'wooc_all_order' => (int)$_POST['tscfwc_setting_setcheck']['wooc_all_order'],
                'tscfwc_key' => (int)$_POST['tscfwc_setting_setcheck']['tscfwc_key']
            ),
        );
        /**
         * status woocommerse save
         */
        if (isset( $_POST["tscfwc_setting_status_wc"])){
            $validatePost['tscfwc_setting_status_wc'] =  array_map(function ($key) {
                return (string)$key;
            }, $_POST["tscfwc_setting_status_wc"]);
        }

        /**
         * cf-7 save
         */
        if (isset($_POST["tscfwc_setting_acsesform"])){
            $validatePost['tscfwc_setting_acsesform'] = array_map(function ($key) {
                return (int)$key;
            }, $_POST["tscfwc_setting_acsesform"]);
        }


        /**
         * wp-forms-save
         */
        if (isset($_POST["tscfwc_setting_acseswpforms"])){

            $validatePost['tscfwc_setting_acseswpforms'] = array_map(function ($key) {
                return (int)$key;
            }, $_POST["tscfwc_setting_acseswpforms"]);
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
