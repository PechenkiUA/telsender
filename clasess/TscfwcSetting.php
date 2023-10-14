<?php

namespace pechenki\Telsender\clasess;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class TscfwcSetting
 * @package pechenki\Telsender\clasess
 */
class TscfwcSetting
{


    /**
     * @var array $setting
     */
    public $setting;

    /**
     * TscfwcSetting constructor.
     * @param $argument
     */
    function __construct()
    {
        $this->loadSettings();
    }

    /**
     * @return void
     */
    public function loadSettings()
    {
        $a = [
            'tscfwc_setting_acseswpforms' => [],
            'tscfwc_setting_wooc_template' => '',
            'tscfwc_setting_acsesform' => [],
            'tscfwc_setting_status_wc' => [
                'wc-processing'
            ],
            'tscfwc_setting_setcheck' => [
                'wooc_check' => 0,
                'wooc_all_order' => 0
            ],
            'tscfwc_enabled' => 0
        ];
        $option = unserialize(get_option(TSCFWC_SETTING));
        if (is_array($option)) {
            $this->setting = array_merge($a, $option);
        } else {
            $this->setting = array_merge($a, []);
        }

    }

    /**
     * @return array
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * Get settings param
     * @param $value
     * @return false|mixed
     */
    public function Option($value)
    {
        if ($value) {
            $return = $this->getSetting();
            if (isset($return[$value])) {
                return $return[$value];
            } else {
                return false;
            }


        } else {
            return $this->getSetting();

        }
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (!empty($this->setting[$name]) && $this->setting[$name]) {

            return $this->setting[$name];
        }
        return false;


    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {

        $this->setting[$name] = $value;
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
     * @return array|false
     */
    protected function post($param = null)
    {
        if (isset($_POST)) {
            $post = $_POST;
            if (isset($post[$param])) {

                if (is_array($post[$param])) {
                    return array_combine(array_keys($post[$param]), array_map(function ($s) {
                        return sanitize_text_field($s);
                    }, array_values($post[$param])));
                }

                return filter_input(INPUT_POST, $param);
            }

            if ($param) {
                return false;
            }

            return array_combine(array_keys($post), array_map(function ($s) {
                return filter_input(INPUT_POST, $s);
            }, array_values($post)));
        }
        return false;


    }

    /**
     * @param $param
     * @return array|false|mixed
     */
    protected function server($param = null)
    {
        if (isset($_SERVER)) {
            $server = $_SERVER;
            if (isset($server[$param])) {
                return filter_input(INPUT_SERVER, $param);
            }
            if ($param) {
                return false;
            }

            return array_combine(array_keys($server), array_map(function ($s) {
                return filter_input(INPUT_SERVER, $s);
            }, array_keys($server)));
        }
        return false;
    }

    /**
     * @param $param
     * @return array|false|mixed
     */
    protected function session($param = null)
    {
        if (isset($_SESSION)) {
            $session = $_SESSION;
            if (isset($session[$param])) {
                return filter_var($session[$param], FILEINFO_RAW);
            }
            if ($param) {
                return false;
            }

        }
        return false;
    }


}
