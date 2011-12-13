<?php

App::uses('I18n', 'I18n');

class L10nUtility {

  static public function makeTime($datetime){
    $date = $time = array(0, 0, 0);

    $parts = explode(" ", $datetime);
    $date = explode("-", $parts[0]);
    if($parts[1]) {
      $time = explode(":", $parts[1]);      
    }

    return mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);
  }

  static public function getFormat($key = 'time.default') {
    return I18n::translate($singular, $plural = null, $domain = null);
  }

  static public function localize($timestamp = time(), $format = 'time.default') {
    return strftime(self::getFormat($format), self::makeTime($timestamp));
  }

}