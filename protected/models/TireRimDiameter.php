<?php

class TireRimDiameter extends CActiveRecord
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
		return 'tire_rim_diameter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('value, rank', 'required'),
			array('value, rank', 'numerical', 'integerOnly' => true),
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
		Yii::app()->cache->clear(Tags::TAG_TIRE_RIM_DIAMETER);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'value' => Yii::t('admin', 'Value'),
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
		$criteria->compare('value',$this->value);
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
		$key = Tags::TAG_TIRE_RIM_DIAMETER . '_getAll__';
		$data = Yii::app()->cache->get($key);
		if (empty($data)) {
			$data = (array)self::model()->findAll(array('order'=>'rank, value'));
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_RIM_DIAMETER));
		}
		
		return $data;
	}	
	
	public static function getList()
	{
		return CHtml::listData(self::getAll(), 'id', 'value');
	}	
	
	public static function getValueById($value)
	{
		$list = self::getList();
		if (isset($list[$value])) {
			return $list[$value];
		} else {
			return false;
		}
	}	
	
	public static function getListByModelProject($model_id)
	{
		$model_id = (int) $model_id;
		
		$key = Tags::TAG_TIRE_RIM_DIAMETER . '_getListByModelProject_' . $model_id;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			$allItems = self::getList();
			$sql = "SELECT rim_diameter_id, rear_rim_diameter_id, COUNT( * ) AS c FROM  `project` WHERE model_id = {$model_id} GROUP BY rim_diameter_id, rear_rim_diameter_id";
			
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			$dataCount = array();
			foreach ($rows as $row) {
				if (!empty($row['rim_diameter_id']) || !empty($row['rear_rim_diameter_id'])) {
					if ($row['rim_diameter_id'] == $row['rear_rim_diameter_id']) {
						if(isset($dataCount[$row['rim_diameter_id']])) {
							$dataCount[$row['rim_diameter_id']] += $row['c'];
						} else {
							$dataCount[$row['rim_diameter_id']] = $row['c'];
						}
					} else {
						if (!empty($row['rim_diameter_id'])) {
							if(isset($dataCount[$row['rim_diameter_id']])) {
								$dataCount[$row['rim_diameter_id']] += $row['c'];
							} else {
								$dataCount[$row['rim_diameter_id']] = $row['c'];
							}
						}
						
						if (!empty($row['rear_rim_diameter_id'])) {
							if(isset($dataCount[$row['rear_rim_diameter_id']])) {
								$dataCount[$row['rear_rim_diameter_id']] += $row['c'];
							} else {
								$dataCount[$row['rear_rim_diameter_id']] = $row['c'];
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
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_RIM_DIAMETER, Tags::TAG_PROJECT));
		}
		
		return $data;		
	}	
	
	public static function getItemByValue($value)
	{
		$key = Tags::TAG_TIRE_RIM_DIAMETER . '_ITEM_'.$value;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			$criteria = new CDbCriteria();
			$criteria->compare('value', $value);
			$model = self::model()->find($criteria);
			
			if (!empty($model)) {
				$data = array(
					'id' => $model->id,
					'value' => $model->value,
				);
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_RIM_DIAMETER));
		}	
		
		return $data;	
	}	

	public static function getListFront($attributes=array())
	{
		$key = Tags::TAG_TIRE_RIM_DIAMETER . 'getListFront' . serialize($attributes);
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			$where = array();
			foreach ($attributes as $k=>$v) {
				$where[] = "$k = '$v'";
			}
			if (!empty($where)) {
				$where = 'WHERE ' . implode(' AND ', $where);
			} else {
				$where = '';
			}
			
			$items = Yii::app()->db->createCommand("SELECT DISTINCT rim_diameter_id FROM tire $where")->queryAll();
			$ids = array();
			foreach ($items as $item) {
				$ids[] = (int)$item['rim_diameter_id'];
			}

			if (!empty($ids)) {
				$sql = "SELECT id, value FROM tire_rim_diameter WHERE id IN (".implode(',', $ids).") ORDER BY value";
				
				$items = Yii::app()->db->createCommand($sql)->queryAll();
				
				foreach ($items as $item) {
					$data[$item['id']] = $item['value'];
				}							
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_RIM_DIAMETER, Tags::TAG_TIRE));
		}
		
		return $data;			
	}	
		
	
		
	
	
}