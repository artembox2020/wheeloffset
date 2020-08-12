<?php

class RimBoltPattern extends CActiveRecord
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
		return 'rim_bolt_pattern';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('value', 'required'),
			array('value', 'length', 'max'=>20),			
			array('value_inches', 'length', 'max'=>20),			
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
		Yii::app()->cache->clear(Tags::TAG_RIM_BOLT_PATTERN);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'value' => Yii::t('admin', 'Value'),
			'value_inches' => Yii::t('admin', 'Inch value'),
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
		$criteria->compare('value',$this->value, true);
		$criteria->compare('value_inches', $this->value_inches, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public static function getAll($attr='value') 
	{
		$key = Tags::TAG_RIM_BOLT_PATTERN . '_getAll_' . $attr;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = CHtml::listData(self::model()->findAll(array('order'=>'value')), 'id', $attr);
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_RIM_BOLT_PATTERN));
		}
		
		return $data;
	}
		
	public static function getListOfBoltPatterns() 
	{
		$key = Tags::TAG_RIM_BOLT_PATTERN . '_getListOfBoltPatterns_';
		$data = Yii::app()->cache->get($key);
		
		if ($data === false || 1) {
			$sql = "SELECT 
						bolt_pattern_id, 
						COUNT( * ) AS c
					FROM  `auto_model_year` 
					WHERE bolt_pattern_id IS NOT NULL AND bolt_pattern_id <>0
					GROUP BY bolt_pattern_id";
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			$count = 0;
			$yearData = array();
			foreach ($rows as $row) {
				$count+=$row['c'];
				$yearData[$row['bolt_pattern_id']] = $row['c'];
			}
			
			$data = array();
			$items = self::model()->findAll(array('order'=>'value'));
			foreach ($items as $item) {
				$percent = 0;
				if (isset($yearData[$item->id])) {
					$percent = $yearData[$item->id]/$count * 100;
				}
						
				$data[] = array(
					'value' => $item->value,
					'value_inches' => $item->value_inches,
					'percent' => round($percent, 0),
				);
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_RIM_BOLT_PATTERN, Tags::TAG_MODEL_YEAR));
		}
		
		return $data;
	}
		
}