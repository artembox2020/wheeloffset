<?php

class AutoMake extends CActiveRecord
{
	const CACHE_KEY_LIST_FRONT = 'MAKES_LIST_FRONT_1';
	
	public $file; 


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
		return 'auto_make';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			array('alias', 'unique'),
			array('is_active, is_deleted', 'numerical', 'integerOnly' => true),
			array(
				'file', 
				'file', 
				'types'=>'jpg,png,gif,jpeg',
				'allowEmpty'=>true
			),	
			array('description', 'safe',),	
		);
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
	
	protected function beforeSave() {

		if (!empty($this->file)) {
			if (!$this->isNewRecord)
				$this->_deleteImage();
			
			$this->image_ext = $this->file->getExtensionName();
		}				
			
		return parent::beforeSave();
    }	
	
	
	public function afterSave()
	{	
		if (!empty($this->file)) {
			$this->file->saveAs($this->getImage_directory(true) . 'origin.'.$this->image_ext);
		}	
		
		$this->_clearCache();
		
		return parent::afterSave();
	}

    public function afterDelete() 
	{
		$this->_deleteImage();
		$this->_clearCache();	
			
        return parent::afterDelete();
    }	
	
    private function _deleteImage() 
	{
        if (!empty($this->image_ext)) {
            Yii::app()->file->set($this->image_directory)->delete();
            $this->image_ext = '';
        }
    }	
	
	public function getThumb($width=null, $height=null, $mode='origin')
	{
		return self::image(array(
			'ext' => $this->image_ext,
			'id' => $this->id,
			'width' => $width,
			'height' => $height,
			'mode' => $mode,
		));
	}	

	public static function image($attributes)
	{
		$ext = $attributes['ext'];
		$id = $attributes['id'];
		$width = isset($attributes['width'])?$attributes['width']:null;
		$height = isset($attributes['height'])?$attributes['height']:null;
		$mode = isset($attributes['mode'])?$attributes['mode']:'origin';

		$dir = self::model()->getImage_directory(false, $id);
		$originFile = $dir . 'origin.' . $ext;
		
		if (empty($ext) || !is_file($originFile)) {
			return "http://www.placehold.it/{$width}x{$height}/EFEFEF/AAAAAA";
		}
	
		if ($mode == 'origin') {
			return '/photos/make/'.$id.'/origin.'. $ext;
		}
	
		$fileName = $mode . '_w' . $width . '_h' . $height . '.' . $ext;
		$filePath = $dir . $fileName;
		if (!is_file($filePath)) {
			if ($mode == 'resize') {
				Yii::app()->iwi->load($originFile)
							   ->resize($width, $height)
							   ->save($filePath);
			} else {
				Yii::app()->iwi->load($originFile)
							   ->crop($width, $height)
							   ->save($filePath);
			}
		}
		
		return '/photos/make/'. $id . '/'. $fileName;
	}	

    public function getImage_directory($mkdir=false, $id=null) {
		$id = (empty($id))?$this->id:$id;
	
		$directory = Yii::app()->basePath . '/../photos/make/' . $id . '/';
        if (!$mkdir) {
			return $directory;
		
		} else {

			if (file_exists($directory) == false) {
				mkdir($directory);
				chmod($directory, 0777);
			}	

			return $directory;
		}
    }

	
	public function getImage_preview()
	{
		return $this->getThumb(100,60, 'crop') . '?'.time();
	}	
	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => Yii::t('admin', 'Title'),
			'file' => Yii::t('admin', 'Image'),
			'alias' => Yii::t('admin', 'Alias'),
			'image_preview' => Yii::t('admin', 'Image'),
			'is_active' => Yii::t('admin', 'Published'),
			'is_deleted' => Yii::t('admin', 'Deleted'),
			'description' => Yii::t('admin', 'Description'),
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
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('title',$this->title, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
	public static function getAll()
	{
		return CHtml::listData(self::model()->findAll(), 'id', 'title');
	}

	public static function getAllFront()
	{
		$data = Yii::app()->cache->get(self::CACHE_KEY_LIST_FRONT);
			
		if ($data == false) {
			$criteria=new CDbCriteria;
			$criteria->compare('is_active', 1);	
			$criteria->compare('is_deleted', 0);	
			$criteria->order = 'title';	
			$data = CHtml::listData(self::model()->findAll($criteria), 'urlFront', 'title');
			Yii::app()->cache->set(self::CACHE_KEY_LIST_FRONT, $data, 0, new Tags(Tags::TAG_MAKE));
		}
		
		return $data;
	}

	public static function getAllFrontFull()
	{
		$key	= Tags::TAG_MAKE . '_getAllFrontFull';
		$data 	= Yii::app()->cache->get($key);
			
		if ($data == false) {
			$data = array();
			
			$criteria=new CDbCriteria;
			$criteria->compare('is_active', 1);	
			$criteria->compare('is_deleted', 0);	
			$criteria->order = 'title';	
			
			$items = self::model()->findAll($criteria);
			foreach ($items as $item) {
				$data[$item->id] = array(
					'id' => $item->id,
					'alias' => $item->alias,
					'title' => $item->title,
				);
			}
		

		Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE));
		}
		
		return $data;
	}

	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_MAKE);
	}
	
	public function getUrlFront()
	{
		return '/'.$this->alias . '/';
	}
	
	public static function getModels($make_id)
	{
		$key = Tags::TAG_MODEL . '_LIST_MODELS_'.$make_id;
		$dataModels = Yii::app()->cache->get($key);
		if ($dataModels == false && !is_array($dataModels)) {
			$dataModels = array();
		
			$criteria = new CDbCriteria();
			$criteria->compare('t.is_active', 1);
			$criteria->compare('t.is_deleted', 0);
			$criteria->compare('t.make_id', $make_id);
			$criteria->compare('Make.is_active', 1);
			$criteria->compare('Make.is_deleted', 0);
			$criteria->with = array('Make' => array('together'=>true));
			
			$models = AutoModel::model()->findAll($criteria);

			foreach ($models as $model) {
				$price = $model->getMinMaxMsrp();
				$curbWeight = $model->getMinMaxCurbWeight();
				$lastCompletion = AutoModel::getLastCompletion($model['id']);
				$years = AutoModel::getYears($model['id']);
				$lastYear = AutoModel::getLastYear($model['id']);
				
				$row = array(
					'id' => $model->id,
					'title' => $model->title,
					'alias' => $model->alias,
					'url' => $model->urlFront,
					'is_bulb' => $model->is_bulb,
					'price' => array(
						'min' => $price['mmin'],
						'max' => $price['mmax'],
					),
					'curbWeight' => array(
						'min' => (float)$curbWeight['mmin'],
						'max' => (float)$curbWeight['mmax'],
					),
					'completion' => array(
						'engine' => AutoSpecsOption::getV('engine', $lastCompletion['specs_engine']),
						'fuel_economy_city' => AutoSpecsOption::getV('fuel_economy__city', $lastCompletion['specs_fuel_economy__city']),
						'fuel_economy_highway' => AutoSpecsOption::getV('fuel_economy__highway', $lastCompletion['specs_fuel_economy__highway']),
						'standard_seating' => AutoSpecsOption::getV('standard_seating', $lastCompletion['specs_standard_seating']),
					),
					'years' => $years,
				);
				
				if (!empty($lastYear)) {
					$row['lastYear'] = $lastYear['year'];
					$row['photo'] = $lastYear['photo'];
				}
				
				$dataModels[] = $row;
			}
			
			Yii::app()->cache->set($key, $dataModels, 0, new Tags(Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
		}
		
		return $dataModels;
	}	
	
	public static function getMakeByAlias($alias)
	{
		$key = Tags::TAG_MAKE . '_ITEM_'.$alias;
		$make = Yii::app()->cache->get($key);
		if ($make == false) {
			$make = array();
			$criteria = new CDbCriteria();
			$criteria->compare('t.is_active', 1);
			$criteria->compare('t.is_deleted', 0);
			$criteria->compare('t.alias', $alias);
			$model = AutoMake::model()->find($criteria);
			
			if (!empty($model)) {
				$make = array(
					'id' => $model->id,
					'url' => $model->urlFront,
					'alias' => $model->alias,
					'title' => $model->title,
					'description' => $model->description,
					'photo' => $model->getThumb(150, null, 'resize'),
					//'photo_300x250' => $model->getThumb(300, 250, 'resize'),
				);
			}
			
			Yii::app()->cache->set($key, $make, 0, new Tags(Tags::TAG_MAKE));
		}	
		
		return $make;
	}	

	public static function getMakesByYear($year)
	{
		$year = (int) $year;
		$key = Tags::TAG_MAKE . '_MAKES_BY_YEAR__'.$year;
		$data = Yii::app()->cache->get($key);
		if ($data == false) {
			$data = array();
			
			$sql = "SELECT 
						k.title AS title,
						k.alias AS alias
					FROM auto_model_year AS y
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						k.is_active = 1 AND 
						k.is_deleted = 0 AND
						m.is_active = 1 AND 
						m.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						y.year = {$year}
					GROUP BY k.id
					ORDER BY title ASC
					";
					
			$items = Yii::app()->db->createCommand($sql)->queryAll();	
			foreach ($items as $item) {
				$data[$item['alias']] = $item['title'];
			}			

			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR));
		}	
		
		return $data;
	}

    public static function getLugnutsData($make_id)
    {
        $make_id = (int)$make_id;
        $key = Tags::TAG_MODEL . '_getLugnutsData_' . $make_id;
        $data = Yii::app()->cache->get($key);
        if ($data === false) {
            $data = array();
            $sql = "SELECT
                        y.year,
                        y.model_id,
						CAST( GROUP_CONCAT(DISTINCT y.thread_size_id ORDER BY y.thread_size_id DESC) AS CHAR(10000) CHARACTER SET utf8) AS `thread_size_ids`,
						CAST( GROUP_CONCAT(DISTINCT y.center_bore_id ORDER BY y.center_bore_id DESC) AS CHAR(10000) CHARACTER SET utf8) AS `center_bore_size_ids`
					FROM auto_model_year AS y
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					WHERE m.make_id={$make_id} AND y.is_active=1 AND y.is_deleted=0
					GROUP BY
					    y.model_id,
					    y.year
			";
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            $data = [];
            $listCenterBore = RimCenterBore::getAll();
            $listThreadSize = RimThreadSize::getAll();

            foreach ($items as $item) {
                if (!array_key_exists($item['model_id'], $data)) {
                    $data[$item['model_id']] = [
                        'thread_sizes' => [],
                        'center_bore_sizes' => [],
                    ];
                }
                $threadSizeIds = explode(",", $item['thread_size_ids']);
                $centerBoreSizeIds = explode(",", $item['center_bore_size_ids']);

                foreach ($threadSizeIds as $sizeId) {
                    if (!empty($listThreadSize[$sizeId]) && !in_array($listThreadSize[$sizeId], $data[$item['model_id']]['thread_sizes'])) {
                        $data[$item['model_id']]['thread_sizes'][] = $listThreadSize[$sizeId];
                    }
                }

                foreach ($centerBoreSizeIds as $sizeId) {
                    if (!empty($listCenterBore[$sizeId]) && !in_array($listCenterBore[$sizeId], $data[$item['model_id']]['center_bore_sizes'])) {
                        $data[$item['model_id']]['center_bore_sizes'][] = $listCenterBore[$sizeId];
                    }
                }
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR));
        }

        return $data;
    }

	public static function getWheelsData($make_id)
	{
		$make_id = (int) $make_id;
		$key = Tags::TAG_MODEL . '_getWheelsData_'.$make_id;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			$sql = "
				SELECT 
					@model_id:=m.id,
					m.id AS model_id,
					(SELECT MIN(trd_from.value)  
						FROM auto_model_year AS y
						LEFT JOIN tire_rim_diameter AS trd_from ON y.tire_rim_diameter_from_id = trd_from.id AND y.model_id=@model_id
						WHERE y.tire_rim_diameter_from_id IS NOT NULL AND y.is_active=1 AND y.is_deleted=0
					) AS trd_min,
					(SELECT MIN(CAST(trw_from.value AS UNSIGNED))
						FROM auto_model_year AS y
						LEFT JOIN rim_width AS trw_from ON y.rim_width_from_id = trw_from.id AND y.model_id=@model_id 
						WHERE y.rim_width_from_id IS NOT NULL AND y.is_active=1 AND y.is_deleted=0
					) AS trw_min,	
					
					(SELECT MAX(trd_to.value)  
						FROM auto_model_year AS y
						LEFT JOIN tire_rim_diameter AS trd_to ON y.tire_rim_diameter_to_id = trd_to.id AND y.model_id=@model_id
						WHERE y.tire_rim_diameter_to_id IS NOT NULL AND y.is_active=1 AND y.is_deleted=0
					) AS trd_max,

					(SELECT MAX(CAST(trw_to.value AS UNSIGNED))
						FROM auto_model_year AS y
						LEFT JOIN rim_width AS trw_to ON y.rim_width_to_id = trw_to.id AND y.model_id=@model_id
						WHERE y.rim_width_to_id IS NOT NULL AND y.is_active=1 AND y.is_deleted=0
					) AS trw_max,
					
					(SELECT MIN(or_from.value)
						FROM auto_model_year AS y
						LEFT JOIN rim_offset_range AS or_from ON y.offset_range_from_id = or_from.id AND y.model_id=@model_id
						WHERE y.offset_range_from_id IS NOT NULL AND y.is_active=1 AND y.is_deleted=0
					) AS or_min,
					(SELECT MAX(or_to.value)
						FROM auto_model_year AS y
						LEFT JOIN rim_offset_range AS or_to ON y.offset_range_to_id = or_to.id AND y.model_id=@model_id
						WHERE y.offset_range_to_id IS NOT NULL AND y.is_active=1 AND y.is_deleted=0
					) AS or_max,
					
					(SELECT MIN(p_trd_from.value)  
						FROM project AS p
						LEFT JOIN tire_rim_diameter AS p_trd_from ON p.rim_diameter_id = p_trd_from.id 
						WHERE p.model_id=@model_id AND p.rim_diameter_id IS NOT NULL
					) AS p_rd_min,
					(SELECT MAX(p_trd_to.value)  
						FROM project AS p
						LEFT JOIN tire_rim_diameter AS p_trd_to ON p.rim_diameter_id = p_trd_to.id
						WHERE p.model_id=@model_id AND p.rim_diameter_id IS NOT NULL
					) AS p_rd_max,
					
					(SELECT MIN(CAST(p_rw_from.value AS UNSIGNED))
						FROM project AS p
						LEFT JOIN rim_width AS p_rw_from ON p.rim_width_id = p_rw_from.id 
						WHERE p.model_id=@model_id AND p.rim_width_id IS NOT NULL
					) AS p_rw_min,
					(SELECT MAX(CAST(p_rw_to.value AS UNSIGNED))
						FROM project AS p
						LEFT JOIN rim_width AS p_rw_to ON p.rim_width_id = p_rw_to.id
						WHERE p.model_id=@model_id AND p.rim_width_id IS NOT NULL
					) AS p_rw_max,
					
					(SELECT MIN(p_or_from.value)
						FROM project AS p
						LEFT JOIN rim_offset_range AS p_or_from ON p.rim_offset_range_id = p_or_from.id 
						WHERE p.model_id=@model_id AND p.rim_offset_range_id IS NOT NULL
					) AS p_or_min,
					(SELECT MAX(p_or_to.value)
						FROM project AS p
						LEFT JOIN rim_offset_range AS p_or_to ON p.rim_offset_range_id = p_or_to.id
						WHERE p.model_id=@model_id AND p.rim_offset_range_id IS NOT NULL
					) AS p_or_max
					
				FROM auto_model AS m
				WHERE make_id = {$make_id}
				GROUP BY m.id			

			";

			$items = Yii::app()->db->createCommand($sql)->queryAll();	
			foreach ($items as $item) {
				$data[$item['model_id']] = array(
					'trd_min' => $item['trd_min'],
					'trw_min' => $item['trw_min'],
					'trd_max' => $item['trd_max'],
					'trw_max' => $item['trw_max'],
					'or_min' => $item['or_min'],
					'or_max' => $item['or_max'],
					'p_rd_min' => $item['p_rd_min'],
					'p_rd_max' => $item['p_rd_max'],
					'p_rw_min' => $item['p_rw_min'],
					'p_rw_max' => $item['p_rw_max'],
					'p_or_min' => $item['p_or_min'],
					'p_or_max' => $item['p_or_max'],
				);
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_PROJECT));
		}	
		
		return $data;
	}	
    
    public static function getItemsByProductCategoryId($categoryId)
    {
		$key = Tags::TAG_MAKE . 'getItemsByProductCategoryId'.$categoryId;
		$data = Yii::app()->cache->get($key);
		if ($data == false) {
			
			$sql = "SELECT 
						k.title AS title,
						k.alias AS alias,
						k.id AS id
					FROM product_category_model AS cm
					LEFT JOIN auto_model AS m ON cm.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						k.is_active = 1 AND 
						k.is_deleted = 0 AND
						m.is_active = 1 AND 
						m.is_deleted = 0 AND
						cm.is_active = 1 AND 
						cm.category_id = {$categoryId}
					GROUP BY k.id
					ORDER BY title ASC
					";
					
			$data = Yii::app()->db->createCommand($sql)->queryAll();	

			Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MAKE, 
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
		}	
		
		return $data;        
    }

}