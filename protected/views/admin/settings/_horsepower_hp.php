<p class="help-block">used: [hp]</p>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_horsepower_hp_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_horsepower_hp_title]',isset($values['seo_horsepower_hp_title']) ? $values['seo_horsepower_hp_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_horsepower_hp_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_horsepower_hp_keywords]',isset($values['seo_horsepower_hp_keywords']) ? $values['seo_horsepower_hp_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_horsepower_hp_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[seo_horsepower_hp_description]',isset($values['seo_horsepower_hp_description']) ? $values['seo_horsepower_hp_description'] : '', array('class'=>'span6'));?>
	</div>
</div>
