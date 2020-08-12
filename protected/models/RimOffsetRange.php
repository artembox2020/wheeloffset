<?php

class RimOffsetRange extends CActiveRecord
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
		return 'rim_offset_range';
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
			array('value', 'numerical', 'integerOnly' => true, 'min'=>-80, 'max'=>80),		
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
		Yii::app()->cache->clear(Tags::TAG_RIM_OFFSET_RANGE);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'value' => Yii::t('admin', 'Value'),
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
		$criteria->compare('value',$this->value);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public static function getAll() 
	{
		$key = Tags::TAG_RIM_OFFSET_RANGE . '_getAll_';
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = CHtml::listData(self::model()->findAll(array('order'=>'value')), 'id', 'value');
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_RIM_OFFSET_RANGE));
		}
		
		return $data;
	}
		
	public static function getListByModelProject($model_id)
	{
		$model_id = (int) $model_id;
		
		$key = Tags::TAG_RIM_OFFSET_RANGE . '_getListByModelProject_' . $model_id;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			$allItems = self::getAll();
			$sql = "SELECT rim_offset_range_id, rear_rim_offset_range_id, COUNT( * ) AS c FROM  `project` WHERE model_id = {$model_id} GROUP BY rim_offset_range_id, rear_rim_offset_range_id";
			
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			$dataCount = array();
			foreach ($rows as $row) {
				if (!empty($row['rim_offset_range_id']) || !empty($row['rear_rim_offset_range_id'])) {
					if ($row['rim_offset_range_id'] == $row['rear_rim_offset_range_id']) {
						if(isset($dataCount[$row['rim_offset_range_id']])) {
							$dataCount[$row['rim_offset_range_id']] += $row['c'];
						} else {
							$dataCount[$row['rim_offset_range_id']] = $row['c'];
						}
					} else {
						if (!empty($row['rim_offset_range_id'])) {
							if(isset($dataCount[$row['rim_offset_range_id']])) {
								$dataCount[$row['rim_offset_range_id']] += $row['c'];
							} else {
								$dataCount[$row['rim_offset_range_id']] = $row['c'];
							}
						}
						
						if (!empty($row['rear_rim_offset_range_id'])) {
							if(isset($dataCount[$row['rear_rim_offset_range_id']])) {
								$dataCount[$row['rear_rim_offset_range_id']] += $row['c'];
							} else {
								$dataCount[$row['rear_rim_offset_range_id']] = $row['c'];
							}
						}
					}
				}
			}

			foreach ($allItems as $id=>$title) {
				if (isset($dataCount[$id])) {
					$data[$id] = $title . ' ('.$dataCount[$id].')';
				}
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_RIM_OFFSET_RANGE, Tags::TAG_PROJECT));
		}
		
		return $data;		
	}	
    
	public static function getList()
	{
		return CHtml::listData(self::getAll(), 'id', 'value');
	}	
    
		
}