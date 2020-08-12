<?php
/**
 * Класс TextHelper
 * Вспомогательные ф-ции для работы с текстом
 * @author maxshurko@gmail.com
 * @copyright Aiken Studio
 * @version 7.0.1
 */
class TextHelper {

    /* Список месяцев на разных языках */
    public static $months = array(
        'ru' => array (
            1 => 'январь',  2 => 'февраль', 3 => 'март', 4 => 'апрель', 5 => 'май', 6 => 'июнь', 7 => 'июль',
            8 => 'август', 9 => 'сентябрь', 10 => 'октябрь', 11 => 'ноябрь', 12 => 'декабрь',
        ),
        'ua' => array (
            1 => 'січень', 2 => 'лютий', 3 => 'березень', 4 => 'квітень', 5 => 'травень', 6 => 'червень', 7 => 'пипень',
            8 => 'серпень', 9 => 'вересень', 10 => 'жовтень', 11 => 'листопад', 12 => 'грудень',
        ),
    );

    /* Список склоненных месяцев на разных языках */
    public static $bandedMonths = array(
        'ru' => array (
            1 => 'января',  2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня', 7 => 'июля',
            8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря',
        ),
        'ua' => array (
            1 => 'січня', 2 => 'лютого', 3 => 'березня', 4 => 'квітня', 5 => 'травня', 6 => 'червня', 7 => 'пипня',
            8 => 'серпня', 9 => 'вересеня', 10 => 'жовтеня', 11 => 'листопада', 12 => 'груденя',
        ),
    );	
	
    /**
     * Обрезка UTF строки
     * 
     * @static
     * @param string $string
     * @param int $length
     * @param string $postfix
     * @return bool|string
     */
    public static function truncate($string, $length = 50, $breakWords = false, $postfix = '...')
    {
        $wordEndChars = ',.?!;:'; /* символы окончания слова */
        $truncated = trim($string);
        $length = (int)$length;
        if (!$string) {
            return $truncated;
        }
        $fullLength = iconv_strlen($truncated, 'UTF-8');
        if ($fullLength > $length) {
            $truncated = trim(iconv_substr($truncated, 0, $length, 'UTF-8'));
            if (!$breakWords) {
                $words = explode(' ', $truncated);
                $wordCount = sizeof($words);
                if (rtrim($words[$wordCount-1], $wordEndChars) == $words[$wordCount-1]) {
                    unset($words[$wordCount-1]);
                }
                $wordCount = sizeof($words);
                if (!empty($words[$wordCount-1])) {
                	$words[$wordCount-1] = rtrim($words[$wordCount-1], $wordEndChars);	
                }
                $truncated = implode(' ', $words);
            }
            $truncated .= $postfix;
        }
        return $truncated;
    }

    /**
     * @param string $st Кириллический текст
     * @return string Транслит
     */
    public static function translit($string, $reverse = false)
    {
        $table = array(
            '_' => '-',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'YO',
            'Ж' => 'ZH',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'J',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'CSH',
            'Ь' => '',
            'Ы' => 'Y',
            'Ъ' => '',
            'Э' => 'E',
            'Ю' => 'YU',
            'Я' => 'YA',

            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'csh',
            'ь' => '',
            'ы' => 'y',
            'ъ' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            
            'ї' => 'yi',
            'і' => 'i',
            'є' => 'ye',
        );

        $output = str_replace(
            array_keys($table),
            array_values($table),$string
        );

        return $output;
    }

    /**
     * Преобразуем текст в ссылко-безопасный (можно использовать в ссылках)
     * @static
     * @param string $string
     * @param string $c символ для замены недопустимых для ссылки знаков
     * @return mixed
     */
    public static function urlSafe($string, $c = '-')
    {
        $string = preg_replace('/[^-a-z0-9_]+/i', $c, strtolower(self::translit($string)));
        $string = preg_replace('/^-/i', '', $string);
        $string = preg_replace('/-$/i', '', $string);
        return $string;
    }

    /**
     * Declension. Expressions should be array like ('гвоздь', 'гвоздя', 'гвоздей')
     *
     * @param int $int Number for declension
     * @param array $expressions Expressions array
     * @return string
     */
    public static function declension($int, $expressions) {
        if (count($expressions) < 3) $expressions[2] = $expressions[1];
        $int = (int)$int;
        $count = $int % 100;
        if ($count >= 5 && $count <= 20) {
            $result = $expressions['2'];
        } else {
            $count = $count % 10;
            if ($count == 1) {
                $result = $expressions['0'];
            } elseif ($count >= 2 && $count <= 4) {
                $result = $expressions['1'];
            } else {
                $result = $expressions['2'];
            }
        }
        return $result;
    }

    /**
     * Строим красивую сумму
     * @param string $p
     * @return string
     */
    public function summBuilder($p)
    {
    	$d = explode('.', $p);
    	return sprintf("%2d,%02d", $d[0], $d[1]);
    }
    
    /**
     * Меняем пробелы на значи подчеркивания
     * @param string $str
     * @return string
     */
    public function underlined($str) { return preg_replace('/\s+/', '_', $str);}
    
    public function aliased($str) { return preg_replace('/[\s]+/', '-', strtolower($str));}
    
    public static function getMonths($language = 'ru') 
    { 
    	return self::$months[$language];
   	}

   	/**
   	 * Получить вектор с днями месяца
   	 * @return array
   	 */
   	public static function getMonthDays($pref = false)
   	{
   		$days = array();
   		for ($a = 1, $b = 31; $a <= $b; $a ++) { 
			if ($pref) {
   				$days[$a] = $a<10 ? '0'.$a : $a;				
			} else {
				$days[$a] = $a;
			}
   		}
   		return $days;
   	}
   	
   	/**
   	 * Получить года, начиная со starter
   	 * @param integer $starter
   	 * @return array
   	 */
   	public static function getYearsList($starter = 0, $limit = false)
   	{
   		$years = array();
   		$currentYear = (int)date('Y')-$starter;
   		
   		for ($a = $currentYear, $b = $currentYear-60, $count = 0; $a > $b; $a --, $count++) { 
			
   			if ($limit && $count>=$limit) {
				break;	
			}
			
   			$years[$a] = $a;
   		}
   		
   		return $years;
   	}
    
   	/**
   	 * Парсим дату, возвращаем unix timestamp
   	 * @param string $date -- дата, в формате day.month.year
   	 * @return integer -- unix timestamp
   	 */
   	public static function parseDate($date)
   	{
   		$result = null;
   		if (preg_match('/^(\d+?)\.(\d+?)\.(\d{4})/', $date, $match) && !empty($match)) {
   			
   			return mktime(0, 0, 0, $match[2], $match[1], $match[3]);
   		}
   		return $result;
   	}
   	
   	/**
     * Компрессим строку
     * @param string $str
     * @return string
     */
    public static function compress($str)
    {
    	return preg_replace('/\n\r|\r\n|\n|\r|\t| {2}/', '', $str);
	}
	
	/**
	 * Обертка для htmlspecialchars_decode
	 */
	public function htmlDecode($str)
	{
		$additionalChars = array('&laquo;', '&raquo;');
		$str = str_replace($additionalChars, '', $str);
		$str = utf8_encode(html_entity_decode($str));
		return $str;
	}
	
	/**
	 * Парсим строку и преобразуем в unix-timestamp
	 * @param string $date
	 * @return integer
	 */
	public static  function unixTimeStamp($date = null)
	{
		$date = trim($date);
		$unixTimeStamp = $date;
		if (!empty($date)) {
			
			$timeParts = array();
			if (preg_match('/[\.\:\s]/', $date)) {
				
				$dateTimeParts = explode(' ', $date);
				if (is_array($dateTimeParts) && count($dateTimeParts) == 2) {
					
					$dividers = array('.', ':',);
					foreach ($dateTimeParts as $index => $part) { $timeParts = array_merge($timeParts, explode($dividers[$index], $part));}
				} else if (is_array($dateTimeParts) && count($dateTimeParts) == 1) {
					
					$timeParts = array_merge($timeParts, explode('.', $dateTimeParts[0]));
				} else {
					
					$unixTimeStamp = $date;
				}
			}
			if (is_array($timeParts) && count($timeParts)) { 
				$unixTimeStamp = mktime(isset($timeParts[3]) ? $timeParts[3] : 0, isset($timeParts[4]) ? $timeParts[4] : 0, 0, $timeParts[1], $timeParts[0], $timeParts[2]);
			}
		}
		return $unixTimeStamp;
	}
	
	/**
	 * Получить дату публикации
	 * @return string
	 */
	public static function getDateAsTitle($date, $getTime = true, $getYear = true, $getMounth = true, $getDay = true, $getHowOld = true)
	{
		$now = getdate();
		$date = getdate($date);
		if ($getHowOld && $date['mon'] === $now['mon'] && $date['year'] === $now['year'] && $date['mday'] >= $now['mday']-1) {
			if ($date['mday'] === $now['mday']) {
				$day = Yii::t('default', 'Сегодня');
			}
			if ($date['mday'] === $now['mday']-1) {
				$day = Yii::t('default', 'Вчера');
			}
		} else {			
			$day = '';
			$day .= ($getDay  ? $date['mday'] : '');
			$day .= ($getMounth ? $getDay ? ' '.TextHelper::$bandedMonths[Yii::app()->language][$date['mon']] : ' '.TextHelper::$months[Yii::app()->language][$date['mon']] : '');
			$day .= ($getYear ? ' '.$date['year'] : '');
			$day = trim($day);
		}
		
		if ($getTime) {
			return sprintf('%s в %02d:%02d', $day, $date['hours'], $date['minutes']);			
		} else {
			return $day;
		}
	}
	
	/**
	 * Получить разницу времени
	 *
	 * @param intager $date
	 * @param boolean $getTime
	 * @param intager $period
	 * @return string
	 */
	public static function getDateHowOld($date, $getTime = true, $period = 7200)
	{
		$old = time()-$date;

		if ($old<$period) {
			$result = array();
			
			$hourOld = floor($old/3600);
			$minutesOld = ceil(($old - $hourOld*3600)/60);
			
			if ($hourOld) {
				$result[] = $hourOld.' '.Yii::t('default', 'час|часа|часов', $hourOld);
			}
			
			if ($minutesOld) {
				$result[] = $minutesOld.' мин.';
			}			

			return $result ? implode(', ', $result).' назад' : '';
		} else {
			return self::getDateAsTitle($date, $getTime);
		}
	}
	
	/**
	 * Определяем латиницу
	 * @param string $s
	 * @return boolean
	 */
	public static function isLat($s) { return ord($s) >= 65 && ord($s) <=90 || ord($s) >= 97 && ord($s) <= 122;}
	
	/**
	 * Получить вывод даты в указанном формате
	 * @param string $format
	 * @param intager $date
	 * @return string
	 */
	public static function getFormatDate($format, $date = null)
	{
		if (Yii::app()->language=='en'){
			return date($format, $date);	
		} else {
			$format = ' '.$format;
			return self::getDateAsTitle($date, strpos($format, 'H'), strpos($format, 'Y'), (strpos($format, 'm') || strpos($format, 'M') || strpos($format, 'F')), (strpos($format, 'd') || strpos($format, 'j')), false);
		}
	}
	
	public function ucfirst($str, $encoding = "UTF-8", $lowerStrEnd = false) 
	{
		
		$firstLetter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
		$strEnd = "";
		
		if ($lowerStrEnd) {
			$strEnd = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
		} else {
			$strEnd = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
		}
		
		$str = $firstLetter . $strEnd;
		return $str;
    }
    
    /**
     * Получить красивую цену
     * @param integer $price
     * @param mixed $empty - что выводить если пустое значение
     * @param mixed $template - шаблон вывода значения через sprintf
     * @return string
     */
    public static function getTextPrice($price, $empty = 0, $template = '')
    {
    	if ($price) {
    		
	    	$textPrice = number_format($price, 0, ',', ' ');
	    	
	    	if ($template) {
	    		$textPrice = sprintf($template, $textPrice);
	    	}	
	    	
    	} else {
    		$textPrice = $empty;
    	}

    	return $textPrice;
    }
    public static function getDayOfWeek(){
        return array(1=>'Понедельник',2=>'Вторник',3=>'Среда',4=>'Четверг',5=>'Пятница',6=>'Субота',7=>'Воскресенье');
    }
	
    public static function f($v)
	{
		if (substr($v, -1) == '0') {
			$v =  substr($v, 0, -1);
		}
		
		return $v;
	}
}
