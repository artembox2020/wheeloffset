<?php
/**
 * Класс PlatformModel
 */
class PlatformModel extends CActiveRecord
{
	/**
	 * Получить экземпляр класса
	 * @return PlatformModel
	 */
	public static function model($className = __CLASS__) { return parent::model($className);}

	/**
	 * Ассоциированная таблица
	 * @return string
	 */
	public function tableName() { return 'platform_model';}

	/**
	 * Правила валидации
	 * @return array
	 */
	public function rules()
	{
		return array(
			array(
				'platform_id, model_id, year_from, year_to',
				'required',
			),
			array('year_from, year_to', 'numerical', 'integerOnly' => true),
		);
	}
	
	protected function afterValidate()
	{
		if (is_numeric($this->year_to) && is_numeric($this->year_from) && $this->year_to < $this->year_from) {
			$this->addError('year_to', '"Year to" should be more "Year from"');
		}
		
		if (!$this->hasErrors()) {
			$criteria=new CDbCriteria;
			$criteria->compare('model_id',$this->model_id);				
			$criteria->compare('platform_id',$this->platform_id);	
			if (!empty($this->id)) {
				$criteria->addCondition("id <> {$this->id}");
			}
			$model = $this->find($criteria);
			if (!empty($model)) {
				$this->addError('model_id', 'This relation Models and Platforms already exist');
			}
		}
		
		return parent::afterValidate();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'Model' => array(self::BELONGS_TO, 'AutoModel', 'model_id', 'together'=>true,),
            'Platform' => array(self::BELONGS_TO, 'Platform', 'platform_id', 'together'=>true,),
        );
	}	
	
	
	public function afterSave()
	{		
		// set platform model
		if ($this->isNewRecord) {
			$criteria=new CDbCriteria;
			$criteria->compare('model_id',$this->model_id);			
			$criteria->addCondition("year <= {$this->year_to} AND year >= {$this->year_from} AND platform_model_id IS NULL");
			$items = AutoModelYear::model()->findAll($criteria);
			foreach ($items as $item) {
				$item->platform_model_id = $this->id;
				$item->save(false);
			}
		}

		$this->_clearCache();
		
		return parent::afterSave();
	}
	
    public function afterDelete() 
	{
		$this->_clearCache();	
			
        return parent::afterDelete();
    }		

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'platform_id' => Yii::t('admin', 'Platform'),
			'model_id' => Yii::t('admin', 'Model'),
			'year_from' => Yii::t('admin', 'Year from'),
			'year_to' => Yii::t('admin', 'Year to'),
		);
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

		$criteria->compare('id',$this->id);
		$criteria->compare('model_id',$this->model_id);
		$criteria->compare('platform_id',$this->platform_id);
		$criteria->compare('year_from',$this->year_from);
		$criteria->compare('year_to',$this->year_to);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_AUTO_PLATFORM_MODEL);
	}	
	
	public function getModelTitle()
	{
		return $this->Model->Make->title . " " . $this->Model->title;
	}
	
	public static function getListByModel($model_id)
	{
		$model_id = (int) $model_id;
		
		$criteria=new CDbCriteria;
		$criteria->compare('t.model_id',$model_id);
		$criteria->with = array('Platform'=>array('together'=>true));
		$items = self::model()->findAll($criteria);
		$data = array();
		
		foreach ($items as $item) {
			$data[$item->id] = "{$item->year_from}–{$item->year_to} {$item->Platform->title}";
		}
		
		return $data;
	}
	
	
}