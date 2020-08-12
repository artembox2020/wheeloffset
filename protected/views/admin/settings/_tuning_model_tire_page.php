<p class="help-block">used: [make], [model], [num], [tire] .</p><br/>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_tuning_model_tire_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_tuning_model_tire_title]',isset($values['seo_tuning_model_tire_title']) ? $values['seo_tuning_model_tire_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_tuning_model_tire_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_tuning_model_tire_meta_keywords]',isset($values['seo_tuning_model_tire_meta_keywords']) ? $values['seo_tuning_model_tire_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_tuning_model_tire_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[seo_tuning_model_tire_meta_description]',isset($values['seo_tuning_model_tire_meta_description']) ? $values['seo_tuning_model_tire_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_tuning_model_tire_description"><?=Yii::t('admin', 'Model Description Template')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[tuning_model_tire_description]',isset($values['tuning_model_tire_description']) ? $values['tuning_model_tire_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

