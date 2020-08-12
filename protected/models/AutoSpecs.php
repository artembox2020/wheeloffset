<?php

class AutoSpecs extends CActiveRecord
{	
	const SPEC_ENGINE = 120;
	const SPEC_TRANSMISSION = 141;

	const CACHE_KEY_LIST = 'AUTO_SPECS_LIST__';
	const CACHE_KEY_LIST_TYPE = 'AUTO_SPECS_LIST_TYPE_';
	const CACHE_KEY_LIST_ALIAS = 'AUTO_SPECS_LIST_ALIAS_';

	const TYPE_STRING = 0;
	const TYPE_INT = 1;
	const TYPE_FLOAT = 2;
	const TYPE_CHECKBOX = 3;
	const TYPE_SELECT = 4;
	
	private $_oldAttributes = array();
		
	public $post_options;
	public $optionsErrorsIndex;	
	public $maxlength = 2;	
		
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BodyStyle the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'auto_specs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, alias', 'required'),
			array('append, post_options', 'safe'),
			array('alias', 'unique'),
			array('rank, type, is_filter, is_required, maxlength, group_id', 'numerical', 'integerOnly' => true),
			//array('maxlength', 'length', 'min' => 2, 'max' => 128),
			array('post_options', 'validateOptions', ),			
		);
	}		

	public function validateOptions()
	{
		if ($this->type == self::TYPE_SELECT) {
			foreach ($this->post_options as $index => $item) {
				if (empty($item['value'])) {
					$this->optionsErrorsIndex['value'][$index] = 1;
				}
			}
			if (!empty($this->optionsErrorsIndex)) {
				$this->addError('post_options', 'Значения списка пустое');
			}
		}
	}	
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'Group' => array(self::BELONGS_TO, 'AutoSpecsGroup', 'group_id', 'together'=>true,),
			'Options' => array(self::HAS_MANY, 'AutoSpecsOption', 'specs_id'),
	    );
	}	
	
	public function afterFind()
	{
		$this->_oldAttributes = $this->attributes;
		
		return parent::afterFind();
	}
	
	public function beforeValidate()
	{
		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
		
		$this->alias = self::slug($this->alias);
	
		return parent::beforeValidate();
	}
	
	public static function slug($value) 
	{
		$value = str_replace(array('(', ')', ' ', '-', '.', ',', '/', '&', ';'), '_', strtolower($value));
		$value = str_replace('__', '_', $value);			
	
		return $value;
	}
	
	public function afterSave()
	{
		if ($this->isNewRecord) {
			$this->addField();
		} else {
			//$this->updateField();
		}
		
		//options
		if (!$this->isNewRecord && $this->scenario == 'updateAdmin') {
			if ($this->type == self::TYPE_SELECT) {
				$optionIds = array();
				foreach ((array)$this->post_options as $option)  {
					if (isset($option['id'])) 
						$optionIds[] = $option['id'];				
				}
				
				$criteria = new CDbCriteria();
				$criteria->compare('specs_id', $this->id);
				$issetOptions = AutoSpecsOption::model()->findAll($criteria);
				foreach ($issetOptions as $issetOption) {
					if (!in_array($issetOption->id, $optionIds)) {
						$issetOption->delete();
					}
				}	
			} else {
				 $criteria = new CDbCriteria();
				 $criteria->compare('specs_id', $this->id);
				
				 $options = AutoSpecsOption::model()->findAll($criteria);
				 foreach ($options as $option) {
					$option->delete();
				 }
			}
		}
		
		if ($this->type == self::TYPE_SELECT && is_array($this->post_options)) {		
			foreach ($this->post_options as $option) {			
				$optionModel = null;
				if (isset($option['id']))  {
					$optionModel = AutoSpecsOption::model()->findByPk($option['id']);
				}
					
				if (empty($optionModel)) {
					$optionModel = new AutoSpecsOption();
					$optionModel->specs_id = $this->id;
				}			
			
				$optionModel->value = $option['value'];
				$optionModel->save();
			}
		}	
		
		
		$this->clearCache();
		
		return parent::afterSave();
	}	
	
	public function afterDelete()
	{
		$this->clearCache();
		
		//$this->deleteField();
		
		return parent::afterDelete();
	}	
	
	private function clearCache()
	{
		Yii::app()->cache->delete(self::CACHE_KEY_LIST);
		Yii::app()->cache->delete(self::CACHE_KEY_LIST_TYPE);
		Yii::app()->cache->delete(self::CACHE_KEY_LIST_ALIAS);
		Yii::app()->cache->delete(AutoSpecsOption::CACHE_KEY_LIST . $this->id);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => Yii::t('admin', 'Title'),
			'group_id' => Yii::t('admin', 'Group'),
			'alias' => Yii::t('admin', 'Alias'),
			'is_required' => Yii::t('admin', 'Required'),
			'is_filter' => Yii::t('admin', 'On in Filter'),
			'type' => Yii::t('admin', 'Type'),
			'append' => Yii::t('admin', 'Append'),
			'rank' => Yii::t('admin', 'Rank'),
		);
	}
	
	public static function getTypes()
	{
		return array(
			self::TYPE_STRING => Yii::t('admin', 'String (input)'),
			self::TYPE_INT => Yii::t('admin', 'Integer number (input)'),
			self::TYPE_FLOAT => Yii::t('admin', 'Float number (input)'),
			self::TYPE_CHECKBOX => Yii::t('admin', 'Boolean (checkbox)'),
			self::TYPE_SELECT => Yii::t('admin', 'List (select)'),
		);
	}
	
	public function getTypeTitle()
	{
		$types = self::getTypes();
		if ($types[$this->type]) {
			return $types[$this->type];
		} else {
			return false;
		}
	}	
	
	public static function getTypesType()
	{
		return array(
			self::TYPE_STRING => 'VARCHAR(128)',
			self::TYPE_INT => 'INT(11)',
			self::TYPE_FLOAT => 'FLOAT(9,2)',
			self::TYPE_CHECKBOX => 'TINYINT(1) UNSIGNED',
			self::TYPE_SELECT => 'INT(11) UNSIGNED',
		);
	}
	
	public function getTypesTypeTitle()
	{
		$types = self::getTypesType();
		if (isset($types[$this->type])) {
			return $types[$this->type];
		} else {
			return $types[self::TYPE_STRING];
		}
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.title',$this->title, true);
		$criteria->compare('t.alias',$this->alias, true);
		$criteria->compare('t.append',$this->append, true);
		$criteria->compare('t.group_id',$this->group_id);
		$criteria->compare('t.type',$this->type);
		$criteria->compare('t.is_filter',$this->is_filter);
		$criteria->compare('t.is_required',$this->is_required);
		$criteria->compare('t.rank',$this->rank);
		$criteria->with = array('Group');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public static function getAll()
	{
		$data = Yii::app()->cache->get(self::CACHE_KEY_LIST);
		if (empty($data)) {
			$data = (array)self::model()->findAll();
			Yii::app()->cache->set(self::CACHE_KEY_LIST, $data, 60*60*24*31);
		}
		
		return $data;
	}	
	
	public static function getAllType()
	{
		$data = Yii::app()->cache->get(self::CACHE_KEY_LIST_TYPE);
		if (empty($data)) {
			$criteria=new CDbCriteria;
			$data = CHtml::listData(self::model()->findAll($criteria), 'id', 'type');
			Yii::app()->cache->set(self::CACHE_KEY_LIST_TYPE, $data, 60*60*24*31);
		}
		
		return $data;
	}	
	
	public static function getAllAlias()
	{
		$data = Yii::app()->cache->get(self::CACHE_KEY_LIST_ALIAS);
		if (empty($data)) {
			$criteria=new CDbCriteria;
			$data = CHtml::listData(self::model()->findAll($criteria), 'alias', 'id');
			Yii::app()->cache->set(self::CACHE_KEY_LIST_ALIAS, $data, 60*60*24*31);
		}
		
		return $data;
	}	
	
	public static function getAllWithGroup()
	{
		$criteria=new CDbCriteria;
		$criteria->condition = 't.group_id IS NOT NULL';
		$criteria->order = 'Group.rank, Group.id, t.rank, t.id';
		$criteria->with = array('Group' => array('together'=>true));
		$specs = self::model()->findAll($criteria);
		$data = array();
		foreach ($specs as $spec) {
			if (!isset($data[$spec->group_id]['title']))
				$data[$spec->group_id]['title'] = $spec->Group->title;
			
			$data[$spec->group_id]['specs'][$spec->alias] = array(
				'title' => $spec->title,
				'type' => $spec->type,
				'append' => $spec->append,
				'id' => $spec->id,
			);
		}
		
		$criteria=new CDbCriteria;
		$criteria->condition = 'group_id IS NULL';
		$criteria->order = 'rank';
		$specs = self::model()->findAll($criteria);		
			
		if (!empty($specs)) {	
			$data[0]['title'] = 'Without Group';	
			foreach ($specs as $spec) {
				$data[0]['specs'][$spec->alias] = array(
					'title' => $spec->title,
					'type' => $spec->type,
					'append' => $spec->append,
					'id' => $spec->id,
				);			
			}	
		}
			
		return $data;
	}	
	
	public static function getAllWithAttributes()
	{
		$criteria=new CDbCriteria;
		$specs = self::model()->findAll($criteria);
		$data = array();
		foreach ($specs as $spec) {
			
			$data[$spec->id] = $spec->attributes;
		}
		
		return $data;
	}	
	
	public function addField()
	{
		$attribute = AutoCompletion::PREFIX_SPECS.$this->alias;
		
		if (!AutoCompletion::model()->hasAttribute($attribute))
		Yii::app()->db->createCommand()->addColumn('auto_completion', $attribute, $this->getTypesTypeTitle());
	}

	public function updateField()
	{
		if ($this->_oldAttributes['alias'] != $this->alias) {
			Yii::app()->db->createCommand()->renameColumn('auto_completion', AutoCompletion::PREFIX_SPECS.$this->_oldAttributes['alias'], AutoCompletion::PREFIX_SPECS.$this->alias);	
		}
		
		if ($this->_oldAttributes['type'] != $this->type) {
			Yii::app()->db->createCommand()->alterColumn('auto_completion', AutoCompletion::PREFIX_SPECS.$this->alias, $this->getTypesTypeTitle());	
		}
	}

	public function deleteField()
	{
		$attribute = AutoCompletion::PREFIX_SPECS.$this->alias;
		
		if (AutoCompletion::model()->hasProperty($attribute)) {
			Yii::app()->db->createCommand()->dropColumn('auto_completion', $attribute);
		}
	}
	
	public function getDataOptions()
	{
		if (Yii::app()->request->isPostRequest) {
			return $this->post_options;
		} else {
			if ($this->isNewRecord) {
				return array();
			} else {
				$data = array();
				foreach ($this->Options as $option) {
					$data[] =  $option->attributes;
				}
				
				return $data;
			}
		}
	}
	

}