<?php

class ArrayHelper {
	
	public static function getArrayСircleNeighbor($itemIds, $current_id)
	{
		$dataIds = array();
	
		$size = sizeof($itemIds);
		sort($itemIds);	
	
		foreach ($itemIds as $key=>$id) {
			if ($current_id == $id) {
				
				if (isset($itemIds[$key-1])) {
					$dataIds[] = $itemIds[$key-1];
				} else if (isset($itemIds[$size-1])) {
					$dataIds[] = $itemIds[$size-1];
				}
				
				if (isset($itemIds[$key-2])) {
					$dataIds[] = $itemIds[$key-2];
				} else if (isset($itemIds[$size-2])) {
					if (!in_array($itemIds[$size-1], $dataIds)) {
						$dataIds[] = $itemIds[$size-1];
					} else {
						$dataIds[] = $itemIds[$size-2];
					}
				}
				
				if (isset($itemIds[$key-3])) {
					$dataIds[] = $itemIds[$key-3];
				} else if (isset($itemIds[$size-3])) {
					if (!in_array($itemIds[$size-1], $dataIds)) {
						$dataIds[] = $itemIds[$size-1];
					} else if (!in_array($itemIds[$size-2], $dataIds)) {
						$dataIds[] = $itemIds[$size-2];
					} else {
						$dataIds[] = $itemIds[$size-3];
					}	
				}
				
				if (isset($itemIds[$key+1])) {
					$dataIds[] = $itemIds[$key+1];
				} else if (isset($itemIds[0])) {
					$dataIds[] = $itemIds[0];
				}
				
				if (isset($itemIds[$key+2])) {
					$dataIds[] = $itemIds[$key+2];
				} else if (isset($itemIds[1])) {
					if (!in_array($itemIds[0], $dataIds)) {
						$dataIds[] = $itemIds[0];
					} else {
						$dataIds[] = $itemIds[1];
					}	
				}
				
				if (isset($itemIds[$key+3])) {
					$dataIds[] = $itemIds[$key+3];
				} else if (isset($itemIds[2])) {
					if (!in_array($itemIds[0], $dataIds)) {
						$dataIds[] = $itemIds[0];
					} else if (!in_array($itemIds[1], $dataIds)) {
						$dataIds[] = $itemIds[1];
					} else {
						$dataIds[] = $itemIds[2];
					}
				}
			}
		}	
			
		return $dataIds;	
	}
}