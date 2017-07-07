<?php
class BaseTest extends PHPUnit_Framework_TestCase {
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testArr() {
		$taw = [ ];
		\Tian\Base\Arr::set ( $taw, "/taw/xpath/v", 'ann', '/' );
		// var_dump($taw);
		$this->assertEquals ( "ann", \Tian\Base\Arr::get ( $taw, "/taw/xpath/v", null, '/' ) );
		$c = & \Tian\Base\Arr::ref ( $taw, "/taw/xpath/v", '/' );
		$c = "balabala";
		$this->assertEquals ( "balabala", \Tian\Base\Arr::get ( $taw, "/taw/xpath/v", null, '/' ) );
		$c = & \Tian\Base\Arr::ref ( $taw, "/taw/xpath", '/' );
		$c ['v'] = "gg";
		$this->assertEquals ( "gg", \Tian\Base\Arr::get ( $taw, "/taw/xpath/v", null, '/' ) );
		\Tian\Base\Arr::set ( $taw, "taw.xpath.vv", 'garri' );
		$this->assertEquals ( "garri", $c ['vv'] );
		$this->assertTrue ( \Tian\Base\Arr::has ( $taw, 'taw.xpath.vv' ) );
		$this->assertFalse ( \Tian\Base\Arr::has ( $taw, 'taw.xpath.v.v' ) );
		\Tian\Base\Arr::set ( $taw, 'balabala.qq.cc.v', 'g' );
		\Tian\Base\Arr::forget ( $taw, 'taw.xpath.v' );
		$this->assertFalse ( \Tian\Base\Arr::has ( $taw, 'taw.xpath.v' ) );
		$dot = \Tian\Base\Arr::dot ( $taw );
		$this->assertArraySubset ( [ 
				"taw.xpath.vv" => "garri",
				"balabala.qq.cc.v" => "g" 
		], $dot );
	}
	public function testForWhere() {
		$taw = [ ];
		\Tian\Base\Arr::set ( $taw, '.', 'g' );
		$this->assertEquals('g',\Tian\Base\Arr::get($taw,'.'));
		$c = & \Tian\Base\Arr::ref($taw, null);
		$c[] = 'bb';
		$this->assertArraySubset ( [
				0 => "bb",
				"" => "g"
		], $c );
		
	}
	public function testshuffle() {
		$taw = [ 
				1,
				2,
				3,
				4,
				5 
		];
		$new = \Tian\Base\Arr::shuffle ( $taw );
		// var_dump($taw,$new);
	}
	public function testWhere() {
		$taw = [ 
				'a' => 1,
				2,
				3,
				4,
				5 
		];
		$new = \Tian\Base\Arr::where ( $taw, function ($v, $k) {
			// var_dump($k,$v);
			// echo "\n";
			return $k === 'a';
		} );
		$this->assertArraySubset ( [ 
				'a' => 1 
		], $taw );
	}
	public function testColumn() {
		$records = array (
				array (
						'id' => 2135,
						'first_name' => 'John',
						'last_name' => 'Doe' 
				),
				array (
						'id' => 3245,
						'first_name' => 'Sally',
						'last_name' => 'Smith' 
				),
				array (
						'id' => 5342,
						'first_name' => 'Jane',
						'last_name' => 'Jones' 
				),
				array (
						'id' => 5623,
						'first_name' => 'Peter',
						'last_name' => 'Doe' 
				) 
		);
		
		$new = \Tian\Base\Arr::column ( $records, 'last_name', 'id' );
		$this->assertArraySubset ( [ 
				'2135' => 'Doe',
				'3245' => 'Smith',
				'5342' => 'Jones',
				'5623' => 'Doe' 
		], $new );
	}
	public function testAssoc() {
		$taw = [ 
				1,
				2,
				5,
				4 
		];
		$bad = [ 
				'a' => 1,
				'b' => 2 
		];
		$this->assertFalse ( \Tian\Base\Arr::isAssoc ( $taw ) );
		$this->assertTrue ( \Tian\Base\Arr::isAssoc ( $bad ) );
	}
	
	public function testStr() {
		$this->assertTrue(\Tian\Base\Str::startsWith("tiananwei", "tian"));
		$this->assertTrue(\Tian\Base\Str::endsWith("tiananwei", "anwei"));
	}
}

