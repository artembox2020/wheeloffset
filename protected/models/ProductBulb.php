<?php

class ProductBulb extends CActiveRecord
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
		return 'product_bulb';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('part', 'required'),
			array('id', 'safe'),
			array('alias', 'unique'),
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
		if (empty($this->alias) && !empty($this->part)) { 
			$this->alias = $this->part;
		}
		
		$this->alias = TextHelper::urlSafe($this->alias);
	}	

	
	public function afterSave()
	{
		$this->clearCache();
		
		return parent::afterSave();
	}	
	
	public function afterDelete()
	{
		return parent::afterDelete();
	}	
	
	private function clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT_BULB);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
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

		$criteria->compare('id', $this->id);
		$criteria->compare('part', $this->part, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
    }		
	
	public static function getItems($exceptIds = [])
	{
		$key = Tags::TAG_PRODUCT_BULB . 'getItems_' . serialize($exceptIds);
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = [];
			
            $where = '';
            if (!empty($exceptIds)) {
                $where = "WHERE id NOT IN(". implode(',', $exceptIds) .")";
            }
            
			$data = Yii::app()->db->createCommand("SELECT part, alias FROM " . self::model()->tableName() . " {$where}")->queryAll();
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_BULB));
		}
		
		return $data;
	}

	public static function getItemByAlias($alias)
	{
		$key = Tags::TAG_PRODUCT_BULB . '_getItemByAlias_' . $alias;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = [];
			
			$row = Yii::app()->db->createCommand("SELECT * FROM " . self::model()->tableName() . " WHERE alias = '{$alias}'")->queryRow();
			if (!empty($row)) {
				$data = $row;
			}
 			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_BULB));
		}
		
		return $data;
	}

	public static function getItemsByYear($model_year_id)
	{
		$key = Tags::TAG_PRODUCT_BULB . '_getItemsByYear' . $model_year_id;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = [];
			
            
            $sql = "
                SELECT 
                    b.id AS bulb_id,
                    b.alias AS bulb_alias,
                    b.part AS part,
                    p.title AS position,
                    p.id AS position_id,
                    p.short_title AS position_short_title,
                    p.alias AS position_alias,
                    p.class AS class,
                    GROUP_CONCAT(DISTINCT i.type) AS types
                FROM auto_model_year_bulb AS vs
                LEFT JOIN product_bulb AS b ON vs.bulb_id = b.id
                LEFT JOIN product_bulb_position AS p ON vs.position_id = p.id
                LEFT JOIN product_bulb_items AS i ON b.id = i.bulb_id
                WHERE vs.model_year_id = {$model_year_id}
                GROUP BY p.id, b.id
                ORDER BY p.type ASC, p.id ASC
            ";
            
            $data = Yii::app()->db->createCommand($sql)->queryAll();
            
			Yii::app()->cache->set($key, $data, 0, new Tags(
                Tags::TAG_PRODUCT_BULB_POSITION, 
                Tags::TAG_MODEL_YEAR
            ));
		}
		
		return $data;
	}	
	
	public static function getYears()
	{
		$key = Tags::TAG_PRODUCT_BULB . '_getYears_';
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = [];
			$sql = "SELECT  
						y.year AS v
					FROM auto_model_year AS y
					LEFT JOIN auto_model AS m ON y.model_id = m.id 
					LEFT JOIN auto_make AS k ON m.make_id = k.id 
					WHERE 
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						m.is_active = 1 AND
						m.is_bulb = 1 AND
						m.is_deleted = 0 AND
						k.is_active = 1 AND
						k.is_deleted = 0
					GROUP BY y.year
					ORDER BY v DESC
					";
			
			
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			
			foreach ($rows as $row) {
				$data[] = $row['v'];
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_BULB, Tags::TAG_MODEL_YEAR));
		}
		
		return $data;
	}	
	
	
	public static function getMakesByYear($year)
	{
		$key = Tags::TAG_PRODUCT_BULB . '_getMakesByYear' . $year;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = [];
			$sql = "SELECT  
						k.alias AS alias,
						k.title AS title
					FROM auto_model_year AS y
					LEFT JOIN auto_model AS m ON y.model_id = m.id 
					LEFT JOIN auto_make AS k ON m.make_id = k.id 
					WHERE 
						y.year = {$year} AND 
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						m.is_active = 1 AND
						m.is_bulb = 1 AND
						m.is_deleted = 0 AND
						k.is_active = 1 AND
						k.is_deleted = 0						
					GROUP BY k.id
					ORDER BY title
					";
			
			
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			
			foreach ($rows as $row) {
				$data[$row['alias']] = $row['alias'];
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR, Tags::TAG_MODEL, Tags::TAG_MAKE));
		}
		
		return $data;
	}	
	
	public static function getModelsByMake($make_id, $year)
	{
		$key = Tags::TAG_PRODUCT_BULB . 'getModelsByMake_' . $make_id . '_' . $year;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = [];
			$sql = "SELECT  
						m.alias AS alias,
						m.title AS title
					FROM auto_model_year AS y
					LEFT JOIN auto_model AS m ON y.model_id = m.id 
					LEFT JOIN auto_make AS k ON m.make_id = k.id 
					WHERE 
						y.year = {$year} AND
						m.make_id = {$make_id} AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						m.is_active = 1 AND
						m.is_bulb = 1 AND
						m.is_deleted = 0 AND
						k.is_active = 1 AND
						k.is_deleted = 0						
					GROUP BY m.id
					ORDER BY title
					";
			
			
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			
			foreach ($rows as $row) {
				$data[$row['alias']] = $row['alias'];
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_MODEL_YEAR, Tags::TAG_MODEL, Tags::TAG_MAKE));
		}
		
		return $data;
	}	
    
    public static function resolvePartName($value)
    {
        if ($value === 'NEON') {
            $value = 'LED';
        } elseif (substr_count($value, 'LED') && $value !== 'LED') {
            $value = str_replace('LED', ' LED', $value);
        }
        return $value;
    }

    public static function getItem($attributes)
    {
        $model = self::model()->findByAttributes($attributes);
        if (empty($model)) {
            $model = new self;
            $model->attributes = $attributes;
            $model->save();
        }
        return $model;
    }
    
	public static function getList()
	{
		return CHtml::listData(self::model()->findAll(['order' => 'part ASC']), 'id', 'part');
	}
    
}