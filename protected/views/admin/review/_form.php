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

		<?php echo $form->dropDownListRow($model, 'model_id', AutoModel::getAllWithMake(),array('empty'=>''))?>
		
		<?php $years = AutoModelYear::getAllByModel($model->model_id);?>
		<?php $model->post_model_year = $model->getPost_model_year();?>
		<?php echo $form->dropDownListRow($model, 'post_model_year', $years, array('size'=>count($years), 'multiple'=>true))?>
		
		<?php echo $form->textAreaRow($model, 'description', array('style'=>'width: 500px; height: 200px;'))?>
		<div style="margin-left: 180px;">Markers: [<?=implode('], [', ReviewVsModelYear::getMarkers());?>]</div>
		
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
		
    <?php $this->endWidget()?>
</div>

<script>
$('#Review_model_id').change(function(e) {
	var _list = $('#Review_post_model_year');
	_list.empty();
	$.post('/admin/modelYear/getByModel', {'id': $(this).val()} , function(response) {
		i=0;
		$.each(response.items, function(value, label) {
			_list.append('<option value="'+value+'">'+label+'</option>');
			i++;
		});
		_list.attr('size', i);
	}, 'json');
});
</script>


