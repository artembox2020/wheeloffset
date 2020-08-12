<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>
		
		<?php $tires = Tire::model()->getList();?>
		<?php echo $form->dropDownListRow($model, 'tire_id', $tires, array('empty'=>'','class'=>'span6'))?>
		<?php echo $form->textFieldRow($model, 'from', array('class'=>'span6'))?>
		<?php echo $form->textFieldRow($model, 'to', array('class'=>'span2'))?>
			
		<div id="tire_rear">	
		<?php echo $form->textFieldRow($model, 'rear_from', array('class'=>'span6'))?>
		<?php echo $form->textFieldRow($model, 'rear_to', array('class'=>'span2'))?>	
		</div>
			
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
		
    <?php $this->endWidget()?>
</div>

<script>
function toogleTire(tire) {
	if ((tire.split("R").length - 1) == 2) {
		$('#tire_rear').show();
	} else {
		$('#tire_rear').hide();
	}
}

$('#TireRimWidthRange_tire_id').change(function(){
	toogleTire($('#TireRimWidthRange_tire_id option:selected').text());
})

<?php $tireText = isset($tires[$model->tire_id])?$tires[$model->tire_id]:'';?>
toogleTire('<?=$tireText?>');

</script>
