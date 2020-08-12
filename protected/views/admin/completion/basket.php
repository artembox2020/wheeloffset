<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Basket Completions');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Basket Completions')?></h3>
    </div>
	
    <div class="grid">
        <?php 
		
		
		$actionButtons = array();
			$actionButtons[] = 	array(
				'id' => 'btn_restore',
				'buttonType' => 'button',
				'type' => 'success',
				'size' => 'small',
				'label' => Yii::t('admin', 'Restore'),
				'click' => 'js:function(values){	
					var ids = new Array();
					values.each(function(idx){
						 ids.push($(this).val())	
					});	
					$.ajax({
						type: "POST",
						url: "/admin/completion/trash/?value=0",
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
								url: "/admin/completion/delete",
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
				'title',
				array(
					'name' => 'model_year_id',
					'value' => '$data->ModelYear?$data->ModelYear->Model->title:"-"',	
					'filter' => AutoModel::getAllWithMake(),
				),					
				array(
					'name' => 'year',
					'value' => '$data->ModelYear?$data->ModelYear->year:"-"',
					'filter' => true,
				),					
            );
					
		$this->widget('bootstrap.widgets.TbExtendedGridView', array(
            'dataProvider' => $model->search(),
			'ajaxUrl'=> $this->createUrl('/admin/completion/basket'),
            'filter' => $model,
            'type' => 'striped bordered condensed',
            'columns' => $columns,
			'id' => 'list-grid',
			'bulkActions' => $bulkActions,
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