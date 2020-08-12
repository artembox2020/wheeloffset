<?php

class AdminRoleAccess extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SalonCategory the static model class
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
		return 'admin_role_access';
	}
	
	public function getPrimaryKey()
	{
		return array('role_id', 'access_id');
	}	

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('role_id, access_id', 'required'),
		);
	}
	
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
			'access' => array(self::BELONGS_TO, 'Access', 'access_id', 'together'=>true),
        );
    }	
	

}