<?php
class TaggingBehavior extends CBehavior {
 
	const PREFIX = '__tag__';
	 
	/**
	 * @param $tags
	 * @return void
	 */
	 public function clear($tags) {
	 
		foreach ((array)$tags as $tag) {
			$this->owner->set(self::PREFIX.$tag, time());
		}
	}
}