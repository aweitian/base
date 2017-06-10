<?php

/**
 * @date 2017/6/8 17:50:44
 */
namespace Tian\Base;

class Str {
	/**
	 * Determine if a given string starts with a given substring.
	 *
	 * @param string $haystack        	
	 * @param string|array $needles        	
	 * @return bool
	 */
	public static function startsWith($haystack, $needles) {
		foreach ( ( array ) $needles as $needle ) {
			if ($needle != '' && substr ( $haystack, 0, strlen ( $needle ) ) === ( string ) $needle) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Determine if a given string ends with a given substring.
	 *
	 * @param string $haystack        	
	 * @param string|array $needles        	
	 * @return bool
	 */
	public static function endsWith($haystack, $needles) {
		foreach ( ( array ) $needles as $needle ) {
			if (substr ( $haystack, - strlen ( $needle ) ) === ( string ) $needle) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Generate a more truly "random" alpha-numeric string.
	 *
	 * @param int $length        	
	 * @return string
	 */
	public static function random($length = 16) {
		$string = '';
		while ( ($len = strlen ( $string )) < $length ) {
			$size = $length - $len;
			$bytes = random_bytes ( $size );
			$string .= substr ( str_replace ( [ 
					'/',
					'+',
					'=' 
			], '', base64_encode ( $bytes ) ), 0, $size );
		}
		return $string;
	}
	/**
	 *
	 * @param string $str        	
	 * @param int $from        	
	 * @param int $len        	
	 * @return string
	 */
	public static function utf8Substr($str, $from, $len) {
		return preg_replace ( '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' . '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str );
	}
}