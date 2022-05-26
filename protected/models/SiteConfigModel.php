<?php

class SiteConfigModel extends CActiveRecord 
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
		return 'settings_wheeloffset';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		$rules = array(
			array('key', 'required'),
			array('value', 'safe'),
			array('key', 'unique'),
		);

		return $rules;
	}
	
	public static function saveData($items)
	{
		self::model()->deleteAll();
		Yii::app()->cache->delete(SiteConfig::CACHE_KEY);
		
		foreach ($items as $k=>$v) {
			$model = new self;
			$model->key = $k;
			$model->value = $v;
			$model->save();
		}
	}

}
