<?php
/**
 * Класс AutoSpecsOption
 * @author dorosh_2009@meta.ua
 * @copyright Aiken Interactive 2013
 * @version 8.0.0
 * 
 * @property integer $id						-- id 
 * @property integer $specs_id				-- id атрибута
 * @property integer $value						-- значение
 */
class AutoSpecsOption extends CActiveRecord
{
	const CACHE_KEY_LIST = 'AUTO_SPECS_OPTION_LIST_____';

	public static function model($className = __CLASS__) 
	{ 
		return parent::model($className);
	}
	
	public function tableName() 
	{ 
		return 'auto_specs_option';
	}	
	
	/**
	 * Правила валидации
	 * @return array
	 */
	public function rules()
	{
		return array(
			array(
				'specs_id, value',
				'required',
			),
		);
	}

	/**
	 * Взаимосвязи
	 * @return array
	 */
	public function relations()
	{
		return array(
			'Specs' => array(self::BELONGS_TO, 'AutoSpecs', 'specs_id', 'together'=>true),
		);
	}
	
	public function afterSave()
	{
		$this->clearCache();
		
		return parent::afterSave();
	}	
	
	public function afterDelete()
	{
		AutoCompletion::model()->updateAll(array("specs_".$this->Specs->alias => NULL));
		$this->clearCache();
		
		return parent::afterDelete();
	}	
	
	private function clearCache()
	{
		Yii::app()->cache->delete(self::CACHE_KEY_LIST . $this->specs_id);
	}
	
	public static function getAllBySpecs($specs_id)
	{
		$key = self::CACHE_KEY_LIST . $specs_id;
		$data = Yii::app()->cache->get($key);
		if (empty($data) && !is_array($data)) {
			$criteria=new CDbCriteria;
			$criteria->compare('specs_id', $specs_id);		
			$data = CHtml::listData(self::model()->findAll($criteria), 'id', 'value');
			Yii::app()->cache->set($key, $data, 60*60*24*31);
		}
		
		return (array)$data;
	}
	
	public static function getIdByValueAndSpecsId($specs_id, $value) 
	{
		$options = self::getAllBySpecs($specs_id);
		$options = array_flip($options);
		if (isset($options[$value])) {
			return $options[$value];
		} else {
			return false;
		}
	}
	
	public static function getV($specs, $value) 
	{
		$dataSpecsAlias = AutoSpecs::getAllAlias();
		if (isset($dataSpecsAlias[$specs])) {
			$dataSpecsType = AutoSpecs::getAllType();
			if (isset($dataSpecsType[$dataSpecsAlias[$specs]]) && $dataSpecsType[$dataSpecsAlias[$specs]] == AutoSpecs::TYPE_SELECT) {
				$options = self::getAllBySpecs($dataSpecsAlias[$specs]);
				if (isset($options[$value])) {
					return $options[$value];
				} else {
					$value;
				}
			} else {
				return $value;
			}
		} else {
			return $value;
		}
	}
	
	
}