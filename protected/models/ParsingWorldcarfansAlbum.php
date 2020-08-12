<?php

class ParsingWorldcarfansAlbum extends CActiveRecord
{
	const LOGO_PATH = '/photos/parsing/worldcarfans/album/';
	const LOGO_EXT = '.jpg';


	public $logo_url;
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
		return 'parsing_worldcarfans_album';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, url', 'required'),
			array('logo_url, is_new, model_year_id', 'safe'),
		);
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
	
   protected function beforeSave() 
   {
		if ($this->isNewRecord) {
			$this->create_time = time();
			$this->is_new = 1;
		}				
			
		return parent::beforeSave();
    }	
	
	
	public function afterSave()
	{	
		if ($this->isNewRecord) {
			$content = @file_get_contents($this->logo_url);
			if ($content);
				file_put_contents($this->_fullFilePath(), $content);
		}	
		
		return parent::afterSave();
	}
	
	private function _fullFilePath()
	{
		return str_replace('protected/', '', Yii::getPathOfAlias('webroot') . self::LOGO_PATH . $this->id . self::LOGO_EXT) ;
	}

    public function beforeDelete() 
	{
		@unlink($this->_fullFilePath());
	
		$criteria = new CDbCriteria();
		$criteria->compare('album_id', $this->id);
		$photos = ParsingWorldcarfansAlbumPhoto::model()->findAll($criteria);
		foreach ($photos as $photo) {
			$photo->delete();
		}
		
        return parent::beforeDelete();
    }	
	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => Yii::t('admin', 'Title'),
			'image_preview' => Yii::t('admin', 'Image'),
			'create_time' => Yii::t('admin', 'Time Parsing'),
			'model_year_id' => Yii::t('admin', 'Model By Year'),
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
		$criteria->compare('is_new',$this->is_new);
		$criteria->compare('title',$this->title, true);
		$criteria->with = array('ModelYear');

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
	
	public static function moveToModelYear()
	{
		$criteria=new CDbCriteria;
		$criteria->addCondition('model_year_id	> 0');
		$criteria->compare('is_new',1);		
		
		$albums = self::model()->findAll($criteria);
		foreach ($albums as $album) {
		
			$criteria = new CDbCriteria();
			$criteria->compare('is_new',1);	
			$criteria->compare('album_id', $album->id);
			$photos = ParsingWorldcarfansAlbumPhoto::model()->findAll($criteria);
			foreach ($photos as $photo) {
				$photo->is_new = 0;
				$photo->save(false);
				
				$modelYearPhoto = new AutoModelYearPhoto;
				$modelYearPhoto->year_id = $album->model_year_id;
				$modelYearPhoto->file_url = $photo->getFullFilePath();
				$modelYearPhoto->save();
			}		
		
			$album->is_new = 0;
			$album->save(false);
		}
	}

}