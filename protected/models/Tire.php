<?php

class Tire extends CActiveRecord
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
		return 'tire';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('vehicle_class_id', 'required'),
			array('vehicle_class_id', 'uniqueTire'),
			array('is_rear, rear_section_width_id, rear_aspect_ratio_id, rear_rim_diameter_id, is_runflat, vehicle_class_id, section_width_id, aspect_ratio_id, rim_diameter_id, load_index_id', 'numerical', 'integerOnly' => true),
			array('id', 'safe', 'on' => 'search'),
		);
	}	

	public function uniqueTire()
	{
		$criteria=new CDbCriteria;
		if (!$this->isNewRecord) {
			$criteria->addCondition("t.id <> {$this->id}");
		}

		$criteria->compare('t.vehicle_class_id', $this->getEmpty($this->vehicle_class_id));
		$criteria->compare('t.section_width_id', $this->getEmpty($this->section_width_id));
		$criteria->compare('t.aspect_ratio_id', $this->getEmpty($this->aspect_ratio_id));
		$criteria->compare('t.rim_diameter_id', $this->getEmpty($this->rim_diameter_id));
		$criteria->compare('t.load_index_id', $this->getEmpty($this->load_index_id));
		$criteria->compare('t.is_runflat', (int)$this->is_runflat);	
		$criteria->compare('t.is_rear', (int)$this->is_rear);
		if ($this->is_rear) {
			$criteria->compare('t.rear_section_width_id', $this->getEmpty($this->rear_section_width_id));
			$criteria->compare('t.rear_aspect_ratio_id', $this->getEmpty($this->rear_aspect_ratio_id));
			$criteria->compare('t.rear_rim_diameter_id', $this->getEmpty($this->rear_rim_diameter_id));	
		}
		
		$model = $this->find($criteria);
		
		if (!empty($model)) {
			$this->addError('vehicle_class_id', 'Tire already exsists for this parameters');
		}

	}
	
	private function getEmpty($value)
	{
		return empty($value) ? null : (int) $value;
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'SectionWidth' => array(self::BELONGS_TO, 'TireSectionWidth', 'section_width_id', 'together'=>true,), //value
            'AspectRatio' => array(self::BELONGS_TO, 'TireAspectRatio', 'aspect_ratio_id', 'together'=>true,), //value
            'RimDiameter' => array(self::BELONGS_TO, 'TireRimDiameter', 'rim_diameter_id', 'together'=>true,), //value        
            'LoadIndex' => array(self::BELONGS_TO, 'TireLoadIndex', 'load_index_id', 'together'=>true,), //index
            'Type' => array(self::BELONGS_TO, 'TireType', 'type_id', 'together'=>true,), //value
			'VehicleClass' => array(self::BELONGS_TO, 'TireVehicleClass', 'vehicle_class_id', 'together'=>true,), //title
            'RearSectionWidth' => array(self::BELONGS_TO, 'TireSectionWidth', 'rear_section_width_id', 'together'=>true,), //value
            'RearAspectRatio' => array(self::BELONGS_TO, 'TireAspectRatio', 'rear_aspect_ratio_id', 'together'=>true,), //value
            'RearRimDiameter' => array(self::BELONGS_TO, 'TireRimDiameter', 'rear_rim_diameter_id', 'together'=>true,), //value        

		);
	}	
	
	public function beforeSave()
	{
		foreach (array('section_width_id', 'aspect_ratio_id', 'rim_diameter_id', 'load_index_id') as $attribute) {
			if (empty($this->$attribute)) {
				$this->$attribute = null;
			}
		}
		
		if (!$this->is_rear) {
			$this->rear_section_width_id = null;
			$this->rear_aspect_ratio_id = null;
			$this->rear_rim_diameter_id = null;
		}
		
		return parent::beforeSave();
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
		Yii::app()->cache->clear(Tags::TAG_TIRE);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'vehicle_class_id' => 'Vehicle Class',
			'section_width_id' => 'Section Width',
			'aspect_ratio_id' => 'Aspect Ratio',
			'rim_diameter_id' => 'Rim Diameter',
			'load_index_id' => 'Load Index',
			'is_runflat' => 'Runflat',
			'is_rear' => 'Is rear',
			'rear_section_width_id' => 'Rear Section Width',
			'rear_aspect_ratio_id' => 'Rear Aspect Ratio',
			'rear_rim_diameter_id' => 'Rear Rim Diameter',			
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
		$criteria->compare('t.vehicle_class_id',$this->vehicle_class_id);
		$criteria->compare('t.section_width_id',$this->section_width_id);
		$criteria->compare('t.aspect_ratio_id',$this->aspect_ratio_id);
		$criteria->compare('t.rim_diameter_id',$this->rim_diameter_id);
		$criteria->compare('t.load_index_id',$this->load_index_id);
		$criteria->compare('t.is_runflat',$this->is_runflat);	
		$criteria->compare('t.is_rear',$this->is_rear);	
		$criteria->compare('t.rear_section_width_id',$this->rear_section_width_id);
		$criteria->compare('t.rear_aspect_ratio_id',$this->rear_aspect_ratio_id);
		$criteria->compare('t.rear_rim_diameter_id',$this->rear_rim_diameter_id);
		
		$criteria->with = array(
			'VehicleClass', 
			'SectionWidth', 
			'AspectRatio', 
			'RimDiameter', 
			'LoadIndex',
			'RearRimDiameter',
			'RearAspectRatio',
			'RearSectionWidth',
		);	
		
		$sort = array(
			'vehicle_class_id' => 'VehicleClass.code',
			'vehicle_class_id.desc' => 'VehicleClass.code DESC',
			'section_width_id' => 'SectionWidth.value',
			'section_width_id.desc' => 'SectionWidth.value DESC',
			'aspect_ratio_id' => 'AspectRatio.value',
			'aspect_ratio_id.desc' => 'AspectRatio.value DESC',
			'rim_diameter_id' => 'RimDiameter.value',
			'rim_diameter_id.desc' => 'RimDiameter.value DESC',
			'load_index_id' => 'LoadIndex.index',
			'load_index_id.desc' => 'LoadIndex.index DESC',
			'rear_section_width_id' => 'RearSectionWidth.value',
			'rear_section_width_id.desc' => 'RearSectionWidth.value DESC',
			'rear_aspect_ratio_id' => 'RearAspectRatio.value',
			'rear_aspect_ratio_id.desc' => 'RearAspectRatio.value DESC',			
			'rear_rim_diameter_id' => 'RearRimDiameter.value',
			'rear_rim_diameter_id.desc' => 'RearRimDiameter.value DESC',		
		);
		
		if (isset($_GET['Tire_sort']) && isset($sort[$_GET['Tire_sort']])) {
			$criteria->order = $sort[$_GET['Tire_sort']];	
		}
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public function getTitle($vc=false) 
	{
		$title = '';
		
		if (!empty($this->SectionWidth) && !empty($this->AspectRatio) && !empty($this->RimDiameter)) {
			
			if ($vc)
				$title = $this->VehicleClass->code . ' ';
			
			$title.= $this->SectionWidth->value . '/' . $this->AspectRatio->value . ' R' . $this->RimDiameter->value;
			if ($this->is_rear && !empty($this->RearSectionWidth) && !empty($this->RearAspectRatio) && !empty($this->RearRimDiameter)) {
				$title .= ' ' .$this->RearSectionWidth->value . '/' . $this->RearAspectRatio->value . ' R' . $this->RearRimDiameter->value;
			}
		}
			
		return $title;
	}	
	
	public static function getTitleAttr($tire, $vc=false) 
	{
		$title = '';
		
		if (!empty($tire['section_width']) && !empty($tire['aspect_ratio']) && !empty($tire['rim_diameter'])) {
			
			if ($vc)
				$title = $tire['vehicle_class'] . ' ';
			
			$title.= $tire['section_width'] . '/' . $tire['aspect_ratio'] . ' R' . $tire['rim_diameter'];
			if ($tire['is_rear'] && !empty($tire['rear_section_width']) && !empty($tire['rear_aspect_ratio']) && !empty($tire['rear_rim_diameter'])) {
				$title .= ' ' .$tire['rear_section_width'] . '/' . $tire['rear_aspect_ratio'] . ' R' . $tire['rear_rim_diameter'];
			}
		}
			
		return $title;
	}	
	
	public function getList()
	{
		$key = Tags::TAG_TIRE . '__getList__';
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			$criteria=new CDbCriteria;
			$criteria->with = array('SectionWidth', 'AspectRatio', 'RimDiameter', 'RearSectionWidth', 'RearAspectRatio', 'RearRimDiameter');			
			$criteria->order = 'SectionWidth.value, AspectRatio.value, RimDiameter.value';			
			$items = Tire::model()->findAll($criteria);
			foreach ($items as $item) {
				$value = $item->getTitle(true);
				
				if (!empty($value)) {
					$data[$item->id] = $value;
				}
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_TIRE_SECTION_WIDTH, Tags::TAG_TIRE_ASPECT_RATIO, Tags::TAG_TIRE_RIM_DIAMETER));
		}
		
		return $data;
	}
	
	public static function getPopolar($limit=10)
	{
		$key = Tags::TAG_TIRE . '_getPopolar_' . $limit;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			$tireIds = array();
			
			
			$sql = "SELECT tire_id, COUNT(*) AS c FROM `auto_model_year_tire` GROUP BY tire_id ORDER BY c DESC";
			$items = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($items as $item) {
				$tireIds[] = $item['tire_id'];
			}
			
			$criteria=new CDbCriteria;
			$criteria->with = array('VehicleClass', 'SectionWidth', 'AspectRatio', 'RimDiameter', 'RearSectionWidth', 'RearAspectRatio', 'RearRimDiameter');			
			$criteria->addInCondition('t.id', $tireIds);			
			$criteria->order = 'Field(t.id, ' . implode(',', $tireIds) . ')';			
			$criteria->limit = $limit;			
			
			$items = Tire::model()->findAll($criteria);
			foreach ($items as $item) {
				$data[] = array(
					'vehicle_class' => isset($item->VehicleClass)?$item->VehicleClass->code:'',
					'rim_diameter' => isset($item->RimDiameter)?$item->RimDiameter->value:'',
					'section_width' => isset($item->SectionWidth)?$item->SectionWidth->value:'',
					'aspect_ratio' => isset($item->AspectRatio)?$item->AspectRatio->value:'',
				);				
				
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_TIRE_SECTION_WIDTH, Tags::TAG_TIRE_ASPECT_RATIO, Tags::TAG_TIRE_RIM_DIAMETER, Tags::TAG_MODEL_YEAR));
		}
		
		return $data;
	}
	
	public static function getCount($attributes=array())
	{
		$criteria=new CDbCriteria;
		foreach ($attributes as $k=>$v)
			$criteria->compare($k, $v);
		
		return self::model()->count($criteria);
	}
	
	public static function format($attributes, $vehicle_class=true) 
	{
		$tire = $attributes['section_width'] . '/' . $attributes['aspect_ratio'] . ' R' . $attributes['rim_diameter'];
		if ($vehicle_class)
			$tire = $attributes['vehicle_class'] . ' ' . $tire;
			
		return $tire;
	}
	
	public static function formatProfile($attributes, $vehicle_class=true) 
	{
		$tire = $attributes['section_width'] . '/' . $attributes['aspect_ratio'] . ' R' . $attributes['rim_diameter'];
		if ($vehicle_class)
			$tire = $attributes['vehicle_class'] . ' ' . $tire;
		

		if ($attributes['aspect_ratio'] <= 50) {
			$tire .= ' &ndash; low profile';
		} else {
			$tire .= ' &ndash; high profile';
		}
		
		return $tire;
	}
	
	public static function diameter($attributes) 
	{
		return round(($attributes['section_width'] * $attributes['aspect_ratio'] / 2540 * 2) + $attributes['rim_diameter'], 2);
	}
	
	public static function sidewallHeight($attributes) 
	{
		return round(($attributes['section_width'] * $attributes['aspect_ratio'] / 100 / 25.4), 2);
	}
	
	public static function circumference($overallDiameter) 
	{
		return round(($overallDiameter * 3.1415 ), 2);
	}
	
	public static function revsPerMile($circumference) 
	{
		return round((1000*63.36 / $circumference), 2);
	}
	
	public static function url($attributes, $onlyAttr=false) 
	{
		$url = $attributes['vehicle_class'] . '-' . $attributes['section_width'].'-'.$attributes['aspect_ratio'].'r'.$attributes['rim_diameter'].'.html';
		if (!$onlyAttr) {
			$url = '/tires/' . $url;
		}
		
		return $url; 
	}
	
	public static function urlFormat($attributes) 
	{
		$url = $attributes['vehicle_class'] . '-' . $attributes['section_width'].'-'.$attributes['aspect_ratio'].'r'.$attributes['rim_diameter'];

		return $url; 
	}
	
	public static function getItemsByRimDiameterNonRunflat($rim_diameter_id)
	{
		$rim_diameter_id = (int) $rim_diameter_id;
		
		$key = Tags::TAG_TIRE . '__getItemsByRimDiameter__'.$rim_diameter_id;
		$data = Yii::app()->cache->get($key);
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
							r_ar.value AS rear_aspect_ratio
						FROM tire AS t
						LEFT JOIN tire_vehicle_class AS vc ON t.vehicle_class_id = vc.id
						LEFT JOIN tire_rim_diameter AS rd ON t.rim_diameter_id = rd.id
						LEFT JOIN tire_section_width AS sw ON t.section_width_id = sw.id
						LEFT JOIN tire_aspect_ratio AS ar ON t.aspect_ratio_id = ar.id
						LEFT JOIN tire_rim_diameter AS r_rd ON t.rear_rim_diameter_id = r_rd.id
						LEFT JOIN tire_section_width AS r_sw ON t.rear_section_width_id = r_sw.id
						LEFT JOIN tire_aspect_ratio AS r_ar ON t.rear_aspect_ratio_id = r_ar.id
						WHERE rd.id = {$rim_diameter_id} AND t.is_runflat = 0
						ORDER BY rd.value, sw.value, ar.value, t.is_rear
				";
		
			$items = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($items as $item) {
				$data[] = $item;
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_TIRE_SECTION_WIDTH, Tags::TAG_TIRE_ASPECT_RATIO, Tags::TAG_TIRE_RIM_DIAMETER));
		}	
		
		return $data;	
	}	
	
	public static function getItemsByAttributes($attributes)
	{
		$key = Tags::TAG_TIRE . '__getItemByAttributes_'.serialize($attributes);
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			$where = '';
			foreach ($attributes as $k=>$v) {
				$where[] = "$k = '$v'";
			}	
			if (!empty($where)) {
				$where = implode(' AND ', $where);
			}
			
			$sql = "SELECT 
							t.id AS id, 
							t.rim_diameter_id AS rim_diameter_id, 
							t.section_width_id AS section_width_id, 
							t.vehicle_class_id AS vehicle_class_id, 
							t.aspect_ratio_id AS aspect_ratio_id,							
							rd.value AS rim_diameter, 
							sw.value AS section_width, 
							vc.code AS vehicle_class, 
							ar.value AS aspect_ratio
						FROM tire AS t
						LEFT JOIN tire_vehicle_class AS vc ON t.vehicle_class_id = vc.id
						LEFT JOIN tire_rim_diameter AS rd ON t.rim_diameter_id = rd.id
						LEFT JOIN tire_section_width AS sw ON t.section_width_id = sw.id
						LEFT JOIN tire_aspect_ratio AS ar ON t.aspect_ratio_id = ar.id
						WHERE $where
			";
			
			$data = Yii::app()->db->createCommand($sql)->queryAll();
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_TIRE_SECTION_WIDTH, Tags::TAG_TIRE_ASPECT_RATIO, Tags::TAG_TIRE_RIM_DIAMETER));
		}	
		
		return $data;	
	}	
	
	public static function getSimilarSizes($tire)
	{
		$key = Tags::TAG_TIRE . '__getSimilarSizes___' . serialize($tire);
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			$sectionWidth 	 = $tire['section_width'];
			$rimDiameter 	 = $tire['rim_diameter'];
			$sectionWidthMin = $sectionWidth-30;
			$sectionWidthMax = $sectionWidth+30;
			
			$overallDiameter = self::diameter($tire);
			$limitPercent	 = 3;
			
			$sql = "SELECT 
							t.id AS id, 
							rd.value AS rim_diameter, 
							sw.value AS section_width, 
							vc.code AS vehicle_class, 
							ar.value AS aspect_ratio,
						   ((sw.value*ar.value/2540*2)+rd.value) AS overallDiameter,
						   ROUND((100-(((sw.value*ar.value/2540*2)+rd.value)/$overallDiameter * 100))*(-1), 2) as percent
						FROM tire AS t
						LEFT JOIN tire_vehicle_class AS vc ON t.vehicle_class_id = vc.id
						LEFT JOIN tire_rim_diameter AS rd ON t.rim_diameter_id = rd.id
						LEFT JOIN tire_section_width AS sw ON t.section_width_id = sw.id
						LEFT JOIN tire_aspect_ratio AS ar ON t.aspect_ratio_id = ar.id
						WHERE rd.value=$rimDiameter AND sw.value >= $sectionWidthMin AND t.is_runflat=0	ANd t.is_rear=0
						HAVING overallDiameter <= $overallDiameter AND percent >= -$limitPercent
						ORDER BY percent ASC
						LIMIT 5
			";
			
			$dataMin = Yii::app()->db->createCommand($sql)->queryAll();
			
			$sql = "SELECT 
								t.id AS id, 
								rd.value AS rim_diameter, 
								sw.value AS section_width, 
								vc.code AS vehicle_class, 
								ar.value AS aspect_ratio,
							   ((sw.value*ar.value/2540*2)+rd.value) AS overallDiameter,
							   ROUND((100-($overallDiameter/((sw.value*ar.value/2540*2)+rd.value) * 100)), 2) as percent
							FROM tire AS t
							LEFT JOIN tire_vehicle_class AS vc ON t.vehicle_class_id = vc.id
							LEFT JOIN tire_rim_diameter AS rd ON t.rim_diameter_id = rd.id
							LEFT JOIN tire_section_width AS sw ON t.section_width_id = sw.id
							LEFT JOIN tire_aspect_ratio AS ar ON t.aspect_ratio_id = ar.id
							WHERE rd.value=$rimDiameter AND sw.value <= $sectionWidthMax AND t.is_runflat=0	ANd t.is_rear=0
							HAVING overallDiameter >= $overallDiameter AND percent <= $limitPercent
							ORDER BY percent ASC
							LIMIT 10
				";

			$dataMax = Yii::app()->db->createCommand($sql)->queryAll();
			$data = array_merge($dataMin, $dataMax);
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_TIRE_SECTION_WIDTH, Tags::TAG_TIRE_ASPECT_RATIO, Tags::TAG_TIRE_RIM_DIAMETER));
		}	
		
		return $data;	
	}	
	
	public static function getMakeModelsByTireIds($ids)
	{
		$key = Tags::TAG_TIRE . '__getMakeModelsByTireIds_'.serialize($ids);
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			if (!empty($ids)) {	
				
				//select model year ids
				$items = Yii::app()->db->createCommand("SELECT DISTINCT model_year_id FROM auto_model_year_tire WHERE tire_id IN(".implode(',', $ids).")")->queryAll();
				if (!empty($items)) {
					$modelYearIds = array();
					foreach ($items as $item) {
						$modelYearIds[] = $item['model_year_id'];
					}
					
					//select model ids
					if (!empty($modelYearIds)) {
						$items = Yii::app()->db->createCommand("SELECT DISTINCT model_id FROM auto_model_year WHERE id IN(".implode(',', $modelYearIds).")")->queryAll();
						$modelIds = array();
						if (!empty($items)) {
							foreach ($items as $item) {
								$modelIds[] = $item['model_id'];
							}	
							
							//select model with make
							if (!empty($modelIds)) {
								$sql = "SELECT 
											m.id AS model_id, 
											m.alias AS model_alias, 
											m.title AS model_title, 
											make.id AS make_id, 
											make.alias AS make_alias, 
											make.title AS make_title, 
											make.image_ext AS make_image_ext
										FROM auto_model AS m
										LEFT JOIN auto_make AS make ON m.make_id = make.id
										WHERE 
											m.id IN(".implode(',', $modelIds).") AND
											make.is_active = 1 AND
											m.is_active = 1 
										";
								$items = Yii::app()->db->createCommand($sql)->queryAll();	
								foreach ($items as $item){
									$modelAttr = array(
										'id' => $item['model_id'],
										'alias' => $item['model_alias'],
										'title' => $item['model_title'],
									);
								
									if (!isset($data[$item['make_id']])) {
										$data[$item['make_id']] = array(
											'id' => $item['make_id'],
											'title' => $item['make_title'],
											'alias' => $item['make_alias'],	
											'image' => AutoMake::image(array(
												'ext' => $item['make_image_ext'],
												'width' => 150,
												'height' => 80,
												'mode' => 'resize',
												'id' => $item['make_id'],
											)),											
										);
									}
									
									$data[$item['make_id']]['models'][] = $modelAttr;
								}
							}	
						}
					}					
				}
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_TIRE_VEHICLE_CLASS, Tags::TAG_TIRE_SECTION_WIDTH, Tags::TAG_TIRE_ASPECT_RATIO, Tags::TAG_TIRE_RIM_DIAMETER));
		}	
		
		return $data;	
	}	
	
	public static function getModelYearsByMakeTireIds($make_id, $ids)
	{
		$key = Tags::TAG_TIRE . '_getModelYearsByMakeTireIds_'. $make_id . '_' . serialize($ids);
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			if (!empty($ids)) {	
				
				//select model year ids
				$items = Yii::app()->db->createCommand("SELECT DISTINCT model_year_id FROM auto_model_year_tire WHERE tire_id IN(".implode(',', $ids).")")->queryAll();
				if (!empty($items)) {
					$modelYearIds = array();
					foreach ($items as $item) {
						$modelYearIds[] = $item['model_year_id'];
					}
					
					//select model ids

					if (!empty($modelYearIds)) {
								$sql = "SELECT 
											m.id AS model_id, 
											m.alias AS model_alias, 
											m.title AS model_title, 
											y.id AS year_id, 
											y.year AS year
										FROM auto_model_year AS y
										LEFT JOIN auto_model AS m ON y.model_id = m.id
										WHERE 
											y.id IN(".implode(',', $modelYearIds).") AND
											y.is_active = 1  AND 
											m.make_id = $make_id AND
											m.is_active = 1 
											
										";
								$items = Yii::app()->db->createCommand($sql)->queryAll();	
								foreach ($items as $item){
								
									if (!isset($data[$item['model_id']])) {
										$lastYear = AutoModel::getLastYear($item['model_id']);
									
										$data[$item['model_id']] = array(
											'id' => $item['model_id'],
											'title' => $item['model_title'],
											'alias' => $item['model_alias'],											
											'photo' => $lastYear['photo'],											
										);
									}
									
									$data[$item['model_id']]['years'][$item['year_id']] = $item['year'];
								}
					}					
				}
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_TIRE_VEHICLE_CLASS, Tags::TAG_TIRE_SECTION_WIDTH, Tags::TAG_TIRE_ASPECT_RATIO, Tags::TAG_TIRE_RIM_DIAMETER));
		}	
		
		return $data;	
	}	
	
	public static function getAutoModelsByTire($tire_id)
	{
		$tire_id = (int) $tire_id;
		
		$key = Tags::TAG_TIRE . '_getAutoModelsByTire_' . $tire_id;
		$data = Yii::app()->cache->get($key);
		if ($data == false) {
			$data = array();
			
			$sql 	= "SELRCT model_year_id FROM auto_model_year_tire WHERE tire_id = {$tire_id}";
			$items 	= Yii::app()->db->createCommand($sql)->queryAll();
			$modelYearIds = array();
			foreach ($items as $item) {
				$modelYearIds[] = $item['model_year_id'];
			}
			
			if (!empty($modelYearIds)) {
			
				$sql = "SELECT 
								y.id AS year_id, 
								y.year AS year, 
								model.id AS model_id, 
								model.alias AS model_alias, 
								model.title AS model, 
								make.id AS make_id, 
								make.title AS make_title, 
								make.alias AS make_alias
								make.image_ext AS make_image_ext
							FROM auto_model_year AS y
							LEFT JOIN auto_model AS model ON y.model_id = model.id
							LEFT JOIN auto_make AS make ON model.make_id = make.id
							WHERE y.id IN(".implode(',', $modelYearIds).")
				";
		
				$items = Yii::app()->db->createCommand($sql)->queryAll();
				
				foreach ($items as $item) {
					$row = $item;
					$row['make_image'] = AutoMake::image(array(
						'ext' => $item['make_image_ext'],
						'id' => $item['make_id'],
						'width' => 150,
						'height' => 80,
						'mode' => 'resize',
					));
					
					$data[] = $row;
				}
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_TIRE_SECTION_WIDTH, Tags::TAG_TIRE_ASPECT_RATIO, Tags::TAG_TIRE_RIM_DIAMETER));
		}	
		
		return $data;	
	}	
	
	public static function getYearRangeModel($model_id, $tire_id)
	{
		$model_id = (int) $model_id;
		$tire_id = (int) $tire_id;
		
		$key = Tags::TAG_TIRE . '_getYearRangeModel_' . $model_id . '_' . $tire_id;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			$sql = "SELECT 
						MIN(y.year) AS mmin,
						MAX(y.year) AS mmax
					FROM auto_model_year_tire AS vs
					LEFT JOIN auto_model_year AS y ON vs.model_year_id = y.id
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					WHERE m.id = {$model_id} AND vs.tire_id = {$tire_id}
				";
		
			$data = Yii::app()->db->createCommand($sql)->queryRow();

			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_TIRE, Tags::TAG_MODEL_YEAR));
		}	
		
		return $data;	
	}	
	
}