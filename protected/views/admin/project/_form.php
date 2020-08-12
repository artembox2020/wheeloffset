<?php echo $form->checkboxRow($model, 'is_active')?>

<?php echo $form->dropDownListRow($model, 'make_id', AutoMake::getAll(),array('empty'=>''))?>

<?php echo $form->dropDownListRow($model, 'model_id', AutoModel::getAllByMake($model->make_id),array('empty'=>''))?>
		
<?php echo $form->dropDownListRow($model, 'model_year_id', AutoModelYear::getAllByModel($model->model_id, 1), array('empty'=>''))?>

<?php echo $form->textFieldRow($model, 'wheel_manufacturer')?>

<?php echo $form->textFieldRow($model, 'wheel_model')?>

<?php echo $form->dropDownListRow($model, 'rim_diameter_id', TireRimDiameter::getList(),array('empty'=>''))?>

<?php echo $form->dropDownListRow($model, 'rim_width_id', RimWidth::getAll(),array('empty'=>''))?>

<?php echo $form->dropDownListRow($model, 'rim_offset_range_id', RimOffsetRange::getAll(),array('empty'=>''))?>
	
<?php echo $form->checkboxRow($model, 'is_staggered_wheels')?>
	
<div id="container-is_staggered_wheels">
	<?php echo $form->dropDownListRow($model, 'rear_rim_diameter_id', TireRimDiameter::getList(),array('empty'=>''))?>

	<?php echo $form->dropDownListRow($model, 'rear_rim_width_id', RimWidth::getAll(),array('empty'=>''))?>

	<?php echo $form->dropDownListRow($model, 'rear_rim_offset_range_id', RimOffsetRange::getAll(),array('empty'=>''))?>
	
	<?php echo $form->textFieldRow($model, 'rear_wheel_manufacturer')?>
	
	<?php echo $form->textFieldRow($model, 'rear_wheel_model')?>
</div>	
	
<hr>	
<?php echo $form->textFieldRow($model, 'tire_manufacturer')?>

<?php echo $form->textFieldRow($model, 'tire_model')?>	
	
<?php echo $form->dropDownListRow($model, 'tire_section_width_id', TireSectionWidth::getList(),array('empty'=>''))?>	

<?php echo $form->dropDownListRow($model, 'tire_aspect_ratio_id', TireAspectRatio::getList(),array('empty'=>''))?>	

<?php echo $form->dropDownListRow($model, 'tire_vehicle_class_id', TireVehicleClass::getList(),array('empty'=>''))?>	
	
<?php echo $form->checkboxRow($model, 'is_staggered_tires')?>	
	
<div id="container-is_staggered_tires">
	<?php echo $form->dropDownListRow($model, 'rear_tire_section_width_id', TireSectionWidth::getList(),array('empty'=>''))?>	

	<?php echo $form->dropDownListRow($model, 'rear_tire_aspect_ratio_id', TireAspectRatio::getList(),array('empty'=>''))?>

	<?php echo $form->textFieldRow($model, 'rear_tire_manufacturer')?>
	
	<?php echo $form->textFieldRow($model, 'rear_tire_model')?>	
</div>		

<?php echo $form->textFieldRow($model, 'source')?>
	
<?php echo $form->textAreaRow($model, 'description', array('class'=>'ckeditor'))?>		

<script>
<?=HtmlHelper::select($model, 'make_id', 'model_id', '/admin/model/getByMake', '$("#Project_model_year_id").empty()')?>
<?=HtmlHelper::select($model, 'model_id', 'model_year_id', '/admin/modelYear/getByModel?onlyNotDeleted=1')?>

$('#Project_is_staggered_wheels').change(function(){
	toogleCheckbox('is_staggered_wheels', $(this).is(':checked'));
})

$('#Project_is_staggered_tires').change(function(){
	toogleCheckbox('is_staggered_tires', $(this).is(':checked'));
})

function toogleCheckbox(attr, val) {
	if (val) {
		$('#container-'+attr).show();
	} else {
		$('#container-'+attr).hide();
	}
}

toogleCheckbox('is_staggered_wheels', <?=(int)$model->is_staggered_wheels?>);
toogleCheckbox('is_staggered_tires', <?=(int)$model->is_staggered_tires?>);

</script>
