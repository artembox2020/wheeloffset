<?php

class RimWidth extends CActiveRecord
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
		return 'rim_width';
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
			array('value', 'numerical'),			
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
		Yii::app()->cache->clear(Tags::TAG_RIM_WIDTH);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'value' => Yii::t('admin', 'Width'),
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
		$key = Tags::TAG_RIM_WIDTH . 'getAll';
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			$criteria = new CDbCriteria;
			$criteria->select = "id,  CAST(value AS DECIMAL(5,2)) AS value";
			$criteria->order = 'value';
			
			$items = CHtml::listData(self::model()->findAll($criteria), 'id', 'value');
			foreach ($items as $k=>$v) {
				$data[$k] = TextHelper::f($v);
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_RIM_WIDTH));
		}
		
		return $data;
	}	
	
	public static function getValueById($value)
	{
		$list = self::getAll();
		if (isset($list[$value])) {
			return $list[$value];
		} else {
			return false;
		}
	}	
	
	public static function getListByModelProject($model_id)
	{
		$model_id = (int) $model_id;
		
		$key = Tags::TAG_RIM_WIDTH . '_getListByModelProject_' . $model_id;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			$allItems = self::getAll();
			$sql = "SELECT rim_width_id, rear_rim_width_id, COUNT( * ) AS c FROM  `project` WHERE model_id = {$model_id} GROUP BY rim_width_id, rear_rim_width_id";
			
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			$dataCount = array();
			foreach ($rows as $row) {
				if (!empty($row['rim_width_id']) || !empty($row['rear_rim_width_id'])) {
					if ($row['rim_width_id'] == $row['rear_rim_width_id']) {
						if(isset($dataCount[$row['rim_width_id']])) {
							$dataCount[$row['rim_width_id']] += $row['c'];
						} else {
							$dataCount[$row['rim_width_id']] = $row['c'];
						}
					} else {
						if (!empty($row['rim_width_id'])) {
							if(isset($dataCount[$row['rim_width_id']])) {
								$dataCount[$row['rim_width_id']] += $row['c'];
							} else {
								$dataCount[$row['rim_width_id']] = $row['c'];
							}
						}
						
						if (!empty($row['rear_rim_width_id'])) {
							if(isset($dataCount[$row['rear_rim_width_id']])) {
								$dataCount[$row['rear_rim_width_id']] += $row['c'];
							} else {
								$dataCount[$row['rear_rim_width_id']] = $row['c'];
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
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_RIM_WIDTH, Tags::TAG_PROJECT));
		}
		
		return $data;		
	}		
		
}