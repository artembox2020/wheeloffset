<?php

class ProductBulbPosition extends CActiveRecord
{		
    const TYPE_FORWARD = 1;
    const TYPE_EXTERIOR = 2;
    const TYPE_INTERIOR = 3;
    
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'product_bulb_position';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('title', 'required'),
			array('id, type', 'safe'),
		);
	}		
	
	public function afterSave()
	{
		$this->clearCache();
		
		return parent::afterSave();
	}	
	
	public function afterDelete()
	{
		return parent::afterDelete();
	}	
	
	private function clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT_BULB_POSITION);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'type' => 'Type',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('title',$this->title);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::title()->request->getParam('pageSize', Yii::title()->params->defaultPerPage),
			),			
		));
    }
    
    public static function getItem($attributes)
    {
        $model = self::model()->findByAttributes($attributes);
        if (empty($model)) {
            $model = new self;
            $model->attributes = $attributes;
            $model->save(false);
        }
        return $model;
    }
    
    public static function getTypes()
    {
        return [
            self::TYPE_FORWARD => 'Forward',
            self::TYPE_EXTERIOR => 'Exterior',
            self::TYPE_INTERIOR => 'Interior',
        ];
    }

    public static function getTypeByTitle($typeTitle)
    {
        $typesTitle = array_flip(self::getTypes());
        
        return isset($typesTitle[$typeTitle]) ? $typesTitle[$typeTitle] : null;
    }
}