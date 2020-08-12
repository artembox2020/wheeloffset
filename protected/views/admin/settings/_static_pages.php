<?php 
	$pages = array(
		'about' => 'About us',
	);

	$tabs = array();
	$i=0;
	foreach ($pages as $alias=>$title) {
		$tabs[] = array(
					'label'=>$title, 
					'active'=>$i==0, 
					'content' => $this->renderPartial('_static_pages_item', array(
						'form'=>$form,
						'values'=>$values,
						'alias'=>$alias,
					), 
					true
				));
		$i++;
	}	
?>        
<?php $this->widget('bootstrap.widgets.TbTabs', array(
	'placement'=>'left',
    'tabs'=>$tabs
));?>

