<?php

class EbayAutoTrim extends CActiveRecord
{
    public $post_regions;
    
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
		return 'ebay_auto_trim';
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
			array('epid, body_id, post_regions, drive_type_id, engine_id, model_year_id, num_doors', 'safe'),
		);
	}
	
	public function afterSave()
	{	
		$this->_clearCache();
		
        if (!empty($this->post_regions)) {
            foreach ($this->post_regions as $region_id) {
                $region_id = (int) $region_id;
                if (!$region_id) {
                    continue;;
                }
                
                $sql = "INSERT INTO ebay_auto_trim_vs_region (trim_id, region_id)
                        VALUES ({$this->id}, {$region_id})";
                Yii::app()->db->createCommand($sql)->execute();        
            }
        }
        
        
		return parent::afterSave();
	}

    public function afterDelete() 
	{
		$this->_clearCache();	
			
        return parent::afterDelete();
    }	
    
	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_EBAY_TRIM);
	}    
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
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
		$criteria->compare('title',$this->title, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
}