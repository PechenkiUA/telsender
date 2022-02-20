<?php

namespace pechenki\Telsender\clasess;

/**
 * curl sender
 */
class TelegramSend
{
    /**
     * @var string $cfname
     */
    public $cfname;
    /**
     * @var string $titleform
     */
    public $titleform;
    /**
     * @var false|mixed|string $Pechenki_key
     */
    public $Pechenki_key;
    /**
     * @var mixed|string $Chat_id
     */
    public $Chat_id;
    /**
     * @var mixed|string $Token
     */
    public $Token;
    /**
     * @var bool $isSendPechenki
     */
    public $isSendPechenki;
    /**
     * @var string $parse_mode
     */
    public $parse_mode = 'HTML';
    /**
     * @var string $acsess_tags
     */

    public $is_send = false;
    /**
     * @var string $acsess_tags
     */

    public $acsess_tags = '<b><strong><i><u><em><ins><s><strike><a><code><pre>';

    /**
     * TelegramSend constructor.
     */
    public function __construct()
    {
        $tscfwc_setting = new TscfwcSetting(get_option(TSCFWC_SETTING));

        $this->Pechenki_key = $tscfwc_setting->Option('tscfwc_setting_newtoken');
        $this->Chat_id = $tscfwc_setting->Option('tscfwc_setting_chatid');
        $this->Token = $tscfwc_setting->Option('tscfwc_setting_token');
        $opt = $tscfwc_setting->Option('tscfwc_setting_setcheck');
        if (isset($opt['tscfwc_key'])) {
            $this->isSendPechenki = $opt['tscfwc_key'];
        }


    }

    /**
     * @param string $p_text
     */
    public function PechenkiSend($p_text = 'Нет текста')
    {
        $Pechenki_key = $this->Pechenki_key;
        wp_remote_post('https://bot.pechenki.top/bot.php', array(
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array('Ptoken' => $Pechenki_key,
                'text' => stripcslashes(html_entity_decode($p_text)),
                'parse_mode' => $this->parse_mode),
            'cookies' => array()
        ));


    }

    /**
     * send message to telegram
     */
    public function requestToTelegram($reply, $type = 'sendMessage')
    {

        $data = array('chat_id' => $this->Chat_id,
            'text' => stripcslashes(html_entity_decode($reply)),
            'parse_mode' => $this->parse_mode,
        );
        $return = $this->TelegramSendMesage($data);

        if ($return['ok'] == false) log::setLog(json_encode($return));

        return $return;


    }


    public function saveTsMail($data)
    {
        $isdata = get_option('ts__dataMessage');
        if ($isdata) {
            $newData = unsereliaze($isdata);
        }
    }

    /**
     * @param $data
     * @return false|mixed|string
     */
    private function TelegramSendMesage($data)
    {

        $return = wp_remote_post('https://api.telegram.org/bot' . $this->Token . '/sendMessage', array(
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => $data,
            'cookies' => array()
        ));

        if (is_wp_error($return)) {

            return json_encode(['ok' => false, 'curl_error_code' => $return->get_error_message()]);
        } else {
            $return = json_decode($return['body'], true);
        }

        return $return;

    }

    /**
     * @param string $value - text message
     */
    public function SendMesage($value)
    {
        if ($this->isSendPechenki) {

            if (!$this->is_send){
                $this->PechenkiSend(strip_tags($value, $this->acsess_tags));// send message through pecheni
                $this->is_send = true;
            }


        } else {

          return $this->requestToTelegram(strip_tags($value, $this->acsess_tags));// send message through server Telegram

        }

    }

    public function UpdateMessage($text,$message_id){

        $data = array('chat_id' => $this->Chat_id,
            'text' => stripcslashes(html_entity_decode($text)),
            'parse_mode' => $this->parse_mode,
            'message_id'=>intval($message_id)
        );

        $return = wp_remote_post('https://api.telegram.org/bot' . $this->Token . '/editMessageText', array(
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => $data,
            'cookies' => array()
        ));

        if (is_wp_error($return)) {

            return json_encode(['ok' => false, 'curl_error_code' => $return->get_error_message()]);
        } else {
            $return = json_decode($return['body'], true);
        }

        return $return;

        if ($return['ok'] == false) log::setLog(json_encode($return));

        return $return;
    }


}
