<?php
/**
 * Класс HtmlHelper
 * @author dorosh_2009@meta.ua
 * 
 * Помощник html
 */
class HtmlHelper
{
	
	public static function select($model, $parent, $child, $url, $function='')
	{
		$js = "$('#".get_class($model)."_".$parent."').change(function(e) {";
		$js.= 	"$('#".get_class($model)."_".$child."').empty();";
		$js.= 	"$('#".get_class($model)."_".$child."').append('<option value=\"\"></option>');";
		$js.= 	"$.post('".$url."', {'id': $(this).val()} , function(response) {";
		$js.= 		"$.each(response.items, function(value, lable){";
		$js.= 			"$('#".get_class($model)."_".$child."').append('<option value=\"'+value+'\">'+lable+'</option>');";
		$js.= 		"});";
		$js.= 		"{$function}";
		$js.= 	"}, 'json');";
		$js.= "});";
		
		return $js;	
	}
	
	public static function getYesNoFilter()
	{
		return array(
			1 => Yii::t('admin', 'Yes'),
			0 => Yii::t('admin', 'No'),
		);
	}
	
	public static function getYesNoValue($value)
	{
		$items = self::getYesNoFilter();
		if ($items[$value]) {
			return $items[$value];
		} else {
			return false;
		}
	}
	
	public static function worldcarfansTitle($data)
	{
		if ($data->is_new == 0) {
			return $data->title;
		} else {
			return CHtml::link($data->title, '#', array('rel'=>$data->id, 'class'=>'js-worldcarfans-link'));
		}
	}
	
	public static function price($value)
	{
		return '$' . number_format($value, 0, '', ',');
	}
}