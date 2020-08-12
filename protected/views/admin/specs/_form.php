<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>

		<?php echo $form->textFieldRow($model, 'title', array('class'=>'span6'))?>
		
		<?php echo $form->textFieldRow($model, 'alias', array('class'=>'span6'))?>
        
		<?php echo $form->dropDownListRow($model, 'group_id', AutoSpecsGroup::getAll(), array('empty'=>''))?>
		
		<?php echo $form->checkBoxRow($model, 'is_filter')?>
		
		<?php echo $form->checkBoxRow($model, 'is_required')?>		
		
		<?php echo $form->textFieldRow($model, 'append', array('class'=>'span2'))?>
		
		<?php echo $form->textFieldRow($model, 'rank', array('class'=>'span2'))?>
		
		<?php echo $form->dropDownListRow($model, 'type', AutoSpecs::getTypes())?>
		
	<div id="options_container" class="row-fluid" style="margin-left: 50px;">
		
		<?php if (!empty($model->sizesErrors)):?>
		<div class="alert alert-block alert-error">
			<p>Необходимо исправить следующие ошибки:</p>
			<ul>
			<?php foreach ($model->sizesErrors as $err):?>
				<li><?=$err?></li>
			<?php endforeach;?>
			</ul>
		</div>	
		<?php endif;?>		
				
		<div class="control-group " style="width:250px;">
			<label class="control-label">Значения списка</label>

			<div class="controls">
				<div class="row" style="width: 800px;">

				<div id="container-list-options">	
				<?php $dataOptions = $model->getDataOptions();?>
				
				<?php if (!empty($dataOptions)):?>
					<?php foreach ($dataOptions as $i => $option): ?>

						<div class="size_item">
								
							<?php if (isset($option['id'])):?>
								<?= CHtml::hiddenField("AutoSpecs[post_options][{$i}][id]", $option['id']) ?>
							<?php endif;?>	
								
							<div class="clear" style="height: 10px;"></div>

							<div class="span6">
								<?= CHtml::textField("AutoSpecs[post_options][{$i}][value]", $option['value'], array("class"=>(isset($model->optionsErrorsIndex['value'][$i])?'error':''))) ?>
							</div>

							<?php if ($i > 0): ?>
								<div class="span1">
									<a class="remove btn btn-danger" href="#"
									   onclick="$(this).parent().parent().remove(); return false">
										x
									</a>
								</div>
							<?php endif; ?>

						</div>

					<?php endforeach; ?>
				
				<?php else :?>
						<div class="size_item">

							<div class="clear" style="height: 10px;"></div>

							<div class="span6  js-no-standart">
								<?= CHtml::textField("AutoSpecs[post_options][0][value]", '') ?>
							</div>
						

						</div>			
				<?php endif;?>
				</div>
				
				<div class="clear"></div>
				<a href="#" class="btn btn-success js-btn-add-line" >+</a>

				</div>

			</div>

		</div>	
	</div>		
		
	<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
	
    <?php $this->endWidget()?>
</div>

<script>
<?=HtmlHelper::select($model, 'model_id', 'model_year_id', '/admin/modelYear/getByModel')?>

var rowIndex = <?=sizeof($dataOptions)?>;
$('.js-btn-add-line').click(function(e){
	e.preventDefault();
	rowIndex++;
	
	html = '<div class="size_item">' +
				'<div class="clear" style="height: 2px;"></div>' +
				'<div class="span6">' +
					'<input class="js-sizes-size" type="text" value="" name="AutoSpecs[post_options]['+rowIndex+'][value]">' +                      
				'</div>' +
				'<div class="span1">' +
					'<a class="remove btn btn-danger" href="#" onclick="$(this).parent().parent().remove(); return false">x</a>'+
				'</div>' +
			'</div>';
		
	$('#container-list-options').append(html);
})


$('#AutoSpecs_type').change(function(){
	val = $(this).val();
	showFields(val)
})

function showFields(val) {
	$('#AutoSpecs_append').parent().parent().hide();
	$('#options_container').hide();
	
	if (val == <?=AutoSpecs::TYPE_SELECT?>) {
		$('#options_container').show();
	}
	
	if (val == <?=AutoSpecs::TYPE_STRING?> ||
		val == <?=AutoSpecs::TYPE_INT?> ||
		val == <?=AutoSpecs::TYPE_FLOAT?>
	) {
		$('#AutoSpecs_append').parent().parent().show();
	}
}
showFields(<?=$model->type?>);
</script>

