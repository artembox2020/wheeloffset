<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>
	
		<?php echo $form->textFieldRow($model, 'title', array('class'=>'span6'))?>
		<?php echo $form->textFieldRow($model, 'alias', array('class'=>'span2'))?>
		<?php echo $form->dropDownListRow($model, 'model_id', AutoModel::getAllWithMake(),array('empty'=>''))?>
		
		<?php 
		$years = array();
		$items=AutoModelYear::getAllByModel(isset($model->model_id)?$model->model_id:0);
		foreach ($items as $item) {
			$years[$item] = $item;
		}
		?>
		
		<?php echo $form->dropDownListRow($model, 'year_from', $years,array('empty'=>''))?>
		<?php echo $form->dropDownListRow($model, 'year_to', $years,array('empty'=>''))?>
		
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
		
    <?php $this->endWidget()?>
</div>

<script>
$('#AutoModelYearChassis_model_id').change(function(e) {
	$('#AutoModelYearChassis_year_from').empty();
	$('#AutoModelYearChassis_year_from').append('<option value=""></option>');
	$('#AutoModelYearChassis_year_to').empty();
	$('#AutoModelYearChassis_year_to').append('<option value=""></option>');
	
	$.post('/admin/modelYear/getByModel', {'id': $(this).val()} , function(response) {
		$.each(response.items, function(value, lable){
			$('#AutoModelYearChassis_year_from').append('<option value="'+lable+'">'+lable+'</option>');
			$('#AutoModelYearChassis_year_to').append('<option value="'+lable+'">'+lable+'</option>');
		});
	}, 'json');
});
</script>

