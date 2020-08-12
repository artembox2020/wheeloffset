<?php
/**
 * ELinkUpdateColumn class file.
 *
 * This column assumes that the filename is saved as a path to the
 * image that is to be rendered. If no pathPrefix is given, it 
 * assumes Yii::app()->baseUrl as a prefix for the image.
 * 
 * Example Usage:
 *
	Yii::import('application.components.ELinkUpdateColumn');
  $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'photo-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		array(
			'class'=>'ELinkUpdateColumn',
			'name' => 'filename',
			'htmlOptions' => array('style' => 'width: 150px;'),
			),
		'album.title',
		'album.category.title',
		'title',
		'filename',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
 *
 * @author Herbert Maschke<thyseus@gmail.com>
 * @link http://www.yiiframework.com/
 * @license http://www.yiiframework.com/license/
 */

Yii::import('zii.widgets.grid.CDataColumn');

/**
 * CImageColumn represents a grid view column that displays an image, and optional, a link
 *
 */
class ELinkUpdateColumn extends CDataColumn
{
	public $linkHtmlOptions;
	public $url = null;

	protected function renderDataCellContent($row,$data)
	{		
		$url = !empty($this->url) ? str_replace('%id%', $data->id, $this->url) : Yii::app()->controller->createUrl("update",array("id"=>$data->id));
	
		if($this->value!==null)
			$value=$this->evaluateExpression($this->value,array('data'=>$data,'row'=>$row));
		elseif($this->name!==null)
			$value=CHtml::value($data,$this->name);
		$title = $value===null ? $this->grid->nullDisplay : $this->grid->getFormatter()->format($value,$this->type);
		
		if (Access::is($this->htmlOptions['access'])) 
			echo CHtml::link($title, $url, $this->linkHtmlOptions);
		else
			echo $title;
	}
}
