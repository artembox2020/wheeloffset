<div class="control-group ">
	<label class="control-label required" for="Config_seo_weight_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_weight_title]',isset($values['seo_weight_title']) ? $values['seo_weight_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_weight_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_weight_meta_keywords]',isset($values['seo_weight_meta_keywords']) ? $values['seo_weight_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_weight_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[seo_weight_meta_description]',isset($values['seo_weight_meta_description']) ? $values['seo_weight_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_weight_header_text_block"><?=Yii::t('admin', 'Header Text Block')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[weight_header_text_block]',isset($values['weight_header_text_block']) ? $values['weight_header_text_block'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_weight_footer_text_block"><?=Yii::t('admin', 'Footer Text Block')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[weight_footer_text_block]',isset($values['weight_footer_text_block']) ? $values['weight_footer_text_block'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>