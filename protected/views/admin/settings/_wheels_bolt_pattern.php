<div class="control-group ">
	<label class="control-label required" for="Config_seo_wheels_bolt_pattern_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_wheels_bolt_pattern_title]',isset($values['seo_wheels_bolt_pattern_title']) ? $values['seo_wheels_bolt_pattern_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_wheels_bolt_pattern_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_wheels_bolt_pattern_meta_keywords]',isset($values['seo_wheels_bolt_pattern_meta_keywords']) ? $values['seo_wheels_bolt_pattern_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_wheels_bolt_pattern_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[seo_wheels_bolt_pattern_meta_description]',isset($values['seo_wheels_bolt_pattern_meta_description']) ? $values['seo_wheels_bolt_pattern_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_wheels_bolt_pattern_header_text_block"><?=Yii::t('admin', 'Header Text Block')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[wheels_bolt_pattern_header_text_block]',isset($values['wheels_bolt_pattern_header_text_block']) ? $values['wheels_bolt_pattern_header_text_block'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>