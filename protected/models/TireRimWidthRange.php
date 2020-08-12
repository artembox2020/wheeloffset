<?php

class TireRimWidthRange extends CActiveRecord
{		
	public $storage = array();

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
		return 'tire_rim_width_range';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tire_id, from, to', 'required'),
			array('tire_id', 'unique', 'message'=>'{attribute} has already been taken.'),
			array('tire_id', 'numerical', 'integerOnly' => true),
			array('from, to, rear_from, rear_to', 'numerical'),
			array('id', 'safe', 'on' => 'search'),
		);
	}		
		
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' 		=> 'ID',
			'tire_id' 	=> Yii::t('admin', 'Tire'),
			'from'		=> Yii::t('admin', 'From'),
			'to'		=> Yii::t('admin', 'To'),
			'rear_from'		=> Yii::t('admin', 'Rear from'),
			'rear_to'		=> Yii::t('admin', 'Rear to'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'Tire' => array(self::BELONGS_TO, 'Tire', 'tire_id', 'together'=>true,), //value
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

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.tire_id',$this->tire_id);
		$criteria->compare('t.from',$this->from);
		$criteria->compare('t.to',$this->to);
		$criteria->compare('t.rear_from',$this->rear_from);
		$criteria->compare('t.rear_to',$this->rear_to);
		$criteria->with = array('Tire'=>array('together'=>true));
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public function import()
	{
		TireRimWidthRange::model()->deleteAll();
		
		$filename = Yii::getPathOfAlias('webroot') . '/import/Tire_replacement_pressure.xls';
		Yii::import('ext.phpexcelreader.JPhpExcelReader');
		$excelReader = new JPhpExcelReader($filename);		
		$tireRange = array();
		
		//d($excelReader->sheets[1]['cells']);
		
		//pessenger
		foreach ($excelReader->sheets[0]['cells'] as $data) {
			$tire = false;
			
			if (isset($data[1])) {
				$tire = $this->_importGetTire($data[1]);
				if ($tire !== false) {
					$tires[] = $tire;
				}
			} else if (isset($data[2])) {
				$tire = $this->_importGetTire($data[2]);
				if ($tire !== false) {
					$tires[] = $tire;
				}
			}
			
			if ($tire !== false) {
				$tire['vehicle_class_id'] = 1;
				$rangeAttributes = array(
					'tire_id' => $this->_importGetTireId($tire),
				);
				
				if (isset($data[5])) {
					$expl = explode('-', $data[5]);
					if (count($expl) == 2) {
						$rangeAttributes['from'] = (float)$expl[0];
						$rangeAttributes['to'] = (float)$expl[1];
					}
 				}
				
				
				$tireRange[] = $rangeAttributes;
			}
		}	
		
		//LT
		foreach ($excelReader->sheets[1]['cells'] as $data) {
			$tire = false;
			
			if (isset($data[1])) {
				$tire = $this->_importGetTire($data[1]);
				if ($tire !== false) {
					$tires[] = $tire;
				}
			} 
			
			if ($tire !== false) {
				$tire['vehicle_class_id'] = 2;
				$rangeAttributes = array(
					'tire_id' => $this->_importGetTireId($tire),
				);
				
				if (empty($rangeAttributes['tire_id'])) {
					d($data, 0);
				}
				
				if (isset($data[3])) {
					$expl = explode('-', $data[3]);
					if (count($expl) == 2) {
						$rangeAttributes['from'] = (float)$expl[0];
						$rangeAttributes['to'] = (float)$expl[1];
					}
 				}
				
				
				$tireRange[] = $rangeAttributes;
			}
		}		
		

		foreach ($tireRange as $attr) {
			$model = TireRimWidthRange::model()->findByAttributes(array('tire_id'=>$attr['tire_id']));
			if (empty($model)) {
				$model = new TireRimWidthRange();
			}
			$model->attributes = $attr;
			$model->save();
		}
		
		die();
	}
	
	private function _importGetTire($string) {
		$tire = array();
		if (preg_match('/([0-9]{1,3}(.*?))\/([0-9]{1,3}(.*?))R([0-9]{1,3}(.*?))/', $string, $match)) {
			if (!empty($match[1]) && !empty($match[3]) && !empty($match[5])) {
				$tire = array (
					'section_width_id'	=> $this->_importGetModelByValue('TireSectionWidth', $match[1]),
					'aspect_ratio_id' 	=> $this->_importGetModelByValue('TireAspectRatio', $match[3]),
					'rim_diameter_id' 	=> $this->_importGetModelByValue('TireRimDiameter', $match[5]),
				);
				
				return $tire;
			}
		}

		return false;
	}
	
	private function _importGetModelByValue($modelName, $value) {
		$value = (float) $value;
		
		if (!isset($this->storage[$modelName][$value])) {		
			$model = CActiveRecord::model($modelName)->findByAttributes(array('value'=>$value));
			if (empty($model)) {
				$model = new $modelName;
				$model->value = $value;
				$model->rank = 0;
				$model->save();
			}
			
			$this->storage[$modelName][$value] = $model->id;
		} 
		
		return $this->storage[$modelName][$value];
	}
			
	private function _importGetTireId($attributes) {
		
		$tire = Tire::model()->findByAttributes($attributes);
		if (empty($tire)) {
			$tire = new Tire;
			$tire->attributes = $attributes;
			$tire->save();
		}
		
		if (!empty($tire))
			return $tire->id;
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
		Yii::app()->cache->clear(Tags::TAG_TIRE_RIM_WIDTH_RANGE);
	}
	
	public static function getRangeTire($tire_id)
	{
		$tire_id	= (int) $tire_id;
		$key 		= Tags::TAG_TIRE_RIM_WIDTH_RANGE . '__getRangeTire_' . $tire_id;
		$data 		= Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			
			$model = self::model()->findByAttributes(array('tire_id'=>$tire_id));
			if (!empty($model)) {
				$data['front']['from']	= $model->from;
				$data['front']['to'] 	= $model->to;				
			
				if (!empty($model->rear_from) && !empty($model->rear_to)) {
					$data['rear']['from']	= $model->rear_from;
					$data['rear']['to'] 	= $model->rear_to;					
				}
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE_RIM_WIDTH_RANGE));
		}
		
		return $data;
	}
			
	public static function getData()
	{
		$key 		= Tags::TAG_TIRE_RIM_WIDTH_RANGE . '_getData_';
		$data 		= Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			
				$sql = "SELECT 
							t.is_rear AS is_rear, 
							vc.code AS vehicle_class, 
							rd.value AS rim_diameter, 
							sw.value AS section_width, 
							ar.value AS aspect_ratio,
							r_rd.value AS rear_rim_diameter, 
							r_sw.value AS rear_section_width, 
							r_ar.value AS rear_aspect_ratio,
							r.from AS front_from,
							r.to AS front_to,
							r.rear_from AS rear_from,
							r.rear_to AS rear_to
						FROM tire_rim_width_range AS r
						LEFT JOIN tire AS t ON r.tire_id = t.id
						LEFT JOIN tire_vehicle_class AS vc ON t.vehicle_class_id = vc.id
						LEFT JOIN tire_rim_diameter AS rd ON t.rim_diameter_id = rd.id
						LEFT JOIN tire_section_width AS sw ON t.section_width_id = sw.id
						LEFT JOIN tire_aspect_ratio AS ar ON t.aspect_ratio_id = ar.id
						LEFT JOIN tire_rim_diameter AS r_rd ON t.rear_rim_diameter_id = r_rd.id
						LEFT JOIN tire_section_width AS r_sw ON t.rear_section_width_id = r_sw.id
						LEFT JOIN tire_aspect_ratio AS r_ar ON t.rear_aspect_ratio_id = r_ar.id
				";
		
			$items = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($items as $tire) {
				$data[Tire::getTitleAttr($tire,true)] = array(
					'is_rear' => $tire['is_rear'],
					'front' => array(
						'from' => $tire['front_from'],
						'to' => $tire['front_to'],
					),
					'rear' => array(
						'from' => $tire['rear_from'],
						'to' => $tire['rear_to'],
					),
				);
			}
			
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_TIRE_RIM_WIDTH_RANGE));
		}
		
		return $data;
	}
			
}