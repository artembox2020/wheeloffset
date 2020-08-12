<?php

class Access extends CActiveRecord
{
	public static $_resources = null;

	const CACHE_KEY = 'ACCESSS';

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
        return 'access';
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
            array('id', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
	
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => 'Название',
        );
    }	

    public static function getTree() {
		$cache = Yii::app()->cache->get(self::CACHE_KEY);
		if (empty($cache)) {
			$cache = array();
			//0-й уровень
			$criteria=new CDbCriteria;
			$criteria->addCondition('parent_id IS NULL');
			$roots = self::model()->findAll($criteria);
			foreach ($roots as $root) {
				$cache[$root->id] = array(
					'id' => $root->id,
					'title' => $root->title,
				); 

				//1-й уровень
				$criteria=new CDbCriteria;
				$criteria->compare('parent_id', $root->id);
				$levels1 = self::model()->findAll($criteria);				
				foreach ($levels1 as $level1) {
					$cache[$root->id]['items'][$level1->id] = array(
						'id' => $level1->id,
						'title' => $level1->title,
					); 
				
					//2-й уровень
					$criteria=new CDbCriteria;
					$criteria->compare('parent_id', $level1->id);
					$levels2 = self::model()->findAll($criteria);						
					foreach ($levels2 as $level2) {
						$cache[$root->id]['items'][$level1->id]['items'][$level2->id] = array(
							'id' => $level2->id,
							'title' => $level2->title,
						); 	

						//3-й уровень
						$criteria=new CDbCriteria;
						$criteria->compare('parent_id', $level2->id);
						$levels3 = self::model()->findAll($criteria);						
						foreach ($levels3 as $level3) {
							$cache[$root->id]['items'][$level1->id]['items'][$level2->id]['items'][$level3->id] = array(
								'id' => $level3->id,
								'title' => $level3->title,
							); 					
						}

						
					}
				}				
			}
			
			Yii::app()->cache->set(self::CACHE_KEY, $cache, 60*60*24);
		}
		
		return $cache;
    }	
	
	/*
	@params
	string $alias
	returned 
	1 - boot
	other - code exception
	*/
	public static function is($alias, $returned=1)
	{
		$resources = self::getResources();
		$access = isset($resources[$alias]);
		
		if ($returned == 1) {
			return $access;
		} else if (!$access) {
			 throw new CHttpException($returned, 'У вас недостаточно прав для выполнения указанного действия');
		}
	}
	
	public static function getResources()
	{
		if (self::$_resources === null) {
	
			if (Yii::app()->admin->isGuest) {
				return array();
			} else {
			
				if (empty(Yii::app()->admin->model)) {
					return false;
				}
										
				$role_id = Yii::app()->admin->model->role_id;
				$key = AdminRole::CACHE_KEY_ACCESS . $role_id;
				$cache = Yii::app()->cache->get($key);
				if (empty($cache)) {
					$cache = array();

					$criteria = new CDbCriteria();
					$criteria->compare('role_id', (int)$role_id);
					$criteria->with = array('access');
					$items = AdminRoleAccess::model()->findAll($criteria);
					foreach ($items as $item) {
						$cache[$item->access->alias] = 1;
					}
					Yii::app()->cache->set($key, $cache, 60*60*24*31);
				}
				self::$_resources = $cache;

				return $cache;
			}
		} else {
			return self::$_resources;
		}
	}
	
		

}