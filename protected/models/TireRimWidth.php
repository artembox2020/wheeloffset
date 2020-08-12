<?php

class TireRimWidth extends CActiveRecord
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
		return 'tire_rim_width';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rim_width, min_width, opt_width, max_width', 'required'),
			array('min_width, opt_width, max_width', 'numerical', 'integerOnly' => true),
			array('rim_width', 'numerical'),
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
		Yii::app()->cache->clear(Tags::TAG_TIRE_RIM_WIDTH);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'rim_width' => Yii::t('admin', 'Rim Width'),
			'min_width' => Yii::t('admin', 'Minimal Tire width'),
			'opt_width' => Yii::t('admin', 'optimal Tire width'),
			'max_width' => Yii::t('admin', 'Maximum Tire width'),
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
		$criteria->compare('rim_width',$this->rim_width);
		$criteria->compare('min_width',$this->min_width);
		$criteria->compare('opt_width',$this->opt_width);
		$criteria->compare('max_width',$this->max_width);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public static function getRangeWidth($width) 
	{
		$width = (int) $width;
		$key = Tags::TAG_TIRE_RIM_WIDTH . 'getRangeWidth'.$width;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			$sql = "SELECT rim_width FROM tire_rim_width WHERE {$width} IN (min_width, opt_width, max_width) ORDER BY rim_width ASC";
			$items = Yii::app()->db->createCommand($sql)->queryAll();
			if (!empty($items)) {
				$data['min'] = $items[0]['rim_width'];
				$end = end($items);
				$data['max'] = $end['rim_width'];
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_RIM_WIDTH));
		}
		
		return $data;
	}
		
	public static function getAll()
	{
		$key = Tags::TAG_TIRE_RIM_WIDTH . '_getAll__';
		$data = Yii::app()->cache->get($key);
		if (empty($data)) {
            $criteria=new CDbCriteria;
            $criteria->order = '`value` ASC';
            
			$data = self::model()->findAll($criteria);
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_RIM_WIDTH));
		}
		
		return $data;
	}	
	
	public static function getList()
	{
		return CHtml::listData(self::getAll(), 'id', 'value');
	}	
    
}