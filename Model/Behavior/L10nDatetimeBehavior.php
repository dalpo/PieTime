<?php
App::uses('ConnectionManager', 'Model');
App::uses('L10nUtility', 'PieTime.Lib');

class L10nDatetimeBehavior extends ModelBehavior {

  protected $_Model = null;

  protected $_defaultOptions = array();

  protected $_options = array();

  protected $_l10nDataTypes = array('datetime', 'timestamp', 'date');

  protected $_ignoredFields = array('created', 'updated', 'modified');

  private $__connection = null;

  private $__l10nFields = array();

  private $__dataTypes = array();

  private $__beforeSaveFields = array();


  public function setup(Model &$Model, $options = array()) {
    $this->_options[$Model->alias] = array_merge($this->_defaultOptions, $options);
    $this->_Model =& $Model;
    $this->__connection =& ConnectionManager::getDataSource($this->_Model->useDbConfig);

    foreach($this->_Model->getColumnTypes() as $field => $dataType) {
      if(in_array($dataType, $this->_l10nDataTypes) && !in_array($field, $this->_ignoredFields)) {
        $this->__l10nFields[$this->_Model->alias][$field] = $dataType;
      }
    }

    foreach($this->__connection->columns as $dataType => $info) {
      if(isset($info['format'])) {
        $this->__dataTypes[$this->_Model->alias][$dataType] = $info['format'];
      }
    }
  }


  public function getOptions() {
    return $this->_options[$this->_Model->alias];
  }


  public function getL10nFields() {
    return $this->__l10nFields[$this->_Model->alias];
  }


  public function getDataTypes() {
    return $this->__dataTypes[$this->_Model->alias];
  }


  public function getFormatByDataType($datatype = null) {
    if($datatype) {
      $datatypes = $this->getDataTypes();
      return $datatypes[$datatype];
    } else {
      return null;
    }
  }

  
  public function beforeSave(Model &$Model) {

    $this->_Model =& $Model;

    foreach($this->getL10nFields() as $field => $dataType) {
      //delocalize method!

      // $this->__beforeSaveFields[$this->_Model->alias][$field] = $this->_Model->data[$this->_Model->alias][$field];

      $this->_Model->data[$this->_Model->alias][$field] = $this->toServerTime(
        $field,
        $this->_Model->data[$this->_Model->alias][$field], 
        $dataType
      );
    }

    parent::beforeSave($Model);
    return true;
  }


  public function toServerTime($field = null, $datetime = null, $datatype = 'datetime') {

    if(empty($this->_Model->data[$this->_Model->alias][$field])) {
      return false;
    }

    $clientFormat = $this->getClientFormatByField($field, $datatype);
    $serverFormat = $this->getFormatByDataType($datatype);

    // $clientExplodedFormat = preg_split("/[^a-zA-Z]/", str_replace("%", "", $clientFormat));
    preg_match("/[^a-zA-Z]/", str_replace("%", "", $clientFormat), $clientExplodedSymbol);
    $clientExplodedFormat = explode($clientExplodedSymbol[0], str_replace("%", "", $clientFormat));
    $clientExplodedValue = explode($clientExplodedSymbol[0], $this->_Model->data[$this->_Model->alias][$field]);

    $clientValues = array();
    foreach ($clientExplodedFormat as $idx => $format) {
      $clientValues[$format] = $clientExplodedValue[$idx];
    }

    preg_match_all("/[^a-zA-Z]/", str_replace("%", "", $serverFormat), $serverExplodedSymbols);

    $serverResult = '';
    for ($i = 0; $i < strlen($serverFormat); $i++) { 
      if(in_array($serverFormat[$i], $serverExplodedSymbols[0])) {
        $serverResult.= $serverFormat[$i];
      } else {
        $serverResult.= (isset($clientValues[$serverFormat[$i]]) ? $clientValues[$serverFormat[$i]] : "00");
      }
    }

    return $serverResult;
  }


  public function getClientFormatByField($field = null, $datatype = 'datetime') {
    
    //@todo: Check if exist "{field}CustomFormat" method for custom input format

    switch ($datatype) {
      case 'time':
        $formatType = "time";
        break;
      default:
        $formatType = "date";
        break;
    }

    return L10nUtility::getFormat("{$formatType}.default");
  }
  
  
}
