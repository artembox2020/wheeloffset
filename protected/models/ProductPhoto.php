<?php

/**
 *
 * The followings are the available model relations:
 * @property News $institution
 *
 */
class ProductPhoto extends CActiveRecord
{
    const PATH = '/photos/products/';
    
    public $image_url;
    
    
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
        return 'product_photo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, image_url', 'required'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'product_id' => 'Product',
        );
    }
		
	public function beforeSave()
	{
		if (!empty($this->image_url)) {
            $this->saveImage($this->image_url);
		}
		
		$this->_clearCache();
		
		return parent::beforeSave();
	}	
	
	private function _clearCache()
	{
		Yii::app()->cache->delete(Tags::PRODUCT_PHOTO . '_ITEM_' . $this->product_id);
	}
	
	public function beforeDelete()
	{
		$this->_deleteImage();
		
		$this->_clearCache();
		
		return parent::beforeDelete();
	}
	
	private function _deleteImage()
    {
        if ($this->image) {
		}
    }	
	 
    /**
     * @return string
     */
    public function getStoragePath() : string
    {
        return TextHelper::getRandomString(3). '/' . TextHelper::getRandomString(3) . '/' . TextHelper::getRandomString(3);
    }
    
    public function saveImage($url) 
    {
        $dir = $this->getStoragePath();
        $fileName = TextHelper::getRandomString(5) . '.jpg';
        
        $path = Yii::app()->basePath . '/..' . self::PATH;
        foreach (explode('/', $dir) as $part) {
            $path .= $part . '/';
            if (!is_dir($path)) {
				mkdir($path);
				chmod($path, 0777);                
            }
        }
        $content = file_get_contents($url);
        $path .= $fileName;
        
        file_put_contents($path, $content);
        $this->image = $dir . '/' . $fileName;
    }
	
	public static function getThumb($path, $width=null, $height=null, $mode='origin')
	{
        $originFile = Yii::app()->basePath . '/..' . self::PATH . $path;
        
		if (!is_file($originFile)) {
            return false;
		}
			
		if ($mode == 'origin' || (!$width && !$height)) {
			return self::PATH . $path;
		}
        
        $pi = pathinfo($originFile);
        
        $expl = explode('..', $pi['dirname']);
        $dirPath = $expl[1];
        
		$fileName = $pi['filename'];
        if ($width) {
            $fileName .= '-w' . $width;
        }
        if ($height) {
            $fileName .= '-h' . $height;
        }
        $fileName.= '.' . $pi['extension'];
        
		$filePath = $pi['dirname'] . $fileName;
		if (!is_file($filePath)) {
			if ($mode == 'resize') {
				Yii::app()->iwi->load($originFile)->resize($width, $height)->save($filePath);
			} else {
				Yii::app()->iwi->load($originFile)->crop($width, $height)->save($filePath);
			}
		}
		
		return $dirPath . $fileName;
	}	
}