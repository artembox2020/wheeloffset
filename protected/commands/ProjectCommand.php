<?php
class ProjectCommand extends CConsoleCommand
{
	public function init() 
	{
		ini_set('max_execution_time', 3600*12);
		return parent::init();
	}

	public function actionImport()
	{
		$url = "http://www.rimtuck.com/setup/view/";
		//Project::model()->deleteAll();
		for ($id=1;$id<=2055;$id++) {
			$parseUrl = $url . $id;
			$content = CUrlHelper::getPage($parseUrl, '', '');
			$content = str_replace(array("&nbsp;", "\n", "\t", "\r"), "", $content);
			file_put_contents(Yii::getPathOfAlias('webroot').'/../import/'.$id.'.html', $content);
			
			preg_match('/<tr><td class="blk" width="1" ><\/td><td class="bgh1" height="21" colspan="6"><b>VEHICLE<\/b><\/td><td class="bgh1_id"><b>ID# '.$id.'<\/b><\/td><td class="blk" width="1" ><\/td><\/tr><tr><td class="blk"><\/td><td colspan="7"class="sep" height="1"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Make:<\/td><td class="bg" colspan="6">(.*?)<\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td colspan="7"class="sep" height="1"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Model:<\/td><td class="bg" colspan="6">(.*?)<\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td colspan="7"class="sep" height="1"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Year:<\/td><td class="bg" colspan="6">(.*?)<\/td><td class="blk"><\/td><\/tr>/', $content, $match);

			if (isset($match[3])) {
				$attributes = array();
				
				$attributes['make_id'] = $this->_getModelId('AutoMake', array(
					'title'=>$match[1]
				));
				
				$attributes['model_id'] = $this->_getModelId('AutoModel', array(
					'make_id'=>$attributes['make_id'],
					'title'=>$match[2]
				));
				
				$attributes['model_year_id'] = $this->_getModelId('AutoModelYear', array(
					'model_id'=>$attributes['model_id'],
					'year'=>$match[3]
				));
				
				preg_match('/<tr><td class="blk"><\/td><td class="bgh2" colspan="2" height="21"><b>FRONT<\/b><\/td><td class="blk" width="1" ><\/td><td width="10"><\/td><td class="blk" width="1" ><\/td><td class="bgh2" colspan="2"><b>REAR<\/b><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bgh1" align="left" width="80"><b>WHEELS<\/b><\/td><td class="bgh1" align="left" width="100"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bgh1" align="left" width="80"><b>WHEELS<\/b><\/td><td class="bgh1" align="left" width="100"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Manufacture:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bg">Manufacture:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Model:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bg">Model:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Diameter:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bg">Diameter:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Width:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bg">Width:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Offset:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bg">Offset:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><\/tr>/', $content, $matchWheels);				
				
				
				$attributes['id'] = $id;
				$attributes['wheel_manufacturer'] = $matchWheels[1];
				$attributes['wheel_model'] = $matchWheels[3];

				$attributes['rim_diameter_id'] = $this->_getModelId('TireRimDiameter', array(
					'value'=>$matchWheels[5],
				));
				
				$attributes['rim_width_id'] = $this->_getModelId('RimWidth', array(
					'value'=>$matchWheels[7],
				));
				
				$attributes['rim_offset_range_id'] = $this->_getModelId('RimOffsetRange', array(
					'value'=>$matchWheels[9],
				));
				
				if ($matchWheels[1]!=$matchWheels[2] || $matchWheels[3]!=$matchWheels[4] || $matchWheels[5]!=$matchWheels[6] || $matchWheels[7]!=$matchWheels[8] || $matchWheels[9]!=$matchWheels[10]) {
					$attributes['is_staggered_wheels']  = 1;
					
					$attributes['rear_rim_diameter_id'] = $this->_getModelId('TireRimDiameter', array(
						'value'=>$matchWheels[6],
					));
					
					$attributes['rear_rim_width_id'] = $this->_getModelId('RimWidth', array(
						'value'=>$matchWheels[8],
					));
					
					$attributes['rear_rim_offset_range_id'] = $this->_getModelId('RimOffsetRange', array(
						'value'=>$matchWheels[10],
					));					
				}
				
				preg_match('/<tr><td class="blk"><\/td><td class="bgh1" align="left"><b>TIRES<\/b><\/td><td class="bgh1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bgh1" align="left"><b>TIRES<\/b><\/td><td class="bgh1"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Manufacture:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bg">Manufacture:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Model:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bg">Model:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Width:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bg">Width:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="sep" colspan="2" height="1"><\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="sep" colspan="2"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg">Profile:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><td><\/td><td class="blk"><\/td><td class="bg">Profile:<\/td><td class="bg">(.*?)<\/td><td class="blk"><\/td><\/tr>/', $content, $matchTire);				

				$attributes['tire_manufacturer'] = $matchTire[1];
				preg_match('/<tr><td class="blk"><\/td><td class="bgh1" height="21" colspan="7" ><b>NOTES<\/b><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td colspan="7"class="sep" height="1"><\/td><td class="blk"><\/td><\/tr><tr><td class="blk"><\/td><td class="bg" colspan="7"width=370>(.*?)<\/td><td class="blk"><\/td><\/tr>/', $content, $matchDesc);				

				$attributes['tire_manufacturer'] = $matchTire[1];
				$attributes['tire_model'] = $matchTire[3];				
				$attributes['description'] = $matchDesc[1];				
				
				$attributes['tire_section_width_id'] = $this->_getModelId('TireSectionWidth', array(
					'value'=>$matchTire[5],
				));
				
				$attributes['tire_aspect_ratio_id'] = $this->_getModelId('TireAspectRatio', array(
					'value'=>$matchTire[7],
				));
								
				if ($matchTire[1]!=$matchTire[2] || $matchTire[3]!=$matchTire[4] || $matchTire[5]!=$matchTire[6] || $matchTire[7]!=$matchTire[8]) {
					$attributes['is_staggered_tires']  = 1;
					
					$attributes['rear_tire_section_width_id'] = $this->_getModelId('TireSectionWidth', array(
						'value'=>$matchTire[6],
					));
					
					$attributes['rear_tire_aspect_ratio_id'] = $this->_getModelId('TireAspectRatio', array(
						'value'=>$matchTire[8],
					));				
				}	
				
				preg_match_all('/<a href="(.*?)" target=_blank><img src="(.*?)" border=0><\/a>/', $content, $matchImages);				
				
				$project = Project::model()->findByPk($attributes['id']);
				if (!empty($project)) {
					if (isset($attributes['rear_rim_width_id'])) {
						$project->rear_rim_width_id = $attributes['rear_rim_width_id'];
						$project->save();
						echo "updated {$project->id} \n";
					}
				} 
				/*
				else {
					$project = new Project;
					$project->attributes = $attributes;
					//$project->save();					
					echo "created {$id} \n";	
				}
		
				$project = new Project;
				$project->attributes = $attributes;
				$project->save();
							
				echo "{$project->id} \n";			
							
				foreach ($matchImages[2] as $matchImage) {
					$photo = new ProjectPhoto;
					$photo->project_id = $project->id;
					$photo->filePath = str_replace('tn.', '.', $matchImage);
					$photo->save();
					
					echo " - {$photo->id} \n";
				}
				*/
			}
		}
	}
	
	
	private function _getModelId($modelName, $attributes) {
		$model = CActiveRecord::model($modelName)->findByAttributes($attributes);
		if (empty($model)) {
			$model = new $modelName;
			$model->attributes = $attributes;
			if ($model->hasAttribute('is_active'))
				$model->is_active = 0;
			$model->save();
		}
		
		return $model->id;
	}
	
}