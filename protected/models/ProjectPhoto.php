<?php

/**
 * This is the model class for table "project_photo".
 *
 * The followings are the available columns in table 'project_photo':
 * @property integer $id
 * @property integer $project_id
 * @property integer $rank
 * @property string $name
 * @property string $description
 * @property string $file
 *
 */
class ProjectPhoto extends CActiveRecord
{
    public $file;
    public $filePath;
	
	public $watemark = false;

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
        return 'project_photo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('project_id', 'required'),
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
            array('id, project_id, rank, name, description, file', 'safe', 'on' => 'search'),
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
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'project_id' => 'Project',
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
        $criteria->compare('project_id', $this->project_id);
        $criteria->compare('rank', $this->rank);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
       
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
	
	public function beforeSave()
	{
		if ($this->isNewRecord) {
			$maxRankNumber = Yii::app()->db->createCommand()
			  ->select('max(rank) as rank')
			  ->from('project_photo')
			  ->where("project_id={$this->project_id}")
			  ->queryScalar();
			$this->rank = $maxRankNumber + 1;				
		}
		
        if (empty($this->file) == false) { 
			$this->image_extension = $this->file->getExtensionName();
		}
        if (empty($this->filePath) == false) { 
			$expl = explode('.', $this->filePath);
			$this->image_extension = end($expl);
		}
		
		return parent::beforeSave();
	}
		
	public function afterSave()
	{
        if (empty($this->file) == false)
		{ 
			$this->file->saveAs($this->getImage_directory(true) . '/origin.' . $this->image_extension);
		}	
	
		if (empty($this->filePath) == false) 
		{ 
			file_put_contents($this->getImage_directory(true) . '/origin.' . $this->image_extension, CUrlHelper::getPage($this->filePath, '', ''));
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
        if ($this->image_extension)
        {
			Yii::app()->file->set($this->image_directory)->delete();
        }
    }	
	
	public function getPublic_id()
	{
		return $this->id;
	}

    public function getImage_directory($mkdir=false)
    {
        if ($mkdir == false)
		{
			return Yii::app()->basePath . '/../photos/project/' . $this->project->id . '/' . $this->public_id;
		}
		else
		{
			$directory = Yii::app()->basePath . '/../photos/project/' . $this->project->id;
			if (file_exists($directory) == false)
			{
				mkdir($directory);
				chmod($directory, 0777);
			}
				
			$directory .= '/' . $this->public_id;
			if (file_exists($directory) == false)
			{
				mkdir($directory);
				chmod($directory, 0777);
			}	
			
			return $directory;
		}
    }

	public function getPreview()
	{
		return $this->getThumb(300,200,'resize');
	}	
	
	public function getThumb($width=null, $height=null, $mode='origin')
	{
		$dir = $this->getImage_directory(true) . '/';
		$originFile = $dir . 'origin.' . $this->image_extension;
		
		if (!is_file($originFile)) {
			return false;
		}
			
		if ($mode == 'origin') {
			return '/photos/project/' . $this->project->id . '/' . $this->public_id . '/origin.'. $this->image_extension;
		}
	
		$fileName = $mode . '_w' . $width . '_h' . $height . '.' . $this->image_extension;
		$filePath = $dir . $fileName;
		if (!is_file($filePath)) {
			if ($mode == 'resize') {
				Yii::app()->iwi->load($originFile)->resize($width, $height)->save($filePath);
			} else {
				Yii::app()->iwi->load($originFile)->crop($width, $height)->save($filePath);
			}
		}
		
		return '/photos/project/' . $this->project->id . '/' . $this->public_id . '/'. $fileName;
	}	
	
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PROJECT_PHOTO . $this->project_id);
	}		
	
}