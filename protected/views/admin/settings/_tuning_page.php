<div class="control-group ">
	<label class="control-label required" for="Config_seo_tuning_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_tuning_title]',isset($values['seo_tuning_title']) ? $values['seo_tuning_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_tuning_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_tuning_meta_keywords]',isset($values['seo_tuning_meta_keywords']) ? $values['seo_tuning_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_tuning_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[seo_tuning_meta_description]',isset($values['seo_tuning_meta_description']) ? $values['seo_tuning_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_tuning_header_text_block"><?=Yii::t('admin', 'Header Text Block')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[tuning_header_text_block]',isset($values['tuning_header_text_block']) ? $values['tuning_header_text_block'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_tuning_footer_text_block"><?=Yii::t('admin', 'Footer Text Block')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[tuning_footer_text_block]',isset($values['tuning_footer_text_block']) ? $values['tuning_footer_text_block'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>