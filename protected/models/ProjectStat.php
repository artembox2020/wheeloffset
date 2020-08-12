<?php

class ProjectStat extends CActiveRecord
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
		return 'project_stat';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('total, total_day, total_month, date', 'required'),
		);
	}
		
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'date' 			=> Yii::t('admin', 'Date'),
			'total' 		=> Yii::t('admin', 'Total'),
			'total_day' 	=> Yii::t('admin', 'Total day'),
			'total_month' 	=> Yii::t('admin', 'Total month'),
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

		$criteria->compare('date',$this->date);
		$criteria->compare('total',$this->total);
		$criteria->compare('total_day',$this->total_day);
		$criteria->compare('total_month',$this->total_month);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),	
			'sort' => array('defaultOrder' => 'date DESC',),
		));
	}

	
}