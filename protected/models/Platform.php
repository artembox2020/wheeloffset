<?php

class Platform extends CActiveRecord
{
	public $post_models = array(); 
	
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
		return 'platform';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, alias, category_id', 'required'),
			array('alias', 'unique'),
			array('id', 'safe',),	
		);
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

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'Category' => array(self::BELONGS_TO, 'PlatformCategory', 'category_id', 'together'=>true,),
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
		
		$this->alias = TextHelper::urlSafe($this->alias);
	}		
	
	public function afterSave()
	{		
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
			'title' => Yii::t('admin', 'Title'),
			'alias' => Yii::t('admin', 'Alias'),
			'category_id' => Yii::t('admin', 'Category'),
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
		$criteria->compare('title',$this->title, true);
		$criteria->compare('alias',$this->alias, true);
		$criteria->compare('category_id',$this->category_id);
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_AUTO_PLATFORM);
	}
	
	
	public static function getAll()
	{
		$key = Tags::TAG_AUTO_PLATFORM . '___getAll_';
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = CHtml::listData(self::model()->findAll(), 'id', 'title');
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_AUTO_PLATFORM));
		}
		
		return $data;
	}
	
}