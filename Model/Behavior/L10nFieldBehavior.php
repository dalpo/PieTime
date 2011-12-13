<?php
App::uses('ConnectionManager', 'Model');

class L10nFieldBehavior extends ModelBehavior {

  protected $_Model = null;

  protected $_defaultOptions = array();

  protected $_options = array();

  protected $_l10nDataTypes = array('datetime', 'timestamp', 'date');

  protected $_ignoredFields = array('created', 'updated', 'modified');

  private $__connection = null;

  private $__l10nFields = array();

  private $__dataTypes = array();


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

  public function getDataType() {
    return $this->__dataTypes[$this->_Model->alias];
  }
  
  public function beforeSave(Model &$Model) {
    parent::beforeSave($Model);
    $this->_Model =& $Model;
    $dataTypes = $this->getDataType();
    foreach($this->getL10nFields() as $field => $dataType) {
      //delocalize method!
    }
  }
  
  
}
