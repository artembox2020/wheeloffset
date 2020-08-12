<?php

class ProductBrand extends CActiveRecord
{
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
		return 'product_brand';
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
            array('id, alias', 'safe'),
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
			
        return parent::afterSave();
    }	
    
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT_BRAND);
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
	
    public function searchWithProducts()
    {
        $where = [];
        if (!empty($this->title)) {
            $where[] = "t.title LIKE '%{$this->title}%'";
        }
        $where = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT 
            t.id AS id,
            t.title AS title,
            (SELECT
                        COUNT(*) 
                        FROM product AS p
                        WHERE t.id = p.brand_id
                    ) AS count_products,
            IF((SELECT
                        COUNT(*) 
                        FROM product AS p
                        WHERE t.id = p.brand_id
                    ), (SELECT
                        COUNT(DISTINCT p.id) 
                        FROM product AS p
                        LEFT JOIN product_compatibility AS pc ON p.id = pc.product_id
                        WHERE pc.trim_id IS NOT NULL AND t.id = p.brand_id
                     ) / (SELECT
                        COUNT(*) 
                        FROM product AS p
                        WHERE t.id = p.brand_id
                    ) * 100, 0) AS percent         
            FROM product_brand AS `t`  
        {$where}";    
        
        $count=Yii::app()->db->createCommand("SELECT COUNT(*) FROM product_brand AS t {$where}")->queryScalar();
        $dataProvider=new CSqlDataProvider($sql, array(
            'totalItemCount'=>$count,
            'sort'=>array(
                'attributes'=>array(
                    'id', 'title', 'count_products', 'percent',
                ),
            ),
            'pagination'=>array(
                'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
            ),
        ));

        return $dataProvider;
    }
    
    public static function getEbayCatIdsByCatId($categoryId): array
    {
        $categoryId = (int) $categoryId;
        return Yii::app()->db->createCommand("SELECT id FROM product_ebay_category WHERE category_id = {$categoryId}")->queryColumn();  
    }
    
    public function searchItemsInCategory($categoryId)
    {
        $ebayCatdIds = self::getEbayCatIdsByCatId($categoryId);
        $ebayCatdIds[] = 0;
        $ebayCatdIds = implode(',', $ebayCatdIds);
     
        $where = [];
        if (!empty($this->title)) {
            $where[] = "t.title LIKE '%{$this->title}%'";
        }
        if (!empty($this->id)) {
            $where[] = "b.id = " . (int) $this->id;
        }
        $where = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $whereCount = !empty($where) ? 'AND ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT 
            b.id AS id,
            b.title AS title,
            (SELECT
                        COUNT(*) 
                        FROM product AS p
                        LEFT JOIN product_vs_ebay_category AS vs ON p.id = vs.product_id
                        WHERE b.id = p.brand_id AND vs.ebay_category_id IN ({$ebayCatdIds})
                    ) AS count_products
        FROM product_brand AS `b`  
        {$where}
        HAVING count_products > 0";    
        
        $count=Yii::app()->db->createCommand("
            SELECT 
                COUNT(DISTINCT b.id)
            FROM product_brand AS `b`
            LEFT JOIN product AS p ON b.id = p.brand_id
            LEFT JOIN product_vs_ebay_category AS vs ON p.id = vs.product_id AND vs.ebay_category_id IN ({$ebayCatdIds})
            WHERE vs.ebay_category_id IS NOT NULL {$whereCount}
        ")->queryScalar();
        
        $dataProvider=new CSqlDataProvider($sql, array(
            'totalItemCount'=>$count,
            'sort'=>array(
                'attributes'=>array(
                    'id', 'title', 'count_products',
                ),
            ),
            'pagination'=>array(
                'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
            ),
        ));

        return $dataProvider;
    }
    
    public function searchWithProductsAndByCategories($id)
    {
        $where = [];
        if (!empty($this->title)) {
            $where[] = "c.title LIKE '%{$this->title}%'";
        }
        $where = !empty($where) ? 'AND ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT 
                c.id AS id,
                c.title AS title,
                par.title AS parent_title,
                COUNT(*) AS count_products
            FROM `product_vs_ebay_category` AS vs
            LEFT JOIN product AS p ON vs.product_id = p.id
            LEFT JOIN product_ebay_category AS c ON vs.ebay_category_id = c.id
            LEFT JOIN product_ebay_category AS par ON c.parent_id = par.id
            WHERE p.brand_id = {$id} {$where}
            GROUP BY c.id 
        ";    

        $dataProvider=new CSqlDataProvider($sql, array(
            'sort'=>array(
                'attributes'=>array(
                    'id','title', 'count_products', 'parent_title',
                ),
            ),
            'pagination'=>false, 
        ));

        return $dataProvider;
    }
    
}