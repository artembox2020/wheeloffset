<?php

class ProductCategory extends CActiveRecord
{
    const PHOTO_DIR = '/photos/parts/';

    public $file;
    public $is_delete_photo;
    
    public $prosErrors;
    public $consErrors;
    public $is_text;
    public $texts;
    public $post_models;
    public $count_seo_model_texts;
    
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
		return 'product_category';
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
			array('is_active, rank, is_delete_photo', 'numerical', 'integerOnly' => true),
			array(
				'file', 
				'file', 
				'types'=>'jpg,png,gif,jpeg',
				'allowEmpty'=>true
			),	
			array('header_text, footer_text, count_seo_model_texts, parent_id, texts, post_models, is_text', 'safe',),	
			array('make_meta_title, make_meta_description, make_meta_keywords, make_header_text, make_footer_text', 'safe'),	
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
	
	protected function afterValidate()
	{
        /*
        foreach ($this->texts as $i => $item) {
            if (empty($item['pros']['text'])) {
                $this->prosErrors[$i] = 'Field is required';
            }
            if (empty($item['cons']['text'])) {
                $this->consErrors[$i] = 'Field is required';
            }
        }

        if (!empty($this->prosErrors) || !empty($this->consErrors)) {
            $this->addError('texts', 'faild');
        }
         */
        
        $criteria = new CDbCriteria;
        $criteria->compare('category_id', $this->id);
        if (!empty($this->id) &&
            is_array($this->post_models) && 
            count($this->post_models) && 
            count($this->post_models) > ProductCategoryModelSeoText::model()->count($criteria)) {
            $this->addError('post_models', 'Top up your SEO texts for this category');
        }
        
		return parent::afterValidate();
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
        
        if ($this->is_text) {
            $issetIds = [];
            $mapType = [
                'pros' => ProductCategoryText::TYPE_PROS,
                'cons' => ProductCategoryText::TYPE_CONS,
            ];
            $n=1;
            foreach ($this->texts as $item) {
                //dd($this->texts);
                foreach (['pros', 'cons'] as $typeTitle) {
                    $modelText = null;
                    $attr = $item[$typeTitle];
                    if (!empty($attr['id'])) {
                        $modelText = ProductCategoryText::model()->findByPk($attr['id']);
                    }
                    if (empty($modelText)) {
                        $modelText = new ProductCategoryText;
                    }
                    $modelText->category_id = $this->id;
                    $modelText->type = $mapType[$typeTitle];
                    $modelText->text = $attr['text'];
                    $modelText->number = $n;
                    $modelText->save();
                    $issetIds[] = $modelText->id;
                }
                $n++;
            }
            $where = "WHERE category_id = {$this->id}";
            if (!empty($issetIds)) {
                $where.= ' AND id NOT IN('. implode(',', $issetIds) .')';
            }
            
            $sql = "DELETE FROM product_category_text $where";
            Yii::app()->db->createCommand($sql)->execute();
        }    

        if (!empty($this->id) &&
            is_array($this->post_models) && 
            count($this->post_models)
         ) {
            $criteria = new CDbCriteria;
            $criteria->compare('category_id', $this->id);
            $criteria->limit = count($this->post_models);
            $seoTexts = ProductCategoryModelSeoText::model()->findAll($criteria);
            
            foreach ($this->post_models as $i => $modelId) {
                $criteria = new CDbCriteria;
                $criteria->compare('t.id', $modelId);
                $criteria->with = ['Make'];
                $model = AutoModel::model()->find($criteria);
                if (!empty($model)) {
                    $params = [
                        'model' => $model->title,
                        'make' => $model->Make->title,
                    ];
                    
                    $categoryModel = new ProductCategoryModel;
                    $categoryModel->category_id = $this->id;
                    $categoryModel->model_id = $model->id;
                    $categoryModel->make_id = $model->make_id;
                    $categoryModel->is_active = 1;
                    $categoryModel->header_text = str_replace_params($seoTexts[$i]->header_text, $params);
                    $categoryModel->footer_text = str_replace_params($seoTexts[$i]->footer_text, $params);
                    if ($categoryModel->save()) {
                        $seoTexts[$i]->delete();
                    } else {
                        dd($categoryModel->errors);
                    }
                }
            }
        }
        
		return parent::afterValidate();
        
        
        return parent::afterSave();
	}

    public function afterDelete() 
	{
		$this->_deleteImage();
		$this->_clearCache();	
        
        return parent::afterDelete();
    }	
    
    public function getPostTexts()
    {
        $data = [];
        if (Yii::app()->request->isPostRequest) {
            $data = $this->texts;
        } else {
            $criteria = new CDbCriteria;
            $criteria->compare('category_id', $this->id);
            $items = ProductCategoryText::model()->findAll($criteria);
            $mapType = [
                ProductCategoryText::TYPE_PROS => 'pros',
                ProductCategoryText::TYPE_CONS => 'cons',
            ];
            foreach ($items as $item) {
                $data[$item->number-1][$mapType[$item->type]]['id'] = $item->id;
                $data[$item->number-1][$mapType[$item->type]]['text'] = $item->text;
                $data[$item->number-1][$mapType[$item->type]]['number'] = $item->number;
            }
        }
        
        return $data;
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'count_seo_model_texts' => Yii::t('admin', 'Model texts'),
			'title' => Yii::t('admin', 'Title'),
			'file' => Yii::t('admin', 'Image'),
			'alias' => Yii::t('admin', 'Alias'),
			'image_preview' => Yii::t('admin', 'Image'),
			'is_active' => Yii::t('admin', 'Published'),
			'header_text' => Yii::t('admin', 'Header text'),
			'footer_text' => Yii::t('admin', 'Footer text'),
			'parent_id' => Yii::t('admin', 'Parent category'),
			'is_delete_photo' => Yii::t('admin', 'Delete image'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
        $where = [];
        if (!empty($this->title)) {
            $where[] = "t.title LIKE '%{$this->title}%'";
        }
        if (!empty($this->alias)) {
            $where[] = "t.alias LIKE '%{$this->alias}%'";
        }
        if (!empty($this->id)) {
            $where[] = "t.id = '$this->id'";
        }
        if (!empty($this->rank)) {
            $where[] = "t.rank = '$this->rank'";
        }
        if (!empty($this->is_active)) {
            $where[] = "t.is_active = '$this->is_active'";
        }
        if (!empty($this->parent_id)) {
            $where[] = "t.parent_id = '$this->parent_id'";
        }
        $where = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT 
            t.id AS id,
            t.title AS title,
            t.alias AS alias,
            t.is_active AS is_active,
            t.`rank` AS `rank`,
            t.title AS parent_title,
            t.image AS image,
            (SELECT
                        COUNT(*) 
                        FROM product_category_model_seo_text AS st
                        WHERE st.category_id = t.id
                    ) AS count_seo_model_texts
        FROM product_category AS `t`  
        {$where}";    
        
        $count=Yii::app()->db->createCommand("SELECT COUNT(*) FROM product_category AS t {$where}")->queryScalar();
        $dataProvider=new CSqlDataProvider($sql, array(
            'totalItemCount'=>$count,
            'sort'=>array(
                'attributes'=>array(
                    'id', 'title', 'rank', 'alias', 'is_active', 'parent_title', 'count_seo_model_texts',
                ),
            ),
            'pagination'=>array(
                'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
            ),
        ));

        return $dataProvider;
	}
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'parent' => array(self::BELONGS_TO, 'ProductCategory', 'parent_id', 'together' => true,),
        );
    }
    
	
	public static function getList($level = 2)
	{
        $criteria=new CDbCriteria;
        $criteria->condition = 'parent_id IS NULL';
        $criteria->order = '`rank` ASC';
        
        $items = self::model()->findAll($criteria);
        $data = [];
        
        foreach ($items as $item) {
            $data[$item->id] = $item->title;
            if ($level >= 2) {
                $criteria=new CDbCriteria;
                $criteria->condition = 'parent_id = ' . $item->id;
                $criteria->order = '`rank` ASC';
                $children = self::model()->findAll($criteria);
                foreach ($children as $child) {
                    $data[$child->id] = ' -- ' . $child->title;
                    
                    if ($level >= 3) {
                        $criteria=new CDbCriteria;
                        $criteria->condition = 'parent_id = ' . $child->id;
                        $criteria->order = '`rank` ASC';
                        $subs = self::model()->findAll($criteria);
                        foreach ($subs as $sub) {
                            $data[$sub->id] = ' ---- ' . $sub->title;
                        }
                    }
                }
            }
        }
		return $data;
	}


	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT_CATEGORY);
	}
	
	public function getUrl()
	{
        $treeMap = self::getTreeMap();
        
		return $treeMap[$this->id]['url'];
	}
    
	public static function getRootItemsWithChildren()
	{
		$key = Tags::TAG_PRODUCT_CATEGORY . 'getRootItemsWithChildren';
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
			$data = [];
            $criteria = new CDbCriteria;
            $criteria->condition = 'is_active = 1';			
            $criteria->order = '`rank` ASC';			
            $items = self::model()->findAll($criteria);
		    
            foreach ($items as $root) {
                if (empty($root->parent_id)) {
                    $data[$root->id] = [
                        'id' => $root->id,
                        'alias' => $root->alias,
                        'title' => $root->title,
                        'url' => $root->getUrl(),
                    ];
                    
                    foreach ($items as $child) {
                        if ($child->parent_id == $root->id) {
                            $data[$root->id]['children'][$child->id] = [
                                'title' => $child->title,
                                'url' => $child->getUrl(),
                                'alias' => $child->alias,
                                'image' => $child->preview,
                            ];
                        }
                    }
                }
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY));
		}
		
		return $data;        
	}
	
	public static function getChildItemsWithSubs($rootId)
	{
		$key = Tags::TAG_PRODUCT_CATEGORY . 'getChildItemsWithSubs_' . $rootId;
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
			$data = [];
            $criteria = new CDbCriteria;
            $criteria->condition = 'is_active = 1';			
            $criteria->order = '`rank` ASC';			
            $items = self::model()->findAll($criteria);
		    
            foreach ($items as $child) {
                if (!empty($child->parent_id) && $child->parent_id === $rootId) {
                    $data[$child->id] = [
                        'title' => $child->title,
                        'url' => $child->getUrl(),
                        'image' => $child->preview,
                    ];
                    
                    foreach ($items as $sub) {
                        if ($sub->parent_id == $child->id) {
                            $data[$child->id]['children'][$sub->id] = [
                                'title' => $sub->title,
                                'url' => $sub->getUrl(),
                            ];
                        }
                    }
                }
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY));
		}
		
		return $data;        
	}
	
	public static function getActiveIds()
	{
		$key = Tags::TAG_PRODUCT_CATEGORY . 'getActiveIds';
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
			$data = [];
            $criteria = new CDbCriteria;
            $criteria->condition = 'is_active = 1';			
            $criteria->order = '`rank` ASC';			
            $items = self::model()->findAll($criteria);
		    
            foreach ($items as $root) {
                if (empty($root->parent_id)) {
                    $data[] = $root->id;
                    
                    foreach ($items as $child) {
                        if ($child->parent_id == $root->id) {
                            $data[] = $child->id;
                            
                            foreach ($items as $sub) {
                                if ($sub->parent_id == $child->id) {
                                    $data[] = $sub->id;
                                }
                            }
                        }
                    }
                }
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY));
		}
		
		return $data;        
	}
    
    public static function getTreeMap($is_active = 1)
	{
		$key = Tags::TAG_PRODUCT_CATEGORY . '_getTreeMap_' . $is_active;
		$data = Yii::app()->cache->get($key);
		
        if ($data === false || 1) {
            
            $mapCategoryEbay = [];
            $sql = "SELECT id, category_id FROM product_ebay_category WHERE category_id IS NOT NULL";
            foreach (Yii::app()->db->createCommand($sql)->queryAll() as $item) {
                $mapCategoryEbay[$item['category_id']][] = $item['id'];
            }
            
			$data = [];
            $criteria = new CDbCriteria;
            if ($is_active) {
                $criteria->condition = 'is_active = 1';		
            }
            $criteria->order = '`rank` ASC';			
            $items = self::model()->findAll($criteria);
		    
            foreach ($items as $root) {
                if (empty($root->parent_id)) {
                    $data[$root->id]['level'] = 1;
                    $data[$root->id]['id'] = $root->id;
                    $data[$root->id]['title'] = $root->title;
                    $data[$root->id]['alias'] = $root->alias;
                    $data[$root->id]['rank'] = $root->rank;
                    $data[$root->id]['url'] = '/' . $root->alias . '-parts/';
                    $data[$root->id]['ids'] = [$root->id];
                    $data[$root->id]['pds'] = [];
                    $data[$root->id]['root_id'] = null;
                    $data[$root->id]['ebay_category_ids'] = !empty($mapCategoryEbay[$root->id]) ? $mapCategoryEbay[$root->id] : [];
                    
                    foreach ($items as $child) {
                        if ($child->parent_id == $root->id) {
                            $data[$child->id]['level'] = 2;
                            $data[$child->id]['id'] = $child->id;
                            $data[$child->id]['parent_id'] = $root->id;
                            $data[$child->id]['title'] = $child->title;
                            $data[$child->id]['alias'] = $child->alias;
                            $data[$child->id]['rank'] = $child->rank;
                            $data[$child->id]['url'] = $data[$root->id]['url'] . $child->alias . '/';
                            $data[$child->id]['ids'] = [$child->id];
                            $data[$child->id]['pds'] = [$root->id];
                            $data[$child->id]['root_id'] = $root->id;
                            $data[$root->id]['ids'][] = $child->id;
                            
                            $ebayCategoryIds = !empty($mapCategoryEbay[$child->id]) ? $mapCategoryEbay[$child->id] : [];
                            $data[$child->id]['ebay_category_ids'] = $ebayCategoryIds;
                            foreach ($ebayCategoryIds as $ebayCategoryId) {
                                if (!in_array($ebayCategoryId, $data[$root->id]['ebay_category_ids'])) {
                                    $data[$root->id]['ebay_category_ids'][] = $ebayCategoryId;
                                }
                            }
                            
                            foreach ($items as $sub) {
                                if ($sub->parent_id == $child->id) {
                                    $data[$sub->id]['level'] = 3;
                                    $data[$sub->id]['id'] = $sub->id;
                                    $data[$sub->id]['parent_id'] = $child->id;
                                    $data[$sub->id]['title'] = $sub->title;
                                    $data[$sub->id]['alias'] = $sub->alias;
                                    $data[$sub->id]['rank'] = $sub->rank;
                                    $data[$sub->id]['url'] = $data[$child->id]['url'] . $sub->alias . '/';
                                    $data[$sub->id]['ids'] = [$sub->id];
                                    $data[$sub->id]['pds'] = [$root->id, $child->id];
                                    $data[$sub->id]['root_id'] = $root->id;
                                    $data[$child->id]['ids'][] = $sub->id;
                                    $data[$root->id]['ids'][] = $sub->id;
                                    
                                    $ebayCategoryIds = !empty($mapCategoryEbay[$sub->id]) ? $mapCategoryEbay[$sub->id] : [];
                                    $data[$sub->id]['ebay_category_ids'] = $ebayCategoryIds;
                                    foreach ($ebayCategoryIds as $ebayCategoryId) {
                                        if (!in_array($ebayCategoryId, $data[$root->id]['ebay_category_ids'])) {
                                            $data[$root->id]['ebay_category_ids'][] = $ebayCategoryId;
                                        }
                                        if (!in_array($ebayCategoryId, $data[$child->id]['ebay_category_ids'])) {
                                            $data[$child->id]['ebay_category_ids'][] = $ebayCategoryId;
                                        }
                                    }
                                    
                                }
                            }
                        }
                    }
                }
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY, Tags::TAG_PRODUCT_EBAY_CATEGORY));
		}
		
		return $data;        
	}
    
    public static function getRootItemByAlias($alias) 
    {
		$key = Tags::TAG_PRODUCT_CATEGORY . '_getRootItemByAlias' . $alias;
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
            
			$data = [];
            $criteria = new CDbCriteria;
            $criteria->condition = "is_active = 1 AND parent_id IS NULL AND alias = '{$alias}'";			
            $item = self::model()->find($criteria);
		    $data = [];
            if (!empty($item)) {
                $data = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'header_text' => $item->header_text,
                    'footer_text' => $item->footer_text,
                    'url' => $item->getUrl(),
                    'make_meta_title' => $item->make_meta_title,
                    'make_meta_description' => $item->make_meta_description,
                    'make_meta_keywords' => $item->make_meta_keywords,
                    'make_header_text' => $item->make_header_text,
                    'make_footer_text' => $item->make_footer_text,
                ];
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY));
		}
		
		return $data;        
    }

    public static function getChildItemByParentIdAndAlias($parentId, $alias) 
    {
		$key = Tags::TAG_PRODUCT_CATEGORY . '_getChildItemByParentIdAndAlias' . $parentId . '_' . $alias;
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
			$data = [];
            $criteria = new CDbCriteria;
            $criteria->condition = "is_active=1 AND parent_id={$parentId} AND alias='{$alias}'";			
            $item = self::model()->find($criteria);
		    $data = [];
            if (!empty($item)) {
                $data = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'header_text' => $item->header_text,
                    'footer_text' => $item->footer_text,
                    'url' => $item->getUrl(),
                    'make_meta_title' => $item->make_meta_title,
                    'make_meta_description' => $item->make_meta_description,
                    'make_meta_keywords' => $item->make_meta_keywords,
                    'make_header_text' => $item->make_header_text,
                    'make_footer_text' => $item->make_footer_text,
                ];
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY));
		}
		
		return $data;        
    }
    
    public static function getItemsByParentId($parentId) 
    {
		$key = Tags::TAG_PRODUCT_CATEGORY . 'getItemsByParentId' . $parentId;
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
			$data = [];
            $criteria = new CDbCriteria;
            $criteria->condition = "is_active=1 AND parent_id={$parentId}";			
            $items = self::model()->findAll($criteria);
		    $data = [];
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'url' => $item->getUrl(),
                ];
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_CATEGORY));
		}
		
		return $data;        
    }
    
    public static function findById($id)
    {
        return self::model()->findByPk($id);
    }
    
    
    
    public function getModelIds($is_active = null): array
    {
        $where = '';
        if (!is_null($is_active)) {
            $where = " AND is_active = " . $is_active;
        }
        
        $sql = "SELECT model_id FROM product_category_model WHERE category_id = $this->id {$where}";
        return Yii::app()->db->createCommand($sql)->queryColumn();
    }
    
    public function setModelText(ProductCategoryModel $categoryModel)
    {
        
    }
}