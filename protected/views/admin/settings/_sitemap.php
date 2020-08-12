<div class="control-group ">
	<label class="control-label required" for="Config_sitemap_domain_url"><?=Yii::t('admin', 'Domain Url')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[sitemap_domain_url]',isset($values['sitemap_domain_url']) ? $values['sitemap_domain_url'] : '', array('class'=>'span6', 'placeholder'=>'http://test.com'));?>
	</div>
</div>

<div class="control-group ">
	<label class="control-label required" for="Config_count_items_in_file"><?=Yii::t('admin', 'Count Items in File')?></label>
	<div class="controls">
		<?php echo CHtml::textField('Config[count_items_in_file]',isset($values['count_items_in_file']) ? $values['count_items_in_file'] : '', array('class'=>'span6'));?>
	</div>
</div>