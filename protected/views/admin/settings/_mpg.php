<div class="control-group ">
	<label class="control-label required" for="Config_seo_mpg_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_mpg_title]',isset($values['seo_mpg_title']) ? $values['seo_mpg_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_mpg_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[seo_mpg_meta_keywords]',isset($values['seo_mpg_meta_keywords']) ? $values['seo_mpg_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_seo_mpg_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[seo_mpg_meta_description]',isset($values['seo_mpg_meta_description']) ? $values['seo_mpg_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_mpg_header_text_block"><?=Yii::t('admin', 'Header Text Block')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[mpg_header_text_block]',isset($values['mpg_header_text_block']) ? $values['mpg_header_text_block'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_mpg_footer_text_block"><?=Yii::t('admin', 'Footer Text Block')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[mpg_footer_text_block]',isset($values['mpg_footer_text_block']) ? $values['mpg_footer_text_block'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>