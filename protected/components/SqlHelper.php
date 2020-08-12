<?php
/**
 * Класс SqlHelper
 * @author dorosh_2009@meta.ua
 * 
 * Помощник sql
 */
class SqlHelper
{
	
	public static function addView($modelName, $id)
	{
		$table = CActiveRecord::model($modelName)->tableName();
		$sql = "UPDATE {$table} SET view_count = view_count +1 WHERE id = {$id}";
		Yii::app()->db->createCommand($sql)->execute();
	}

}