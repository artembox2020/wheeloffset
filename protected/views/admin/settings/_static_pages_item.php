<div class="control-group">
	<label class="control-label required" for="Config_static_<?=$alias?>_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[static_'.$alias.'_title]',isset($values['static_'.$alias.'_title']) ? $values['static_'.$alias.'_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_static_<?=$alias?>_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[static_'.$alias.'_meta_keywords]',isset($values['static_'.$alias.'_meta_keywords']) ? $values['static_'.$alias.'_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_static_<?=$alias?>_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[static_'.$alias.'_meta_description]',isset($values['static_'.$alias.'_meta_description']) ? $values['static_'.$alias.'_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_horsepower_footer_text_block"><?=Yii::t('admin', 'Content')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[static_'.$alias.'_content]',isset($values['static_'.$alias.'_content']) ? $values['static_'.$alias.'_content'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>