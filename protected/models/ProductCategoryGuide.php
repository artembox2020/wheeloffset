<?php

class ProductCategoryGuide extends CActiveRecord
{
    const PHOTO_DIR = '/photos/parts/';

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
		return 'product_category_guide';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('meta_title, meta_description, meta_keywords, content,image', 'safe'),
			array('is_active, rank, is_delete_photo', 'numerical', 'integerOnly' => true),
            array('title,category_id,alias', 'required'),
            array('alias', 'unique'),
			array(
				'file', 
				'file', 
				'types'=>'jpg,png,gif,jpeg',
				'allowEmpty'=>true
			),	
            
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
		$this->_clearCache();
		
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
		$this->_deleteImage();
		$this->_clearCache();	
			
        return parent::afterDelete();
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
            return null;
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
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'is_active' => Yii::t('admin', 'Published'),
			'category_id' => Yii::t('admin', 'Category'),
			'is_delete_photo' => Yii::t('admin', 'Delete image'),
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
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('title',$this->title, true);
		$criteria->compare('meta_title',$this->meta_title, true);
		$criteria->compare('meta_description',$this->meta_description, true);
		$criteria->compare('meta_keywords',$this->meta_keywords, true);
		$criteria->compare('rank',$this->rank);
        
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'category' => array(self::BELONGS_TO, 'ProductCategory', 'category_id', 'together' => true,),
        );
    }
    
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT_CATEGORY_GUIDE);
	}
	
	public function getUrl()
	{
        $treeMap = ProductCategory::getTreeMap();
        
		return $treeMap[$this->category_id]['url'] . 'guides/'. $this->alias . '/';
	}
    
    public function getCategoryTitle()
    {
        $titles = [$this->category->title];
        if (!empty($this->category->parent)) {
            array_unshift($titles, $this->category->parent->title);
            
            if (!empty($this->category->parent->parent)) {
                array_unshift($titles, $this->category->parent->parent->title);
            }
        }
        return implode(' / ', $titles);
    }

	public static function getLatest($limit = 10, $categoryIds = [], $exceptId = false)
	{
		$key = Tags::TAG_PRODUCT_CATEGORY_GUIDE . '_getLatest' . $limit . serialize($categoryIds) . '_' . $exceptId;
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
			$data = [];
            $criteria = new CDbCriteria;
            if ($exceptId) {
                $criteria->condition = "id <> $exceptId";
            }
            $criteria->compare('is_active', 1);			
            $criteria->addInCondition('category_id', !empty($categoryIds) ? $categoryIds : ProductCategory::getActiveIds());			
            $criteria->order = '`id` DESC';			
            $criteria->limit = $limit;			
            $items = self::model()->findAll($criteria);
		    
            foreach ($items as $item) {
                $data[] = [
                    'image' => $item->getThumb(150, 100, 'resize'),
                    'title' => $item->title,
                    'url' => $item->getUrl(),
                ];
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY, Tags::TAG_PRODUCT_CATEGORY_GUIDE));
		}
		
		return $data;        
	}
    
	public static function getData($page, $categoryId = null)
    {
        $categoryTreeMap = ProductCategory::getTreeMap();
        $categoryIds = [];
        if ($categoryId === null) {
            $categoryIds = array_map(function($item) { return $item['id']; }, $categoryTreeMap);
        } else {
            $categoryIds = $categoryTreeMap[$categoryId]['ids'];
            $categoryIds[] = $categoryId;
        }
        
        $condition = "is_active=1";
        if (!empty($categoryIds)) {
            $condition .= " AND category_id IN(" . implode(',', $categoryIds) . ")";
        }
        
        $limit = 10;
        $key = Tags::TAG_PRODUCT_CATEGORY_GUIDE . 'getData' . $page . '_' . $categoryId . '_' . $limit;
        $items = Yii::app()->cache->get($key);
 
        if ($items === false) {

            $offset = ($page - 1) * $limit;
            $criteria = new CDbCriteria;
            $criteria->condition = $condition;
            $criteria->limit = $limit;
            $criteria->offset = $offset;
            $criteria->order = '`id` DESC';
            $rows = self::model()->findAll($criteria);

            $items = [];

            foreach ($rows as $row) {
                $categoryInfo = $categoryTreeMap[$row->category_id];
                
                $items[] = [
                    'id' => $row->id,
                    'url' => $row->url,
                    'title' => $row->title,
                    'description' => TextHelper::truncate(strip_tags($row->content), 150),
                    'image' => $row->getThumb(150, null, 'resize'),
                    'category' => [
                        'id' => $row->category_id,
                        'title' => $categoryInfo['title'],
                        'url' => $categoryInfo['url'],
                    ],
                ];
            }

            Yii::app()->cache->set($key, $items, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY, Tags::TAG_PRODUCT_CATEGORY_GUIDE));
        }

        $key =  Tags::TAG_PRODUCT_CATEGORY_GUIDE . 'getData_count_' . '_' . $categoryId;
        $count = Yii::app()->cache->get($key);

        if ($count === false) {
            $criteria = new CDbCriteria;
            $criteria->condition = $condition;
            $count = self::model()->count($criteria);

            Yii::app()->cache->set($key, $count, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY, Tags::TAG_PRODUCT_CATEGORY_GUIDE));
        }

        return [$count, $items, $limit];
    }    
    
	public static function getItemsByCategoryIds($ids)
	{
		$key = Tags::TAG_PRODUCT_CATEGORY_GUIDE . 'getItemsByCategoryIds' . serialize($ids);
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
			$data = [];
            $criteria = new CDbCriteria;
            $criteria->compare('is_active', 1);			
            $criteria->addInCondition('category_id', $ids);			
            $criteria->order = '`id` DESC';			
            $items = self::model()->findAll($criteria);
		    
            foreach ($items as $item) {
                $data[] = [
                    'image' => $item->getThumb(150, 100, 'resize'),
                    'title' => $item->title,
                    'url' => $item->getUrl(),
                ];
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY, Tags::TAG_PRODUCT_CATEGORY_GUIDE));
		}
		
		return $data;        
	}
    
	public static function getRootCategories()
	{
        $treeMap = ProductCategory::getTreeMap();
		$key = Tags::TAG_PRODUCT_CATEGORY_GUIDE . 'getRootCategories';
		$categoryRootIds = Yii::app()->cache->get($key);
		
        if ($categoryRootIds === false) {
            $sql = "SELECT DISTINCT category_id AS category_id FROM product_category_guide  WHERE is_active=1";
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
            $categoryRootIds = [];
            foreach ($rows as $row) {
                if (isset($treeMap[$row['category_id']])) {
                    $info = $treeMap[$row['category_id']];
                    $rootId = $info['root_id'];
                    if ($rootId === null) {
                        $rootId = $info['id'];
                    }
                    $categoryRootIds[$rootId] = $rootId;
                }
            }
			Yii::app()->cache->set($key, $categoryRootIds, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY, Tags::TAG_PRODUCT_CATEGORY_GUIDE));
		}

        $categories = array_filter($treeMap, function($item) use ($categoryRootIds) {
            return in_array($item['id'], $categoryRootIds);
        });
        
		return $categories;        
	}
    
	public static function getRootChildren($rootId)
	{
        $treeMap = ProductCategory::getTreeMap();
		$key = Tags::TAG_PRODUCT_CATEGORY_GUIDE . 'getRootChildren' . $rootId;
		$childrenIds = Yii::app()->cache->get($key);
		
        if ($childrenIds === false) {
            
            $sql = "SELECT DISTINCT category_id AS category_id FROM product_category_guide  WHERE is_active=1";
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
            $childrenIds = [];
            foreach ($rows as $row) {
                if (isset($treeMap[$row['category_id']])) {
                    $info = $treeMap[$row['category_id']];
                    if ($info['level'] !== 1) {
                        if ($info['level'] === 2 && $info['parent_id'] == $rootId) {
                            $childrenIds[$info['id']] = $info['id'];
                        } elseif ($info['level'] === 3) {
                            $subParentInfo = $treeMap[$info['parent_id']];
                            if ($subParentInfo['parent_id'] == $rootId) {
                                $childrenIds[$subParentInfo['id']] = $subParentInfo['id'];
                            }
                         }
                    }
                }
            }
			Yii::app()->cache->set($key, $childrenIds, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY, Tags::TAG_PRODUCT_CATEGORY_GUIDE));
		}

        $categories = array_filter($treeMap, function($item) use ($childrenIds) {
            return in_array($item['id'], $childrenIds);
        });
        
		return $categories;        
	}
    
	public static function getItem($alias, $categoryId)
	{
		$key = Tags::TAG_PRODUCT_CATEGORY_GUIDE . '_getItem' . $alias . '_' . $categoryId;
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
			$data = [];
            $criteria = new CDbCriteria;
            $criteria->compare('is_active', 1);			
            $criteria->compare('alias', $alias);			
            $criteria->compare('category_id', $categoryId);			
            $item = self::model()->find($criteria);
		    
            if (!empty($item)) {
                $data = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                ];
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY_GUIDE));
		}
		
		return $data;        
	}
    
	
}