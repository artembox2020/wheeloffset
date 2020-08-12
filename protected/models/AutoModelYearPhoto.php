<?php

/**
 * This is the model class for table "model_year_photo".
 *
 * The followings are the available columns in table 'model_year_photo':
 * @property integer $id
 * @property integer $year_id
 * @property integer $rank
 * @property string $name
 * @property string $description
 * @property string $file
 *
 * The followings are the available model relations:
 * @property News $institution
 *
 */
class AutoModelYearPhoto extends CActiveRecord
{
    public $file;
    public $file_url;
	
	public $image_ext = 'jpg';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return GalleryPhoto the static model class
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
        return 'auto_model_year_photo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('year_id', 'required'),
            array('name', 'length', 'max' => 512),
            array('file', 'length', 'max' => 128),
			array(
				'file', 
				'file', 
				'types'=>'jpg,png,gif,jpeg',
				'allowEmpty'=>true
			),			
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, year_id, rank, name, description, file', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'ModelYear' => array(self::BELONGS_TO, 'AutoModelYear', 'year_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'year_id' => 'Model Year',
            'rank' => 'Rank',
            'name' => 'Name',
            'description' => 'Description',
            'file' => 'File Name',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('year_id', $this->year_id);
        $criteria->compare('rank', $this->rank);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
       
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
	
	public function beforeSave()
	{
        if ($this->rank == null) {
			$maxRankNumber = Yii::app()->db->createCommand()
			  ->select('max(rank) as rank')
			  ->from('auto_model_year_photo')
			  ->where("year_id={$this->year_id}")
			  ->queryScalar();
			$this->rank = $maxRankNumber + 1;
        }

		return parent::beforeSave();
	}
	
	public function afterSave()
	{
		if (!empty($this->file)) {
			$this->file_name = "{$this->ModelYear->year}-{$this->ModelYear->Model->Make->alias}-{$this->ModelYear->Model->alias}-{$this->id}.jpg";
			$this->file->saveAs($this->getImage_directory(true) . $this->file_name);
			$this->updateByPk($this->id, array('file_name'=>$this->file_name));
		}
		
		if (!empty($this->file_url)) {
			$this->file_name = "{$this->ModelYear->year}-{$this->ModelYear->Model->Make->alias}-{$this->ModelYear->Model->alias}-{$this->id}.jpg";
			$imageContent = @file_get_contents($this->file_url);
			if (!empty($imageContent)) {
				file_put_contents($this->getImage_directory(true) . $this->file_name, $imageContent);
				$this->updateByPk($this->id, array('file_name'=>$this->file_name));
			}
		}
		
		$this->_clearCache();
		
		return parent::afterSave();
	}	
	
	private function _clearCache()
	{
		Yii::app()->cache->delete(Tags::TAG_MODEL_YEAR_PHOTO . '_MODEL_YEAR_' . $this->year_id);
		Yii::app()->cache->clear(Tags::TAG_MODEL_YEAR_PHOTO);
	}
	
	public function beforeDelete()
	{
		$this->_deleteImage();
		
		$this->_clearCache();
		
		return parent::beforeDelete();
	}
	
	private function _deleteImage()
    {
        if ($this->image_ext) {
			$files = $this->bfglob(Yii::getPathOfAlias('webroot') . '/photos/model_year/', "*{$this->file_name}", 0, 10);			
			foreach ($files as $file) {
				@unlink($file);
			}
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
 
    public function getImage_directory($mkdir=false) {
        if (!$mkdir) {
			return Yii::app()->basePath . '/../photos/model_year/' . $this->year_id . '/';
		
		} else {
			$directory =  Yii::app()->basePath . '/../photos/model_year/' . $this->year_id . '/';
			if (file_exists($directory) == false) {
				mkdir($directory);
				chmod($directory, 0777);
			}		

			return $directory;
		}
    }

    public function getPreview()
    {
        return $this->getThumb(135, 100, 'resize');
    }	
	
	public function getThumb($width=null, $height=null, $mode='origin')
	{
		$dir = $this->getImage_directory();
		$originFile = $dir . $this->file_name;
		
		if (!is_file($originFile)) {
			return "http://www.placehold.it/{$width}x{$height}/EFEFEF/AAAAAA";
		}
		
		if ($mode == 'origin') {
			return '/photos/model_year/' . $this->year_id . '/' . $this->file_name;
		}
		
		$subdir = $mode . $width .'x'.$height;
		$subdirPath = $dir . $subdir;
		$subdirPathFile =$subdirPath . '/' . $this->file_name;
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
		
		return '/photos/model_year/' . $this->year_id . '/'.$subdir.'/'. $this->file_name;
	}	
	
	public static function getYearPhotos($model_year_id)
	{
		$model_year_id = (int) $model_year_id;
		$key = Tags::TAG_MODEL_YEAR_PHOTO . '__MODEL_YEAR___' . $model_year_id;
		$data = Yii::app()->cache->get($key);
		
		if ($data == false && !is_array($data)) {	
			$data = [];
			$criteria=new CDbCriteria;
			$criteria->compare('year_id', $model_year_id);			
			$criteria->order = 'rank';			
			$items = self::model()->findAll($criteria);
			foreach ($items as $item) {
				$data[] = array(
					'name' => $item->name,
					'description' => $item->description,
					'small' => $item->getThumb(150, null, 'resize'),
					'large' => $item->getThumb(500, null, 'resize'),
				);
			}
			
			Yii::app()->cache->set($key, $data, 60*60*24*30);
		}
		
		return $data;
	}
	

	

}