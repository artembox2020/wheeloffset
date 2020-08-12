<?php
require_once('yd/lib/YandexMoney.php');
require_once('yd/sample/consts.php');
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
class User extends CActiveRecord
{

	/**
	 * Салоны
	 */	 
	public $rel_salons = array();

    public $confirmPassword;
    public $verifyCode;
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
	public function sendSms($text) {
		$url = "http://littlesms.ru/api/message/send";
		$user = 'catofbeauty@ya.ru';
		$apikey = 'obwdHk';
		$u = $this;
        $userphone = str_replace("-","",$u->phone);
		$str = "$url?user=$user&apikey=$apikey&recipients=$userphone&message=$text";
		file_get_contents($str);
	}
	public function getOffBonusToMobile($phone, $howMuch) {
		$available = $this->getBonus();
		if ($howMuch > $available) {
			return "not enough bonuses";
		}
        //$ym = new YandexMoney(CLIENT_ID);
        $ym = new YandexMoney('C05B65B8D5464D7BC8991233B40F46C5C2B36BE82EDD7E73C0AAD3F320597265');
		$token = "410012015141371.4012C3763E47D7338D59A9AF914986CE4F0AE1ED44DCE1530DADFE9DFBBE82AC242215163B3009B772511246CDEF467C43F06FD1376CBCE7F2E90AAC3C2B9354F9439708629C4989B8D069E8AD95142C19EE141C713C3F281B1ECA71AEB45B282CADE32A92CA40C88296C868B00ADEFFA36848D1AEEBF9377E20456D6184AB06";
        $params["pattern_id"] = "phone-topup";
        $params["phone-number"] = $phone;
        $params["test_payment"] = true;
        $params["amount"] = $howMuch;
        $resp = $ym->requestPaymentShop($token, $params);
		if ($resp->isSuccess()) {
			$requestId = $resp->getRequestId();
			$resp = $ym->processPaymentByWallet($token, $requestId);
			if ($resp->isSuccess()) {
                $bon = new Bonus();
                $bon->phone = $phone;
                $date = date_create('NOW');
                $bon->date = date_format($date, 'Y-m-d H:i:s');
                $bon->count = $howMuch;
                $bon->client_id = $this->id;
                $bon->save();
			} else {
				return "К сожалению, сервис временно недоступен.";
			}
		} else {
			return "Проверьте правильность введенного телефона";
		}
		return "Платеж успешно завершен!";
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, login, role_id, active, email, phone,first_name,last_name,city_id', 'required'),
			array('password, confirmPassword', 'required', 'on'=>'insert'),
			array('login, password', 'length', 'max'=>128),
			array('username, email', 'length', 'max'=>255),
			array('rel_salons', 'safe'),
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
                'match',
                'pattern' => '/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/',
                'message' => 'Некорректный формат поля {attribute}'
            ),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('activate_code', 'safe'),
			array('id, login, password, username, role_id, active, salon_id, city_id, birth_date,phone,regdate,referer', 'safe', 'on'=>'search'),
            //array('verifyCode','captcha'),
            array('phone', 'length', 'min'=>16, 'max'=>16),
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
            'role' => array(self::BELONGS_TO, 'Role', 'role_id'),
            'city'=>array(self::BELONGS_TO, 'City', 'city_id'),
 			'salons' => array(self::HAS_MANY, 'SalonUser', 'user_id'),           
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
	
	public function afterSave()
	{
		if (!$this->isNewRecord && in_array($this->scenario, array('updateForm'))) {
			SalonUser::model()->deleteAllByAttributes(array('user_id'=>$this->id));
		}

		//Сохраняем салоны
		if (!empty($this->rel_salons)) {
			foreach ($this->rel_salons as $salon_id) {
				$item = new SalonUser;
				$item->user_id = $this->id;
				$item->salon_id = $salon_id;
				$item->save();
			}
		}
		
		return parent::afterSave();
	}	
	
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Ник',
			'login' => 'Логин',
			'password' => 'Пароль',
            'birth_date'=>'День рождения',
			'first_name' => 'Имя',
			'last_name' => 'Фамилия',
			'role_id' => 'Роль',
            'active' => 'Активный',
            'email' => 'Email',
            'phone' => 'Телефон',
            'confirmPassword' => 'Повторить пароль',
            'verifyCode' => 'Введите код с картинки',
            'salon_id'=>'Салон',
            'activate_code'=>'Код активации',
            'city_id'=>'Город',
            'city'=>'Город',
            'referer'=>'Ссылка',
            'regdate'=>'Дата регистрации',
            'full_name'=>'ФИО',
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('login',$this->login,true);
        $criteria->compare('city_id',$this->city_id,true);
        $criteria->compare('active',$this->active,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->order = 'active asc';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>20,
            ),
		));
	}
	public function getLastVisit() {
		$params = array(
			'client_id' => $this->id
		);
		$u = User::model()->findByPK(Yii::app()->user->id);
		if ($u->salon_id > 0) {
			$params['salon_id'] = $u->salon_id;
		}

		$r = Relation::model()->findByAttributes(
			$params,
			new CDbCriteria(array('order'=>'`when` desc','limit'=>1)));
		return ($r===null)?'':date('d.m.Y H:i', strtotime($r->when));
	}
	public function getPribyl() {
		$params = array(
			'client_id' => $this->id
		);
		$u = User::model()->findByPK(Yii::app()->user->id);
		if ($u->salon_id > 0) {
			$params['salon_id'] = $u->salon_id;
		}
		$rels = Relation::model()->findAllByAttributes($params);
		$c = 0;
		foreach ($rels as $r) {
			$c += $r->cost;
		}
		return $c;
	}
	public function getBonus() {
		$rels = Relation::model()->findAllByAttributes(array('client_id' => $this->id));
		$c = 0;
		foreach ($rels as $r) {
			$c += $r->bonus;
		}
        $pays = Bonus::model()->findAllByAttributes(array('client_id' => $this->id));
        foreach ($pays as $r) {
            $c -= $r->count;
        }
		return $c;
	}
    public function getUserCity($city_id) {
        $city = City::model()->findByAttributes(array('id' => $city_id));
        return ($city===null)?'':$city->name;
    }
    public static function getString($id) {
        return User::model()->findByPk($id)->last_name." ".User::model()->findByPk($id)->first_name;
    }
    public static function getDate($date) {
        $date = date('d.m.Y H:i', strtotime($date));
        return $date;
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