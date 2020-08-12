<?php
/**
 * Author: Yuriy Sorokolat aka Sw00p (Aiken Studio)
 * Date: 07.06.2010
 * Time: 13:33:05
 */

class SiteConfig {
	
	const CACHE_KEY = '__SETTINGS_';
	
    protected $_data = null;
    protected static $_instance = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * @static
     * @return SiteConfig
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new SiteConfig();
        }
        return self::$_instance;
    }

    /**
     * Получить массив всех данных конфига
     * @return array
     */
    public function getData() {
        $this->_init();
        return $this->_data;
    }

    /**
     * Вернуть значение параметра конфига по его имени
     * 
     * @param string $name
     * @param bool $defaultValue
     * @return mixed
     */
    public function getValue($name, $defaultValue = null) {
        $this->_init();
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return $defaultValue;
    }

	private function _init() {
        if (is_null($this->_data)) {
			$data = Yii::app()->cache->get(self::CACHE_KEY);
			if ($data == false) {
				$data = CHtml::listData(SiteConfigModel::model()->findAll(), 'key', 'value');
				Yii::app()->cache->set(self::CACHE_KEY, $data, 60*60*24);
			}
			
			$this->_data = $data;		
		}
    }
	
}
