<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>

		<?php echo $form->fileFieldRow($model, 'file')?>
			
		<?php if (!$model->isNewRecord):?>
			<img id="image_preview" src="<?=AutoCompletion::getThumb(AutoCompletion::PHOTO_DIR.$model->image_path, 150, 80, 'resize');?>"/>
		
			<?php echo $form->checkBoxRow($model, 'is_delete_photo')?><br/>
		<?php endif;?>

		
		<fieldset>
			<legend><?=Yii::t('admin', 'General')?></legend>	
			<?php echo $form->dropDownListRow($model, 'model_id', AutoModel::getAllWithMake(),array('empty'=>''))?>
		
			<?php echo $form->dropDownListRow($model, 'model_year_id', AutoModelYear::getAllByModel(isset($model->ModelYear)?$model->ModelYear->model_id:0),array('empty'=>''))?>
		
			<?php echo $form->textFieldRow($model, 'title', array('class'=>'span6'))?>	
		</fieldset>
		
		<?php $groups = AutoSpecs::getAllWithGroup();?>
		<?php foreach ($groups as $group):?>
		<fieldset>
			<legend><?=$group['title']?></legend>
			<?php if (isset($group['specs'])):?>
				<?php foreach ($group['specs'] as $alias=>$spec):?>
					<?php if (in_array($spec['type'], array(AutoSpecs::TYPE_FLOAT, AutoSpecs::TYPE_STRING))):?>
						<?php echo $form->textFieldRow($model, 'specs_'.$alias, array('class'=>'span6', 'maxlenght'=>64, 'append'=>$spec['append']))?>
					<?php endif;?>
					<?php if ($spec['type']==AutoSpecs::TYPE_INT):?>
						<?php echo $form->numberFieldRow($model, 'specs_'.$alias, array('class'=>'span6', 'maxlenght'=>11, 'append'=>$spec['append']))?>
					<?php endif;?>
					<?php if ($spec['type']==AutoSpecs::TYPE_CHECKBOX):?>
						<?php echo $form->toggleButtonRow($model, 'specs_'.$alias)?>
					<?php endif;?>
					<?php if ($spec['type']==AutoSpecs::TYPE_SELECT):?>
						<?php echo $form->dropDownListRow($model, 'specs_'.$alias, AutoSpecsOption::getAllBySpecs($spec['id']), array('empty'=>'', 'class'=>'span4', 'append'=>$spec['append']))?>
					<?php endif;?>
				<?php endforeach;?>
			<?php endif;?>
		</fieldset>		
		<?php endforeach;?>
		
	
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
	
    <?php $this->endWidget()?>
</div>

<script>
<?=HtmlHelper::select($model, 'model_id', 'model_year_id', '/admin/modelYear/getByModel')?>
</script>

