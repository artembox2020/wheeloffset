<?php

class EbayAutoModelYear extends CActiveRecord
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
		return 'ebay_auto_model_year';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model_id, year', 'required'),
			array('atk_id, is_active', 'safe',),	
		);
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
		Yii::app()->cache->clear(Tags::TAG_EBAY_MODEL_YEAR);
	}    
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'year' => Yii::t('admin', 'Year'),
			'is_active' => Yii::t('admin', 'Active'),
			'model_id' => Yii::t('admin', 'Model'),
			'atk_id' => Yii::t('admin', 'AutoTk model year'),
		);
	}
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'Model' => array(self::BELONGS_TO, 'EbayAutoModel', 'model_id', 'together' => true,),
            'AtkModelYear' => array(self::BELONGS_TO, 'AutoModelYear', 'atk_id', 'together' => true,),
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
		$criteria->compare('model_id',$this->model_id);
		$criteria->compare('year',$this->year, true);
	
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
}