<?php

class Review extends CActiveRecord
{
	public $post_model_year = array();

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
		return 'review';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, alias, model_id, description', 'required'),
            array('alias', 'unique',),		
            array('id, post_model_year', 'safe',),		
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'Model' => array(self::BELONGS_TO, 'AutoModel', 'model_id', 'together'=>true,),
	    );
	}	
	
	/**
	 * Выполняем ряд действий перед валидацией модели
	 * @return boolean -- результат выполнения операции
	 */
	protected function beforeValidate()
	{
		$this->getDataMarkers();
		
		//создаем алиас к тайтлу
		$this->buildAlias();
		return parent::beforeValidate();
	}
	
	/**
	 * Создаем алиас к тайтлу
	 */
	private function buildAlias()
	{
		if (empty($this->alias) && !empty($this->title)) { 
			$this->alias = $this->title;
		}
		
		$this->alias = TextHelper::urlSafe($this->alias);
	}	
	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => Yii::t('admin', 'Title'),
			'alias' => Yii::t('admin', 'Alias'),
			'model_id' => Yii::t('admin', 'Model'),
			'post_model_year' => Yii::t('admin', 'Model years'),
			'description' => Yii::t('admin', 'Description'),
		);
	}
	
	/**
	 * Выполняем ряд обязательных действий после сохранения модели
	 * @return boolean -- результат выполнения действия
	 */
	protected function afterSave()
	{
		$deletedCompetitors = array();
	
		if ($this->scenario == 'updateAdmin') {
			$sql = 'DELETE FROM review_vs_model_year WHERE review_id = ' . $this->id;
			Yii::app()->db->createCommand($sql)->execute();			
								
			$dataMarkers = $this->getDataMarkers();					
								
			foreach ($this->post_model_year as $model_year_id) {
				$model_year_id = (int) $model_year_id;
				
				foreach ($dataMarkers as $dataMarker) {
					$vs = new ReviewVsModelYear;
					$vs->model_year_id 	= $model_year_id;
					$vs->review_id 		= $this->id;					
					$vs->marker 		= $dataMarker['marker'];					
					$vs->text 			= $dataMarker['text'];					
					$vs->save();					
				}
			}
		}
							
		$this->_clearCache();	
			
		return parent::afterSave();
	}

	public function getPost_model_year()
	{
		if (Yii::app()->request->isPostRequest) {
			return $this->post_model_year;
		} else {
			if ($this->isNewRecord) {
				return array();
			} else {
				
				$criteria = new CDbCriteria;
				$criteria->compare('review_id', $this->id);
				$items = ReviewVsModelYear::model()->findAll($criteria);
				$ids = array();
				foreach ($items as $item) {
					$ids[] = $item->model_year_id;
				}
			
				return $ids;
			}
		}
	}		

	private function getDataMarkers()
	{
		$dataMarker = array();
		
		$this->description = str_replace(array("\n"), array("<br>"), $this->description);
		
		foreach (ReviewVsModelYear::getMarkers() as $marker) {
			preg_match_all("/\[{$marker}\](.*?)\[\/{$marker}\]/", $this->description, $match);	

			$dataReplace = array();
			foreach (ReviewVsModelYear::getMarkers() as $m) {
				if ($m == $marker) {continue;}
				$dataReplace[] = "[{$m}]";
				$dataReplace[] = "[/{$m}]";
			}
			
			foreach ($match[1] as $text) {
				$dataMarker[] = array(
					'marker' => $marker,
					'text' => str_replace($dataReplace, '', $text),
				);
			}
		}
		//d($dataMarker);
		
		return $dataMarker;
	}
	
	public function afterDelete()
	{
		$this->_clearCache();
		
		return parent::afterDelete();
	}

	private function _clearCache()
	{
		Yii::app()->cache->clear(Tags::TAG_REVIEW);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.title',$this->title, true);
		$criteria->compare('t.alias',$this->alias, true);
		$criteria->compare('t.model_id',$this->model_id);
		
		$criteria->with = array(
			'Model' => array('together'=>true),
		);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
			),			
		));
	}
	
}