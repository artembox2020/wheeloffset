<?php if (empty($model->rear_rim_diameter_id)) {$model->rear_rim_diameter_id=$model->rim_diameter_id;}?>

<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>

		<?php echo $form->dropDownListRow($model, 'vehicle_class_id', CHtml::listData(TireVehicleClass::getAll(), 'id', 'title'), array('empty'=>'-'))?>
		
		<?php echo $form->dropDownListRow($model, 'section_width_id', CHtml::listData(TireSectionWidth::getAll(), 'id', 'value'), array('empty'=>'-'))?>
		
		<?php echo $form->dropDownListRow($model, 'aspect_ratio_id', CHtml::listData(TireAspectRatio::getAll(), 'id', 'value'), array('empty'=>'-'))?>
		
		<?php echo $form->dropDownListRow($model, 'rim_diameter_id', CHtml::listData(TireRimDiameter::getAll(), 'id', 'value'), array('empty'=>'-'))?>
				
		<?php echo $form->dropDownListRow($model, 'load_index_id', CHtml::listData(TireLoadIndex::getAll(), 'id', 'index'), array('empty'=>'-'))?>
				
		<?php echo $form->dropDownListRow($model, 'is_runflat', array(0=>'non-runflat', 1=>'runflat'))?>
		
		<?php echo $form->checkboxRow($model, 'is_rear')?>
		
		<div id="rear_container" style="display: <?=$model->is_rear?'block':'none'?>">
			<?php echo $form->dropDownListRow($model, 'rear_section_width_id', CHtml::listData(TireSectionWidth::getAll(), 'id', 'value'), array('empty'=>'-'))?>
			<?php echo $form->dropDownListRow($model, 'rear_aspect_ratio_id', CHtml::listData(TireAspectRatio::getAll(), 'id', 'value'), array('empty'=>'-'))?>
			<?php echo $form->dropDownListRow($model, 'rear_rim_diameter_id', CHtml::listData(TireRimDiameter::getAll(), 'id', 'value'), array('empty'=>'-'))?>
		</div>
		
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
		
		
    <?php $this->endWidget()?>
</div>

<script>
$('#Tire_is_rear').change(function(){
	if ($(this).is(':checked')) {
		$('#rear_container').show();
	} else {
		$('#rear_container').hide();
	}
})
$('#Tire_rim_diameter_id').change(function(){
	val = $(this).val();
	if ($('#Tire_rear_rim_diameter_id').val() == '') {
		$("#Tire_rear_rim_diameter_id [value='"+val+"']").attr("selected", "selected");
	} 
})
</script>

