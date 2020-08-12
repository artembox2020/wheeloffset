<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>

		<?php echo $form->textFieldRow($model, 'value', array('class'=>'span6'))?>
	
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

