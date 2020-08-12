<?php
class Tags implements ICacheDependency {
	
	const TAG_MAKE = '___TAG_MAKE_';
	const TAG_MODEL = '___TAG_MODEL_';
	const TAG_MODEL_YEAR = '___TAG_MODEL_YEAR_';
	const TAG_MODEL_YEAR_PHOTO = '__TAG_MODEL_YEAR_';
	const TAG_MODEL_YEAR_CHASSIS = '_TAG_MODEL_YEAR_CHASSIS_';
	const TAG_COMPLETION = '___TAG_COMPLETION_';
	
	const TAG_TIRE = 'TAG_TIRE';
	const TAG_TIRE_TYPE = 'TAG_TIRE_TYPE';
	const TAG_TIRE_ASPECT_RATIO = 'TAG_TIRE_ASPECT_RATIO';
	const TAG_TIRE_LOAD_INDEX = 'TAG_TIRE_LOAD_INDEX';
	const TAG_TIRE_RIM_DIAMETER = 'TAG_TIRE_RIM_DIAMETER';
	const TAG_TIRE_SECTION_WIDTH = 'TAG_TIRE_SECTION_WIDTH';
	const TAG_TIRE_SPEED_INDEX = 'TAG_TIRE_SPEED_INDEX';
	const TAG_TIRE_VEHICLE_CLASS = 'TAG_TIRE_VEHICLE_CLASS';
	const TAG_TIRE_RIM_WIDTH = 'TAG_TIRE_RIM_WIDTH';
	const TAG_TIRE_RIM_WIDTH_RANGE = 'TAG_TIRE_RIM_WIDTH_RANGE';
	
	const TAG_RIM_WIDTH = 'TAG_RIM_WIDTH';
	const TAG_RIM_BOLT_PATTERN = 'TAG_RIM_BOLT_PATTERN';
	const TAG_RIM_THREAD_SIZE = 'TAG_RIM_THREAD_SIZE';
	const TAG_RIM_CENTER_BORE = 'TAG_RIM_CENTER_BORE';
	const TAG_RIM_OFFSET_RANGE = 'TAG_RIM_OFFSET_RANGE';
	
	const TAG_AUTO_PLATFORM = 'TAG_AUTO_PLATFORM';
	const TAG_AUTO_PLATFORM_CATEGORY = 'TAG_AUTO_PLATFORM_CATEGORY';
	const TAG_AUTO_PLATFORM_MODEL = 'TAG_AUTO_PLATFORM_MODEL';
	
	const TAG_REVIEW			= 'TAG_REVIEW';
	const TAG_PROJECT			= 'TAG_PROJECT';
	const TAG_PROJECT_PHOTO		= 'TAG_PROJECT_PHOTO';
	const TAG_PRODUCT_BULB		= 'TAG_PRODUCT_BULB';
	const TAG_PRODUCT_BULB_ITEM		= 'TAG_PRODUCT_BULB_ITEM';
	const TAG_PRODUCT_BULB_POSITION		= 'TAG_PRODUCT_BULB_POSITION';
	const SEO_TEXT		= 'SEO_TEXT';
	const TAG_PRODUCT_CATEGORY = 'TAG_PRODUCT_CATEGORY';
	const TAG_PRODUCT_EBAY_CATEGORY = 'TAG_PRODUCT_EBAY_CATEGORY';
	const TAG_PRODUCT_CATEGORY_MODEL = 'TAG_PRODUCT_CATEGORY_MODEL';
	const TAG_PRODUCT_CATEGORY_GUIDE = 'TAG_PRODUCT_CATEGORY_GUIDE';
	const TAG_PRODUCT_BRAND = 'TAG_PRODUCT_BRAND';
	const TAG_PRODUCT_TYPE = 'TAG_PRODUCT_TYPE';
	const TAG_PRODUCT_SUBTYPE = 'TAG_PRODUCT_SUBTYPE';
	const TAG_PRODUCT = 'TAG_PRODUCT';
	const TAG_PRODUCT_PHOTO = 'TAG_PRODUCT_PHOTO';
	const PRODUCT_PHOTO = 'PRODUCT_PHOTO';
    
	const TAG_EBAY_MAKE = 'TAG_EBAY_MAKE';
	const TAG_EBAY_MODEL = 'TAG_EBAY_MODEL';
	const TAG_EBAY_MODEL_YEAR = 'TAG_EBAY_MODEL_YEAR';
	const TAG_EBAY_TRIM = 'TAG_EBAY_TRIM';
	const TAG_EBAY_TRIM_BODY = 'TAG_EBAY_TRIM_BODY';
	const TAG_EBAY_TRIM_DRIVE_TYPE = 'TAG_EBAY_TRIM_DRIVE_TYPE';
	const TAG_EBAY_TRIM_ENGINE = 'TAG_EBAY_TRIM_ENGINE';
    
	protected $timestamp;
	protected $tags;
	 
	/**
	 * В качестве параметров передается список тегов
	 *
	 * @params tag1, tag2, ..., tagN
	 */
	 function __construct() {
		$this->tags = func_get_args();
	 }
	 
	/**
	 * Evaluates the dependency by generating and saving the data related with dependency.
	 * This method is invoked by cache before writing data into it.
	 */
	 public function evaluateDependency() {
		$this->timestamp = time();
	 }
	 
	/**
	 * @return boolean whether the dependency has changed.
	 */
	 public function getHasChanged() {
		 $tags = array_map(function($i) { return TaggingBehavior::PREFIX.$i; }, $this->tags);
		 $values = Yii::app()->cache->mget($tags);
		 
		 foreach ($values as $value) {
		 if ((integer)$value > $this->timestamp) { return true; }
		 }
		 
		 return false;
	 }
}