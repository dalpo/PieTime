<?php

App::uses('I18n', 'I18n');

class L10nUtility {

  static public function makeTime($datetime){
    if(!$datetime) {
      return false;
    }

    $date = $time = array(0, 0, 0);

    $parts = explode(" ", $datetime);
    $date = explode("-", $parts[0]);

    if(sizeof($parts) > 1) {
      $time = explode(":", $parts[1]);
    }

    return mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);
  }

  // static public function makeTimeByFormat($datetime, $format = "Y-m-d H:m:s") {}


  static public function getFormat($key = 'time.default') {
    return I18n::translate($key, null, 'pietime');
  }

  static public function localize($timestamp = null, $format = 'time.default') {

    $mktime = self::makeTime($timestamp);
    
    if($mktime) {
      return strftime(
        self::getFormat($format), 
        self::makeTime($timestamp)
      ); 
    } else {
      return null;
    }
  }

}