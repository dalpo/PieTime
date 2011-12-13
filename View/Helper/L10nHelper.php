<?php

App::uses('AppHelper', 'View/Helper');
App::uses('L10nUtility', 'PieTime.Lib');

class L10nHelper extends AppHelper {

  public function localize($timestamp = time(), $format = 'time.default') {
    return L10nUtility::localize($timestamp, $format);
  }

  public function date($date = null, $format = 'default') {
    return $this->localize($date, "date.{$format}");
  }

  public function time($date = null, $format = 'default') {
    return $this->localize($date, "time.{$format}");
  }

}

