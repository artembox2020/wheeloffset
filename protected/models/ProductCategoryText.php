<?php

class ProductCategoryText extends CActiveRecord
{
    const TYPE_PROS = 1;
    const TYPE_CONS = 2;

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
		return 'product_category_text';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('category_id, text, type, number', 'required'),
		);
	}
    	
	
	public function afterSave()
	{	
		$this->_clearCache();
        return parent::afterSave();
	}

    public function afterDelete() 
	{
		$this->_clearCache();	
        return parent::afterDelete();
    }	
    
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_PRODUCT_CATEGORY);
	}
}