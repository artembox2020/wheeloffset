<?php

class AutoModelYear extends CActiveRecord
{

    const PHOTO_DIR = '/photos/model_year_item/';
    const CACHE_KEY_PHOTOS = 'AUTO_MODEL_YEAR_PHOTOS_';

    public $file;
    public $file_url;
    public $is_delete_photo;
    public $image_ext = 'jpg';
    public $post_competitors = array();
    public $post_tires = array();
    public $post_tires_related = array();
    public $post_rims_related = array();

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BodyStyle the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'auto_model_year';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('year, model_id', 'required'),
            array('center_bore_id, thread_size_id, bolt_pattern_id, offset_range_to_id, offset_range_from_id, rim_width_to_id, tire_rim_diameter_to_id, rim_width_from_id, tire_rim_diameter_from_id, id, chassis_id, year', 'numerical', 'integerOnly' => true,),
            array('is_active, is_deleted, is_delete_photo, platform_model_id, year', 'numerical', 'integerOnly' => true),
            array('post_tires, post_competitors, post_tires_related, post_rims_related', 'safe',),
            array('description', 'safe',),
            array('file', 'length', 'max' => 128),
            array(
                'file',
                'file',
                'types' => 'jpg,png,gif,jpeg',
                'allowEmpty' => true
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'BoltPattern' => array(self::BELONGS_TO, 'RimBoltPattern', 'bolt_pattern_id', 'together' => true,),
            'Model' => array(self::BELONGS_TO, 'AutoModel', 'model_id', 'together' => true,),
            'PlatformModel' => array(self::BELONGS_TO, 'PlatformModel', 'platform_model_id', 'together' => true,),
            'Chassis' => array(self::BELONGS_TO, 'AutoModelYearChassis', 'chassis_id', 'together' => true,),
            'galleryPhotos' => array(self::HAS_MANY, 'AutoModelYearPhoto', 'year_id', 'order' => '`rank` ASC',),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'year' => Yii::t('admin', 'Year'),
            'image_preview' => Yii::t('admin', 'Image'),
            'model_id' => Yii::t('admin', 'Model'),
            'post_competitors' => Yii::t('admin', 'Competitors'),
            'image_preview' => Yii::t('admin', 'Image'),
            'is_active' => Yii::t('admin', 'Published'),
            'is_deleted' => Yii::t('admin', 'Deleted'),
            'is_delete_photo' => Yii::t('admin', 'Delete Photo'),
            'description' => Yii::t('admin', 'Description'),
            'file' => 'File Name',
            'chassis_id' => 'Chassis',
            'post_tires' => 'Tires',
            'tire_rim_diameter_from_id' => 'Tire Rim Diameter from',
            'rim_width_from_id' => 'Rim Width from',
            'tire_rim_diameter_to_id' => 'Tire Rim Diameter to',
            'rim_width_to_id' => 'Rim Width to',
            'offset_range_from_id' => 'Rim Offset from',
            'offset_range_to_id' => 'Rim Offset to',
            'bolt_pattern_id' => 'Bolt Pattern',
            'thread_size_id' => 'Thread Size',
            'center_bore_id' => 'Center Bore',
            'platform_model_id' => 'Platform Model',
        );
    }

    protected function afterValidate()
    {
        if (!$this->hasErrors()) {
            $criteria = new CDbCriteria;
            $criteria->compare('model_id', $this->model_id);
            $criteria->compare('year', $this->year);
            if (!$this->isNewRecord) {
                $criteria->addCondition("id <> {$this->id}");
            }

            if (self::model()->count($criteria)) {
                $this->addError('year', "Year {$this->year} alreadey exists");
            }
        }

        return parent::afterValidate();
    }

    protected function beforeSave()
    {
        if ($this->is_delete_photo) {
            $this->_deleteImage();
            $this->file_name = '';
        }

        if ($this->scenario == 'updateAdmin') {
            $this->is_tires = empty($this->post_tires) ? 0 : 1;
        }

        if (empty($this->chassis_id)) {
            $this->chassis_id = null;
        }

        if (empty($this->platform_model_id)) {
            $this->platform_model_id = null;
        }

        return parent::beforeSave();
    }

    /**
     * Выполняем ряд обязательных действий после сохранения модели
     * @return boolean -- результат выполнения действия
     */
    protected function afterSave()
    {
        $deletedCompetitors = array();

        if ($this->scenario == 'updateAdmin') {
            $sql = 'DELETE FROM auto_model_year_competitor WHERE model_year_id = ' . $this->id;
            Yii::app()->db->createCommand($sql)->execute();

            $sql = 'DELETE FROM auto_model_year_tire WHERE model_year_id = ' . $this->id;
            Yii::app()->db->createCommand($sql)->execute();

            $this->post_competitors = (array) $this->post_competitors;
            $this->post_tires = (array) $this->post_tires;

            $sqlParts[] = "model_year_id=$this->id";
            $sqlParts[] = "competitor_id=$this->id";

            $competitors = $this->getCompetitors();
            foreach ($competitors as $id) {
                if (!in_array($id, $this->post_competitors)) {
                    $deletedCompetitors[] = $id;
                }
            }

            foreach ($this->post_competitors as $competitor_id) {
                $competitor_id = (int) $competitor_id;
                if (!$competitor_id)
                    continue;
                foreach ($deletedCompetitors as $deleted_id) {
                    $sqlParts[] = "((model_year_id=$deleted_id AND competitor_id=$competitor_id) OR (model_year_id=$competitor_id AND competitor_id=$deleted_id))";
                }
            }

            $sql = 'DELETE FROM auto_model_year_competitor WHERE ' . implode(' OR ', $sqlParts);
            Yii::app()->db->createCommand($sql)->execute();
        }

        foreach ($this->post_competitors as $competitor_id) {
            if ($this->id == $competitor_id)
                continue;

            $item = new AutoModelYearCompetitor;
            $item->model_year_id = $this->id;
            $item->competitor_id = $competitor_id;
            $item->save();
        }

        if (!empty($this->post_tires)) {
            foreach ($this->post_tires as $tire_id) {
                $item = new AutoModelYearTire;
                $item->model_year_id = $this->id;
                $item->tire_id = $tire_id;
                $item->save();
            }

            if (!empty($this->post_tires_related)) {
                foreach ($this->post_tires_related as $id) {
                    Yii::app()->db->createCommand("DELETE FROM auto_model_year_tire WHERE model_year_id = {$id}")->execute();

                    foreach ($this->post_tires as $tire_id) {
                        $item = new AutoModelYearTire;
                        $item->model_year_id = $id;
                        $item->tire_id = $tire_id;
                        $item->save();
                    }

                    $mYear = $this->findByPk($id);
                    $mYear->is_tires = 1;
                    $mYear->save(false);
                }
            }
        }

        if (!empty($this->file)) {
            if (!$this->isNewRecord) {
                $this->_deleteImage();
            }

            $this->file_name = "{$this->Model->Make->alias}-{$this->Model->alias}-{$this->year}.jpg";
            $this->file->saveAs(self::getImage_directory(true) . $this->file_name);
            $this->updateByPk($this->id, array('file_name' => $this->file_name));
        }

        if (!empty($this->file_url)) {
            if (!$this->isNewRecord) {
                $this->_deleteImage();
            }

            //$imageContent = CUrlHelper::getPage($this->file_url, '', '', $replace=false);	
            $imageContent = @file_get_contents($this->file_url);

            if (!empty($imageContent)) {
                file_put_contents(self::getImage_directory(true) . $this->file_name, $imageContent);
                $this->updateByPk($this->id, array('file_name' => $this->file_name));
            }
        }

        if (!empty($this->post_rims_related)) {
            $attr = array(
                'tire_rim_diameter_from_id' => $this->tire_rim_diameter_from_id,
                'rim_width_from_id' => $this->rim_width_from_id,
                'tire_rim_diameter_to_id' => $this->tire_rim_diameter_to_id,
                'rim_width_to_id' => $this->rim_width_to_id,
                'offset_range_from_id' => $this->offset_range_from_id,
                'offset_range_to_id' => $this->offset_range_to_id,
                'bolt_pattern_id' => $this->bolt_pattern_id,
                'thread_size_id' => $this->thread_size_id,
                'center_bore_id' => $this->center_bore_id,
            );

            foreach ($this->post_rims_related as $id) {
                $modelYear = AutoModelYear::model()->findByPk($id);
                if (!empty($modelYear)) {
                    $modelYear->attributes = $attr;
                    $modelYear->save();
                }
            }
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
        if (!empty($this->file_name)) {
            $files = $this->bfglob(Yii::getPathOfAlias('webroot') . self::PHOTO_DIR, "*{$this->file_name}", 0, 10);
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    function bfglob($path, $pattern = '*', $flags = 0, $depth = 0)
    {
        $matches = array();
        $folders = array(rtrim($path, DIRECTORY_SEPARATOR));

        while ($folder = array_shift($folders)) {
            $m = glob($folder . DIRECTORY_SEPARATOR . $pattern, $flags);
            if (is_array($m))
                $matches = array_merge($matches, $m);

            if ($depth != 0) {
                $moreFolders = glob($folder . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
                $depth = ($depth < -1) ? -1 : $depth + count($moreFolders) - 2;

                if (is_array($moreFolders))
                    $folders = array_merge($folders, $moreFolders);
            }
        }
        return $matches;
    }

    public static function getImage_directory($mkdir = false)
    {
        return Yii::app()->basePath . '/..' . self::PHOTO_DIR;
    }

    public function getPreview()
    {
        return $this->getThumb(150, null, 'resize');
    }

    public function getThumb($width = null, $height = null, $mode = 'origin')
    {
        $dir = self::getImage_directory();
        $originFile = $dir . $this->file_name;

        if (!is_file($originFile)) {
            return "http://www.placehold.it/{$width}x{$height}/EFEFEF/AAAAAA";
        }

        if ($mode == 'origin') {
            return self::PHOTO_DIR . $this->file_name;
        }

        $subdir = $width;
        $subdirPath = $dir . $subdir;
        $subdirPathFile = $subdirPath . '/' . $this->file_name;

        if (file_exists($subdirPath) == false) {
            mkdir($subdirPath);
            chmod($subdirPath, 0777);
        }

        if ($mode == 'resize') {
            Yii::app()->iwi->load($originFile)
                ->resize($width, $height)
                ->save($subdirPathFile);
        } else {
            Yii::app()->iwi->load($originFile)
                ->crop($width, $height)
                ->save($subdirPathFile);
        }

        return self::PHOTO_DIR . $subdir . '/' . $this->file_name;
    }

    public static function thumb($id, $width = null, $height = null, $mode = 'origin')
    {
        $id = (int) $id;
        $key = Tags::TAG_MODEL_YEAR . "_thumb_{$id}_{$width}_{$height}_{$mode}";
        $data = Yii::app()->cache->get($key);
        if ($data === false) {
            $data = '';
            $model = self::model()->findByPk($id);
            if (!empty($model)) {
                $data = $model->getThumb($width, $height, $mode);
            }

            Yii::app()->cache->set($data, $key, 0, new Tags(Tags::TAG_MODEL_YEAR . '_thumb_' . $id));
        }

        return $data;
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.year', $this->year, true);
        $criteria->compare('t.model_id', $this->model_id);
        $criteria->compare('t.is_deleted', $this->is_deleted);
        $criteria->compare('t.is_active', $this->is_active);
        $criteria->compare('t.chassis_id', $this->chassis_id);
        $criteria->compare('t.is_tires', $this->is_tires);
        $criteria->compare('t.platform_model_id', $this->platform_model_id);

        $criteria->with = array(
            'Model' => array('together' => true),
            'PlatformModel' => array('together' => true),
            'PlatformModel.Platform' => array('together' => true),
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
            ),
        ));
    }

    public function searchEmptyTires()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.year', $this->year, true);
        $criteria->compare('t.model_id', $this->model_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.is_active', $this->is_active);
        $criteria->compare('t.chassis_id', $this->chassis_id);
        $criteria->compare('t.is_tires', $this->is_tires);
        $criteria->compare('t.platform_model_id', $this->platform_model_id);
        $criteria->addCondition("NOT EXISTS(SELECT * FROM auto_model_year_tire AS vs WHERE t.id = vs.model_year_id)");
        //$criteria->addCondition('vs.model_year_id IS NULL');			
        //$criteria->join = "LEFT JOIN auto_model_year_tire AS vs ON t.id=vs.model_year_id";					

        $criteria->with = array(
            'Model' => array('together' => true),
            'PlatformModel' => array('together' => true),
            'PlatformModel.Platform' => array('together' => true),
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
            ),
        ));
    }

    public function searchEmptyCompetitors()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.year', $this->year, true);
        $criteria->compare('t.model_id', $this->model_id);
        $criteria->compare('t.is_deleted', $this->is_deleted);
        $criteria->compare('t.is_active', $this->is_active);
        $criteria->addNotInCondition('t.id', self::getIdsIsCompetitors());

        $criteria->with = array('Model' => array('together' => true));

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
            ),
        ));
    }

    public function searchEmptyPhotos()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.year', $this->year, true);
        $criteria->compare('t.model_id', $this->model_id);
        $criteria->compare('t.is_deleted', $this->is_deleted);
        $criteria->compare('t.is_tires', $this->is_tires);
        $criteria->compare('t.is_active', $this->is_active);
        $criteria->addNotInCondition('t.id', self::getIdsIsPhotos());

        $criteria->with = array('Model' => array('together' => true), 'Chassis' => array('together' => true));

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
            ),
        ));
    }

    public function getPhotos()
    {
        $key = self::CACHE_KEY_PHOTOS . $this->id;
        $cache = Yii::app()->cache->get($key);
        if ($cache == false && !is_array($cache)) {
            $cache = $this->galleryPhotos;
            Yii::app()->cache->set($key, $cache, 60 * 60 * 24);
        }

        return $cache;
    }

    public static function getAllByModel($model_id, $onlyNotDeleted = 0)
    {
        $model_id = (int) $model_id;
        $key = Tags::TAG_MODEL_YEAR . 'getAllByModel_' . $model_id . '_' . $onlyNotDeleted;
        $data = Yii::app()->cache->get($key);
        if ($data === false) {
            $model_id = (int) $model_id;

            $criteria = new CDbCriteria;
            $criteria->compare('model_id', $model_id);
            if ($onlyNotDeleted) {
                $criteria->compare('is_deleted', 0);
            }
            $criteria->order = 'year DESC';

            $data = CHtml::listData(self::model()->findAll($criteria), 'id', 'year');
            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR));
        }

        return $data;
    }

    public static function getAll()
    {
        $key = 'MODEL_YEAR_ALL';
        $data = Yii::app()->cache->get($key);
        if (empty($data)) {
            $data = array();
            $criteria = new CDbCriteria;
            $criteria->with = array('Model');
            $items = self::model()->findAll($criteria);
            foreach ($items as $item) {
                $data[$item->id] = $item->Model->title . ' ' . $item->year;
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR));
        }

        return $data;
    }

    public static function getAllByYear($year)
    {
        $year = (int) $year;

        $years[] = $year - 1;
        $years[] = $year + 1;
        $years[] = $year;

        $data = array();
        $criteria = new CDbCriteria;
        $criteria->addInCondition('t.year', $years);
        $criteria->order = 'Make.title, Model.title, t.year DESC';
        $criteria->with = array('Model', 'Model.Make');
        $items = self::model()->findAll($criteria);
        foreach ($items as $item) {
            $data[$item->id] = $item->Model->Make->title . ' ' . $item->Model->title . ' ' . $item->year;
        }

        return $data;
    }

    public function getCompetitors()
    {
        $criteria = new CDbCriteria;
        $criteria->condition = "model_year_id = $this->id OR competitor_id = $this->id";
        $items = AutoModelYearCompetitor::model()->findAll($criteria);
        $data = array();
        foreach ($items as $item) {
            $data[] = ($item->model_year_id == $this->id) ? $item->competitor_id : $item->model_year_id;
        }

        return $data;
    }

    public function getDataCompetitors()
    {
        if (Yii::app()->request->isPostRequest) {
            return $this->post_competitors;
        } else {
            if ($this->isNewRecord) {
                return array();
            } else {
                return $this->getCompetitors();
            }
        }
    }

    public function getTitle()
    {
        return $this->Model->Make->title . " " . $this->Model->title;
    }

    private function _clearCache()
    {
        Yii::app()->cache->clear(Tags::TAG_MODEL_YEAR);
        Yii::app()->cache->clear(Tags::TAG_MODEL_YEAR . '_thumb_' . $this->id);
    }

    public static function getYearByMakeAndModelAndAlias($make_id, $model_id, $year)
    {
        $key = Tags::TAG_MODEL_YEAR . '_ITEM_' . $make_id . '_' . $model_id . '_' . $year;
        $modelYear = Yii::app()->cache->get($key);
        if ($modelYear == false) {
            $modelYear = array();

            $criteria = new CDbCriteria();
            $criteria->compare('t.is_active', 1);
            $criteria->compare('t.is_deleted', 0);
            $criteria->compare('t.model_id', $model_id);
            $criteria->compare('t.year', $year);

            $item = AutoModelYear::model()->find($criteria);

            if (!empty($item)) {
                $modelYear = array(
                    'id' => $item->id,
                    'year' => $item->year,
                    'description' => $item->description,
                    'photo' => $item->getThumb(270, null, 'resize'),
                );
            }

            Yii::app()->cache->set($key, $modelYear, 0, new Tags(Tags::TAG_MODEL_YEAR));
        }

        return $modelYear;
    }

    public static function getModelsByMakeAndYear($make_id, $year)
    {
        $key = Tags::TAG_MODEL_YEAR . '__LIST_BY_MAKE_YEAR___' . $make_id . '_' . $year;
        $data = Yii::app()->cache->get($key);
        if ($data == false) {
            $data = array();

            $criteria = new CDbCriteria();
            $criteria->compare('t.is_active', 1);
            $criteria->compare('t.is_deleted', 0);
            $criteria->compare('t.year', $year);
            $criteria->compare('Model.is_active', 1);
            $criteria->compare('Model.is_deleted', 0);
            $criteria->compare('Model.make_id', $make_id);
            $criteria->with = array('Model');

            $items = AutoModelYear::model()->findAll($criteria);

            foreach ($items as $item) {

                $price = self::getMinMaxSpecs('msrp', $item->id);
                //$lastCompletion = self::getLastCompletion($item->id);

                $row = array(
                    'id' => $item->id,
                    'year' => $item->year,
                    'model' => $item->Model->title,
                    'model_alias' => $item->Model->alias,
                    'photo' => $item->getThumb(150, null, 'resize'),
                    'price' => array(
                        'min' => $price['mmin'],
                        'max' => $price['mmax'],
                    ),
                    /*
                      'completion' => array(
                      'engine' => AutoSpecsOption::getV('engine', $lastCompletion['specs_engine']),
                      'fuel_economy_city' => AutoSpecsOption::getV('fuel_economy__city', $lastCompletion['specs_fuel_economy__city']),
                      'fuel_economy_highway' => AutoSpecsOption::getV('fuel_economy__highway', $lastCompletion['specs_fuel_economy__highway']),
                      'standard_seating' => AutoSpecsOption::getV('standard_seating', $lastCompletion['specs_standard_seating']),
                      ),
                     */
                );

                $data[$item['id']] = $row;
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getOtherMakeYear($models, $model_year_id)
    {
        $model_year_id = (int) $model_year_id;
        $indexes = array();
        foreach ($models as $item) {
            $indexes[] = $item['id'];
        }
        $size = sizeof($indexes);

        $data = array();
        $dataIds = array();

        //d($indexes, 0);

        foreach ($indexes as $key => $id) {
            if ($model_year_id == $id) {

                if (isset($indexes[$key - 1])) {
                    $data[$indexes[$key - 1]] = $models[$indexes[$key - 1]];
                    $dataIds[$indexes[$key - 1]] = $indexes[$key - 1];
                } else if (isset($indexes[$size - 1])) {
                    $data[$indexes[$size - 1]] = $models[$indexes[$size - 1]];
                    $dataIds[$indexes[$size - 1]] = $indexes[$size - 1];
                }

                if (isset($indexes[$key - 2])) {
                    $data[$indexes[$key - 2]] = $models[$indexes[$key - 2]];
                    $dataIds[$indexes[$key - 2]] = $indexes[$key - 2];
                } else if (isset($indexes[$size - 2])) {
                    $data[$indexes[$size - 2]] = $models[$indexes[$size - 2]];
                    $dataIds[$indexes[$size - 2]] = $indexes[$size - 2];
                }

                if (isset($indexes[$key + 1])) {
                    $data[$indexes[$key + 1]] = $models[$indexes[$key + 1]];
                    $dataIds[$indexes[$key + 1]] = $indexes[$key + 1];
                } else if (isset($indexes[$key - 1])) {
                    $data[$indexes[$key - 1]] = $models[$indexes[$key - 1]];
                    $dataIds[$indexes[$key - 1]] = $indexes[$key - 1];
                }

                if (isset($indexes[$key + 2])) {
                    $data[$indexes[$key + 2]] = $models[$indexes[$key + 2]];
                    $dataIds[$indexes[$key + 2]] = $indexes[$key + 2];
                } else if (isset($indexes[$key - 2])) {
                    $data[$indexes[$key - 2]] = $models[$indexes[$key - 2]];
                    $dataIds[$indexes[$key - 2]] = $indexes[$key - 2];
                }

                if (isset($indexes[$key + 3])) {
                    $data[$indexes[$key + 3]] = $models[$indexes[$key + 3]];
                    $dataIds[$indexes[$key + 3]] = $indexes[$key + 3];
                } else if (isset($indexes[$key - 3])) {
                    $data[$indexes[$key - 3]] = $models[$indexes[$key - 3]];
                    $dataIds[$indexes[$key - 3]] = $indexes[$key - 3];
                }

                if (sizeof($dataIds) < 5) {
                    if (isset($indexes[$key - 4])) {
                        $data[$indexes[$key - 4]] = $models[$indexes[$key - 4]];
                        $dataIds[$indexes[$key - 4]] = $indexes[$key - 4];
                    }
                }
            }
        }

        if (sizeof($data) == 5) {
            array_pop($data);
        }

        return $data;
    }

    public static function getMinMaxSpecs($specs, $model_year_id)
    {
        $model_year_id = (int) $model_year_id;

        $key = Tags::TAG_MODEL_YEAR . '_getMinMaxSpecs_' . $specs . '_' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data === false) {
            $sql = "SELECT 
						MAX(c.specs_{$specs}) AS mmax,  
						MIN(c.specs_{$specs}) AS mmin 
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						c.model_year_id = {$model_year_id}
					";

            $data = Yii::app()->db->createCommand($sql)->queryRow();
            $data['mmax'] = (float) $data['mmax'];
            $data['mmin'] = (float) $data['mmin'];

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getMinSpecs($specs, $model_year_id)
    {
        $model_year_id = (int) $model_year_id;

        $key = Tags::TAG_COMPLETION . '_SPECS_MIN_' . $specs . '_' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $sql = "SELECT 
						MIN(c.specs_{$specs}) AS mmin 
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						c.model_year_id = {$model_year_id}
					";

            $data = Yii::app()->db->createCommand($sql)->queryRow();
            $data = (float) $data['mmin'];

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getMaxSpecs($specs, $model_year_id)
    {
        $model_year_id = (int) $model_year_id;

        $key = Tags::TAG_COMPLETION . '_SPECS_MAX_' . $specs . '_' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $sql = "SELECT 
						MAX(c.specs_{$specs}) AS mmax
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						c.model_year_id = {$model_year_id}
					";

            $data = Yii::app()->db->createCommand($sql)->queryRow();
            $data = (float) $data['mmax'];

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getLastCompletion($model_year_id)
    {
        $model_year_id = (int) $model_year_id;

        $key = Tags::TAG_COMPLETION . 'YEAR_LAST_' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $sql = "SELECT 
						c.*,
						y.year AS year
					FROM auto_completion AS c
					LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
					WHERE 
						c.is_active = 1 AND 
						c.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						c.model_year_id = {$model_year_id}
					ORDER BY year DESC
					";

            $data = Yii::app()->db->createCommand($sql)->queryRow();
            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getFrontCompetitors($model_year_id)
    {
        $model_year_id = (int) $model_year_id;

        $key = Tags::TAG_MODEL_YEAR . '_COMPETITORS__' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data == false && !is_array($data)) {
            $data = array();
            $sql = "SELECT 
						*
					FROM auto_model_year_competitor AS c
					WHERE 
						c.model_year_id	 = {$model_year_id} OR 
						c.competitor_id = {$model_year_id}
					";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $ids = array();
            foreach ($rows as $row) {
                $ids[] = (int) (($model_year_id == $row['model_year_id']) ? $row['competitor_id'] : $row['model_year_id']);
            }

            if (!empty($ids)) {
                $criteria = new CDbCriteria();
                $criteria->compare('t.is_active', 1);
                $criteria->compare('t.is_deleted', 0);
                $criteria->addInCondition('t.id', $ids);
                $criteria->compare('Model.is_active', 1);
                $criteria->compare('Model.is_deleted', 0);
                $criteria->compare('Make.is_active', 1);
                $criteria->compare('Make.is_deleted', 0);
                $criteria->with = array('Model', 'Model.Make');
                $criteria->order = 't.id';

                $itemIds = array($model_year_id);
                $items = AutoModelYear::model()->findAll($criteria);
                foreach ($items as $item) {
                    $itemIds[] = (int) $item->id;
                }

                //$dataIds = ArrayHelper::getArrayСircleNeighbor($itemIds, $model_year_id);

                foreach ($items as $item) {

                    /*
                      if (!in_array($item->id, $dataIds)) {
                      continue;
                      }
                     */

                    $price = self::getMinMaxSpecs('msrp', $item->id);
                    $lastCompletion = self::getLastCompletion($item->id);

                    $row = array(
                        'id' => $item->id,
                        'photo' => $item->getThumb(150, null, 'resize'),
                        'year' => $item->year,
                        'model' => $item->Model->title,
                        'model_alias' => $item->Model->alias,
                        'make' => $item->Model->Make->title,
                        'make_alias' => $item->Model->Make->alias,
                        'price' => array(
                            'min' => $price['mmin'],
                            'max' => $price['mmax'],
                        ),
                        'completion' => array(
                            'engine' => AutoSpecsOption::getV('engine', $lastCompletion['specs_engine']),
                            'fuel_economy_city' => AutoSpecsOption::getV('fuel_economy__city', $lastCompletion['specs_fuel_economy__city']),
                            'fuel_economy_highway' => AutoSpecsOption::getV('fuel_economy__highway', $lastCompletion['specs_fuel_economy__highway']),
                            'standard_seating' => AutoSpecsOption::getV('standard_seating', $lastCompletion['specs_standard_seating']),
                        ),
                    );

                    $data[] = $row;
                }
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getCarSpecsAndDimensions($model_year_id)
    {
        $model_year_id = (int) $model_year_id;

        $key = Tags::TAG_MODEL_YEAR . '___CAR_SPECS_AND_DIMENSIONS__' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $lastCompletion = self::getLastCompletion($model_year_id);
            $data['0_60_times'] = self::getMinMaxSpecs('0_60mph__0_100kmh_s_', $model_year_id);
            $data['engine'] = AutoSpecsOption::getV('engine', $lastCompletion['specs_engine']);
            $data['horsepower'] = AutoSpecsOption::getV('engine', $lastCompletion['specs_horsepower']);
            $data['wheelbase'] = self::getMinMaxSpecs('wheelbase', $model_year_id);
            $data['towing_capacity'] = self::getMinMaxSpecs('maximum_trailer_weight', $model_year_id);
            $data['length'] = self::getMinMaxSpecs('exterior_length', $model_year_id);
            $data['clearance'] = self::getMinMaxSpecs('ground_clearance', $model_year_id);
            $data['cargo_space'] = self::getMinMaxSpecs('luggage_volume', $model_year_id);
            $data['curb_weight'] = self::getMinMaxSpecs('curb_weight', $model_year_id);

            $hps = AutoModelYear::getHps($model_year_id);
            if (!empty($hps)) {
                $data['hp']['mmin'] = min($hps);
                $data['hp']['mmax'] = max($hps);
            }

            $v1 = AutoSpecsOption::getV('fuel_economy__city', $lastCompletion['specs_fuel_economy__city']);
            $v2 = AutoSpecsOption::getV('fuel_economy__highway', $lastCompletion['specs_fuel_economy__highway']);
            if (!empty($v1) && !empty($v2)) {
                $data['gas_mileage'] = $v1 . '/' . $v2;
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getRangeHp($model_year_id)
    {
        $hps = AutoModelYear::getHps($model_year_id);
        $data = array();
        if (!empty($hps)) {
            $data['min'] = min($hps);
            $data['max'] = max($hps);
        }
        return $data;
    }

    public static function getIdsIsCompetitors()
    {
        $key = Tags::TAG_MODEL_YEAR . '_IDS_IS_COMPETITORS_';
        $data = Yii::app()->cache->get($key);
        if ($data == false) {
            $data = array();

            $sql = "SELECT DISTINCT model_year_id AS value FROM auto_model_year_competitor";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $data[$row['value']] = $row['value'];
            }

            $sql = "SELECT DISTINCT competitor_id AS value FROM auto_model_year_competitor";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $data[$row['value']] = $row['value'];
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR));
        }

        return $data;
    }

    public static function getIdsIsPhotos()
    {
        $key = Tags::TAG_MODEL_YEAR_PHOTO . '_IDS_IS_PHOTOS_';
        $data = Yii::app()->cache->get($key);
        if ($data == false) {
            $data = array();

            $sql = "SELECT DISTINCT year_id AS value FROM auto_model_year_photo";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $data[$row['value']] = $row['value'];
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR_PHOTO));
        }

        return $data;
    }

    public static function getMostVisited($limit)
    {
        $key = Tags::TAG_MODEL_YEAR . '_MOST_VISITED_' . $limit;
        $data = Yii::app()->cache->get($key);
        if ($data == false) {
            $data = array();

            $criteria = new CDbCriteria();
            $criteria->compare('t.is_active', 1);
            $criteria->compare('t.is_deleted', 0);
            $criteria->compare('Model.is_active', 1);
            $criteria->compare('Model.is_deleted', 0);
            $criteria->compare('Make.is_active', 1);
            $criteria->compare('Make.is_deleted', 0);
            $criteria->order = 't.view_count DESC';
            $criteria->limit = $limit;
            $criteria->with = array('Model', 'Model.Make');

            $items = AutoModelYear::model()->findAll($criteria);

            foreach ($items as $item) {

                $row = array(
                    'id' => $item->id,
                    'year' => $item->year,
                    'model' => $item->Model->title,
                    'model_alias' => $item->Model->alias,
                    'make' => $item->Model->Make->title,
                    'make_alias' => $item->Model->Make->alias,
                );

                $data[] = $row;
            }

            Yii::app()->cache->set($key, $data, 60);
        }

        return $data;
    }

    public static function getYears()
    {
        $key = Tags::TAG_MODEL_YEAR . '__LIST_YEARS__';
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $sql = "SELECT 
						y.year AS year
					FROM auto_model_year AS y
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						k.is_active = 1 AND 
						k.is_deleted = 0 AND
						m.is_active = 1 AND 
						m.is_deleted = 0 AND
						y.is_active = 1 AND
						y.is_deleted = 0
					GROUP BY year
					ORDER BY year DESC
					";

            $items = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($items as $item) {
                $data[$item['year']] = $item['year'];
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR));
        }

        return $data;
    }

    public function getPost_tires($is_post = true)
    {
        if (Yii::app()->request->isPostRequest && $is_post) {
            return $this->post_tires;
        } else {
            if ($this->isNewRecord) {
                return array();
            } else {

                $criteria = new CDbCriteria;
                $criteria->compare('model_year_id', $this->id);
                $criteria->order = 'tire_id';
                $items = AutoModelYearTire::model()->findAll($criteria);
                $ids = array();
                foreach ($items as $item) {
                    $ids[] = $item->tire_id;
                }

                return $ids;
            }
        }
    }

    public static function getListYears($model_id)
    {
        $key = Tags::TAG_MODEL_YEAR . '__getListYears___' . $model_id;
        $data = Yii::app()->cache->get($key);

        if ($data === false) {
            $data = array();
            $criteria = new CDbCriteria;
            $criteria->compare('model_id', $model_id);
            $criteria->order = 'year DESC';
            $items = AutoModelYear::model()->findAll($criteria);
            foreach ($items as $item) {
                $data[] = array(
                    'id' => $item->id,
                    'year' => $item->year,
                    'tires' => $item->getPost_tires(false),
                );
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR, Tags::TAG_TIRE));
        }

        return $data;
    }

    public function getPost_tires_related()
    {
        if (Yii::app()->request->isPostRequest) {
            return $this->post_tires_related;
        } else {
            return array();
        }
    }

    public function getPost_rims_related()
    {
        if (Yii::app()->request->isPostRequest) {
            return $this->post_rims_related;
        } else {
            return array();
        }
    }

    public function getRimModels()
    {
        $data = array();
        $criteria = new CDbCriteria;
        $criteria->compare('model_id', $this->model_id);
        $criteria->order = 'year DESC';
        $items = AutoModelYear::model()->findAll($criteria);
        foreach ($items as $item) {
            $data[$item->id] = array(
                'year' => $item->year,
                'tire_rim_diameter_from_id' => $item->tire_rim_diameter_from_id,
                'rim_width_from_id' => $item->rim_width_from_id,
                'tire_rim_diameter_to_id' => $item->tire_rim_diameter_to_id,
                'rim_width_to_id' => $item->rim_width_to_id,
                'offset_range_from_id' => $item->offset_range_from_id,
                'offset_range_to_id' => $item->offset_range_to_id,
                'bolt_pattern_id' => $item->bolt_pattern_id,
                'thread_size_id' => $item->thread_size_id,
                'center_bore_id' => $item->center_bore_id,
            );
        }

        return $data;
    }

    public static function getTires($model_year_id)
    {
        $model_year_id = (int) $model_year_id;
        $key = Tags::TAG_MODEL_YEAR . 'getTires' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $data = array();

            $tireIds = array();
            $sql = "SELECT tire_id FROM auto_model_year_tire WHERE model_year_id = {$model_year_id}";
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($items as $item) {
                $tireIds[] = $item['tire_id'];
            }

            if (!empty($tireIds)) {

                $sql = "SELECT 
							t.id AS id, 
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
						WHERE t.id IN (" . implode(',', $tireIds) . ")
						ORDER BY rd.value, sw.value, ar.value, t.is_rear
						";
                $items = Yii::app()->db->createCommand($sql)->queryAll();

                foreach ($items as $item) {
                    $data[] = array(
                        'id' => $item['id'],
                        'vehicle_class' => $item['vehicle_class'],
                        'rim_diameter' => $item['rim_diameter'],
                        'section_width' => $item['section_width'],
                        'aspect_ratio' => $item['aspect_ratio'],
                        'is_rear' => $item['is_rear'],
                        'rear_rim_diameter' => $item['rear_rim_diameter'],
                        'rear_section_width' => $item['rear_section_width'],
                        'rear_aspect_ratio' => $item['rear_aspect_ratio'],
                    );
                }
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR, Tags::TAG_TIRE));
        }

        return $data;
    }

    public static function getHps($model_year_id)
    {
        $model_year_id = (int) $model_year_id;
        $key = Tags::TAG_MODEL_YEAR . '_getHps__' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $data = array();

            $sql = "SELECT 
						DISTINCT CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) AS hp
					FROM `auto_completion` AS c 
					WHERE c.is_active=1 AND c.is_deleted=0 AND c.model_year_id={$model_year_id}
					ORDER BY CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) ASC";

            $items = Yii::app()->db->createCommand($sql)->queryAll();

            foreach ($items as $item) {
                $data[] = $item['hp'];
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getFrontCompetitorsHp($model_year_id)
    {
        $model_year_id = (int) $model_year_id;

        $key = Tags::TAG_MODEL_YEAR . '_getFrontCompetitorsHp_' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data == false && !is_array($data)) {
            $data = array();

            $sql = "SELECT 
						c.id AS id, 
						c.model_year_id AS model_year_id, 
						c.title AS title, 
						c.image_path AS image, 
						c.specs_horsepower AS horsepower,
						c.specs_0_60mph__0_100kmh_s_ AS 0_60_mph, 
						c.specs_1_4_mile_time AS mile_time, 
						c.specs_1_4_mile_speed AS mile_speed, 
						c.specs_msrp AS msrp, 
						c.specs_curb_weight AS curb_weight, 
						c.specs_fuel_economy__city AS fuel_economy_city, 
						c.specs_fuel_economy__highway AS fuel_economy_highway, 
						SUBSTRING_INDEX(c.specs_torque, '@', 1) AS torque,
						SUBSTRING_INDEX(c.specs_horsepower, '@', 1) AS hp
					FROM `auto_completion` AS c 
					WHERE 
						c.is_active=1 AND 
						c.is_deleted=0 AND 
						c.model_year_id	= {$model_year_id}
					ORDER BY CONVERT(SUBSTRING_INDEX(c.specs_horsepower, '@', 1), SIGNED INTEGER) ASC
					";

            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $data[$row['id']] = $row;
                $data[$row['id']]['image'] = AutoCompletion::getThumb(AutoCompletion::PHOTO_DIR . $row['image'], 150, 80, 'resize');
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getItemsByHp($hp, $limit, $offset)
    {
        $hp = (int) $hp;
        $limit = (int) $limit;
        $offset = (int) $offset;

        $key = Tags::TAG_MODEL_YEAR . '_getItemsByHp_' . $hp . '_' . $limit . '_' . $offset;
        $data = Yii::app()->cache->get($key);

        if ($data === false) {
            $data = array();

            $engines = AutoSpecsOption::getAllBySpecs(120);

            $sql = "SELECT 
						c.id AS id, 
						c.model_year_id AS model_year_id, 
						c.title AS title, 
						c.image_path AS image, 
						c.specs_horsepower AS horsepower,
						c.specs_0_60mph__0_100kmh_s_ AS 0_60_mph, 
						c.specs_1_4_mile_time AS mile_time, 
						c.specs_1_4_mile_speed AS mile_speed, 
						c.specs_msrp AS msrp, 
						c.specs_curb_weight AS curb_weight, 
						c.specs_fuel_economy__city AS fuel_economy_city, 
						c.specs_fuel_economy__highway AS fuel_economy_highway, 
						c.specs_engine AS engine, 
						SUBSTRING_INDEX(c.specs_torque, '@', 1) AS torque,
						SUBSTRING_INDEX(c.specs_horsepower, '@', 1) AS hp,
						c.title AS completion_title, 
						y.id AS model_year_id,
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
						SUBSTRING_INDEX(c.specs_horsepower, '@', 1) = {$hp}
					ORDER BY y.year DESC
					LIMIT {$offset}, {$limit}
					";

            $modelYearIds = array();
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $data[$row['id']] = $row;
                if (isset($engines[$row['engine']]))
                    $data[$row['id']]['engine'] = $engines[$row['engine']];

                $data[$row['id']]['image'] = AutoCompletion::getThumb(AutoCompletion::PHOTO_DIR . $row['image'], 150, 80, 'resize');
                $modelYearIds[] = $row['model_year_id'];
            }

            if (!empty($modelYearIds)) {
                $criteria = new CDbCriteria;
                $criteria->addInCondition('id', $modelYearIds);
                $criteria->index = 'id';
                $modelYears = self::model()->findAll($criteria);
                foreach ($rows as $row) {
                    if (empty($data[$row['id']]['image'])) {
                        $data[$row['id']]['image'] = $modelYears[$data[$row['id']]['model_year_id']]->getThumb(150, 80, 'resize');
                    }
                }
            }
            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
        }

        return $data;
    }

    public static function getCountItemsByHp($hp)
    {
        $hp = (int) $hp;

        $key = Tags::TAG_MODEL_YEAR . '_getCountItemsByHp_' . $hp;
        $count = Yii::app()->cache->get($key);

        if ($count === false) {

            $sql = "SELECT COUNT(*) AS cc FROM `auto_completion` AS c 
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
						SUBSTRING_INDEX(c.specs_horsepower, '@', 1) = {$hp}
					";

            $count = Yii::app()->db->createCommand($sql)->queryScalar();

            Yii::app()->cache->set($key, $count, 0, new Tags(Tags::TAG_MAKE, Tags::TAG_MODEL, Tags::TAG_MODEL_YEAR, Tags::TAG_COMPLETION));
        }

        return $count;
    }

    public function getPlatformTitle()
    {
        if (isset($this->PlatformModel) && isset($this->PlatformModel->Platform)) {
            return "{$this->PlatformModel->year_from}–{$this->PlatformModel->year_to} {$this->PlatformModel->Platform->title}";
        }
    }

    private static function getTireIdsByModelYears($model_year_ids)
    {
        $key = Tags::TAG_MODEL_YEAR . '_getTireIdsByModelYears_' . '_' . implode('_', $model_year_ids);
        $data = Yii::app()->cache->get($key);

        if ($data === false) {
            $data = array();
            $sql = "SELECT 
							DISTINCT tire_id AS tire_id
							FROM auto_model_year_tire
							WHERE model_year_id IN (" . implode(',', $model_year_ids) . ")";

            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $data[] = $row['tire_id'];
            }

            Yii::app()->cache->get($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR));
        }

        return $data;
    }

    public static function getTireRangeByModelYears($model_year_ids, $dir)
    {
        $key = Tags::TAG_MODEL_YEAR . '__getTireRangeByModelYears_' . $dir . '_' . implode('_', $model_year_ids);
        $data = Yii::app()->cache->get($key);
        $ids = self::getTireIdsByModelYears($model_year_ids);

        if ($data === false) {
            $data = '';

            if (!empty($ids)) {
                $sql = "SELECT 
									vc.code AS vehicle_class, 
									rd.value AS rim_diameter, 
									sw.value AS section_width, 
									ar.value AS aspect_ratio
								FROM tire AS t
								LEFT JOIN tire_vehicle_class AS vc ON t.vehicle_class_id = vc.id
								LEFT JOIN tire_rim_diameter AS rd ON t.rim_diameter_id = rd.id
								LEFT JOIN tire_section_width AS sw ON t.section_width_id = sw.id
								LEFT JOIN tire_aspect_ratio AS ar ON t.aspect_ratio_id = ar.id
								WHERE t.id IN (" . implode(',', $ids) . ")
								ORDER BY rim_diameter {$dir}, section_width {$dir}, aspect_ratio {$dir}";

                $row = Yii::app()->db->createCommand($sql)->queryRow();
                if (!empty($row)) {
                    if (!empty($row['section_width']) && !empty($row['aspect_ratio']) && !empty($row['rim_diameter']))
                        $data = Tire::format($row, false);
                }
            }

            Yii::app()->cache->get($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR, Tags::TAG_TIRE));
        }

        return $data;
    }

    public static function getRimRange($model_year_id)
    {
        $model_year_id = (int) $model_year_id;
        $key = Tags::TAG_MODEL_YEAR . '_getRimRange_' . $model_year_id;
        $data = Yii::app()->cache->get($key);

        if ($data === false) {
            $data = array();

            $sql = "SELECT
							tire_rim_diameter_from_id,
							rim_width_from_id,
							tire_rim_diameter_to_id,
							rim_width_to_id
						FROM auto_model_year
						WHERE id = {$model_year_id}";

            $row = Yii::app()->db->createCommand($sql)->queryRow();
            if (!empty($row)) {
                $diameters = TireRimDiameter::getList();
                $widths = RimWidth::getAll();

                $data['diameter_from'] = isset($diameters[$row['tire_rim_diameter_from_id']]) ? $diameters[$row['tire_rim_diameter_from_id']] : '';
                $data['width_from'] = isset($widths[$row['rim_width_from_id']]) ? $widths[$row['rim_width_from_id']] : '';
                $data['diameter_to'] = isset($diameters[$row['tire_rim_diameter_to_id']]) ? $diameters[$row['tire_rim_diameter_to_id']] : '';
                $data['width_to'] = isset($widths[$row['rim_width_to_id']]) ? $widths[$row['rim_width_to_id']] : '';
            }

            Yii::app()->cache->get($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR));
        }

        return $data;
    }
}
