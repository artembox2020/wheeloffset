<p class="help-block">used: [make] </p>

<div class="control-group ">
	<label class="control-label required" for="Config_product_bulb_make_meta_title"><?=Yii::t('admin', 'Title')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[product_bulb_make_meta_title]',isset($values['product_bulb_make_meta_title']) ? $values['product_bulb_make_meta_title'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_product_bulb_make_meta_keywords"><?=Yii::t('admin', 'Meta Keywords')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[product_bulb_make_meta_keywords]',isset($values['product_bulb_make_meta_keywords']) ? $values['product_bulb_make_meta_keywords'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_product_bulb_make_meta_description"><?=Yii::t('admin', 'Meta Description')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[product_bulb_make_meta_description]',isset($values['product_bulb_make_meta_description']) ? $values['product_bulb_make_meta_description'] : '', array('class'=>'span6'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_product_bulb_make_seo_header_text"><?=Yii::t('admin', 'Header Text Block')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[product_bulb_make_seo_header_text]',isset($values['product_bulb_make_seo_header_text']) ? $values['product_bulb_make_seo_header_text'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_product_bulb_make_seo_footer_text"><?=Yii::t('admin', 'Footer Text Block')?></label>
	<div class="controls">
		<?php echo CHtml::textArea('Config[product_bulb_make_seo_footer_text]',isset($values['product_bulb_make_seo_footer_text']) ? $values['product_bulb_make_seo_footer_text'] : '', array('class'=>'ckeditor'));?>
	</div>
</div>