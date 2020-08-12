<p class="help-block">used: [make] </p>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_0_60_times_make_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_0_60_times_make_title]',isset($values['seo_0_60_times_make_title']) ? $values['seo_0_60_times_make_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_0_60_times_make_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_0_60_times_make_meta_keywords]',isset($values['seo_0_60_times_make_meta_keywords']) ? $values['seo_0_60_times_make_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_0_60_times_make_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[seo_0_60_times_make_meta_description]',isset($values['seo_0_60_times_make_meta_description']) ? $values['seo_0_60_times_make_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_0_60_times_make_description"><?=Yii::t('admin', 'Make Description Template')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[0_60_times_make_description]',isset($values['0_60_times_make_description']) ? $values['0_60_times_make_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

