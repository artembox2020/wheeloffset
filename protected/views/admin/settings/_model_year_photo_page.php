<p class="help-block">used: [make], [model], [year] .</p><br/>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_model_year_photos_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_model_year_photos_title]',isset($values['seo_model_year_photos_title']) ? $values['seo_model_year_photos_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_model_year_photos_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_model_year_photos_meta_keywords]',isset($values['seo_model_year_photos_meta_keywords']) ? $values['seo_model_year_photos_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_model_year_photos_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[seo_model_year_photos_meta_description]',isset($values['seo_model_year_photos_meta_description']) ? $values['seo_model_year_photos_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_model_year_photos_description"><?=Yii::t('admin', 'Description Template')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[model_year_photos_description]',isset($values['model_year_photos_description']) ? $values['model_year_photos_description'] : '', array('class'=>'span6'));?>
	</div>
</div>