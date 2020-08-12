<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider*/
/* @var $model User */

$this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Parsing Albums');
?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Parsing Albums')?></h3>
    </div>
	
	<?php $this->widget('AdminTbAlert', array(
        'block'=>true, 
        'fade'=>true,
        'closeText'=>'&times;',
        'alerts'=>array(
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'),
        ))); 
	?> 	
	
    <div class="button">
			<? $this->widget("bootstrap.widgets.TbButtonGroup", array(
				'type' => 'success',
				'buttons' => array(
					array('label' => Yii::t('admin', 'Move photos to Model by Year'), 'url' => Yii::app()->createUrl("admin/parsing/worldcarfans/modelYear"), 'icon' => 'file white'),
				),
			))?>
		<div class="btn clear-filter"><?=Yii::t('admin', 'Clear Search')?></div>
    </div>
    <div class="grid">
        <?php

		$actionButtons = array();
			$actionButtons[] = 	array(
				'id' => 'btn_move',
				'buttonType' => 'button',
				'type' => 'success',
				'size' => 'small',
				'label' => Yii::t('admin', 'Move'),
				'click' => 'js:function(values){	
					var ids = new Array();
					values.each(function(idx){
						 ids.push($(this).val())	
					});
					checkedIds = ids;
					
					$("#errors-action-models").hide();
					$("#modalWorldcarfans").modal("show");	
					$("#js-modal-title").text("'.Yii::t('admin', 'Move Albums').'");	
				}',
			);

			

		
		if (true) {
			$actionButtons[] = array(
						'id' => 'btn_delete',
						'buttonType' => 'button',
						'type' => 'danger',
						'size' => 'small',
						'label' => Yii::t('admin', 'Delete'),
						'click' => 'js:function(values){
							var ids = new Array();
							values.each(function(idx){
								 ids.push($(this).val())	
							});	
							$.ajax({
								type: "POST",
								url: "/admin/parsing/worldcarfans/deleteAll",
								data: {"ids":ids},
								dataType:"json",
								success: function(response){
									if(response.status == 1){
									   $.fn.yiiGridView.update("list-grid");
									}else{
										alert(response.error);
									}
								}
							});								
						}',
					);
		}
		$bulkActions = array();
		if (!empty($actionButtons)) {
			$bulkActions = array(
				'actionButtons' => $actionButtons,
				'checkBoxColumnConfig' => array(
					'name' => 'id'
				),
			);
		}		
		
		$columns = array(
				array(
					'name'=>'id',
					'value'=>'$data->id',
					'htmlOptions' => array(
						'width' => 60, 
					),					
				),	
				array(
					'name'=>'title',
					'value'=>'HtmlHelper::worldcarfansTitle($data)',
					'type' => 'raw',
				),				
				array(
					'name' => 'model_year_id',
					'value' => '$data->ModelYear?$data->ModelYear->Model->Make->title ." ". $data->ModelYear->Model->title . " ". $data->ModelYear->year:"-"',	
					'filter' => false,//AutoModel::getAllWithMake(),
				),				
				array(
					'name'=>'is_new',
					'value'=>'$data->is_new?"Yes":"No"',
					'filter' => HtmlHelper::getYesNoFilter(),
				),								
				array(
					'name'=>'create_time',
					'value'=>'date("Y-m-d H:i", $data->create_time)',
					'filter' => false,
				),								
                array(
                    'htmlOptions' => array('nowrap'=>'nowrap', 'style' => 'text-align: center;'),
                    'class'=>'bootstrap.widgets.TbButtonColumn',
                    'template' => '{delete}',
                ),
            );
		
		$this->widget('bootstrap.widgets.TbExtendedGridView', array(
            'dataProvider' => $model->search(),
			'ajaxUrl'=> $this->createUrl('/admin/parsing/worldcarfans'),
            'bulkActions' => $bulkActions,
            'filter' => $model,
            'type' => 'striped bordered condensed',
            'columns' => $columns,
			'id' => 'list-grid',
			'pager' => array(
				  'class' => 'bootstrap.widgets.TbPager',
				  'displayFirstAndLast' => true,
			),			
			'template'=>'{summary}{items}<div style="float:left">{pager}</div> <div style="float:right;margin-top:15px;"><span style="color:#666;">per page:</span> '.
			CHtml::dropDownList(
				'pageSize',
				$pageSize,
				Yii::app()->params->perPages,
				array('class'=>'change-pageSize', 'style'=>'width: 70px;')) . '</div>',			
        ))?>
    </div>

</div>

<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('.change-pageSize').live('change', function() {
        $.fn.yiiGridView.update('list-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY); ?>



<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'modalWorldcarfans')); ?>
 <div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4 id="js-modal-title"><?=Yii::t('admin', 'Copy Completions')?></h4>
</div>
<div class="modal-body">

	<div id="errors-action-models" style="display:none;" class="alert in alert-block fade alert-error"></div>	
	
	<div class="control-group">
		<label class="control-label"><?=Yii::t('admin', 'Model')?> <span class="required">*</span></label>
		<div class="controls">
			<?php echo CHtml::dropDownList('ParsingWorldcarfansAlbum[model_id]', '', AutoModel::getAllWithMake(), array('class'=>'span4', 'empty'=>'-')); ?>	
		</div>
	</div>	
	
	<div class="control-group">
		<label class="control-label"><?=Yii::t('admin', 'Year')?> <span class="required">*</span></label>
		<div class="controls">
			<input type="hidden" id="model_year_id" value="">
			<?php echo CHtml::dropDownList('ParsingWorldcarfansAlbum[model_year_id]', '', array(), array('class'=>'span4 js-model_year_id', 'empty'=>'-', 'onchange'=>'$("#model_year_id").val($(this).val())')); ?>	
		</div>
	</div>		
</div>

<div class="modal-footer">
    
	<a onclick="moveCompletions();return false;" class="btn btn-primary" id="btn_models_move" href="#"><?=Yii::t('admin', 'Move')?></a>
		
	<a onclick="$('#modalWorldcarfans').modal('hide');return false;" class="btn" href="#" style="display: inline-block;"><?=Yii::t('admin', 'Cancel')?></a>
	
</div> 
<?php $this->endWidget(); ?>

<script>
var checkedIds;

function moveCompletions() {
	$.ajax({
		type: "POST",
		url: "/admin/parsing/worldcarfans/move",
		data: {"ids":checkedIds, 'model_year_id':$('#model_year_id').val()},
		dataType:"json",
		success: function(response){
			if(response.status == 1){
			   $('#modalWorldcarfans').modal('hide');
			   $.fn.yiiGridView.update("list-grid");
			}else{
				$('#errors-action-models').show().text(response.error);
			}
		}
	});	
}


$('#ParsingWorldcarfansAlbum_model_id').change(function(e) {
	$('.js-model_year_id').empty();
	$('.js-model_year_id').append('<option value=""></option>');
	$.post('/admin/modelYear/getByModel', {'id': $(this).val()} , function(response) {
		$.each(response.items, function(value, lable){
			$('.js-model_year_id').append('<option value="'+value+'">'+lable+'</option>');
		});
	}, 'json');
});

$('body').on('click', '.js-worldcarfans-link', function(e){
	e.preventDefault();
	var ids = new Array();
	ids.push($(this).attr('rel'))	

	checkedIds = ids;
					
	$("#errors-action-models").hide();
	$("#modalWorldcarfans").modal("show");	
	$("#js-modal-title").text("<?=Yii::t('admin', 'Move Albums')?>");
})

</script>