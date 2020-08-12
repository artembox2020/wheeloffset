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
			array('id, app, part', 'safe'),
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

		$criteria->compare('id',$this->id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
    }		
	
	public static function getItems()
	{
		$key = Tags::TAG_PRODUCT_BULB . 'getItems_';
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			$data = Yii::app()->db->createCommand("SELECT part, app, alias FROM " . self::model()->tableName())->queryAll();
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_BULB));
		}
		
		return $data;
	}

	public static function getItemByAlias($alias)
	{
		$key = Tags::TAG_PRODUCT_BULB . '_getItemByAlias_' . $alias;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
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
		$key = Tags::TAG_PRODUCT_BULB . 'getItemsByYear' . $model_year_id;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			
			$rows = Yii::app()->db->createCommand("SELECT bulb_id FROM auto_model_year_bulb WHERE model_year_id = {$model_year_id}")->queryAll();
			$ids = [];
			foreach ($rows as $row) {
				$ids[] = $row['bulb_id'];
			}
			
			if (!empty($ids)) {
				$criteria = new CDbCriteria;
				$criteria->addInCondition('id', $ids);
				$items = self::model()->findAll($criteria);
				foreach ($items as $item) {
					$data[] = [
						'app' => $item->app,
						'part' => $item->part,
					];
				}
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_BULB, Tags::TAG_MODEL_YEAR));
		}
		
		return $data;
	}	
	
	public static function getYears()
	{
		$key = Tags::TAG_PRODUCT_BULB . 'getYears';
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			$sql = "SELECT  
						y.year AS v
					FROM auto_model_year_bulb AS b
					LEFT JOIN auto_model_year AS y ON b.model_year_id = y.id 
					LEFT JOIN auto_model AS m ON y.model_id = m.id 
					LEFT JOIN auto_make AS k ON m.make_id = k.id 
					WHERE 
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						m.is_active = 1 AND
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
		$key = Tags::TAG_PRODUCT_BULB . 'getMakesByYear' . $year;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			$sql = "SELECT  
						k.alias AS alias,
						k.title AS title
					FROM auto_model_year_bulb AS b
					LEFT JOIN auto_model_year AS y ON b.model_year_id = y.id 
					LEFT JOIN auto_model AS m ON y.model_id = m.id 
					LEFT JOIN auto_make AS k ON m.make_id = k.id 
					WHERE 
						y.year = {$year} AND 
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						m.is_active = 1 AND
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
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_BULB, Tags::TAG_MODEL_YEAR, Tags::TAG_MODEL, Tags::TAG_MAKE));
		}
		
		return $data;
	}	
	
	public static function getModelsByMake($make_id, $year)
	{
		$key = Tags::TAG_PRODUCT_BULB . 'getModelsByMake_' . $make_id . '_' . $year;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			$sql = "SELECT  
						m.alias AS alias,
						m.title AS title
					FROM auto_model_year_bulb AS b
					LEFT JOIN auto_model_year AS y ON b.model_year_id = y.id 
					LEFT JOIN auto_model AS m ON y.model_id = m.id 
					LEFT JOIN auto_make AS k ON m.make_id = k.id 
					WHERE 
						y.year = {$year} AND
						m.make_id = {$make_id} AND
						y.is_active = 1 AND
						y.is_deleted = 0 AND
						m.is_active = 1 AND
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
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_BULB, Tags::TAG_MODEL_YEAR, Tags::TAG_MODEL, Tags::TAG_MAKE));
		}
		
		return $data;
	}	
	
	
	public static function getIsssetByMake($make_id)
	{
		$key = Tags::TAG_PRODUCT_BULB . 'getIsssetByMake' . $make_id;
		$data = Yii::app()->cache->get($key);
		if ($data === false) {
			$data = array();
			$sql = "SELECT  
						y.model_id AS model_id,
						COUNT(*) AS c
					FROM auto_model_year_bulb AS b
					LEFT JOIN auto_model_year AS y ON b.model_year_id = y.id 
					LEFT JOIN auto_model AS m ON y.model_id = m.id 
					WHERE m.make_id = {$make_id}
					GROUP BY y.model_id
					";
			
			
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			
			foreach ($rows as $row) {
				$data[$row['model_id']] = $row['c'];
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_PRODUCT_BULB, Tags::TAG_MODEL_YEAR, Tags::TAG_MODEL));
		}
		
		return $data;
	}	
}