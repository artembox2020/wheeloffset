<?php

class AutoCompletion extends CActiveRecord
{
	public $validateSpecs = true;
	public $validateTitle = true;
	
	const PHOTO_DIR = '/photos/completion/';
	
	public $file;
	public $is_delete_photo;
	public $model_id;
	public $year;
	
	const PREFIX_SPECS = 'specs_';
	
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
		return 'auto_completion';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		$rules = array(
			array('model_year_id', 'required'),
			array('model_id, code, alias, url', 'safe'),
			array('year, is_delete_photo', 'numerical', 'integerOnly' => true),
			array('id,is_active, is_deleted', 'numerical', 'integerOnly' => true),
			array(
				'file', 
				'file', 
				'types'=>'jpg,png,gif,jpeg',
				'allowEmpty'=>true
			),		
		);
		
		if ($this->validateTitle) {
			$rules[] = array('title', 'required');
		}

		if ($this->validateSpecs) {
	
			$specs = AutoSpecs::getAll();
			foreach ($specs as $spec) {
				$attribute = self::PREFIX_SPECS.$spec['alias'];
				
				if (!$this->hasAttribute($attribute)) {
					continue;
				}
				
				$rules[] = array($attribute, 'safe');
			
				if ($spec['is_required'])		
					$rules[] = array($attribute, 'required');
					
				if (in_array($spec['type'], array(AutoSpecs::TYPE_INT, AutoSpecs::TYPE_CHECKBOX, AutoSpecs::TYPE_SELECT)))
					$rules[] = array($attribute, 'numerical', 'integerOnly' => true);
				
				if ($spec['type'] == AutoSpecs::TYPE_FLOAT)
					$rules[] = array($attribute, 'numerical');		
			}
		}

		return $rules;
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'ModelYear' => array(self::BELONGS_TO, 'AutoModelYear', 'model_year_id', 'together'=>true,),
        );
	}	

	public function afterFind()
	{
		if (!empty($this->ModelYear) ) { 
			$this->model_id = $this->ModelYear->model_id;		
		}		
		
		return parent::afterFind();
	}	
	
	/**
	 * Выполняем ряд действий перед валидацией модели
	 * @return boolean -- результат выполнения операции
	 */
	protected function beforeValidate()
	{
		//создаем алиас к тайтлу
		$this->buildAlias();
		return parent::beforeValidate();
	}
	
	protected function beforeSave()
	{
		if ($this->is_delete_photo) {
			$this->_deleteImage();
			$this->image_path = '';
		}	
	
        $this->specs_horsepower_rpm = str_replace(',', '', $this->specs_horsepower_rpm);
        $this->specs_torque_rpm = str_replace(',', '', $this->specs_torque_rpm);
        
		$this->update_time = time();
		return parent::beforeSave();
	}
	
	/**
	 * Создаем алиас к тайтлу
	 */
	private function buildAlias()
	{
		if (empty($this->alias) && !empty($this->title)) { 
			$this->alias = $this->title;
		}
		
		$this->alias = TextHelper::urlSafe($this->alias);
	}	
 
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'id' => 'ID',
			'title' => Yii::t('admin', 'Title'),
			'rank' => Yii::t('admin', 'Rank'),
			'model_id' => Yii::t('admin', 'Model'),
			'model_year_id' => Yii::t('admin', 'Model'),
			'year' => Yii::t('admin', 'Year'),
			'is_active' => Yii::t('admin', 'Published'),
			'is_deleted' => Yii::t('admin', 'Deleted'),	
			'is_delete_photo' => Yii::t('admin', 'Delete Photo'),	
			'file' => 'File Name',			
		);
        
		$specs = AutoSpecs::getAll();
		foreach ($specs as $spec) {
			$labels[self::PREFIX_SPECS.$spec['alias']] = $spec['title'];
		}
		
		return $labels;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($conditions=array())
	{
		$criteria=new CDbCriteria;
		
		if (!empty($conditions)) {
			foreach ($conditions as $c) {
				$criteria->addCondition($c);
			}
		}
		
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.title',$this->title, true);
		$criteria->compare('t.is_deleted',$this->is_deleted);
		$criteria->compare('t.is_active',$this->is_active);			
		$criteria->compare('Model.id',$this->model_year_id);			
		
		$criteria->with = array(
			'ModelYear',
			'ModelYear.Model',
		);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
		
		
	}
	
	public static function deleteSpecsAttributes()
	{
		$columns = AutoCompletion::model()->getMetaData()->tableSchema->columns;
		foreach ($columns as $field=>$data) {
			if (preg_match('/^[specs_]/', $field) && $field!='code') {
				Yii::app()->db->createCommand()->dropColumn('auto_completion', $field);
			}
		}
	}
	
	public function afterSave()
	{
		$this->_clearCache();
		
		if (!empty($this->file)) {
			if (!$this->isNewRecord && !empty($this->image_path)) {
				$this->_deleteImage();
			}		
			
			$dir = Yii::getPathOfAlias('webroot') . self::PHOTO_DIR . $this->ModelYear->Model->Make->alias . '/';
			if (!is_dir($dir)) {
				mkdir($dir, 0777);
			}
			$dir .= $this->ModelYear->Model->alias . '/';
			if (!is_dir($dir)) {
				mkdir($dir, 0777);
			}
			$dir .= $this->ModelYear->year . '/';
			if (!is_dir($dir)) {
				mkdir($dir, 0777);
			}
			$image_path =  $this->ModelYear->Model->Make->alias . '/' . $this->ModelYear->Model->alias . '/' . $this->ModelYear->year . '/' . $this->alias.'-'.$this->id . '.'.$this->file->getExtensionName();
			
			$this->file->saveAs(Yii::getPathOfAlias('webroot') . self::PHOTO_DIR . $image_path);
			$this->updateByPk($this->id, array('image_path'=>$image_path));
		}
		
		
		return parent::afterSave();
	}
		
	private function _deleteImage()
    {
        if (!empty($this->image_path)) {
			$pi = pathinfo(Yii::getPathOfAlias('webroot') . self::PHOTO_DIR . $this->image_path);
		
			$files = $this->bfglob($pi['dirname'], "thumb*{$pi['basename']}", 0, 10);			
			foreach ($files as $file) {
				@unlink($file);
			}
			@unlink(Yii::getPathOfAlias('webroot') . self::PHOTO_DIR . $this->image_path);
		}
    }	
	
	function bfglob($path, $pattern = '*', $flags = 0, $depth = 0) {
        $matches = array();
        $folders = array(rtrim($path, DIRECTORY_SEPARATOR));
 
        while($folder = array_shift($folders)) {
            $matches = array_merge($matches, glob($folder.DIRECTORY_SEPARATOR.$pattern, $flags));
            if($depth != 0) {
                $moreFolders = glob($folder.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
                $depth   = ($depth < -1) ? -1: $depth + count($moreFolders) - 2;
                $folders = array_merge($folders, $moreFolders);
            }
        }
        return $matches;
    }	
	
	
	
	public function afterDelete()
	{
		$this->_clearCache();
		
		return parent::afterDelete();
	}
	
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_COMPLETION);
	}
	
	
	public static function getItemsByYear($model_year_id) 
	{
		$model_year_id = (int) $model_year_id;
		
		$key = Tags::TAG_COMPLETION . '__MODEL_BY_YEAR__'.$model_year_id;	
		$data = Yii::app()->cache->get($key);

		if ($data === false) {
			$data = array();
			$criteria=new CDbCriteria;

			$criteria->compare('model_year_id',$model_year_id);
			$criteria->compare('is_deleted',0);
			$criteria->compare('is_active',1);			
				
			$items = self::model()->findAll($criteria);
			foreach ($items as $item) {
				$data[] = $item->attributes;
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_COMPLETION));
		}
		
		return $data;			
	}	
	
	public static function getCurbWeightByModelYear($model_year_id) 
	{
		$data = self::getItemsByYear($model_year_id);
		usort ($data, "cmpArrayCurbWeight");
		
		return $data;
	}	
	
	public static function getMpgByModelYear($model_year_id) 
	{
		$data = self::getItemsByYear($model_year_id);
		usort ($data, "cmpArrayFuelEconomyCity");
		
		return $data;
	}	
	
	public static function getFastest($limit=6)
	{
		$key = Tags::TAG_COMPLETION . '__FASTEST__' . $limit;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$sql = "SELECT 
						MAX(specs_0_60mph__0_100kmh_s_) AS speed,
						c.id AS id,
						c.specs_1_4_mile_time AS mile_time,
						c.specs_1_4_mile_speed AS mile_speed,
						c.specs_horsepower AS horsepower,
						c.specs_torque AS torque,
						c.specs_engine AS engine,
						y.year AS year,
						y.id AS year_id,
						m.title AS model_title,
						m.alias AS model_alias,
						k.title AS make_title,
						k.alias AS make_alias
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						c.specs_0_60mph__0_100kmh_s_ IS NOT NULL AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						m.is_active = 1 AND
						m.is_deleted = 0 AND
						k.is_active = 1 AND
						k.is_deleted = 0
					GROUP BY y.model_id
					ORDER BY speed ASC
					LIMIT {$limit}
					";
					
			$rows = Yii::app()->db->createCommand($sql)->queryAll();	
			$data = array();
			
			$ids = array();
			foreach ($rows as $row) {
				$ids[] = $row['year_id'];
			}
			
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$ids);			
			$criteria->index = 'id';			
			$modelYears = AutoModelYear::model()->findAll($criteria);

			foreach ($rows as $row) {

				$expl = explode('@', $row['horsepower']);
				$row['horsepower'] = $expl[0];
				
				$expl = explode('@', $row['torque']);
				$row['torque'] = $expl[0];
				$row['photo'] = $modelYears[$row['year_id']]->getThumb(150, null, 'resize');
				$row['engine'] = AutoSpecsOption::getV('engine', $row['engine']);
				
				$data[] = $row;
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;		
	}
	
	
	public static function getMakeTimes($make_id)
	{
		$key = Tags::TAG_COMPLETION . '__MAKE_TIMES__' . $make_id;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			$models = AutoMake::getModels($make_id);
			foreach ($models as $model) {
				$times = AutoModel::getMinMaxSpecs('0_60mph__0_100kmh_s_', $model['id']);
				if ($times['mmin'] == 0) {
					continue;
				}
		
				$data[$model['id']]['title'] = $model['title'];
				$data[$model['id']]['alias'] = $model['alias'];
				$data[$model['id']]['url'] = $model['url'];
				$data[$model['id']]['0_60_times'] = $times;
				$data[$model['id']]['mile_time']['max'] = AutoModel::getMaxSpecs('1_4_mile_time', $model['id']);				
				$data[$model['id']]['mile_speed']['max'] = AutoModel::getMaxSpecs('1_4_mile_speed', $model['id']);
				$data[$model['id']]['mile_time']['min'] = AutoModel::getMinSpecs('1_4_mile_time', $model['id']);				
				$data[$model['id']]['mile_speed']['min'] = AutoModel::getMinSpecs('1_4_mile_speed', $model['id']);
			}
				
			usort ($data, "cmpArrayTimes");	
			//d($data);
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;		
	}

	public static function getAccelerationAcrossYears($model_id)
	{
		$key = Tags::TAG_COMPLETION . '_MODEL_ACCELERATION_ACROSS_YEARS_' . $model_id;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			$years = AutoModel::getYears($model_id);
			
			//d($years);
			
			foreach ($years as $year) {
				$times = AutoModelYear::getMinMaxSpecs('0_60mph__0_100kmh_s_', $year['id']);
				
				if ($times['mmin'] == 0) {
					continue;
				}
			
				$data[$year['id']]['year'] = $year['year'];
				$data[$year['id']]['0_60_times'] = $times;
				$data[$year['id']]['mile_time']['max'] = AutoModelYear::getMaxSpecs('1_4_mile_time', $year['id']);				
				$data[$year['id']]['mile_speed']['max'] = AutoModelYear::getMaxSpecs('1_4_mile_speed', $year['id']);
				$data[$year['id']]['mile_time']['min'] = AutoModelYear::getMinSpecs('1_4_mile_time', $year['id']);				
				$data[$year['id']]['mile_speed']['min'] = AutoModelYear::getMinSpecs('1_4_mile_speed', $year['id']);
			}
				
			usort ($data, "cmpArrayYears");	
					
			//d($data, 0);
				
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;		
	}


	
	public static function getCompetitorsAcceleration($model_id)
	{
		$key = Tags::TAG_COMPLETION . '_MODEL_COMPETITORS_ACCELERATION_' . $model_id;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			
			$lastModelYear = AutoModel::getLastYear($model_id);
			$competitors = AutoModelYear::getFrontCompetitors($lastModelYear['id']);

			foreach ($competitors as $competitor) {
				$times = AutoModelYear::getMinMaxSpecs('0_60mph__0_100kmh_s_', $competitor['id']);
				
				if ($times['mmin'] == 0) {
					continue;
				}
			
				$data[$competitor['id']]['year'] = $competitor['year'];
				$data[$competitor['id']]['model'] = $competitor['model'];
				$data[$competitor['id']]['model_alias'] = $competitor['model_alias'];
				$data[$competitor['id']]['make'] = $competitor['make'];
				$data[$competitor['id']]['make_alias'] = $competitor['make_alias'];
				$data[$competitor['id']]['0_60_times'] = $times;
				$data[$competitor['id']]['mile_time']['max'] = AutoModelYear::getMaxSpecs('1_4_mile_time', $competitor['id']);				
				$data[$competitor['id']]['mile_speed']['max'] = AutoModelYear::getMaxSpecs('1_4_mile_speed', $competitor['id']);
				$data[$competitor['id']]['mile_time']['min'] = AutoModelYear::getMinSpecs('1_4_mile_time', $competitor['id']);				
				$data[$competitor['id']]['mile_speed']['min'] = AutoModelYear::getMinSpecs('1_4_mile_speed', $competitor['id']);
			}
				
			usort ($data, "cmpArrayTimes");	
				
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		//d($data);
		
		return $data;		
	}
	
	public static function getCarsWithSame060Time($model_id, $lastModelYear)
	{
		$year = $lastModelYear['year'];
	
		$key = Tags::TAG_COMPLETION . '_MODEL_CARS_WITH_SAME_0_60_TIME___' . $model_id;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false && !is_array($data)) {
			$data = array();
			
			$maxSpeed = AutoModel::getMinSpecs('0_60mph__0_100kmh_s_', $model_id, $lastModelYear['id']);
			
			$sql = "SELECT 
						c.id AS id,
						c.specs_1_4_mile_time AS mile_time,
						c.specs_1_4_mile_speed AS mile_speed,
						c.specs_horsepower AS horsepower,
						c.specs_torque AS torque,
						c.specs_engine AS engine,
						c.specs_0_60mph__0_100kmh_s_ AS speed,
						y.year AS year,
						y.id AS year_id,
						m.title AS model_title,
						m.alias AS model_alias,
						m.id AS model_id,
						k.title AS make_title,
						k.alias AS make_alias
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						c.specs_0_60mph__0_100kmh_s_ = {$maxSpeed} AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						y.year = {$year} AND
						y.model_id <> {$model_id}
					GROUP BY y.model_id
					ORDER BY make_title ASC, model_title ASC
					";
					
			$rows = Yii::app()->db->createCommand($sql)->queryAll();			
			$itemIds = array($model_id);
			foreach ($rows as $row) {
				$itemIds[] = (int)$row['model_id'];
			}	

			$dataIds = ArrayHelper::getArrayСircleNeighbor($itemIds, $model_id);
			
			$lastModelYear = AutoModel::getLastYear($model_id);
			$competitors = AutoModelYear::getFrontCompetitors($lastModelYear['id']);

			foreach ($rows as $row) {
				
				if (!in_array($row['model_id'], $dataIds)) {
					continue;
				}
			
				$data[$row['id']]['year'] = $row['year'];
				$data[$row['id']]['model'] = $row['model_title'];
				$data[$row['id']]['model_alias'] = $row['model_alias'];
				$data[$row['id']]['make'] = $row['make_title'];
				$data[$row['id']]['make_alias'] = $row['make_alias'];
				$data[$row['id']]['speed'] = $row['speed'];
				$data[$row['id']]['mile_time'] = $row['mile_time'];				
				$data[$row['id']]['mile_speed'] = $row['mile_speed'];				
			}
				
			//d($data);
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		//d($data);
		
		return $data;		
	}
	
	public static function getItemsByYearOrderTime($ids)
	{
		$key = Tags::TAG_COMPLETION . '__ITEMS_BY_YEAR_ORDER_TIME___' . serialize($ids);
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {	
			$data = array();
			
			foreach ($ids as $model_year_id) {
				$dataItem = array();
				$items = self::getItemsByYear($model_year_id);
				
				foreach ($items as $item) {
					if ((float)$item['specs_0_60mph__0_100kmh_s_'] == 0) {continue;}
					$dataItem[] = $item;
				}
				
				usort ($dataItem, "cmpCompletionTimes");	
				
				$data[$model_year_id]['items'] = $dataItem;
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_COMPLETION));
		}
	
		return $data;
	}
	
	public static function getSpecsOptionTitle($specs_id, $option_id)
	{
		$options = AutoSpecsOption::getAllBySpecs($specs_id);
		if (isset($options[$option_id])) {
			return $options[$option_id];
		}
	}
	
	public static function getItemSelect($completion_id)
	{
		$model_year_id = (int) $model_year_id;
	
		$key = Tags::TAG_COMPLETION . '__ITEMS_BY_YEAR_ORDER_TIME__' . $model_year_id;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {	
			$items = self::getItemsByYear($model_year_id);
			$data = array();
			foreach ($items as $item) {
				if ((float)$item['specs_0_60mph__0_100kmh_s_'] == 0) {continue;}
				$data[] = $item;
			}
			
			usort ($data, "cmpCompletionTimes");			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_COMPLETION));
		}
	
		return $data;
	}
	
	public static function getHighHorsepower()
	{
		$key = Tags::TAG_COMPLETION . '__getHighHorsepower__';
		$data = Yii::app()->cache->get($key);
		
		if ($data === false && !is_array($data)) {
			$data = array();
			
			$sql = "SELECT 
						c.model_year_id AS model_year_id, 
						c.specs_horsepower AS horsepower,
						c.specs_0_60mph__0_100kmh_s_ AS 0_60_mph, 
						c.specs_1_4_mile_time AS mile_time, 
						c.specs_1_4_mile_speed AS mile_speed, 
						c.specs_msrp AS msrp, 
						c.specs_curb_weight AS curb_weight, 
						c.specs_fuel_economy__city AS fuel_economy_city, 
						c.specs_fuel_economy__highway AS fuel_economy_highway, 
						SUBSTRING_INDEX(c.specs_torque, '@', 1) AS torque,
						SUBSTRING_INDEX(c.specs_horsepower, '@', 1) AS hp,
						y.year AS model_year,
						model.title AS model_title,
						model.alias AS model_alias,
						make.title AS make_title,
						make.alias AS make_alias
					FROM `auto_completion` AS c 
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS model ON y.model_id = model.id
					LEFT JOIN auto_make AS make ON model.make_id = make.id
					WHERE 
						c.is_active=1 AND 
						c.is_deleted=0 AND 
						y.is_active=1 AND  
						y.is_deleted=0 AND 
						model.is_active=1 AND 
						model.is_deleted=0 AND 
						make.is_active=1 AND
						make.is_deleted=0
					GROUP BY c.model_year_id
					ORDER BY CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) DESC
					LIMIT 20";
					
			$rows = Yii::app()->db->createCommand($sql)->queryAll();			
			$modelYearIds = array();
			foreach ($rows as $row) {
				$data[$row['model_year_id']] = $row;
				$modelYearIds[] = $row['model_year_id'];
			}	
			
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$modelYearIds);			
			$modelYears = AutoModelYear::model()->findAll($criteria);			
			foreach ($modelYears as $modelYear) {
				$data[$modelYear->id]['photo'] = $modelYear->getThumb(150, null, 'resize');
			}

			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;		
	}		
		
	public static function getItemsCurbWeight($limit, $order)
	{
		$minYear = date('Y')-1;
			
		$key = Tags::TAG_COMPLETION . '__getItemsCurbWeight_' . $limit . $order . $minYear;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false && !is_array($data)) {
			$data = array();
			
			$sql = "SELECT 
						c.model_year_id AS model_year_id, 
						c.specs_curb_weight AS curb_weight,
						c.title AS completion_title,
						c.id AS completion_id,
						y.year AS model_year,
						model.title AS model_title,
						model.alias AS model_alias,
						make.title AS make_title,
						make.alias AS make_alias
					FROM `auto_completion` AS c 
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS model ON y.model_id = model.id
					LEFT JOIN auto_make AS make ON model.make_id = make.id
					WHERE 
						c.is_active=1 AND 
						c.is_deleted=0 AND 
						y.is_active=1 AND  
						y.is_deleted=0 AND 
						model.is_active=1 AND 
						model.is_deleted=0 AND 
						make.is_active=1 AND
						make.is_deleted=0 AND 
						y.year >= {$minYear} AND
						c.specs_curb_weight IS NOT NULL AND
						c.specs_curb_weight > 1
					GROUP BY c.model_year_id
					ORDER BY curb_weight {$order}
					LIMIT {$limit}";
					
			$rows = Yii::app()->db->createCommand($sql)->queryAll();			
			$modelYearIds = array();
			foreach ($rows as $row) {
				$data[$row['model_year_id']] = $row;
				$modelYearIds[] = $row['model_year_id'];
			}	
			
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$modelYearIds);			
			$modelYears = AutoModelYear::model()->findAll($criteria);			
			foreach ($modelYears as $modelYear) {
				$data[$modelYear->id]['photo'] = $modelYear->getThumb(150, null, 'resize');
			}

			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;		
	}		
		
	public static function getItemsFuelEconomy($limit, $order)
	{
		$minYear = date('Y')-2;
			
		$key = Tags::TAG_COMPLETION . '_getItemsFuelEconomy_' . $limit . $order . $minYear;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			
			$sql = "SELECT 
						c.model_year_id AS model_year_id, 
						c.specs_fuel_economy__city AS fuel_economy_city,
						c.specs_fuel_economy__highway AS fuel_economy_highway,
						c.title AS completion_title,
						c.id AS completion_id,
						y.year AS model_year,
						model.title AS model_title,
						model.alias AS model_alias,
						make.title AS make_title,
						make.alias AS make_alias
					FROM `auto_completion` AS c 
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS model ON y.model_id = model.id
					LEFT JOIN auto_make AS make ON model.make_id = make.id
					WHERE 
						c.is_active=1 AND 
						c.is_deleted=0 AND 
						y.is_active=1 AND  
						y.is_deleted=0 AND 
						model.is_active=1 AND 
						model.is_deleted=0 AND 
						make.is_active=1 AND
						make.is_deleted=0 AND 
						y.year >= {$minYear} AND
						c.specs_curb_weight IS NOT NULL
					GROUP BY c.model_year_id
					ORDER BY fuel_economy_city {$order}
					LIMIT {$limit}";
					
			$rows = Yii::app()->db->createCommand($sql)->queryAll();			
			$modelYearIds = array();
			foreach ($rows as $row) {
				$data[$row['model_year_id']] = $row;
				$modelYearIds[] = $row['model_year_id'];
			}	
			
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$modelYearIds);			
			$modelYears = AutoModelYear::model()->findAll($criteria);			
			foreach ($modelYears as $modelYear) {
				$data[$modelYear->id]['photo'] = $modelYear->getThumb(150, null, 'resize');
			}

			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;		
	}		
		
	public static function getThumb($image_path, $width, $height, $mode='origin')
	{
		$originFile = Yii::getPathOfAlias('webroot') . $image_path;

		if (is_file($originFile)) {
 		
			if ($mode == 'origin') {
				return self::PHOTO_DIR . $image_path;
			} else {
			
				$pathinfo = pathinfo($originFile);
				
				$thumbFileName = "/thumb_{$mode}_{$width}x{$height}_" . $pathinfo['basename']; 
				$thumbFilePath = $pathinfo['dirname'] . $thumbFileName;
				if (!is_file($thumbFilePath)) {
					$image = Yii::app()->iwi->load($originFile);
					if ($mode=='resize') {
						$image->resize($width, $height);
					} else {
						$image->crop($width, $height);
					}
					$image->save($thumbFilePath);
				}
						
				$exp = explode(self::PHOTO_DIR, $pathinfo['dirname']);		
						
				return self::PHOTO_DIR . end($exp) . $thumbFileName;
			} 
		} else {
			return false;
		}
	}
	
	public static function getHpList()
	{
		$key = Tags::TAG_COMPLETION . 'getHpList_';
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			
			$sql = "SELECT 
						DISTINCT CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) AS hp
					FROM `auto_completion` AS c 
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS model ON y.model_id = model.id
					LEFT JOIN auto_make AS make ON model.make_id = make.id
					WHERE 
						c.is_active=1 AND 
						c.is_deleted=0 AND 
						y.is_active=1 AND  
						y.is_deleted=0 AND 
						model.is_active=1 AND 
						model.is_deleted=0 AND 
						make.is_active=1 AND
						make.is_deleted=0
					ORDER BY CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) ASC";
					
			$rows = Yii::app()->db->createCommand($sql)->queryAll();			
			$modelYearIds = array();
			foreach ($rows as $row) {
				$data[] = $row['hp'];
			}	

			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;		
	}		
	
	public static function getMinMaxSpecsHp($hp, $specs)
	{
		$hp = (int) $hp;
	
		$key = Tags::TAG_COMPLETION . '_getMinMaxSpecsHp_' . $hp . '_' . $specs;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$sql = "SELECT 
						MAX(c.specs_{$specs}) AS mmax,  
						MIN(c.specs_{$specs}) AS mmin
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						m.is_active = 1 AND 
						m.is_deleted = 0 AND
						k.is_active = 1 AND 
						k.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND 
						CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) = {$hp}
					";
				
			//d($sql);
				
			$data = Yii::app()->db->createCommand($sql)->queryRow();
			if (!empty($data)) {
				$data['mmax'] = (float) $data['mmax'];
				$data['mmin'] = (float) $data['mmin'];				
			} else {
				$data = array();
			}
			
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;
	}	
	
	public static function getMinMaxSpecsHpTorque($hp)
	{
		$hp = (int) $hp;
	
		$key = Tags::TAG_COMPLETION . '_getMinMaxSpecsHpTorque_' . $hp;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$sql = "SELECT 
						MAX(CONVERT(SUBSTRING_INDEX(c.specs_torque, '@', 1), SIGNED INTEGER)) AS mmax,  
						MIN(CONVERT(SUBSTRING_INDEX(c.specs_torque, '@', 1), SIGNED INTEGER)) AS mmin
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						m.is_active = 1 AND 
						m.is_deleted = 0 AND
						k.is_active = 1 AND 
						k.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND 
						CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) = {$hp}
					";
				
			//d($sql);
				
			$data = Yii::app()->db->createCommand($sql)->queryRow();
			if (!empty($data)) {
				$data['mmax'] = (float) $data['mmax'];
				$data['mmin'] = (float) $data['mmin'];				
			} else {
				$data = array();
			}
			
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;
	}	
	
	public static function getMinMaxSpecsFuelEconomy($hp)
	{
		$hp = (int) $hp;
		$key = Tags::TAG_COMPLETION . '_getMinMaxSpecsFuelEconomy_' . $hp;
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			
			$sql = "SELECT 
						MAX(c.specs_fuel_economy__city) AS ec,  
						c.specs_fuel_economy__highway AS eh
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						m.is_active = 1 AND 
						m.is_deleted = 0 AND
						k.is_active = 1 AND 
						k.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND 
						CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) = {$hp}
					";
		
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			if (!empty($row)) {
				$data['mmax'] = (float) $row['ec'] . '/' . (float) $row['eh'];
			} 
			
			$sql = "SELECT 
						MIN(c.specs_fuel_economy__city) AS ec,  
						c.specs_fuel_economy__highway AS eh
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						m.is_active = 1 AND 
						m.is_deleted = 0 AND
						k.is_active = 1 AND 
						k.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND 
						CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) = {$hp}
					";
		
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			if (!empty($row)) {
				$data['mmin'] = (float) $row['ec'] . '/' . (float) $row['eh'];
			} 		
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $data;
	}	
	
    public static function getPayloadCapacity($data) 
    {
        $value = null;
        
        if (!empty($data['specs_payload'])) {
            $value = (int) $data['specs_payload'];
        } elseif (!empty($data['specs_gross_vehicle_weight_rating_gvwr_']) && !empty($data['specs_curb_weight'])) {
            $value = (int) ($data['specs_gross_vehicle_weight_rating_gvwr_'] - $data['specs_curb_weight']);
        }
        
        return $value;
    }
}

	