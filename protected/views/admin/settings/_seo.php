        <?php $this->widget('bootstrap.widgets.TbTabs', array(
            'placement'=>'left',
            'tabs'=>array(
                array(
					'label'=>Yii::t('admin', 'Home'), 
					'active'=>true, 
					'content' => $this->renderPartial('_home_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Make'),
					'content' => $this->renderPartial('_make_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Model'),
					'content' => $this->renderPartial('_model_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Model by Year'),
					'content' => $this->renderPartial('_model_year_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Model by Year Photos'),
					'content' => $this->renderPartial('_model_year_photo_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Completion'),
					'content' => $this->renderPartial('_completion_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', '0-60 times'),
					'content' => $this->renderPartial('_0_60_times_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', '0-60 times Make'),
					'content' => $this->renderPartial('_0_60_times_make_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', '0-60 times Model'),
					'content' => $this->renderPartial('_0_60_times_model_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Tires'),
					'content' => $this->renderPartial('_tires', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Tire Make'),
					'content' => $this->renderPartial('_tires_make', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Tire Model'),
					'content' => $this->renderPartial('_tires_model', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Tire Model Year'),
					'content' => $this->renderPartial('_tires_model_year', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Tire Diameter'),
					'content' => $this->renderPartial('_tires_diameter', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Tire Size'),
					'content' => $this->renderPartial('_tires_size', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Tire Size Make'),
					'content' => $this->renderPartial('_tires_size_make', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Horsepower'),
					'content' => $this->renderPartial('_horsepower', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Horsepower Make'),
					'content' => $this->renderPartial('_horsepower_make', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Horsepower Model'),
					'content' => $this->renderPartial('_horsepower_model', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Horsepower Model Year'),
					'content' => $this->renderPartial('_horsepower_model_year', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Horsepower value'),
					'content' => $this->renderPartial('_horsepower_hp', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Dimensions'),
					'content' => $this->renderPartial('_dimensions', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),	
                array(
					'label'=>Yii::t('admin', 'Dimensions Make'),
					'content' => $this->renderPartial('_dimensions_make', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Dimensions Model'),
					'content' => $this->renderPartial('_dimensions_model', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Dimensions Model Year'),
					'content' => $this->renderPartial('_dimensions_model_year', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),	
				
				//Tuning
                array(
					'label'=>Yii::t('admin', 'Tuning'),
					'content' => $this->renderPartial('_tuning_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Make'),
					'content' => $this->renderPartial('_tuning_make_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model'),
					'content' => $this->renderPartial('_tuning_model_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Diameter'),
					'content' => $this->renderPartial('_tuning_model_diameter_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Width'),
					'content' => $this->renderPartial('_tuning_model_width_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Tire'),
					'content' => $this->renderPartial('_tuning_model_tire_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Offset'),
					'content' => $this->renderPartial('_tuning_model_offset_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Diameter Width'),
					'content' => $this->renderPartial('_tuning_model_diameter_width_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Tire Offset'),
					'content' => $this->renderPartial('_tuning_model_tire_offset_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Diameter Tire'),
					'content' => $this->renderPartial('_tuning_model_diameter_tire_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Diameter Offset'),
					'content' => $this->renderPartial('_tuning_model_diameter_offset_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Width Tire'),
					'content' => $this->renderPartial('_tuning_model_width_tire_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Width Offset'),
					'content' => $this->renderPartial('_tuning_model_width_offset_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Diameter Tire Offset'),
					'content' => $this->renderPartial('_tuning_model_diameter_tire_offset_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Model Width Tire Offset'),
					'content' => $this->renderPartial('_tuning_model_width_tire_offset_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
				array(
					'label'=>Yii::t('admin', 'Tuning Model Diameter Width Offset'),
					'content' => $this->renderPartial('_tuning_model_diameter_width_offset_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
				array(
					'label'=>Yii::t('admin', 'Tuning Model Diameter Width Tire'),
					'content' => $this->renderPartial('_tuning_model_diameter_width_tire_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
				array(
					'label'=>Yii::t('admin', 'Tuning Model Diameter Width Tire Offset'),
					'content' => $this->renderPartial('_tuning_model_diameter_width_tire_offset_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
				
                array(
					'label'=>Yii::t('admin', 'Tuning Model Width'),
					'content' => $this->renderPartial('_tuning_model_width_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Tuning Project'),
					'content' => $this->renderPartial('_tuning_project_page', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
				//Wheels
                array(
					'label'=>Yii::t('admin', 'Wheels'),
					'content' => $this->renderPartial('_wheels', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Wheels Make'),
					'content' => $this->renderPartial('_wheels_make', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Wheels Model'),
					'content' => $this->renderPartial('_wheels_model', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Wheels Diametr Width'),
					'content' => $this->renderPartial('_wheels_diametr_width', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Wheels Diametr Width Tire'),
					'content' => $this->renderPartial('_wheels_diametr_width_tire', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Wheels Bolt Pattern'),
					'content' => $this->renderPartial('_wheels_bolt_pattern', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Weight'),
					'content' => $this->renderPartial('_weight', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Weight Make'),
					'content' => $this->renderPartial('_weight_make', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Weight Model'),
					'content' => $this->renderPartial('_weight_model', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
			
                array(
					'label'=>Yii::t('admin', 'Mpg'),
					'content' => $this->renderPartial('_mpg', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Mpg Make'),
					'content' => $this->renderPartial('_mpg_make', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Mpg Model'),
					'content' => $this->renderPartial('_mpg_model', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
				
                array(
					'label'=>Yii::t('admin', 'Bulb'),
					'content' => $this->renderPartial('_bulb', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Bulb Make'),
					'content' => $this->renderPartial('_bulb_make', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Bulb Model'),
					'content' => $this->renderPartial('_bulb_model', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Bulb Year'),
					'content' => $this->renderPartial('_bulb_year', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
                array(
					'label'=>Yii::t('admin', 'Bulb Type'),
					'content' => $this->renderPartial('_bulb_type', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),				
			
				
			)
		));?>

