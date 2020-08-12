<?php

class AutoModelYearChassis extends CActiveRecord
{		
	public $make_id;
	
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
		return 'auto_model_year_chassis';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, alias, model_id, year_from, year_to', 'required'),
			array('id, make_id', 'safe'),
			array('year_from, year_to', 'numerical', 'integerOnly' => true),
			array(
				'year_from',
				'compare',
				'compareAttribute'=>'year_to',
				'operator'=>'<',
			),			
		);
	}	
	
	/**
	 * Создаем алиас к тайтлу
	 */
	private function buildAlias()
	{
		if (empty($this->alias) && !empty($this->title)) { 
			$this->alias = $this->title;
		}
		
		$this->alias = str_replace('--', '-', $this->alias);
		
		$this->alias = TextHelper::urlSafe($this->alias);
	}	

	/**
	 * Выполняем ряд действий перед валидацией модели
	 * @return boolean -- результат выполнения операции
	 */
	protected function beforeValidate()
	{
		//создаем алиас к тайтлу
		$this->buildAlias();
		return parent::beforeValidate();
	}	
	
	public function afterSave()
	{
		$this->clearCache();
		
		$criteria = new CDbCriteria;
		$criteria->compare('model_id', $this->model_id);
		$criteria->addCondition("year >= {$this->year_from} AND year <={$this->year_to}");
		$items = AutoModelYear::model()->findAll($criteria);
		foreach ($items as $item) {
			$item->chassis_id = $this->id;
			$item->save(false);
		}
		
		return parent::afterSave();
	}	
	
	public function afterDelete()
	{
		return parent::afterDelete();
	}	
	
	private function clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_MODEL_YEAR_CHASSIS);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => Yii::t('admin', 'Title'),
			'alias' => Yii::t('admin', 'Alias'),
			'model_id' => Yii::t('admin', 'Model'),
			'year_from' => Yii::t('admin', 'Year from'),
			'year_to' => Yii::t('admin', 'Year to'),
			'make_id' => Yii::t('admin', 'Make'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'Model' => array(self::BELONGS_TO, 'AutoModel', 'model_id', 'together'=>true,),
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

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.model_id',$this->model_id);
		$criteria->compare('t.year_from',$this->year_from);
		$criteria->compare('Model.make_id',$this->make_id);
		$criteria->compare('t.year_to',$this->year_to);
		$criteria->compare('t.title',$this->title, true);
		$criteria->compare('t.alias',$this->alias, true);
		$criteria->with = array('Model', 'Model.Make');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public static function getAll()
	{
		$key = Tags::TAG_MODEL_YEAR_CHASSIS . '_getAll_';
		$data = Yii::app()->cache->get($key);
		if (empty($data)) {
			$data = (array)self::model()->findAll();
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR_CHASSIS));
		}
		
		return $data;
	}	
	
	public static function getList()
	{
		return CHtml::listData(self::getAll(), 'id', 'title');
	}
	
}