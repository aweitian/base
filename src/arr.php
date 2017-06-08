<?php

/**
 * @date 2017/6/8 17:50:44
 */
namespace Tian;

class Arr {
	/**
	 * 把数组看成一颗树，KEY为路径,$delimiter为路径分隔符,路径不存在会生成
	 *
	 * @param array $arr        	
	 * @param string $key        	
	 * @param string $name        	
	 * @param string $delimiter        	
	 * @return 路径指向的引用
	 */
	public static function &ref(array &$arr, $key, $delimiter = '.') {
		$tmp = &$arr;
		$config = explode ( $delimiter, trim ( $key, $delimiter ) );
		foreach ( ( array ) $config as $d ) {
			if (! isset ( $tmp [$d] )) {
				$tmp [$d] = [ ];
			}
			$tmp = &$tmp [$d];
		}
		return $tmp;
	}
	/**
	 * 把数组看成一颗树，KEY为路径,$delimiter为路径分隔符,路径不存在会生成
	 *
	 * @param array $arr        	
	 * @param string $key        	
	 * @param string $name        	
	 * @param string $delimiter        	
	 * @return $name
	 */
	public static function set(array &$arr, $key, $name, $delimiter = '.') {
		$tmp = & self::ref ( $arr, $key, $delimiter );
		$tmp = $name;
		return $tmp;
	}
	/**
	 * 把数组看成一颗树，KEY为路径,$delimiter为路径分隔符,路径不存在返回默认值
	 *
	 * @param array $arr        	
	 * @param string $key        	
	 * @param string $default        	
	 * @param string $delimiter        	
	 * @return string|unknown
	 */
	public static function get(array $arr, $key, $default = null, $delimiter = '.') {
		$tmp = $arr;
		$config = explode ( $delimiter, trim ( $key, $delimiter ) );
		foreach ( ( array ) $config as $d ) {
			if (isset ( $tmp [$d] )) {
				$tmp = $tmp [$d];
			} else {
				return $default;
			}
		}
		return $tmp;
	}
}