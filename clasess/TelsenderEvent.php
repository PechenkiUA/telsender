<?php
/**
 *
 */
namespace pechenki\Telsender\clasess;

use pechenki\Telsender\clasess\TelsenderCore;

/**
 * @property mixed|null login_success
 * @property mixed|null login_failed
 * @property mixed|null login_failed_chat_id
 * @property mixed|null login_success_chat_id
 * @property mixed|null wc_add_to_cart_chat_id
 * @property mixed|null default_chat_id
 * @property mixed|null wc_add_to_cart
 * @property mixed|null interception_post_chat_id
 * @property mixed|null interception_post
 * @property mixed|null interception_post_param
 * @property mixed|null interception_post_value
 * @property mixed|null interception_list_val
 * @property mixed|null ts_event_bots
 * @property mixed|null bots
 * @property mixed|null otherbots
 * @property mixed|null token
 * @property mixed|null default_token
 * @property mixed|null utm
 * @property array utm_list_val
 */
class TelsenderEvent
{
    public $settings = [];

    public $telsener;

    /**
     * TelsenderEvent constructor.
     * @param TelsenderCore $Telsender
     */
    public function __construct(TelsenderCore $Telsender)
    {
        $this->telsener = $Telsender;

//        add_action('init',[$this,'init']);
        $this->init();

    }

    /**
     * Load settings to object
     */
    public function loadSettings()
    {
        $settings = [];

        $settings['searchBots'] = [
            'Googlebot' => 'Google bot',
            'Bingbot ' => 'Bing bot',
            'Slurp ' => 'Slurp',
            'DuckDuckBot' => 'DuckDuck Bot',
            'YandexBot' => 'Yandex Bot',
            '' => 'Other',
        ];
        $settings['login_failed'] = get_option('ts_event_login_failed');
        $settings['token'] = get_option('ts_event_token');
        $settings['login_failed_chat_id'] = get_option('ts_event_login_failed_chat_id');

        $settings['login_success'] = get_option('ts_event_login_success');
        $settings['login_success_chat_id'] = get_option('ts_event_login_success_chat_id');

        $settings['interception_post_chat_id'] = get_option('ts_event_interception_post_chat_id');

        $settings['interception_post_value'] = get_option('ts_event_interception_post_value');
        $settings['interception_post_param'] = get_option('ts_event_interception_post_param');


        $settings['utm'] = get_option('ts_event_utm');
        $settings['utm_chat_id'] = get_option('ts_event_utm_chat_id');
        /**
         *
         */
        $utm_list_sours = get_option('ts_event_utm_list_param');
        $utm_list_value = get_option('ts_event_utm_list_value');

        if (isset($utm_list_sours) && isset($utm_list_value)
            && is_array($utm_list_sours) && is_array($utm_list_value
                && !empty($utm_list_sours) && !empty($utm_list_value))
        ) {
            $settings['utm_list_val'] = array_map(function ($a, $b) {
                return [
                    'source' => $a,
                    'value' => $b
                ];
            }, $utm_list_sours, $utm_list_value);
        } else {

            $settings['utm_list_val'][] = [
                'source' => '',
                'value' => ''
            ];
        }
        /**
         * Interception_post
         */
        $settings['interception_post'] = get_option('ts_event_interception_post');
        $interception_value = get_option('ts_event_interception_list_value');
        $interception_title = get_option('ts_event_interception_list_title');
        $interception_param = get_option('ts_event_interception_list_param');


        if (
            isset($interception_value) && isset($interception_param)
            && isset($interception_title) && is_array($interception_title)
            && is_array($interception_param) && is_array($interception_value)
            && !empty($interception_param) && !empty($interception_value)
        ) {
            $settings['interception_list_val'] = array_map(function ($a, $b, $c) {
                return [
                    'param' => $a,
                    'value' => $b,
                    'title' => $c
                ];
            }, $interception_param, $interception_value, $interception_title);
        } else {

            $settings['interception_list_val'][] = [
                'param' => '',
                'value' => '',
                'title' => ''
            ];
        }

        $settings['default_chat_id'] = ($this->telsener->telegram->Chat_id) ?: null;
        $settings['default_token'] = ($this->telsener->telegram->Token) ?: null;

        $settings['wc_add_to_cart'] = get_option('ts_event_wc_add_to_cart');
        $settings['wc_add_to_cart_chat_id'] = get_option('ts_event_wc_add_to_cart_chat_id');


        $this->loadBotsSettings();


        $this->appendSetting($settings);


    }

    private function loadBotsSettings()
    {

        /**
         * Bots
         */
        $settings['bots'] = get_option('ts_event_bots');
        $settings['otherbots'] = get_option('otherbots');
        $settings['bots_list_val'] = get_option('ts_event_bot_list_value')?:[];


        $this->appendSetting($settings);


    }

    public function appendSetting($data)
    {

        $this->settings = array_merge($this->settings, $data);
    }

    /**
     * init
     */
    public function init()
    {
        $this->loadSettings();




        add_action('admin_menu', array($this, 'settingsTemplete'));
        add_action('init', array($this, 'load'));

        /**
         * failed login
         */
        if ($this->login_failed) add_action('wp_login_failed', array($this, 'failedlogin'), 10, 2);
        /**
         * Success login
         */
        if ($this->login_success) add_action('wp_login', array($this, 'login'), 10, 2);

        /**
         * Add to cart WC
         */
        if ($this->wc_add_to_cart) add_action('woocommerce_add_to_cart', array($this, 'TelsenderAddToCart'), 10, 6);

        /**
         *
         */
        if ($this->interception_post) $this->interception_post();

        if ($this->bots) add_action('wp_head', array($this, 'interception_bots'), 99);


        add_shortcode('TS_PAGE', array($this, 'viewPage'));

    }


    /**
     * Interception Post param
     */
    public function interception_post()
    {

        if (!$this->interception_post) return;
        if ($this->token) $this->telsener->telegram->Token = $this->token;
        $post = $this->responsesS();
        $send = false;
        $title = false;
        foreach ($this->interception_list_val as $val) {
            if (isset($post[$val['param']]) && $post[$val['param']] == $val['value']) {
                $title = $val['title'];
                $send = true;
                break;
            }
        }

        if (!$send) return;

        $message = ArrayHelper::ToString($post);

        $message = $title . PHP_EOL . $message;

        $this->telsener->telegram->isSendPechenki = false; // fix
        if ($this->interception_post_chat_id) {
            $this->telsener->telegram->Chat_id = $this->interception_post_chat_id;
        }

        $this->telsener->telegram->SendMesage($message);


    }

    /**
     *
     */

    public function interception_bots()
    {
        if (!$this->bots) return;
        if ($this->token) $this->telsener->telegram->Token = $this->token;
        global $post;
        $server = $this->getServer();
        $userAgent = (isset($server['HTTP_USER_AGENT'])) ? $server['HTTP_USER_AGENT'] : false;
        if (!$this->otherbots) return ;

        $listBotsDetected = array_merge($this->bots_list_val, explode(',', $this->otherbots));

        $send = false;
        if ($listBotsDetected) {
            foreach ($listBotsDetected as $item) {
                if ($userAgent && $userAgent && !empty($item) &&
                    (strpos($userAgent, $item) || strpos($userAgent, $item) === 0)) {
                    $send = true;
                    $bots = $item;
                    break;
                }
                $send = false;
            }
        }

        if (!$send) return;


        $message = <<<'TAG'
Visit Search bot {bot}:  
{REMOTE_ADDR} 
postId: {id} 
#bot{bot}  
TAG;
        $post = $this->responsesS();

        $server = $this->getServer();

        $variable = [];


        $variable['{bot}'] = $bots;
        $variable['{id}'] = get_queried_object_id();
        $variable['{REMOTE_ADDR}'] = (isset($server['REMOTE_ADDR'])) ? $server['REMOTE_ADDR'] : 'None';
        $variable['{USER_AGENT}'] = (isset($server['HTTP_USER_AGENT'])) ? $server['HTTP_USER_AGENT'] : 'None';


        $message = str_replace(array_keys($variable), array_values($variable), $message);
        if ($this->login_failed_chat_id) {
            $this->telsener->telegram->Chat_id = $this->login_failed_chat_id;
        }
        $this->telsener->telegram->isSendPechenki = false; // fix
        $this->telsener->telegram->SendMesage($message);

    }

    /**
     *
     */
    public function load()
    {

    }

    /**
     * @param $cart_item_key
     * @param $product_id
     * @param $quantity
     * @param $variation_id
     * @param $variation
     * @param $cart_item_data
     */
    function TelsenderAddToCart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {

        $product = wc_get_product($product_id);

        $server = $this->getServer();

        $variable = [];
        $variable['{productName}'] = $product->get_name();
        $variable['{quantity}'] = $quantity;
        $variable['{REMOTE_ADDR}'] = (isset($server['REMOTE_ADDR'])) ? $server['REMOTE_ADDR'] : 'None';
        $variable['{USER_AGENT}'] = (isset($server['HTTP_USER_AGENT'])) ? $server['HTTP_USER_AGENT'] : 'None';
        $variable['{REFERER}'] = (isset($server['HTTP_REFERER'])) ? $server['HTTP_REFERER'] : 'None';

        $message = <<<'TAG'

🛒 Add to cart: 
Количество: <code>{quantity}</code>
Продукт: <code>{productName}</code>        
Ip: <code>{REMOTE_ADDR}</code>

#Add_to_cart  
TAG;
        $message = apply_filters('tsevent_after_add_to_cart', $message, $variable);


        $message = str_replace(array_keys($variable), array_values($variable), $message);
        if ($this->wc_add_to_cart_chat_id) {
            $this->telsener->telegram->Chat_id = $this->wc_add_to_cart_chat_id;
        }
        $this->telsener->telegram->isSendPechenki = false; // fix
        $this->telsener->telegram->SendMesage($message);

    }

    /**
     * @param $username
     * @param $error
     */
    public function failedlogin($username, $error)
    {

        if ($this->token) $this->telsener->telegram->Token = $this->token;
        $message = <<<'TAG'

📛 failed login 📛:
Login : <code>{USER_NAME}</code>
Ip: <code>{REMOTE_ADDR}</code>
User agent: <code>{USER_AGENT}</code>        
Domen: <b>{DOMEN}</b>       
  
 
#failed_login  
TAG;
        $post = $this->responsesS();

        $server = $_SERVER;

        $variable = [];

        $variable['{USER_NAME}'] = $username;
        $variable['{Pass}'] = (isset($post['pwd'])) ? $post['pwd'] : 'null';
        $variable['{DOMEN}'] = get_option('siteurl');
        $variable['{REMOTE_ADDR}'] = (isset($server['REMOTE_ADDR'])) ? $server['REMOTE_ADDR'] : 'None';
        $variable['{USER_AGENT}'] = (isset($server['HTTP_USER_AGENT'])) ? $server['HTTP_USER_AGENT'] : 'None';
        $variable['{REFERER}'] = (isset($server['HTTP_REFERER'])) ? $server['HTTP_REFERER'] : 'None';


        $message = str_replace(array_keys($variable), array_values($variable), $message);
        if ($this->login_failed_chat_id) {
            $this->telsener->telegram->Chat_id = $this->login_failed_chat_id;
        }
        $this->telsener->telegram->isSendPechenki = false; // fix
        $this->telsener->telegram->SendMesage($message);


    }

    /**
     * @param $username
     * @param $error
     */
    public function login($user_login, $user)
    {
        if ($this->token) $this->telsener->telegram->Token = $this->token;
        $message = <<<'TAG'
✅ Login success ✅:
Login : <code>{USER_NAME}</code>  
Ip: <code>{REMOTE_ADDR}</code>
User agent: <code>{USER_AGENT}</code>        
Domain: <b>{DOMEN}</b> 
LOCATION: <b>{LOCATION}</b>   
    
#Login_success    
TAG;
        $post = $this->responsesS();

        $server = $this->getServer();

        $variable = [];

        $variable['{USER_NAME}'] = $user_login;
        $variable['{Pass}'] = (isset($post['pwd'])) ? $post['pwd'] : 'null';
        $variable['{DOMEN}'] = get_option('siteurl');
        $variable['{REMOTE_ADDR}'] = (isset($server['REMOTE_ADDR'])) ? $server['REMOTE_ADDR'] : 'None';
        $variable['{USER_AGENT}'] = (isset($server['HTTP_USER_AGENT'])) ? $server['HTTP_USER_AGENT'] : 'None';
        $variable['{REFERER}'] = (isset($server['HTTP_REFERER'])) ? $server['HTTP_REFERER'] : 'None';
        $variable['{LOCATION}'] = $this->detect_city($variable['{REMOTE_ADDR}']);

        $message = str_replace(array_keys($variable), array_values($variable), $message);
        if ($this->login_success_chat_id) {
            $this->telsener->telegram->Chat_id = $this->login_success_chat_id;
        }
        $this->telsener->telegram->isSendPechenki = false; // fix
        $this->telsener->telegram->SendMesage($message);


    }


    public function viewPage($attr)
    {

        global $post;

        $server = $this->getServer();
        if ($this->token) $this->telsener->telegram->Token = $this->token;
        $url = str_replace('/', '_', $server['REQUEST_URI']);

        if (isset($_SESSION[$url])) return false; //  reset one send

        $variable = [];
        $variable['{REMOTE_ADDR}'] = (isset($server['REMOTE_ADDR'])) ? $server['REMOTE_ADDR'] : 'None';
        $variable['{USER_AGENT}'] = (isset($server['HTTP_USER_AGENT'])) ? $server['HTTP_USER_AGENT'] : 'None';
        $variable['{REFERER}'] = (isset($server['HTTP_REFERER'])) ? $server['HTTP_REFERER'] : 'None';
        $variable['{TITLE}'] = get_the_title($post->ID);

        $message = <<<'TAG'
🆙 View page: <b>{TITLE}</b>
Ip: <b>{REMOTE_ADDR}</b>
User agent: <b>{USER_AGENT}</b>
Referer: <b>{REFERER}</b>  
      
#View_page   
TAG;

        $variable = apply_filters('tsevent_view_page', $variable);
        $message = str_replace(array_keys($variable), array_values($variable), $message);
        $this->telsener->telegram->isSendPechenki = false; // fix
        $this->telsener->telegram->SendMesage($message);

        $_SESSION[$url] = 1;

    }

    /**
     *
     */
    public function settingsTemplete()
    {
        add_submenu_page('TelSender-Pro', 'Events', 'Events', 'manage_options', 'telsender-event', array($this, 'renderSettings'));
        add_submenu_page('telsender', 'Events', 'Events', 'manage_options', 'telsender-event', array($this, 'renderSettings'));
    }

    /**
     * @return mixed
     */
    public function renderSettings()
    {

        wp_enqueue_style('multi-select', plugin_dir_url(__FILE__) . '../assets/css/multiselect.css');

        wp_enqueue_style('tsevent-css', plugin_dir_url(__FILE__) . '../assets/css/tsevent.css','0.1');

        wp_enqueue_script('multi-select.', plugin_dir_url(__FILE__) . '../assets/js/multiselect.js');
        wp_enqueue_script('tsevent', plugin_dir_url(__FILE__) . '../assets/js/tsevent.js');
        return $this->render('/assets/template/settings', $this->settings);

    }

    /**
     * @param $path
     * @param array $vars
     * @return mixed
     */
    protected function render($path, $vars = [])
    {
        $pathBase = TELSENDER_DIR;
        extract($vars);
        return require $pathBase . $path . '.php';

    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->settings) && !empty($this->settings[$name])) {

            return $this->settings[$name];
        }
        return null;

    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {

        $this->settings[$name] = $value;
    }

    /**
     * @param $ip
     * @return string
     */
    private function detect_city($ip)
    {
        $server = $this->getServer();

        $response = wp_remote_get('http://ipwhois.app/json/' . $server['REMOTE_ADDR']);
        $json = wp_remote_retrieve_body($response);

        $ipwhois_result = json_decode($json, true);
        return $ipwhois_result['country_code'] . ', ' . $ipwhois_result['region'] . ', ' . $ipwhois_result['city'];
    }

    /**
     * @return array
     */
    private function responsesS()
    {

        return filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING) ?: filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);


    }

    /**
     * @return array|false|null
     */
    private function getServer()
    {
        return filter_input_array(INPUT_SERVER, FILTER_SANITIZE_STRING);

    }


}