<?php

namespace pechenki\Telsender\clasess;

use pechenki\Telsender\clasess\TscfwcSetting;
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
  public $parse_mode = 'html';
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
                                 'text'=> $p_text,
                                'parse_mode'=>$this->parse_mode),
         'cookies'     => array()
        ) );




        }

 public  function requestToTelegram($reply,$type = 'sendMessage') {

    $token = $this->Token;

    $id = $this->Chat_id;

    $data = array('chat_id' => $id ,

                  'text' =>$reply,
                  'parse_mode' => $this->parse_mode,

                );

  return  wp_remote_get( 'https://api.telegram.org/bot'.$token.'/sendMessage?'.http_build_query($data) , array(
      'timeout'     => 5,
      'redirection' => 5,
      'httpversion' => '1.0',
      'blocking'    => true,
      'headers'     => array(),
      'body'        => null,
      'cookies'     => array()
    ) );



  }



  function Sendwooc($reply,$id_author,$type = 'sendMessage') {

    $token = $this->Token;

    $id = $this->Chat_id;

    $data = array('chat_id' => $id_author ,

                  'text' => $reply,
                  'parse_mode' => $this->parse_mode,

                );

    wp_remote_get( 'https://api.telegram.org/bot'.$token.'/sendMessage?'.http_build_query($data) , array(

      'timeout'     => 5,

      'redirection' => 5,

      'httpversion' => '1.0',

      'blocking'    => true,

      'headers'     => array(),

      'body'        => null,

      'cookies'     => array()

    ) );



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
