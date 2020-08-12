<?php

class ProductEbayCategory extends CActiveRecord
{
    public $post_subtypes;
    
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
		return 'product_ebay_category';
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
			array('alias, post_subtypes', 'safe'),
			array('parent_id, ebay_id, category_id', 'safe'),
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => Yii::t('admin', 'Title'),
			'parent_id' => Yii::t('admin', 'Parent category'),
            'ebay_id' => 'Ebay ID',
            'category_id' => 'AutoTK CategoryID',
		);
	}
    
    public function beforeSave()
    {
        if (trim($this->ebay_id) === '') {
            $this->ebay_id = null;
        }
        
        return parent::beforeSave();
    }
    
	public function afterSave()
	{	
        if (!empty($this->post_subtypes)) {
            Yii::app()->db->createCommand("DELETE FROM product_ebay_category_vs_subtypes WHERE category_id = $this->id")->execute();
            $rows = [];
            foreach ($this->post_subtypes as $subtype_id) {
                $rows[] = "({$this->id}, {$subtype_id})";                
            }
            if (!empty($rows)) {
                $sql = "INSERT INTO product_ebay_category_vs_subtypes (category_id, subtype_id) VALUES " . implode(', ', $rows) . ";"; 
                Yii::app()->db->createCommand($sql)->execute();
            }
        }
        
		$this->_clearCache();          
    }

    public function afterDelete() 
	{
		$this->_deleteImage();
		$this->_clearCache();	
			
        return parent::afterDelete();
    }	
    
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT_EBAY_CATEGORY);
	}
    
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'parent' => array(self::BELONGS_TO, 'ProductEbayCategory', 'parent_id', 'together' => true,),
            'category' => array(self::BELONGS_TO, 'ProductCategory', 'category_id', 'together' => true,),
        );
    }
    
	public static function getList($level = 2)
	{
        $criteria=new CDbCriteria;
        $criteria->condition = 'parent_id IS NULL';
        
        $items = self::model()->findAll($criteria);
        $data = [];
        
        foreach ($items as $item) {
            $data[$item->id] = $item->title;
            if ($level >= 2) {
                $criteria=new CDbCriteria;
                $criteria->condition = 'parent_id = ' . $item->id;
                $children = self::model()->findAll($criteria);
                foreach ($children as $child) {
                    $data[$child->id] = ' -- ' . $child->title;
                    
                    if ($level >= 3) {
                        $criteria=new CDbCriteria;
                        $criteria->condition = 'parent_id = ' . $child->id;
                        $subs = self::model()->findAll($criteria);
                        foreach ($subs as $sub) {
                            $data[$sub->id] = ' ---- ' . $sub->title;
                            
                            if ($level >= 4) {
                                $criteria=new CDbCriteria;
                                $criteria->condition = 'parent_id = ' . $sub->id;
                                $fourSubs = self::model()->findAll($criteria);
                                foreach ($fourSubs as $fourSub) {
                                    $data[$fourSub->id] = ' ----- ' . $fourSub->title;
                                }
                            }
                        }
                    }
                }
            }
        }
		return $data;
	}

    public function getParentTitle()
    {
        $titles = [];
        if (!empty($this->parent)) {
            $titles[] = $this->parent->title;
            
            if (!empty($this->parent->parent)) {
                $titles[] = $this->parent->parent->title;
                
                if (!empty($this->parent->parent->parent)) {
                    $titles[] = $this->parent->parent->parent->title;
                    
                    if (!empty($this->parent->parent->parent->parent)) {
                        $titles[] = $this->parent->parent->parent->parent->title;
                    }
                }
            }
        }
        
        return implode(' / ', array_reverse($titles));
    }

    
    public function getCategoryTitle()
    {
        $titles = [];
        if (!empty($this->category)) {
            $titles[] = $this->category->title;
            
            if (!empty($this->category->parent)) {
                $titles[] = $this->category->parent->title;
                
                if (!empty($this->category->parent->parent)) {
                    $titles[] = $this->category->parent->parent->title;
                }
            }
        }
        
        return implode(' / ', array_reverse($titles));
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
		$criteria->compare('ebay_id',$this->ebay_id, true);
		$criteria->compare('title',$this->title, true);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('category_id',$this->category_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}    
    
    public static function findOrCreateByBreadcrumbs($value, $ebayId)
    {
        $model = null;
        $parts = explode(':', $value);
        foreach ($parts as $index => $title) {
            $parentId = $model ? $model->id : null;
            $model = self::findOrCreateByTitleAndParentId(
                $title, $parentId, 
                (count($parts) - 1 === $index) ? $ebayId : null
            );
        }
        
        return $model;
    }

    public static function findOrCreateByTitleAndParentId($title, $parentId = null, $ebayId = null)
    {
        $wheres = ["title = '$title'"];
        if ($parentId === null) {
            $wheres[] = 'parent_id IS NULL';
        } else {
            $wheres[] = "parent_id = " . (int) $parentId;            
        }
        
        $criteria = new CDbCriteria;
        $criteria->condition = implode(' AND ', $wheres);
        $model = self::model()->find($criteria);
        if (empty($model)) {
            $model = new self;
            $model->title = $title;
            $model->parent_id = $parentId;
            $model->ebay_id = $ebayId;
            $model->save();
        } elseif ($model->ebay_id != $ebayId) {
            $model->ebay_id == $ebayId;
            $model->save();
        }
        
        return $model;
    }
    
    public function getSubTypesIds($is_active = null): array
    {
        $sql = "SELECT subtype_id FROM product_ebay_category_vs_subtypes WHERE category_id = $this->id";
        return Yii::app()->db->createCommand($sql)->queryColumn();
    }
    
    public static function getSubTypesIdsByCatIds($ids)
	{
        if (empty($ids)) {
            return [];
        }
        
		$key = Tags::TAG_PRODUCT_EBAY_CATEGORY . 'getSubTypesIdsByCatIds' . serialize($ids);
		$data = Yii::app()->cache->get($key);
        
        if ($data === false) {
            $sql = "SELECT subtype_id FROM product_ebay_category_vs_subtypes WHERE category_id IN(". implode(',', $ids).")";
            $data = Yii::app()->db->createCommand($sql)->queryColumn();
            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_EBAY_CATEGORY));
        }
        return $data;
    }

    public static function getTreeMap()
	{
		$key = Tags::TAG_PRODUCT_EBAY_CATEGORY . '_getTreeMap_';
		$data = Yii::app()->cache->get($key);
		
        if ($data === false) {
            
            $mapCategoryEbay = [];
            $sql = "SELECT id, category_id FROM product_ebay_category WHERE category_id IS NOT NULL";
            foreach (Yii::app()->db->createCommand($sql)->queryAll() as $item) {
                $mapCategoryEbay[$item['category_id']][] = $item['id'];
            }
            
			$data = [];
            $criteria = new CDbCriteria;
            $items = self::model()->findAll($criteria);
		    
            foreach ($items as $root) {
                if (empty($root->parent_id)) {
                    $data[$root->id]['level'] = 1;
                    $data[$root->id]['id'] = $root->id;
                    $data[$root->id]['title'] = $root->title;
                    $data[$root->id]['ids'] = [$root->id];
                    $data[$root->id]['pds'] = [];
                    $data[$root->id]['root_id'] = null;
                    
                    foreach ($items as $child) {
                        if ($child->parent_id == $root->id) {
                            $data[$child->id]['level'] = 2;
                            $data[$child->id]['id'] = $child->id;
                            $data[$child->id]['parent_id'] = $root->id;
                            $data[$child->id]['title'] = $child->title;
                            $data[$child->id]['ids'] = [$child->id];
                            $data[$child->id]['pds'] = [$root->id];
                            $data[$child->id]['root_id'] = $root->id;
                            $data[$root->id]['ids'][] = $child->id;
                            
                            foreach ($items as $sub) {
                                if ($sub->parent_id == $child->id) {
                                    $data[$sub->id]['level'] = 3;
                                    $data[$sub->id]['id'] = $sub->id;
                                    $data[$sub->id]['parent_id'] = $child->id;
                                    $data[$sub->id]['title'] = $sub->title;
                                    $data[$sub->id]['ids'] = [$sub->id];
                                    $data[$sub->id]['pds'] = [$root->id, $child->id];
                                    $data[$sub->id]['root_id'] = $root->id;
                                    $data[$child->id]['ids'][] = $sub->id;
                                    $data[$root->id]['ids'][] = $sub->id;
                                    
                                    foreach ($items as $cld4) {
                                        if ($cld4->parent_id == $sub->id) {
                                            $data[$cld4->id]['level'] = 3;
                                            $data[$cld4->id]['id'] = $cld4->id;
                                            $data[$cld4->id]['parent_id'] = $sub->id;
                                            $data[$cld4->id]['title'] = $cld4->title;
                                            $data[$cld4->id]['ids'] = [$cld4->id];
                                            $data[$cld4->id]['pds'] = [$root->id, $child->id, $sub->id];
                                            $data[$cld4->id]['root_id'] = $root->id;
                                            $data[$sub->id]['ids'][] = $cld4->id;
                                            $data[$child->id]['ids'][] = $cld4->id;
                                            $data[$root->id]['ids'][] = $cld4->id;
                                            
                                            foreach ($items as $cld5) {
                                                if ($cld5->parent_id == $cld4->id) {
                                                    $data[$cld5->id]['level'] = 3;
                                                    $data[$cld5->id]['id'] = $cld5->id;
                                                    $data[$cld5->id]['parent_id'] = $cld4->id;
                                                    $data[$cld5->id]['title'] = $cld5->title;
                                                    $data[$cld5->id]['ids'] = [$cld5->id];
                                                    $data[$cld5->id]['pds'] = [$root->id, $child->id, $sub->id, $cld4->id];
                                                    $data[$cld5->id]['root_id'] = $root->id;
                                                    $data[$cld5->id]['ids'][] = $cld5->id;
                                                    $data[$cld4->id]['ids'][] = $cld5->id;
                                                    $data[$sub->id]['ids'][] = $cld5->id;
                                                    $data[$child->id]['ids'][] = $cld5->id;
                                                    $data[$root->id]['ids'][] = $cld5->id;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_EBAY_CATEGORY));
		}
		
		return $data;        
	}
    
    
}