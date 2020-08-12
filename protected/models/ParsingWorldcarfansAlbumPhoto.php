<?php

class ParsingWorldcarfansAlbumPhoto extends CActiveRecord
{
	const LOGO_PATH = '/photos/parsing/worldcarfans/photo/';
	const LOGO_EXT = '.jpg';


	public $url;
	private $_logo_content;
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
		return 'parsing_worldcarfans_album_photo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, url, album_id', 'required'),
			array('url', 'safe'),
			array('url', 'logoValidate', 'on'=>'insert'),
		);
	}
	
	public function logoValidate()
	{
		$this->_logo_content = @file_get_contents($this->url);
		if (empty($this->_logo_content)) {
			$this->addError('url', 'image 404');
		}
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
			file_put_contents($this->getFullFilePath(), $this->_logo_content);
		}	
		
		return parent::afterSave();
	}
	
	public function getFullFilePath()
	{
		return str_replace('protected/', '', Yii::getPathOfAlias('webroot') . self::LOGO_PATH . $this->id . self::LOGO_EXT) ;
	}

    public function beforeDelete() 
	{
		@unlink($this->getFullFilePath());

        return parent::beforeDelete();
    }	
	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'album_id' => Yii::t('admin', 'Album'),
			'is_new' => Yii::t('admin', 'New'),
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
		$criteria->compare('album_id',$this->album_id);
		$criteria->compare('is_new',$this->is_new);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}


}