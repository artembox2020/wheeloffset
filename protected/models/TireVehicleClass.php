<?php

class TireVehicleClass extends CActiveRecord
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
		return 'tire_vehicle_class';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, title, rank', 'required'),
			array('rank', 'numerical', 'integerOnly' => true),
			array('id', 'safe', 'on' => 'search'),
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
		Yii::app()->cache->clear(Tags::TAG_TIRE_VEHICLE_CLASS);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => Yii::t('admin', 'Code'),
			'title' => Yii::t('admin', 'Title'),
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
		$criteria->compare('title',$this->title, true);
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
		$key = Tags::TAG_TIRE_VEHICLE_CLASS . '_getAll_';
		$data = Yii::app()->cache->get($key);
		if (empty($data)) {
			$data = (array)self::model()->findAll(array('order'=>'`rank`'));
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_VEHICLE_CLASS));
		}
		
		return $data;
	}	
	
	public static function getList()
	{
		return CHtml::listData(self::getAll(), 'id', 'code');
	}	
	
	public static function getListFront()
	{
		$key = Tags::TAG_TIRE_VEHICLE_CLASS . '_getListFront_';
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			$items = Yii::app()->db->createCommand("SELECT DISTINCT vehicle_class_id FROM tire")->queryAll();
			$ids = array();
			foreach ($items as $item) {
				$ids[] = $item['vehicle_class_id'];
			}
			
			if (!empty($ids)) {
				$items = Yii::app()->db->createCommand("SELECT 	id, code FROM tire_vehicle_class WHERE id IN (".implode(',', $ids).") ORDER BY code")->queryAll();
				foreach ($items as $item) {
					$data[$item['id']] = $item['code'];
				}							
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_VEHICLE_CLASS, Tags::TAG_TIRE));
		}
		
		return $data;			
	}	
	
}