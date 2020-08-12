<?php

class EbayAutoModel extends CActiveRecord
{
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
		return 'ebay_auto_model';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, alias, make_id', 'required'),
			array('atk_id, is_active', 'safe',),	
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
    
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_EBAY_MODEL);
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
			'is_active' => Yii::t('admin', 'Active'),
			'make_id' => Yii::t('admin', 'Make'),
			'atk_id' => Yii::t('admin', 'AutoTk model'),
		);
	}
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'Make' => array(self::BELONGS_TO, 'EbayAutoMake', 'make_id', 'together' => true,),
            'AtkModel' => array(self::BELONGS_TO, 'AutoModel', 'atk_id', 'together' => true,),
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
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('atk_id',$this->atk_id);
		$criteria->compare('make_id',$this->make_id);
		$criteria->compare('title',$this->title, true);
		$criteria->compare('alias',$this->alias, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public static function getAll()
	{
		return CHtml::listData(self::model()->findAll(), 'id', 'title');
	}
    
    public static function getItemByAtkId($atk_id)
    {
        $key = Tags::TAG_EBAY_MODEL . 'getItemByAtkId' . $atk_id;
        $model = Yii::app()->cache->get($key);
        if ($model == false) {
            $model = array();

            $criteria = new CDbCriteria();
            $criteria->compare('t.is_active', 1);
            $criteria->compare('t.atk_id', $atk_id);
            $item = self::model()->find($criteria);

            if (!empty($item)) {
                $model = array(
                    'id' => $item->id,
                    'title' => $item->title,
                    'alias' => $item->alias,
                );
            }

            Yii::app()->cache->set($key, $model, 0, new Tags(Tags::TAG_EBAY_MODEL));
        }

        return $model;
    }
    
}