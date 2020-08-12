<?php
/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $firstName
 * @property string $lastName
 * @property string $roleId
 * @property integer $active
 * @property string $email
 * @property string $phone
 */
class Admin extends CActiveRecord
{
    public $confirmPassword;
    public $new_password;

    const DISABLE_USER = 0;
    const ACTIVE_USER = 1;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'admin';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('role_id, active, email, phone,first_name,last_name', 'required'),
			array('password, confirmPassword', 'required', 'on'=>'insert'),
			array('password', 'length', 'max'=>128),
			array('email', 'length', 'max'=>255),
            array(
                'phone',
                'match',
                'pattern' => '/^((\+?7)(-?\d{3})-?)?(\d{3})(-?\d{2})?(-?\d{2})$/',
                'message' => 'Некорректный формат поля {attribute}'
            ),
            array(
                'active',
				'numerical',
				'integerOnly' => true,
            ),
            array(
                'email',
                'email',
            ),
			array('id,password, role_id, active, birth_date, phone, regdate', 'safe'),
     	);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'role' => array(self::BELONGS_TO, 'AdminRole', 'role_id'),
        );
	}

	public function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->regdate = date('Y-m-d H:i:s');
		}
		
		if (!empty($this->new_password)) {
			$this->password = md5($this->new_password);
		}
		
		return parent::beforeSave();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'password' => Yii::t('admin', 'Password'),
            'birth_date'=> Yii::t('admin', 'Birth date'),
			'first_name' => Yii::t('admin', 'First Name'),
			'last_name' => Yii::t('admin', 'Last Name'),
			'role_id' => Yii::t('admin', 'Role'),
            'active' => Yii::t('admin', 'Active'),
            'email' => Yii::t('admin', 'E-mail'),
            'phone' => Yii::t('admin', 'Phone'),
            'confirmPassword' => Yii::t('admin', 'Confirm Password'),
            'regdate'=>Yii::t('admin', 'Registration date'),
            'full_name'=>Yii::t('admin', 'Full Name'),
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
        $criteria->compare('role_id',$this->role_id,true);
        $criteria->compare('active',$this->active,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->order = 'active asc';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}

	
	public function getFull_name()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	public static function getAll()
	{
		return CHtml::listData(self::model()->findAll(), 'id', 'full_name');
	}	
	
}