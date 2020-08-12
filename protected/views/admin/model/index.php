<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Models');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Models')?></h3>
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
		<?php if (Access::is('model.create')):?>
			<? $this->widget("bootstrap.widgets.TbButtonGroup", array(
				'type' => 'success',
				'buttons' => array(
					array('label' => Yii::t('admin', 'Create'), 'url' => Yii::app()->createUrl("admin/model/create"), 'icon' => 'file white'),
				),
			))?>
		<?php endif;?>
        <div class="btn clear-filter"><?=Yii::t('admin', 'Clear Search')?></div>
    </div>
	
	
    <div class="grid">
        <?php 
		
		$actionButtons = array();
		if (Access::is('model.update')) {
			$actionButtons[] = 	array(
				'id' => 'btn_activate',
				'buttonType' => 'button',
				'type' => 'success',
				'size' => 'small',
				'label' => Yii::t('admin', 'Show'),
				'click' => 'js:function(values){	
					var ids = new Array();
					values.each(function(idx){
						 ids.push($(this).val())	
					});	
					$.ajax({
						type: "POST",
						url: "/admin/model/active/?value=1",
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
			$actionButtons[] = 	array(
				'id' => 'btn_deactivate',
				'buttonType' => 'button',
				'type' => 'warning',
				'size' => 'small',
				'label' => Yii::t('admin', 'Hide'),
				'click' => 'js:function(values){	
					var ids = new Array();
					values.each(function(idx){
						 ids.push($(this).val())	
					});	
					$.ajax({
						type: "POST",
						url: "/admin/model/active/?value=0",
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
			
			$actionButtons[] = 	array(
				'id' => 'btn_copy',
				'buttonType' => 'button',
				'type' => 'info',
				'size' => 'small',
				'label' => Yii::t('admin', 'Copy'),
				'click' => 'js:function(values){	
					var ids = new Array();
					values.each(function(idx){
						 ids.push($(this).val())	
					});	
					
					checkedIds = ids;
					$("#errors-action-models").hide();
					$("#btn_models_move").hide();
					$("#btn_models_copy").show();
					$("#modalCopyModel").modal("show");
					$("#js-modal-title").text("'.Yii::t('admin', 'Copy Models').'");
				}',
			);
			
			$actionButtons[] = 	array(
				'id' => 'btn_move',
				'buttonType' => 'button',
				'type' => 'primary',
				'size' => 'small',
				'label' => Yii::t('admin', 'Move'),
				'click' => 'js:function(values){	
					var ids = new Array();
					values.each(function(idx){
						 ids.push($(this).val())	
					});
					checkedIds = ids;
					
					$("#errors-action-models").hide();
					$("#btn_models_copy").hide();
					$("#btn_models_move").show();
					$("#modalCopyModel").modal("show");	
					$("#js-modal-title").text("'.Yii::t('admin', 'Move Models').'");
				}',
			);
		}
		
		if (Access::is('model.delete')) {
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
								url: "/admin/model/trash/?value=1",
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
					'name' => 'image_preview',
					'value' => 'CHtml::link("<img src=\"{$data->image_preview}\">", "#")',
					'type' => 'raw',
					'htmlOptions' => array(
						'width' => 100, 
						'height' => 60, 
					),	
					'filter' => false,
				),	
				array(
					'name' => 'make_id',
					'value' => '$data->Make?$data->Make->title:"-"',	
					'filter' => AutoMake::getAll(),
				),				
				array(
					'class'=>'ELinkUpdateColumn',
					'name' => 'title',
					'htmlOptions' => array(
						'access' => 'model.update', 
					),	
				),	
            );
			
		if (Access::is('model.update')) {
			$columns[] = array(
					'class' => 'bootstrap.widgets.TbToggleColumn',
					'name' => 'is_active',
					'filter' => HtmlHelper::getYesNoFilter(),
					'htmlOptions' => array(
						'width' => 80, 
						'style' => 'text-align: center;', 
					),						
				);
		}			
			
		if (Access::is('model.update') || Access::is('model.delete')) {
			$template = array();
			$buttons = array();
			
			if (Access::is('model.update')) {
				$template[] = '{update}';
				$buttons['update'] = array(
                    'url'=>'Yii::app()->createUrl("admin/model/update", array("id"=>$data->id))',
                );
			}
			
			if (Access::is('model.delete')) {
				$template[] = '{delete}';
				$buttons['delete'] = array(
                    'url'=>'Yii::app()->createUrl("admin/model/trash", array("ids"=>array($data->id), "value"=>1))',
                );				
			}			
			
			$columns[] = array(
				'htmlOptions' => array('nowrap'=>'nowrap', 'style' => 'text-align: center;'),
                 'class'=>'bootstrap.widgets.TbButtonColumn',
                 'template' => implode('', $template),
                 'buttons' => $buttons,
            );
		}
		
		$this->widget('bootstrap.widgets.TbExtendedGridView', array(
            'dataProvider' => $model->search(),
			'ajaxUrl'=> $this->createUrl('/admin/model/index'),
            'filter' => $model,
            'bulkActions' => $bulkActions,
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

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'modalCopyModel')); ?>
 <div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4 id="js-modal-title"><?=Yii::t('admin', 'Copy Models')?></h4>
</div>
<div class="modal-body">

	<div id="errors-action-models" style="display:none;" class="alert in alert-block fade alert-error"></div>	

	<div class="control-group">
		<label class="control-label" for="Offer_post_categories"><?=Yii::t('admin', 'Make')?> <span class="required">*</span></label>
		<div class="controls">
			<input type="hidden" id="make_id" value="">
			<?php echo CHtml::dropDownList('AutoModel[make_id]', '', AutoMake::getAll(), array('class'=>'span4', 'empty'=>'', 'onchange'=>'$("#make_id").val($(this).val())')); ?>	
		</div>
	</div>		


</div>
<div class="modal-footer">
    
	<a onclick="moveModels();return false;" class="btn btn-primary" id="btn_models_move" href="#"><?=Yii::t('admin', 'Move')?></a>
	
	<a onclick="copyModels();return false;" class="btn btn-info" id="btn_models_copy" href="#"><?=Yii::t('admin', 'Copy')?></a>
	
	<a onclick="$('#modalCopyModel').modal('hide');return false;" class="btn" href="#" style="display: inline-block;"><?=Yii::t('admin', 'Cancel')?></a>
	
</div> 
<?php $this->endWidget(); ?>

<script>
var checkedIds;

function moveModels() {
	$.ajax({
		type: "POST",
		url: "/admin/model/move",
		data: {"ids":checkedIds, 'make_id':$('#make_id').val()},
		dataType:"json",
		success: function(response){
			if(response.status == 1){
			   $('#modalCopyModel').modal('hide');
			   $.fn.yiiGridView.update("list-grid");
			}else{
				$('#errors-action-models').show().text(response.error);
			}
		}
	});	
}

function copyModels() {
	$.ajax({
		type: "POST",
		url: "/admin/model/copy",
		data: {"ids":checkedIds, 'make_id':$('#make_id').val()},
		dataType:"json",
		success: function(response){
			if(response.status == 1){
			   $('#modalCopyModel').modal('hide');
			   $.fn.yiiGridView.update("list-grid");
			}else{
				$('#errors-action-models').show().text(response.error);
			}
		}
	});	
}

</script>