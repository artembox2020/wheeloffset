<p class="help-block">used: [make], [model], [front_rim_diameter], [front_rim_width], [front_offset], [front_tiresize], [rear_rim_diameter], [rear_rim_width], [rear_offset], [rear_tiresize], [rims_brand]
 .</p><br/>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_tuning_project_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_tuning_project_title]',isset($values['seo_tuning_project_title']) ? $values['seo_tuning_project_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_tuning_project_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_tuning_project_meta_keywords]',isset($values['seo_tuning_project_meta_keywords']) ? $values['seo_tuning_project_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_tuning_project_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[seo_tuning_project_meta_description]',isset($values['seo_tuning_project_meta_description']) ? $values['seo_tuning_project_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>


