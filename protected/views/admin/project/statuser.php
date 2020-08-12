<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Statistic Projects by Users');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Statistic Projects by Users')?></h3>
    </div>
    
    <div class="grid">
        <?php 
		$this->widget('bootstrap.widgets.TbExtendedGridView', array(
            'dataProvider' => $model->search(),
			'ajaxUrl'=> $this->createUrl('/admin/project/statuser'),
            'filter' => $model,
		    'type' => 'striped bordered condensed',
            'columns' => array(
				array(
					'name' => 'user_id',
					'value' => 'isset($data->User)?$data->User->full_name:"-"',	
					'filter' => Admin::getAll(),
				),												
				'date',
				'total',
				'total_day',
				'total_month',
            ),
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