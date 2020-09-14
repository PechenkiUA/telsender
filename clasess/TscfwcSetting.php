<?php
namespace pechenki\Telsender\clasess;
/**
 * Settings
 */
class TscfwcSetting {
  public $setting;

  function __construct($argument){
    $this->setting = $argument;
  }

  public function getSetting()
  {
    return $this->setting;
  }

  function Option($value){
    if ($value) {
      $return =  unserialize($this->getSetting());
      if (isset(  $return[$value] )) {
        return $return[$value];
      }else{
          return false;
      }


    }else{
      return unserialize($this->getSetting()); // get_option( 'ts__globalSetind' )

    }
  }




/*

*/
  // public function SendFile($url='')
  // {
  //
  //   if ($this->telegram_proxy && !$this->isSendPechenki) {
  //
  //     $this->SendTelFileProxy($url);// send file through proxy
  //
  //   }elseif($this->isSendPechenki) {
  //
  //       $this->PechenkiSendFile($url);// send file through pecheni
  //
  //   }else{
  //
  //     $this->SendTelFile($url);// send file through server Telegram
  //
  //   }
  // }



}
