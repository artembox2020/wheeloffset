<?php
/**
 * Класс FilterHelper
 * @author dorosh_2009@meta.ua
 * 
 * Помощник фильтра
 */
class FilterHelper
{
	
	public static function tuningParse($filter)
	{
		$filterData = array();
		
		//fetch diameter
		preg_match('/wheels\-((\d*){2}?)/', $filter, $matches);
		if (isset($matches[1]) && is_int($matches[1])) {
			$filterData['diameter'] = $matches[1];
		}
		
		//fetch width
		preg_match('/wheels-(\d*)x(\d*).(\d*)/', $filter, $matches);
		if (count($matches) == 4) {
			$filterData['width'] = $matches[2] .'.'.$matches[3];
		} else {
			preg_match('/wheels-(\d*).(\d*)-width/', $filter, $matches);
			if (count($matches) == 3) {
				$filterData['width'] = $matches[1] .'.'.$matches[2];
			}
		}
		
		//d($filterData);
		
		//fetch tire
		preg_match('/tire-(\d*)/', $filter, $matches);
		if (isset($matches[1])) {
			$filterData['tire'] = $matches[1];
		}
		
		//fetch offset
		preg_match('/offset(.*)/', $filter, $matches);
		if (isset($matches[1])) {
			$filterData['offset'] = $matches[1];
		}
		
		$listDiameter 		= TireRimDiameter::getList();
		$listWidth 			= RimWidth::getAll();		
		$listTire 			= TireSectionWidth::getList();		
		$listOffset 		= RimOffsetRange::getAll();		

		if (isset($filterData['diameter'])) {
			$list = array_flip($listDiameter);
			if (isset($list[$filterData['diameter']])) {
				$filterData['rim_diameter_id'] = $list[$filterData['diameter']];
			} else {
				throw new CHttpException(404,'Page cannot be found.');
			}
		}
		
		if (isset($filterData['width'])) {
			$list = array_flip($listWidth);
			if (isset($list[$filterData['width']])) {
				$filterData['rim_width_id'] = $list[$filterData['width']];
			} else {
				throw new CHttpException(404,'Page cannot be found.');
			}
		}
		
		if (isset($filterData['tire'])) {
			$list = array_flip($listTire);
			if (isset($list[$filterData['tire']])) {
				$filterData['tire_section_width_id'] = $list[$filterData['tire']];
			} else {
				throw new CHttpException(404,'Page cannot be found.');
			}
		}
		
		if (isset($filterData['offset'])) {
			$list = array_flip($listOffset);
			if (isset($list[$filterData['offset']])) {
				$filterData['rim_offset_range_id'] = $list[$filterData['offset']];
			} else {
				throw new CHttpException(404,'Page cannot be found.');
			}
		}
		
		return $filterData;
	}
	
	function getFirstExpl($del, $string) {
		$expl = explode($del, $string);
		return trim($expl[0]);
	}
}