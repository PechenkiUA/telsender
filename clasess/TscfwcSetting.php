<?php

namespace pechenki\Telsender\clasess;
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
    function __construct($argument)
    {
        $this->setting = $argument;
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
    function Option($value)
    {
        if ($value) {
            $return = unserialize($this->getSetting());
            if (isset($return[$value])) {
                return $return[$value];
            } else {
                return false;
            }


        } else {
            return unserialize($this->getSetting());

        }
    }


}
