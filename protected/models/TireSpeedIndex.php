<?php

class TireSpeedIndex extends CActiveRecord
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
		return 'tire_speed_index';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code', 'required'),
			array('rank', 'numerical', 'integerOnly' => true),
			array('id, mph, kmh, rank', 'safe'),
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
		Yii::app()->cache->clear(Tags::TAG_TIRE_SPEED_INDEX);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => Yii::t('admin', 'Code'),
			'mph' => Yii::t('admin', 'mph'),
			'kmh' => Yii::t('admin', 'km/h'),
			'rank' => Yii::t('admin', 'Rank'),
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
		$criteria->compare('code',$this->code, true);
		$criteria->compare('mph',$this->mph, true);
		$criteria->compare('kmh',$this->kmh, true);
		$criteria->compare('rank',$this->rank);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public static function getAll()
	{
		$key = Tags::TAG_TIRE_SPEED_INDEX . '_getAll__';
		$data = Yii::app()->cache->get($key);
		if (empty($data)) {
			$data = (array)self::model()->findAll(array('order'=>'rank, code'));
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_SPEED_INDEX));
		}
		
		return $data;
	}	
	
}