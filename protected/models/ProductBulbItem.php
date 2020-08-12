<?php

class ProductBulbItem extends CActiveRecord
{		
    const PHOTO_DIR = '/photos/bulb/';
    
    const TYPE_HALOGEN = 1;
    const TYPE_LED = 2;
    const TYPE_HID = 3;
    
    public $file;
    public $is_delete_photo;
    
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
		return 'product_bulb_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bulb_id, title, type, badge, description, code', 'required'),
			array('id, badge, is_delete_photo', 'safe'),
            array(
                'file',
                'file',
                'types' => 'jpg,png,gif,jpeg',
                'allowEmpty' => true
            ),
		);
	}		
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'bulb_id' => 'Bulb',
			'title' => 'Title',
			'type' => 'Type',
			'badge' => 'Badge',
			'description' => 'Description',
			'code' => 'Code',
			'preview' => 'Image',
		);
	}
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'bulb' => array(self::BELONGS_TO, 'ProductBulb', 'bulb_id', 'together' => true,),
        );
    }
    
    protected function beforeSave()
    {
        if ($this->is_delete_photo) {
            $this->_deleteImage();
            $this->image = '';
        }

        return parent::beforeSave();
    }
    
	public function afterSave()
	{
		$this->clearCache();
		
        if (!empty($this->file)) {
            if (!$this->isNewRecord) {
                $this->_deleteImage();
            }

            $this->image = rand(1000,9999) . time() . ".jpg";
            $this->file->saveAs(self::getImage_directory(true) . $this->image);
            $this->updateByPk($this->id, array('image' => $this->image));
        }

        return parent::afterSave();
	}	
	
	public function afterDelete()
	{
		return parent::afterDelete();
	}	
	
	private function clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT_BULB_ITEM);
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

		$criteria->compare('id', $this->id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('badge', $this->badge, true);
		$criteria->compare('code', $this->code, true);
		$criteria->compare('type', $this->type);
		$criteria->compare('bulb_id', $this->bulb_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
    }		
	    
    private function _deleteImage()
    {
        if (!empty($this->image)) {
            $files = $this->bfglob(Yii::getPathOfAlias('webroot') . self::PHOTO_DIR, "*{$this->image}", 0, 10);
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    public static function getImage_directory($mkdir = false)
    {
        return Yii::app()->basePath . '/..' . self::PHOTO_DIR;
    }

    public function getPreview()
    {
        return $this->getThumb(100, 60, 'resize');
    }

    public function getThumb($width = null, $height = null, $mode = 'origin')
    {
        $dir = self::getImage_directory();
        $originFile = $dir . $this->image;

        if (!is_file($originFile)) {
            return "http://www.placehold.it/{$width}x{$height}/EFEFEF/AAAAAA";
        }

        if ($mode == 'origin') {
            return self::PHOTO_DIR . $this->image;
        }

        $subdir = $width;
        $subdirPath = $dir . $subdir;
        $subdirPathFile = $subdirPath . '/' . $this->image;

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

        return self::PHOTO_DIR . $subdir . '/' . $this->image;
    }

    public static function thumb($image, $width = null, $height = null, $mode = 'origin')
    {
        $placeholder = "http://www.placehold.it/{$width}x{$height}/EFEFEF/AAAAAA";
        if (empty($image)) {
            return $placeholder;
        }
        
        $thumbFile = "_thumb_{$width}_{$height}_{$mode}_{$image}";
        
        $dir = self::getImage_directory();
        if (is_file($dir . $thumbFile)) {
            return self::PHOTO_DIR . $thumbFile;
        }
        
        $originFile = $dir . $image;

        if (!is_file($originFile)) {
            return $placeholder;
        }

        if ($mode == 'origin') {
            return self::PHOTO_DIR . $image;
        }

        if ($mode == 'resize') {
            Yii::app()->iwi->load($originFile)
                ->resize($width, $height)
                ->save($dir . $thumbFile);
        } else {
            Yii::app()->iwi->load($originFile)
                ->crop($width, $height)
                ->save($dir . $thumbFile);
        }

        return self::PHOTO_DIR . $thumbFile;        
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
    
	public static function getTypes()
    {
        return [
            self::TYPE_HALOGEN => 'Halogen',
            self::TYPE_LED => 'LED',
            self::TYPE_HID => 'HID',
        ];
    }
	
    public static function getTypeTitle($value)
    {
        $list = self::getTypes();
        return isset($list[$value]) ? $list[$value] : null;
    }
    
	public static function getFrontTypes()
    {
        return [
            self::TYPE_HALOGEN => 'Halogen Lights',
            self::TYPE_LED => 'LED Lights',
            self::TYPE_HID => 'Xenon HID Conversion Kit',
        ];
    }
    
    public static function getFrontTypeTitle($value)
    {
        $list = self::getFrontTypes();
        return isset($list[$value]) ? $list[$value] : null;
    }
    
	public static function getItemsByType($bIds)
	{
        if (empty($bIds)) {
            return;
        }
        
		$key = Tags::TAG_PRODUCT_BULB_ITEM . 'getItemsByType' . serialize($bIds);
		$data = Yii::app()->cache->get($key);
		if ($data === false ) {
			$data = [];
			
            
            $sql = "
                SELECT 
                    i.*,
                    b.part AS part
                FROM product_bulb_items AS i
                LEFT JOIN product_bulb AS b ON i.bulb_id = b.id
                WHERE i.bulb_id IN(" . implode(',', $bIds) .")
            ";
            
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            
            foreach ($items as $item) {
                $item['image'] = self::thumb($item['image'], 100, 100, 'crop');
                $data[$item['bulb_id']][$item['type']][] = $item;
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_PRODUCT_BULB_POSITION, 
                Tags::TAG_PRODUCT_BULB_ITEM, 
                Tags::TAG_PRODUCT_BULB
            ));
		}
		
		return $data;
	}
    
	public static function getForwardItemsByYears($yIds)
	{
        if (empty($yIds)) {
            return;
        }
        
		$key = Tags::TAG_PRODUCT_BULB_ITEM . 'getForwardItemsByYears' . serialize($yIds);
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = [];
			
            
            $sql = "
                SELECT 
                    vs.model_year_id AS model_year_id,
                    vs.position_id AS position_id,
                    vs.bulb_id AS bulb_id,
                    i.type AS type,
                    b.part AS part,
                    b.alias AS bulb_alias,
                    p.alias AS position_alias,
                    p.short_title AS short_title
                FROM auto_model_year_bulb AS vs
                LEFT JOIN product_bulb AS b ON vs.bulb_id = b.id
                LEFT JOIN product_bulb_position AS p ON vs.position_id = p.id
                LEFT JOIN product_bulb_items AS i ON b.id = i.bulb_id
                WHERE 
                    vs.model_year_id IN(" . implode(',', $yIds) .") AND 
                    i.type IS NOT NULL AND
                    p.type = " . ProductBulbPosition::TYPE_FORWARD . "
                ORDER BY p.id ASC
            ";
            
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            
            foreach ($items as $item) {
                $vId = $item['model_year_id'] . '_' . $item['position_id'] . '_' . $item['bulb_id'];
                $data[$item['model_year_id']][$vId] = $item;
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_PRODUCT_BULB_POSITION, 
                Tags::TAG_PRODUCT_BULB_ITEM, 
                Tags::TAG_PRODUCT_BULB
            ));
		}
		
		return $data;
	}
    
}