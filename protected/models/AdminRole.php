<?php

class AdminRole extends CActiveRecord
{
	const CACHE_KEY_ACCESS = 'ADMIN_ROLE_ACCESS_';

	public $post_access = array();

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
        return 'admin_role';
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
            array('id, post_access', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
			'access' => array(self::MANY_MANY, 'Access', 'admin_role_access(role_id, access_id)'),
        );
    }
	
	public function afterSave()
	{
		if ($this->scenario == 'update') {
			AdminRoleAccess::model()->deleteAllByAttributes(array('role_id'=>$this->id));
		}

		//Сохраняем доступ
		if (!empty($this->post_access)) {
			foreach ($this->post_access as $access_id) {
				$item = new AdminRoleAccess;
				$item->role_id = $this->id;
				$item->access_id = $access_id;
				$item->save();
			}
		}
		
		Yii::app()->cache->delete(self::CACHE_KEY_ACCESS . $this->id);
		
		return parent::afterSave();
	}	
	
	public function afterDelete()
	{	
		Yii::app()->cache->delete(self::CACHE_KEY_ACCESS . $this->id);
		
		return parent::afterDelete();	
	}

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => 'Title',
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
	
    public static function getAll() {
        $items = array();
        foreach (self::model()->findAll() as $model) {
            $items[$model->id] = $model->title;
        }

        return $items;
    }	

	public function getAccessForForm()
	{
		if (Yii::app()->request->isPostRequest) {
			return $this->post_access;
		} else {
			if ($this->isNewRecord) {
				return array();
			} else {
				$data = array();
				foreach ($this->access as $access) {
					$data[] = $access->id;
				}
				return $data;
			}
		}		
	}
	
}