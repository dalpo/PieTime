<?php
App::uses('ConnectionManager', 'Model');

class L10nFieldBehavior extends ModelBehavior {

  protected $_Model = null;

  protected $_defaultOptions = array();

  protected $_options = array();

  protected $_dataTypes = array('date', 'datetime', 'time', 'timestamp');

  protected $_ignoredFields = array('created', 'updated', 'modified');

  private $__connection = null;

  private $__l10nFields = array();


  public function setup(Model &$model, $options = array()) {

    $this->_options[$model->name] = array_merge($this->_defaultOptions, $options);
    $this->_Model =& $model;
    $this->__connection =& ConnectionManager::getDataSource($this->_model->useDbConfig);
    
    foreach($this->__connection->columns as $field => $info) {
      if(isset($info['format']) && in_array($info['format'], $this->_dataTypes)) {
        $this->__l10nFields[$this->_Model->name][$field] = $info['format'];
      }
    }
  }

  public function getOptions() {
    return $this->_options[$this->_Model->name];
  }

  public function getL10nFields() {
    return $this->__localizedFields[$this->_Model->name];
  }




  /**********************************/
  
  private $_columnTypes;
  
  private $__cakeAutomagicFields = array('created', 'updated', 'modified');
  
  private $__typesFormat;

  public function setup(Model &$model, $options = array()) {
    $this->settings = array(
      'ignoreAutomagic' => true
    );
    
    $this->_Model =& $model;
    $this->_columnTypes = $this->_Model->getColumnTypes();
    
    
    $db =& ConnectionManager::getDataSource($this->_model->useDbConfig);
    
    foreach($db->columns as $type => $info)
    {
      if(isset($info['format']))
      {
        $this->__typesFormat[$type] = $info['format'];
      }
    }
  }

  public function beforeValidate(Model &$model)
  {
    $this->_model =& $model;
    $this->_modelFields = $this->_model->getColumnTypes();

    parent::beforeValidate($model);
    
    return $this->localizeData();
  }
  
  public function beforeSave(Model &$model)
  {
    $this->_model =& $model;
    $this->_modelFields = $this->_model->getColumnTypes();

    parent::beforeSave($model);
    
    return $this->localizeData();
  }
  
  /**
   * Invoca localização das informações no callback beforeFind
   * 
   * @see ModelBehavior::beforeFind()
   */
  public function beforeFind(&$model, $query)
  {
    $this->_model =& $model;
    $this->_modelFields = $this->_model->getColumnTypes();
    
    parent::beforeFind($model, $query);
    
    $this->localizeData($query['conditions']);
    
    return $query;
  }
  
}
