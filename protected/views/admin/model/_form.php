<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>
        <?php echo $form->dropDownListRow($model, 'make_id', AutoMake::getAll(),array('empty'=>''))?>
		<?php echo $form->textFieldRow($model, 'title')?>
        <?php echo $form->textFieldRow($model, 'alias')?>
		
		<?php $model->post_competitors = $model->getPost_competitors()?>
		<div class="control-group">
				<label class="control-label required" for="AutoModel_post_competitors"><?php echo $model->getAttributeLabel('post_competitors')?></label>
				<div class="controls">
				<?php $this->widget('ext.chosen.Chosen',array(
					'model' => $model, 
					'attribute' => 'post_competitors', 
					'data' => AutoModel::getAllWithMakeTitle(),
					'multiple' => true,
					'noResults' => Yii::t('admin', 'Not found'),
					'placeholderMultiple'=>$model->getAttributeLabel('post_competitors'),
					'htmlOptions'=>array(
						'style' => 'width:800px;'
					),		   
				));?>
				</div>
		</div>	
		
		
		<?php echo $form->fileFieldRow($model, 'file')?>
		<br/>
		
		<?php if (!$model->isNewRecord):?>
			<img id="image_preview" src="<?=$model->image_preview?>"/>
		<?php endif;?>
		<br>
		
		<?php echo $form->textAreaRow($model, 'description', array('class'=>'ckeditor'))?>
		
		<?php echo $form->textAreaRow($model, 'text_times', array('class'=>'ckeditor'))?>
		<?php echo $form->textAreaRow($model, 'text_wheels', array('class'=>'ckeditor'))?>
		<?php echo $form->textAreaRow($model, 'text_tires', array('class'=>'ckeditor'))?>
		<?php echo $form->textAreaRow($model, 'text_horsepower', array('class'=>'ckeditor'))?>
		<?php echo $form->textAreaRow($model, 'text_dimensions', array('class'=>'ckeditor'))?>
		<?php echo $form->textAreaRow($model, 'text_tuning', array('class'=>'ckeditor'))?>
	
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
	
    <?php $this->endWidget()?>
</div>
