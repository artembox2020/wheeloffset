<?php

class ProductCategoryModel extends CActiveRecord
{
	private $oldAttributes = [];
    
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
		return 'product_category_model';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('meta_title, meta_description, meta_keywords, meta_h1, header_text, footer_text', 'safe'),
			array('is_active', 'numerical', 'integerOnly' => true),
            array('category_id, model_id, make_id', 'required')
		);
	}
    
    public function afterFind()
    {
        $this->oldAttributes = $this->attributes;
        
        return parent::afterFind();
    }    
        
    public function afterValidate()
    {
        $this->validateUniqueModel();
        $this->validateActivityModelInParent();
        
        parent::afterValidate();
    }
    
    public function validateUniqueModel()
    {
        if (!$this->hasErrors() && !empty($this->model_id) && !empty($this->category_id)) {
            $criteria=new CDbCriteria;
            if (!empty($this->id)) {
                $criteria->condition = "id <> $this->id";
            }
            $criteria->compare('model_id',$this->model_id);
            $criteria->compare('category_id',$this->category_id);
            if (self::model()->count($criteria)) {
                $this->addError('model_id', 'Model already exists in this category');
            }
        }
    }
    
    public function validateActivityModelInParent()
    {
        if (!$this->hasErrors() && !empty($this->model_id) && !empty($this->category_id)) {
            $category = ProductCategory::findById($this->category_id);
            if (!empty($category->parent_id)) {
                $parentCategoryModel = self::model()->findByAttributes([
                    'model_id' => $this->model_id,
                    'category_id' => $category->parent_id,
                    'is_active' => 1,
                ]);
                
                if ($parentCategoryModel === null) {
                    
                    
                    $this->addError('model_id', 'The model must be added and active in the parent category <b>'. $category->parent->title .'</b>');
                
                } elseif (!empty($parentCategoryModel->category->parent_id)) {
                    $rootCategory = ProductCategory::findById($parentCategoryModel->category->parent_id);
                    $rootCategoryModel = self::model()->findByAttributes([
                        'model_id' => $this->model_id,
                        'category_id' => $rootCategory->id,
                        'is_active' => 1,
                    ]);
                    if ($rootCategoryModel === null) {
                        $this->addError('model_id', 'The model must be added and active in the parent category <b>'. $rootCategory->title .'</b>');
                    }
                }
            }
        }
    }
    
	public function afterSave()
	{	
        if (!empty($this->oldAttributes['is_active']) && 
            $this->oldAttributes['is_active'] != $this->is_active && 
            !$this->is_active
        ) {
            $this->inactiveInChildsModel();
        }
        
		$this->_clearCache();
		
        return parent::afterSave();
	}
    
    public function inactiveInChildsModel()
    {
        $selfId = $this->category_id;
        $treeMap = ProductCategory::getTreeMap(0);
        $childsIds = array_filter($treeMap[$this->category_id]['ids'], function($id) use ($selfId) {
            return $id != $selfId; 
        });
        
        if (!empty($childsIds)) {  
            $sql =  "UPDATE " . $this->tableName() . " SET is_active = 0 " . 
                    "WHERE model_id = {$this->model_id} AND category_id IN(" . implode(',', $childsIds) . ")";
            Yii::app()->db->createCommand($sql)->execute();        
        }
    }

    public function afterDelete() 
	{
        $this->deleteChildsModel();
        
		$this->_clearCache();	
			
        return parent::afterDelete();
    }	
    
    public function deleteChildsModel()
    {
        $selfId = $this->category_id;
        $treeMap = ProductCategory::getTreeMap(0);
        $childsIds = array_filter($treeMap[$this->category_id]['ids'], function($id) use ($selfId) {
            return $id != $selfId; 
        });
        
        if (!empty($childsIds)) {  
            $sql =  "DELETE FROM " . $this->tableName() . 
                    " WHERE model_id = {$this->model_id} AND category_id IN(" . implode(',', $childsIds) . ")";
            Yii::app()->db->createCommand($sql)->execute();        
        }
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
			'make_id' => Yii::t('admin', 'Make'),
			'model_id' => Yii::t('admin', 'Model'),
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
		$criteria->compare('make_id',$this->make_id);
		$criteria->compare('meta_title',$this->meta_title, true);
		$criteria->compare('meta_description',$this->meta_description, true);
		$criteria->compare('meta_keywords',$this->meta_keywords, true);
		$criteria->compare('meta_h1',$this->meta_h1, true);

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
            'model' => array(self::BELONGS_TO, 'AutoModel', 'model_id', 'together' => true,),
            'make' => array(self::BELONGS_TO, 'AutoMake', 'make_id', 'together' => true,),
        );
    }
    
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT_CATEGORY_MODEL);
	}
	
	public function getUrl()
	{
		return '/'.$this->alias . '/';
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
    
    public static function getByMakeCategoryWithChildren($makeId)
    {
        $key = Tags::TAG_PRODUCT_CATEGORY_MODEL . 'getByMakeCategoryWithChildren' . $makeId;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {

            $sql = "SELECT 
                    cm.category_id AS category_id
                FROM product_category_model AS cm
                LEFT JOIN auto_model AS m ON cm.model_id = m.id
                LEFT JOIN product_category AS c ON cm.category_id = c.id
                WHERE 
                    c.is_active = 1 AND 
                    cm.is_active = 1 AND 
                    m.make_id = {$makeId} AND
                    m.is_active = 1 AND 
                    m.is_deleted = 0
                ORDER BY c.rank ASC        
                ";
            
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            
            $data = self::normalizeCategoryChildTree($items);
            
            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }
    
    public static function getByMakeIdAndCategoryIdChildItemsWithSubs($categoryId, $makeId)
    {
        $key = Tags::TAG_PRODUCT_CATEGORY_MODEL . 'getByMakeIdAndCategoryIdChildItemsWithSubs' . $categoryId . '_' . $makeId;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $sql = "SELECT 
                    cm.category_id AS category_id
                FROM product_category_model AS cm
                LEFT JOIN auto_model AS m ON cm.model_id = m.id
                LEFT JOIN product_category AS c ON cm.category_id = c.id
                WHERE 
                    c.is_active = 1 AND 
                    cm.is_active = 1 AND 
                    m.make_id = {$makeId} AND
                    m.is_active = 1 AND 
                    m.is_deleted = 0
                ORDER BY c.rank ASC        
                ";
            
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            
            $data = self::normalizeChildSubTreeByCategory($items, $categoryId);
            
            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }

    public static function getByModelIdAndCategoryIdChildItemsWithSubs($categoryId, $modelId)
    {
        $key = Tags::TAG_PRODUCT_CATEGORY_MODEL . 'getByModelIdAndCategoryIdChildItemsWithSubs' . $categoryId . '_' . $modelId;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {

            $sql = "SELECT 
                    cm.category_id AS category_id
                FROM product_category_model AS cm
                LEFT JOIN product_category AS c ON cm.category_id = c.id
                WHERE 
                    c.is_active = 1 AND 
                    cm.is_active = 1 AND 
                    cm.model_id = {$modelId}
                ORDER BY c.rank ASC        
                ";
            
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            
            $data = self::normalizeChildSubTreeByCategory($items, $categoryId);
            
            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }
    
    public static function getByModelCategoryWithChildren($modelId)
    {
        $key = Tags::TAG_PRODUCT_CATEGORY_MODEL . 'getByModelCategoryWithChildren' . $modelId;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $sql = "SELECT 
                    cm.category_id AS category_id
                FROM product_category_model AS cm
                LEFT JOIN product_category AS c ON cm.category_id = c.id
                WHERE 
                    c.is_active = 1 AND 
                    cm.is_active = 1 AND 
                    cm.model_id = {$modelId}
                ORDER BY c.rank ASC        
                ";
            
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            
            $data = self::normalizeCategoryChildTree($items);
            
            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }
    
    public static function normalizeCategoryChildTree($items) 
    {
        $treeMap = ProductCategory::getTreeMap();
        $data = [];
        
        foreach ($items as $item) {
            $itemInfo = $treeMap[$item['category_id']] ?? false;
            if (!$itemInfo) {
                continue;
            }
            
            if ($itemInfo['level'] === 2) {
                //attach parent category
                $parentInfo = $treeMap[$itemInfo['parent_id']] ?? false;
                if (!$parentInfo) {
                    continue;
                }
                $data[$parentInfo['id']] = [
                    'id' => $parentInfo['id'],
                    'title' => $parentInfo['title'],
                    'url' => $parentInfo['url'],
                ];
                // set children
                $data[$parentInfo['id']]['children'][$itemInfo['id']] = [
                    'id' => $itemInfo['id'],
                    'title' => $itemInfo['title'],
                    'url' => $itemInfo['url'],
                ];
            } elseif ($itemInfo['level'] === 1) {
                $data[$itemInfo['id']] = [
                    'id' => $itemInfo['id'],
                    'title' => $itemInfo['title'],
                    'url' => $itemInfo['url'],
                ];
            }
        }
        
        return $data;
    }
    
    public static function normalizeChildSubTreeByCategory($items, $rootCategoryId) 
    {
        $treeMap = ProductCategory::getTreeMap();
        $data = [];
        
        $rootInfo = $treeMap[$rootCategoryId];
        //d($rootInfo, 0);
        
        foreach ($items as $item) {
            $itemInfo = $treeMap[$item['category_id']] ?? false;
            
            if ($rootInfo['level'] === 1) {
            
                if (!$itemInfo || $itemInfo['level'] === 1) {
                    continue;
                }

                if ($itemInfo['level'] === 3) {
                    //attach parent category
                    $parentInfo = $treeMap[$itemInfo['parent_id']] ?? false;
                    if (!$parentInfo) {
                        continue;
                    }
                    $data[$parentInfo['id']] = [
                        'id' => $parentInfo['id'],
                        'title' => $parentInfo['title'],
                        'url' => $parentInfo['url'],
                    ];
                    // set children
                    $data[$parentInfo['id']]['children'][$itemInfo['id']] = [
                        'id' => $itemInfo['id'],
                        'title' => $itemInfo['title'],
                        'url' => $itemInfo['url'],
                    ];
                } elseif ($itemInfo['level'] === 2) {
                    $data[$itemInfo['id']] = [
                        'id' => $itemInfo['id'],
                        'title' => $itemInfo['title'],
                        'url' => $itemInfo['url'],
                    ];
                }
            } elseif ($rootInfo['level'] === 2) {
                if ($itemInfo['level'] === 3 && $itemInfo['parent_id'] == $rootInfo['id']) {
                    $data[$itemInfo['id']] = [
                        'id' => $itemInfo['id'],
                        'title' => $itemInfo['title'],
                        'url' => $itemInfo['url'],
                    ];
                }
            }
        }
        
        return $data;
    }
    
    public static function getMakeByAliasAndCategoryId($categoryId, $makeAlias)
    {
        $key = Tags::TAG_PRODUCT_CATEGORY_MODEL . 'getMakeByAliasAndCategoryId' . $categoryId . '_' . $makeAlias;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            
            $sql = "SELECT 
						k.id AS id,
						k.title AS title,
						k.alias AS alias
					FROM product_category_model AS cm
					LEFT JOIN auto_model AS m ON cm.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
					WHERE 
						k.is_active = 1 AND 
						k.is_deleted = 0 AND
						m.is_active = 1 AND 
						m.is_deleted = 0 AND
						cm.is_active = 1 AND 
						cm.category_id = {$categoryId} AND
						k.alias = '{$makeAlias}'
					";
            
            $row = Yii::app()->db->createCommand($sql)->queryRow();
            $data = [];
            if (!empty($row)) {
                $row['url'] = '/' . $row['alias'] . '/';
                $data = $row;
            }
            
            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }
    
    public static function getModelByAliasAndMakeIdAndCategoryId($categoryId, $makeId, $modelAlias)
    {
        $key = Tags::TAG_PRODUCT_CATEGORY_MODEL . 'getModelByMakeIdAndCategoryId' . $categoryId . '_' . $categoryId . '_' . $modelAlias;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            
            $sql = "SELECT 
						m.id AS id,
						m.title AS title,
						m.alias AS alias,
						m.image_ext AS image_ext,
                        cm.meta_h1 AS meta_h1,
                        cm.meta_title AS meta_title,
                        cm.meta_description AS meta_description,
                        cm.meta_keywords AS meta_keywords,
                        cm.header_text AS header_text,
                        cm.footer_text AS footer_text
					FROM product_category_model AS cm
					LEFT JOIN auto_model AS m ON cm.model_id = m.id
					WHERE 
						m.is_active = 1 AND 
						m.is_deleted = 0 AND
						m.make_id = {$makeId} AND
						cm.is_active = 1 AND 
						cm.category_id = {$categoryId} AND 
						m.alias = '{$modelAlias}' 
					";
            
            $row = Yii::app()->db->createCommand($sql)->queryRow();
            $data = [];
            if (!empty($row)) {
                $data = $row;
            }
            
            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }
    
    public static function getModelsByMakeIdAndCategoryIdsWithChildCategories($makeId, $categoryIds, $level = 2)
    {
        $key = Tags::TAG_MODEL . '__getModelsByMakeIdAndCategoryIdsWithChildCategories_' . $makeId . '_' . serialize($categoryIds) . '_' . $level;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $treeMap = ProductCategory::getTreeMap();
            
            $sql = "SELECT 
                    m.id AS model_id,
                    m.alias AS model_alias,
                    m.image_ext AS model_image_ext,
                    m.title AS model_title,
                    cm.category_id AS category_id,
                    cm.meta_h1 AS meta_h1,
                    cm.id AS cm_id,
                    (SELECT y.file_name 
                        FROM auto_model_year AS y
                        WHERE 
                            m.id = y.model_id AND 
                            y.file_name != ''
                        ORDER BY y.`year` DESC 
                        LIMIT 1) AS file_name
                FROM product_category_model AS cm
                LEFT JOIN auto_model AS m ON cm.model_id = m.id
                LEFT JOIN product_category AS c ON cm.category_id = c.id
                WHERE 
                    c.is_active = 1 AND 
                    cm.is_active = 1 AND 
                    m.is_active = 1 AND 
                    m.is_deleted = 0 AND 
                    m.make_id = {$makeId} AND 
                    cm.category_id IN(" . implode(",", $categoryIds) . ")
                ORDER BY m.title ASC        
                ";
            
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            
            $data = [];
            foreach ($rows as $row) {
                $data[$row['model_id']]['cm_id'] = $row['cm_id'];
                $data[$row['model_id']]['meta_h1'] = $row['meta_h1'];
                $data[$row['model_id']]['title'] = $row['model_title'];
                $data[$row['model_id']]['alias'] = $row['model_alias'];
                
                if (empty($data[$row['model_id']]['image'])) {
                    $data[$row['model_id']]['image'] = AutoModelYear::getThumbByFile($row['file_name'], 150, null, 'resize');
                }
                
                $categoryInfo = $treeMap[$row['category_id']];
                
                if ($level === 2) {
                    if ($categoryInfo['level'] === 2) {
                        $data[$row['model_id']]['categories'][$row['category_id']] = [
                            'title' => $categoryInfo['title'],
                            'url' => $categoryInfo['url'],
                        ];
                    } elseif ($categoryInfo['level'] === 3) {
                        $subParent = $treeMap[$categoryInfo['parent_id']];
                        $data[$row['model_id']]['categories'][$categoryInfo['parent_id']] = [
                            'title' => $subParent['title'],
                            'url' => $subParent['url'],
                        ];

                    }
                } elseif ($level === 3 && $categoryInfo['level'] === 3) {
                     $data[$row['model_id']]['categories'][$categoryInfo['title']] = [
                        'title' => $categoryInfo['title'],
                        'url' => $categoryInfo['url'],
                    ];
                }
            }
            
            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }
	
    public static function getRootItemsByMakeId($makeId)
    {
        $key = Tags::TAG_MODEL . '_getRootItemsByMakeId__' . $makeId;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $treeMap = ProductCategory::getTreeMap();
            
            $sql = "SELECT 
                    cm.category_id AS category_id
                FROM product_category_model AS cm
                LEFT JOIN auto_model AS m ON cm.model_id = m.id
                LEFT JOIN product_category AS c ON cm.category_id = c.id
                WHERE 
                    cm.is_active = 1 AND 
                    m.is_active = 1 AND 
                    m.is_deleted = 0 AND 
                    m.make_id = {$makeId} AND
                    c.is_active = 1 AND    
                    c.parent_id IS NULL    
                ORDER BY c.rank ASC        
                ";
            
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            
            $data = [];
            foreach ($rows as $row) {
                $categoryInfo = $treeMap[$row['category_id']];
                
                $data[$categoryInfo['id']] = [
                    'id' => $categoryInfo['title'],
                    'title' => $categoryInfo['title'],
                    'url' => $categoryInfo['url'],
                ];
            }
            
            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }
	
    public static function getChildItemsByParentIdMakeId($parentId, $makeId)
    {
        $key = Tags::TAG_MODEL . 'getChildItemsByParentIdMakeId' . $makeId . '_' . $parentId;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {
            $treeMap = ProductCategory::getTreeMap();
            
            $sql = "SELECT 
                    cm.category_id AS category_id
                FROM product_category_model AS cm
                LEFT JOIN auto_model AS m ON cm.model_id = m.id
                LEFT JOIN product_category AS c ON cm.category_id = c.id
                WHERE 
                    cm.is_active = 1 AND 
                    m.is_active = 1 AND 
                    m.is_deleted = 0 AND 
                    m.make_id = {$makeId} AND
                    c.is_active = 1 AND    
                    c.parent_id IS NOT NULL    
                ORDER BY c.rank ASC        
                ";
            
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            
            $data = [];
            foreach ($rows as $row) {
                $categoryInfo = $treeMap[$row['category_id']];
                if ($categoryInfo['level'] !== 2) {
                    continue;
                }
                
                $data[$categoryInfo['id']] = [
                    'id' => $categoryInfo['title'],
                    'title' => $categoryInfo['title'],
                    'url' => $categoryInfo['url'],
                ];
            }
            
            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL, 
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }

    public static function getMakes()
    {
        $key = Tags::TAG_MODEL . 'getMakes';
        $data = Yii::app()->cache->get($key);

        if ($data == false) {

            $sql = "SELECT 
                    k.id AS id,
                    k.alias AS alias,
                    k.title AS title
                FROM product_category_model AS cm
                LEFT JOIN auto_model AS m ON cm.model_id = m.id
                LEFT JOIN auto_make AS k ON m.make_id = k.id
                LEFT JOIN product_category AS c ON cm.category_id = c.id
                WHERE 
                    cm.is_active = 1 AND 
                    m.is_active = 1 AND 
                    m.is_deleted = 0 AND 
                    k.is_active = 1 AND 
                    k.is_deleted = 0 AND 
                    c.is_active = 1 
                GROUP BY k.id        
                ORDER BY k.title";

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

    public static function getModelsByMake($makeId)
    {
        $key = Tags::TAG_MODEL . 'getModelsByMake' . $makeId;
        $data = Yii::app()->cache->get($key);

        if ($data == false) {

            $sql = "SELECT 
                    m.id AS id,
                    m.alias AS alias,
                    m.title AS title,
                    (SELECT y.file_name 
                        FROM auto_model_year AS y
                        WHERE 
                            m.id = y.model_id AND 
                            y.file_name != ''
                        ORDER BY y.`year` DESC 
                        LIMIT 1) AS file_name                    
                FROM product_category_model AS cm
                LEFT JOIN auto_model AS m ON cm.model_id = m.id
                LEFT JOIN product_category AS c ON cm.category_id = c.id
                WHERE 
                    cm.is_active = 1 AND 
                    m.is_active = 1 AND 
                    m.is_deleted = 0 AND 
                    m.make_id = $makeId AND
                    c.is_active = 1 
                GROUP BY m.id        
                ORDER BY m.title";

            $data = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($data as & $row) {
                if (empty($row['file_name'])) {
                    $row['image'] = AutoModelYear::getThumbByFile($row['file_name'], 150, null, 'resize');
                }
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_MODEL_YEAR,
                Tags::TAG_MAKE,
                Tags::TAG_MODEL,
                Tags::TAG_PRODUCT_CATEGORY,
                Tags::TAG_PRODUCT_CATEGORY_MODEL
            ));
        }

        return $data;
    }

}