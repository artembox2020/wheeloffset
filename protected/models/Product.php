<?php

class Product extends CActiveRecord
{
    public $ebay_category_ids = [];
    public $type_ids = [];
    public $subtype_ids = [];
    
    public $model_id;
    public $category_id;
    
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
		return 'product';
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
			array('rank, is_active', 'numerical', 'integerOnly' => true),
            array('alias, epid, manufacture_part_number, brand_id, type_id, subtype_id, '
                . 'ebay_category_ids, subtype_ids, type_ids', 'safe'),
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
			
    public function afterDelete() 
	{
		$this->_clearCache();	
			
        return parent::afterDelete();
    }	
    
    public function afterSave() 
	{
		$this->_clearCache();	
			
        if (!empty($this->ebay_category_ids)) {
            foreach ($this->ebay_category_ids as $ebay_category_id) {
                $ebay_category_id = (int) $ebay_category_id;
                if (!$ebay_category_id) {
                    continue;;
                }
                
                $sql = "INSERT INTO product_vs_ebay_category (product_id, ebay_category_id)
                        VALUES ({$this->id}, {$ebay_category_id})";
                Yii::app()->db->createCommand($sql)->execute();        
            }
        }
         
        if (!empty($this->subtype_ids)) {
            foreach ($this->subtype_ids as $subtype_id) {
                $subtype_id = (int) $subtype_id;
                if (!$subtype_id) {
                    continue;;
                }

                $sql = "INSERT INTO product_vs_subtype (product_id, subtype_id)
                        VALUES ({$this->id}, {$subtype_id})";
                Yii::app()->db->createCommand($sql)->execute();        
            }
        }
         
        if (!empty($this->type_ids)) {
            foreach ($this->type_ids as $type_id) {
                $type_id = (int) $type_id;
                if (!$type_id) {
                    continue;;
                }

                $sql = "INSERT INTO product_vs_type (product_id, type_id)
                        VALUES ({$this->id}, {$type_id})";
                Yii::app()->db->createCommand($sql)->execute();        
            }
        }
         
        return parent::afterSave();
    }	
    
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT);
	}
     
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'is_active' => Yii::t('admin', 'Published'),
			'rank' => Yii::t('admin', 'Rank'),
			'title' => Yii::t('admin', 'Title'),
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
		$criteria->compare('title',$this->title, true);
		$criteria->compare('rank',$this->rank);
        
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
    
    public static function getItemsByCategoriesAndModel(array $ebayCategoryIds, int $model_id)
    {
        if (empty($ebayCategoryIds)) {
            return [];
        }
        
        $subTypeIds = ProductEbayCategory::getSubTypesIdsByCatIds($ebayCategoryIds);
        
        $key = Tags::TAG_PRODUCT . 'getItemsByCategoriesAndModel' . serialize($ebayCategoryIds) . '_' . serialize($subTypeIds) . '_' . $model_id;
        $data = Yii::app()->cache->get($key);
        if ($data == false) {
            
            //$ebayCategoryIds = [21];
            
            $where = "eb_my.model_id = {$model_id} AND vs_ec.ebay_category_id IN (" . implode(',', $ebayCategoryIds) . ")";
            if (!empty($subTypeIds)) {
                $where.= " AND subtype.id IN(". implode(',', $subTypeIds).")";
            }
            
            $sql = "SELECT 
                    p.id,
                    p.title,
                    p.manufacture_part_number,
                    ph.image AS image,
                    b.title AS brand,
                    GROUP_CONCAT(DISTINCT type.title ORDER BY type.title  SEPARATOR', ') AS types,
                    GROUP_CONCAT(DISTINCT subtype.title ORDER BY subtype.title  SEPARATOR', ') AS subtypes,
                    GROUP_CONCAT(DISTINCT eb_my.year ORDER BY eb_my.year  SEPARATOR',') AS years
                FROM product AS p
                LEFT JOIN product_vs_ebay_category AS vs_ec ON p.id = vs_ec.product_id
                LEFT JOIN product_compatibility AS vs_cb ON p.id = vs_cb.product_id
                LEFT JOIN ebay_auto_trim AS eb_trim ON vs_cb.trim_id = eb_trim.id
                LEFT JOIN ebay_auto_model_year AS eb_my ON eb_trim.model_year_id = eb_my.id
                LEFT JOIN product_brand AS b ON p.brand_id = b.id
                LEFT JOIN product_photo AS ph ON p.id = ph.product_id
                LEFT JOIN product_vs_type AS vs_type ON p.id = vs_type.product_id
                LEFT JOIN product_type AS type ON vs_type.type_id = type.id
                LEFT JOIN product_vs_subtype AS vs_subtype ON p.id = vs_subtype.product_id
                LEFT JOIN product_subtype AS subtype ON vs_subtype.subtype_id = subtype.id
                WHERE {$where}
                GROUP BY p.id 
                ORDER BY eb_my.year DESC";
            
            $data = Yii::app()->db->createCommand($sql)->queryAll();    
            
            foreach ($data as & $item) {
                if (!empty($item['image'])) {
                    $item['image'] = ProductPhoto::getThumb($item['image'], 150, null, 'resize');
                }
            }
            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT, Tags::TAG_PRODUCT_PHOTO, Tags::TAG_PRODUCT_EBAY_CATEGORY));
        }

        return $data;
    }
    
    public function searchByBrandAndCategory($brandId, $categoryId)
    {
        $where = [];
        if (!empty($this->title)) {
            $where[] = "p.title LIKE '%{$this->title}%'";
        }
        $where = !empty($where) ? 'AND ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT 
                p.id AS id,
                p.title AS title,
                GROUP_CONCAT(DISTINCT(`type`.title) SEPARATOR ', ') AS types,
                GROUP_CONCAT(DISTINCT(`subtype`.title) SEPARATOR ', ') AS subtypes
            FROM `product_vs_ebay_category` AS vs
            LEFT JOIN product AS p ON vs.product_id = p.id
            LEFT JOIN product_ebay_category AS c ON vs.ebay_category_id = c.id
            LEFT JOIN product_vs_type AS vs_type ON vs.product_id = vs_type.product_id
            LEFT JOIN product_type AS `type` ON vs_type.type_id = `type`.id
            LEFT JOIN product_vs_subtype AS vs_subtype ON vs.product_id = vs_subtype.product_id
            LEFT JOIN product_subtype AS `subtype` ON vs_subtype.subtype_id = `subtype`.id
            WHERE p.brand_id = {$brandId} AND vs.ebay_category_id = {$categoryId} {$where}
            GROUP BY vs.product_id
        ";    

        $count=Yii::app()->db->createCommand("
            SELECT 
                COUNT(DISTINCT vs.product_id)
            FROM `product_vs_ebay_category` AS vs
            LEFT JOIN product AS p ON vs.product_id = p.id
            LEFT JOIN product_ebay_category AS c ON vs.ebay_category_id = c.id
            WHERE p.brand_id = {$brandId} AND vs.ebay_category_id = {$categoryId} {$where}
        "
        )->queryScalar();
        
        $dataProvider=new CSqlDataProvider($sql, array(
            'totalItemCount'=>$count,
            'sort'=>array(
                'attributes'=>array(
                    'id','title',
                ),
            ),
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
        ));

        return $dataProvider;
    }
    public function searchByModelAndCategory()
    {
        $where = [];
        if (!empty($this->title)) {
            $where[] = "p.title LIKE '%{$this->title}%'";
        }
        $categoryId = (int) $this->category_id;
       
        if ($categoryId) {
            $where[] = "vs_cat.ebay_category_id = {$categoryId} ";
        }
        
        $where = !empty($where) ? 'AND ' . implode(' AND ', $where) : '';
        
        $sql = "                        
            SELECT
                p.id AS id,
                p.title AS title,
                GROUP_CONCAT(DISTINCT(`type`.title) SEPARATOR ', ') AS types,
                GROUP_CONCAT(DISTINCT(`subtype`.title) SEPARATOR ', ') AS subtypes
            FROM product_compatibility AS `com`
            LEFT JOIN ebay_auto_trim AS tr ON com.trim_id = tr.id
            LEFT JOIN product AS p ON com.product_id = p.id
            LEFT JOIN ebay_auto_model_year AS y ON tr.model_year_id = y.id
            LEFT JOIN ebay_auto_model AS m ON y.model_id = m.id
            LEFT JOIN product_vs_ebay_category AS vs_cat ON com.product_id = vs_cat.product_id
            LEFT JOIN product_vs_type AS vs_type ON com.product_id = vs_type.product_id
            LEFT JOIN product_type AS `type` ON vs_type.type_id = `type`.id
            LEFT JOIN product_vs_subtype AS vs_subtype ON com.product_id = vs_subtype.product_id
            LEFT JOIN product_subtype AS `subtype` ON vs_subtype.subtype_id = `subtype`.id
            WHERE m.id = {$this->model_id} {$where}
            GROUP BY com.product_id    
        ";
       
        $count=Yii::app()->db->createCommand("SELECT
                COUNT(DISTINCT com.product_id)
            FROM product_compatibility AS `com`
            LEFT JOIN ebay_auto_trim AS tr ON com.trim_id = tr.id
            LEFT JOIN product AS p ON com.product_id = p.id
            LEFT JOIN ebay_auto_model_year AS y ON tr.model_year_id = y.id
            LEFT JOIN ebay_auto_model AS m ON y.model_id = m.id
            LEFT JOIN product_vs_ebay_category AS vs_cat ON com.product_id = vs_cat.product_id
            WHERE m.id = {$this->model_id} {$where}"
        )->queryScalar();
        
        $dataProvider=new CSqlDataProvider($sql, array(
            'totalItemCount'=>$count,
            'sort'=>array(
                'attributes'=>array(
                    'id','title',
                ),
            ),
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
        ));

        return $dataProvider;
    }
	
}