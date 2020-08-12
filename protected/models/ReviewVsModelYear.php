<?php

class ReviewVsModelYear extends CActiveRecord
{
	const MARKER_TIRES 		= 'tires';
	const MARKER_060 		= '060';
	const MARKER_HP 		= 'hp';
	const MARKER_DIMENSIONS = 'dimensions';
	
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
		return 'review_vs_model_year';
	}
	
	public function getPrimaryKey()
	{
		return 'id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model_year_id, review_id, marker, text', 'required'),
		);
	}

	public static function getMarkers()
	{
		return array(
			self::MARKER_TIRES,
			self::MARKER_060,
			self::MARKER_HP,
			self::MARKER_DIMENSIONS,
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'Review' => array(self::BELONGS_TO, 'Review', 'review_id', 'together'=>true,),
        );
	}	
	
	public static function getTextModelYear($marker, $model_year_id)
	{
		$model_year_id = (int) $model_year_id;
		$key 		   =  Tags::TAG_REVIEW . '_getTextModelYear_' . $marker . '_' . $model_year_id;
		
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			$criteria = new CDbCriteria;
			$criteria->compare('marker', $marker);
			$criteria->compare('model_year_id', $model_year_id);
			$items = self::model()->findAll($criteria);
			
			foreach ($items as $item) {
				$data[] = $item->text;
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_REVIEW));
		}

		return $data;
	}
	
	public static function getTextModel($marker, $model_id)
	{
		$model_id = (int) $model_id;
		$key 		   =  Tags::TAG_REVIEW . '_getTextModelYear_' . $marker . '_' . $model_id;
		
		$data = Yii::app()->cache->get($key);
		
		if ($data === false) {
			$data = array();
			$criteria = new CDbCriteria;
			$criteria->compare('t.marker', $marker);
			$criteria->compare('Review.model_id', $model_id);
			$criteria->with = array('Review'=>array('together'=>true));
			$items = self::model()->findAll($criteria);
			
			foreach ($items as $item) {
				$data[$item->text] = $item->text;
			}
			
			Yii::app()->cache->set($key, $data, 0, new Tags(Tags::TAG_REVIEW));
		}

		return $data;
	}
	
}