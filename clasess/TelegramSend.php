<?php

namespace pechenki\Telsender\clasess;

use pechenki\Telsender\clasess\TscfwcSetting;
use pechenki\Telsender\clasess\TelegramApi;
use pechenki\Telsender\clasess\log;
/**

 * curl sender

 */



class TelegramSend{

  public $cfname="";
  public $titleform="";
  public $Pechenki_key="";
  public $Chat_id="";
  public $Token="";
  public $isSendPechenki = '';
  public $parse_mode = 'HTML';
  public $acsess_tags = '<b><strong><i><u><em><ins><s><strike><a><code><pre>';

  public function __construct() {
      $tscfwc_setting = new TscfwcSetting(get_option( TSCFWC_SETTING ));

      $this->Pechenki_key = $tscfwc_setting->Option('tscfwc_setting_newtoken');
      $this->Chat_id = $tscfwc_setting->Option('tscfwc_setting_chatid');
      $this->Token = $tscfwc_setting->Option('tscfwc_setting_token');
      $opt = $tscfwc_setting->Option('tscfwc_setting_setcheck');
      if (isset($opt['tscfwc_key'])) {
          $this->isSendPechenki = $opt['tscfwc_key'];
      }


  }



      public  function PechenkiSend($p_text='Нет текста') {
        $Pechenki_key = $this->Pechenki_key;
        wp_remote_post( 'https://bot.pechenki.top/bot.php', array(
         'timeout'     => 5,
         'redirection' => 5,
         'httpversion' => '1.0',
         'blocking'    => true,
         'headers'     => array(),
         'body'        => array( 'Ptoken' => $Pechenki_key,
                                 'text'=> stripcslashes(html_entity_decode($p_text)),
                                'parse_mode'=>$this->parse_mode),
         'cookies'     => array()
        ) );




        }
/**
 * send message to telegram
 */
 public  function requestToTelegram($reply,$type = 'sendMessage') {    

    $data = array('chat_id' => $this->Chat_id,
                  'text' => stripcslashes(html_entity_decode($reply)),
                  'parse_mode' => $this->parse_mode,
                );
  $TelegramApi = new TelegramApi($this->Token);
  $return = $TelegramApi->sendMessage($data);
  
  if (!$return['ok']) log::setLog(json_encode($return));
 
  
  }


  public function saveTsMail($data) {
      $isdata = get_option('ts__dataMessage');
      if ($isdata) {
         $newData = unsereliaze($isdata);
      }
  }


  /*
  send message
  */
    public function SendMesage($value)
    {
      if ($this->isSendPechenki) {

        $this->PechenkiSend(strip_tags($value,$this->acsess_tags));// send message through pecheni

      }else{

        $this->requestToTelegram(strip_tags($value,$this->acsess_tags));// send message through server Telegram

      }

    }


}
