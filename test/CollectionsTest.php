<?php

class UnderscoreCollectionsTest extends PHPUnit_Framework_TestCase {
  
  public function testEach() {
    // from js
    $test =& $this;
    __u::each(array(1,2,3), function($num, $i) use ($test) {
      $test->assertEquals($num, $i+1, 'each iterators provide value and iteration count');
    });
    
    $answers = array();
    $context = (object) array('multiplier'=>5);
    __u::each(array(1,2,3), function($num) use (&$answers, $context) {
      $answers[] = $num * $context->multiplier;
    });
    $this->assertEquals(array(5,10,15), $answers, 'context object property accessed');
    
    $answers = array();
    $obj = (object) array('one'=>1, 'two'=>2, 'three'=>3);
    __u::each($obj, function($value, $key) use (&$answers) {
      $answers[] = $key;
    });
    $this->assertEquals(array('one','two','three'), $answers, 'iterating over objects works');
    
    $answer = null;
    __u::each(array(1,2,3), function($num, $index, $arr) use (&$answer) {
      if(__u::includ($arr, $num)) $answer = true;
    });
    $this->assertTrue($answer, 'can reference the original collection from inside the iterator');
    
    $answers = 0;
    __u::each(null, function() use (&$answers) {
      $answers++;
    });
    $this->assertEquals(0, $answers, 'handles a null property');
    
    // extra
    $test =& $this;
    __u(array(1,2,3))->each(function($num, $i) use ($test) {
      $test->assertEquals($num, $i+1, 'each iterators provide value and iteration count within OO-style call');
    });
    
    // docs
    $str = '';
    __u::each(array(1, 2, 3), function($num) use (&$str) { $str .= $num . ','; });
    $this->assertEquals('1,2,3,', $str);

    $str = '';
    $multiplier = 2;
    __u::each(array(1, 2, 3), function($num, $index) use ($multiplier, &$str) {
      $str .= $index . '=' . ($num * $multiplier) . ',';
    });
    $this->assertEquals('0=2,1=4,2=6,', $str);
  }
  
  public function testMap() {
    // from js
    $this->assertEquals(array(2,4,6), __u::map(array(1,2,3), function($num) {
      return $num * 2;
    }), 'doubled numbers');
    
    $ifnull = __u::map(null, function() {});
    $this->assertTrue(is_array($ifnull) && count($ifnull) === 0, 'handles a null property');
    
    $multiplier = 3;
    $func = function($num) use ($multiplier) { return $num * $multiplier; };
    $tripled = __u::map(array(1,2,3), $func);
    $this->assertEquals(array(3,6,9), $tripled);
    
    $doubled = __u(array(1,2,3))->map(function($num) { return $num * 2; });
    $this->assertEquals(array(2,4,6), $doubled, 'OO-style doubled numbers');
  
    $this->assertEquals(array(2, 4, 6), __u::map(array(1, 2, 3), function($n) { return $n * 2; }));
    $this->assertEquals(array(2, 4, 6), __u(array(1, 2, 3))->map(function($n) { return $n * 2; }));
    
    $doubled = __u::collect(array(1, 2, 3), function($num) { return $num * 2; });
    $this->assertEquals(array(2, 4, 6), $doubled, 'aliased as "collect"');
    
    // docs
    $this->assertEquals(array(3,6,9), __u::map(array(1, 2, 3), function($num) { return $num * 3; }));
    $this->assertEquals(array(3,6,9), __u::map(array('one'=>1, 'two'=>2, 'three'=>3), function($num, $key) { return $num * 3; }));
  }
  
  public function testFind() {
    // from js
    $this->assertEquals(2, __u::find(array(1,2,3), function($num) { return $num * 2 === 4; }), 'found the first "2" and broke the loop');
    
    // extra
    $iterator = function($n) { return $n % 2 === 0; };
    $this->assertEquals(2, __u::find(array(1, 2, 3, 4, 5, 6), $iterator));
    $this->assertEquals(false, __u::find(array(1, 3, 5), $iterator));
    $this->assertEquals(false, __u(array(1,3,5))->find($iterator), 'works with OO-style calls');
    $this->assertEquals(__u::find(array(1,3,5), $iterator), __u::detect(array(1,3,5), $iterator), 'alias works');
    
    // docs
    $this->assertEquals(2, __u::find(array(1, 2, 3, 4), function($num) { return $num % 2 === 0; }));
  }
  
  public function testFilter() {
    // from js
    $evens = __u::filter(array(1,2,3,4,5,6), function($num) { return $num % 2 === 0; });
    $this->assertEquals(array(2, 4, 6), $evens, 'selected each even number');
    
    // extra
    $odds = __u(array(1,2,3,4,5,6))->filter(function($num) { return $num % 2 !== 0; });
    $this->assertEquals(array(1,3,5), $odds, 'works with OO-style calls');
    
    $evens = __u::filter(array(1,2,3,4,5,6), function($num) { return $num % 2 === 0; });
    $this->assertEquals(array(2,4,6), $evens, 'aliased as filter');
    
    $iterator = function($num) { return $num % 2 !== 0; };
    $this->assertEquals(__u::filter(array(1,3,5), $iterator), __u::select(array(1,3,5), $iterator), 'alias works');
    
    // docs
    $this->assertEquals(array(2,4), __u::filter(array(1, 2, 3, 4), function($num) { return $num % 2 === 0; }));
  }
  
  public function testReject() {
    // from js
    $odds = __u::reject(array(1,2,3,4,5,6), function($num) { return $num % 2 === 0; });
    $this->assertEquals(array(1, 3, 5), $odds, 'rejected each even number');
    
    // extra
    $evens = __u(array(1,2,3,4,5,6))->reject(function($num) { return $num % 2 !== 0; });
    $this->assertEquals(array(2,4,6), $evens, 'works with OO-style calls');
  
    // docs
    $this->assertEquals(array(1, 3), __u::reject(array(1, 2, 3, 4), function($num) { return $num % 2 === 0; }));
  }
  
  public function testAll() {
    // from js
    $this->assertTrue(__u::all(array(), __u::identity()), 'the empty set');
    $this->assertTrue(__u::all(array(true, true, true), __u::identity()), 'all true values');
    $this->assertFalse(__u::all(array(true, false, true), __u::identity()), 'one false value');
    $this->assertTrue(__u::all(array(0, 10, 28), function($num) { return $num % 2 === 0;  }), 'even numbers');
    $this->assertFalse(__u::all(array(0, 11, 28), function($num) { return $num % 2 === 0;  }), 'odd numbers');
    
    // extra
    $this->assertTrue(__u::all(array()));
    $this->assertFalse(__u::all(array(null)));
    $this->assertFalse(__u::all(0));
    $this->assertFalse(__u::all('0'));
    $this->assertFalse(__u::all(array(0,1)));
    $this->assertTrue(__u::all(array(1)));
    $this->assertTrue(__u::all(array('1')));
    $this->assertTrue(__u::all(array(1,2,3,4)));
    $this->assertTrue(__u(array(1,2,3,4))->all(), 'works with OO-style calls');
    $this->assertTrue(__u(array(true, true, true))->all(__u::identity()));
    
    $this->assertTrue(__u(array(true, true, true))->every(__u::identity()), 'aliased as "every"');
  
    // docs
    $this->assertFalse(__u::all(array(1, 2, 3, 4), function($num) { return $num % 2 === 0; }));
    $this->assertTrue(__u::all(array(1, 2, 3, 4), function($num) { return $num < 5; }));
  }
  
  public function testAny() {
    // from js
    $this->assertFalse(__u::any(array()), 'the empty set');
    $this->assertFalse(__u::any(array(false, false, false)), 'all false values');
    $this->assertTrue(__u::any(array(false, false, true)), 'one true value');
    $this->assertFalse(__u::any(array(1, 11, 29), function($num) { return $num % 2 === 0; }), 'all odd numbers');
    $this->assertTrue(__u::any(array(1, 10, 29), function($num) { return $num % 2 === 0; }), 'an even number');
    
    // extra
    $this->assertFalse(__u::any(array()));
    $this->assertFalse(__u::any(array(null)));
    $this->assertFalse( __u::any(array(0)));
    $this->assertFalse(__u::any(array('0')));
    $this->assertTrue(__u::any(array(0, 1)));
    $this->assertTrue(__u::any(array(1)));
    $this->assertTrue(__u::any(array('1')));
    $this->assertTrue(__u::any(array(1,2,3,4)));
    $this->assertTrue(__u(array(1,2,3,4))->any(), 'works with OO-style calls');
    $this->assertFalse(__u(array(1,11,29))->any(function($num) { return $num % 2 === 0; }));
    
    $this->assertTrue(__u::some(array(false, false, true)), 'alias as "some"');
    $this->assertTrue(__u(array(1,2,3,4))->some(), 'aliased as "some"');
  
    // docs
    $this->assertTrue(__u::any(array(1, 2, 3, 4), function($num) { return $num % 2 === 0; }));
    $this->assertFalse(__u::any(array(1, 2, 3, 4), function($num) { return $num === 5; }));
  }
  
  public function testInclud() {
    // from js
    $this->assertTrue(__u::includ(array(1,2,3), 2), 'two is in the array');
    $this->assertFalse(__u::includ(array(1,3,9), 2), 'two is not in the array');
    $this->assertTrue(__u(array(1,2,3))->includ(2), 'OO-style includ');
    
    // extra
    $collection = array(true, false, 0, 1, -1, 'foo', array(), array('meh'));
    $this->assertTrue(__u::includ($collection, true));
    $this->assertTrue(__u::includ($collection, false));
    $this->assertTrue(__u::includ($collection, 0));
    $this->assertTrue(__u::includ($collection, 1));
    $this->assertTrue(__u::includ($collection, -1));
    $this->assertTrue(__u::includ($collection, 'foo'));
    $this->assertTrue(__u::includ($collection, array()));
    $this->assertTrue(__u::includ($collection, array('meh')));
    $this->assertFalse(__u::includ($collection, 'true'));
    $this->assertFalse(__u::includ($collection, '0'));
    $this->assertFalse(__u::includ($collection, '1'));
    $this->assertFalse(__u::includ($collection, '-1'));
    $this->assertFalse(__u::includ($collection, 'bar'));
    $this->assertFalse(__u::includ($collection, 'Foo'));
    
    $this->assertTrue(__u::contains((object) array('moe'=>1, 'larry'=>3, 'curly'=>9), 3), '__::includ on objects checks their values');
    
    // docs
    $this->assertTrue(__u::includ(array(1, 2, 3), 3));
  }
  
  public function testInvoke() {
    // from js
    // the sort example from js doesn't work here because sorting occurs in place in PHP
    $list = array(' foo', ' bar ');
    $this->assertEquals(array('foo','bar'), __u::invoke($list, 'trim'), 'trim applied on array');
    $this->assertEquals((object) array('foo','bar'), __u::invoke((object) $list, 'trim'), 'trim applied on object');
    $this->assertEquals(array('foo','bar'), __u($list)->invoke('trim'), 'works with OO-style call');
  
    // docs
    $this->assertEquals(array('foo', 'bar'), __u::invoke(array(' foo', ' bar '), 'trim'));
  }
  
  public function testReduce() {
    // from js
    $sum = __u::reduce(array(1,2,3), function($sum, $num) { return $sum + $num; }, 0);
    $this->assertEquals(6, $sum, 'can sum up an array');
    
    $context = array('multiplier'=>3);
    $sum = __u::reduce(array(1,2,3), function($sum, $num) use ($context) { return $sum + $num * $context['multiplier']; }, 0);
    $this->assertEquals(18, $sum, 'can reduce with a context object');
    
    $sum = __u::reduce(array(1,2,3), function($sum, $num) { return $sum + $num; }, 0);
    $this->assertEquals(6, $sum, 'default initial value');
    
    $ifnull = null;
    try { __u::reduce(null, function() {}); }
    catch(Exception $e) { $ifnull = $e; }
    $this->assertFalse($ifnull === null, 'handles a null (without initial value) properly');
    
    $this->assertEquals(138, __u::reduce(null, function(){}, 138), 'handles a null (with initial value) properly');
    
    $sum = __u(array(1,2,3))->reduce(function($sum, $num) { return $sum + $num; });
    $this->assertEquals(6, $sum, 'OO-style reduce');
    
    $sum = __u::inject(array(1,2,3), function($sum, $num) { return $sum + $num; }, 0);
    $this->assertEquals(6, $sum, 'aliased as "inject"');
    
    $sum = __u::foldl(array(1,2,3), function($sum, $num) { return $sum + $num; }, 0);
    $this->assertEquals(6, $sum, 'aliased as "foldl"');
    
    // docs
    $this->assertEquals(6, __u::reduce(array(1, 2, 3), function($memo, $num) { return $memo + $num; }, 0));
  }
  
  public function testReduceRight() {
    // from js
    $list = __u::reduceRight(array('foo', 'bar', 'baz'), function($memo, $str) { return $memo . $str; }, '');
    $this->assertEquals('bazbarfoo', $list, 'can perform right folds');
    
    $ifnull = null;
    try { __u::reduceRight(null, function() {}); }
    catch(Exception $e) { $ifnull = $e; }
    $this->assertFalse($ifnull === null, 'handles a null (without initial value) properly');
    
    $this->assertEquals(138, __u::reduceRight(null, function(){}, 138), 'handles a null (with initial value) properly');
    
    // extra
    $list = __u(array('moe','curly','larry'))->reduceRight(function($memo, $str) { return $memo . $str; }, '');
    $this->assertEquals('larrycurlymoe', $list, 'can perform right folds in OO-style');
    
    $list = __u::foldr(array('foo', 'bar', 'baz'), function($memo, $str) { return $memo . $str; }, '');
    $this->assertEquals('bazbarfoo', $list, 'aliased as "foldr"');
    
    $list = __u::foldr(array('foo', 'bar', 'baz'), function($memo, $str) { return $memo . $str; });
    $this->assertEquals('bazbarfoo', $list, 'default initial value');
    
    // docs
    $list = array(array(0, 1), array(2, 3), array(4, 5));
    $flat = __u::reduceRight($list, function($a, $b) { return array_merge($a, $b); }, array());
    $this->assertEquals(array(4, 5, 2, 3, 0, 1), $flat);
  }
  
  public function testPluck() {
    // from js
    $people = array(
      array('name'=>'moe', 'age'=>30),
      array('name'=>'curly', 'age'=>50)
    );
    $this->assertEquals(array('moe', 'curly'), __u::pluck($people, 'name'), 'pulls names out of objects');
    
    // extra: array
    $stooges = array(
      array('name'=>'moe',   'age'=> 40),
      array('name'=>'larry', 'age'=> 50, 'foo'=>'bar'),
      array('name'=>'curly', 'age'=> 60)
    );
    $this->assertEquals(array('moe', 'larry', 'curly'), __u::pluck($stooges, 'name'));
    $this->assertEquals(array(40, 50, 60), __u::pluck($stooges, 'age'));
    $this->assertEquals(array('bar'), __u::pluck($stooges, 'foo'));
    $this->assertEquals(array('bar'), __u($stooges)->pluck('foo'), 'works with OO-style call');
    
    // extra: object
    $stooges_obj = new StdClass;
    foreach($stooges as $stooge) {
      $name = $stooge['name'];
      $stooges_obj->$name = (object) $stooge;
    }
    $this->assertEquals(array('moe', 'larry', 'curly'), __u::pluck($stooges, 'name'));
    $this->assertEquals(array(40, 50, 60), __u::pluck($stooges, 'age'));
    $this->assertEquals(array('bar'), __u::pluck($stooges, 'foo'));
    $this->assertEquals(array('bar'), __u($stooges)->pluck('foo'), 'works with OO-style call');
  
    // docs
    $stooges = array(
      array('name'=>'moe', 'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals(array('moe', 'larry', 'curly'), __u::pluck($stooges, 'name'));
  }
  
  public function testMax() {
    // from js
    $this->assertEquals(3, __u::max(array(1,2,3)), 'can perform a regular max');
    $this->assertEquals(1, __u::max(array(1,2,3), function($num) { return -$num; }), 'can performa a computation-based max');
    
    // extra
    $stooges = array(
      array('name'=>'moe',   'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals($stooges[2], __u::max($stooges, function($stooge) { return $stooge['age']; }));
    $this->assertEquals($stooges[0], __u::max($stooges, function($stooge) { return $stooge['name']; }));
    $this->assertEquals($stooges[0], __u($stooges)->max(function($stooge) { return $stooge['name']; }), 'works with OO-style call');
  
    // docs
    $stooges = array(
      array('name'=>'moe', 'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals(array('name'=>'curly', 'age'=>60), __u::max($stooges, function($stooge) { return $stooge['age']; }));
  }
  
  public function testMin() {
    // from js
    $this->assertEquals(1, __u::min(array(1,2,3)), 'can perform a regular min');
    $this->assertEquals(3, __u::min(array(1,2,3), function($num) { return -$num; }), 'can performa a computation-based max');
    
    // extra
    $stooges = array(
      array('name'=>'moe',   'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals($stooges[0], __u::min($stooges, function($stooge) { return $stooge['age']; }));
    $this->assertEquals($stooges[2], __u::min($stooges, function($stooge) { return $stooge['name']; }));
    $this->assertEquals($stooges[2], __u($stooges)->min(function($stooge) { return $stooge['name']; }), 'works with OO-style call');
  
    // docs
    $stooges = array(
      array('name'=>'moe', 'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals(array('name'=>'moe', 'age'=>40), __u::min($stooges, function($stooge) { return $stooge['age']; }));
  }
  
  public function testSortBy() {
    // from js
    $people = array(
      (object) array('name'=>'curly', 'age'=>50),
      (object) array('name'=>'moe', 'age'=>30)
    );
    $people_sorted = __u::sortBy($people, function($person) { return $person->age; });
    $this->assertEquals(array('moe', 'curly'), __u::pluck($people_sorted, 'name'), 'stooges sorted by age');
    
    // extra
    $stooges = array(
      array('name'=>'moe',   'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals($stooges, __u::sortBy($stooges, function($stooge) { return $stooge['age']; }));
    $this->assertEquals(array($stooges[2], $stooges[1], $stooges[0]), __u::sortBy($stooges, function($stooge) { return $stooge['name']; }));
    $this->assertEquals(array(5, 4, 6, 3, 1, 2), __u::sortBy(array(1, 2, 3, 4, 5, 6), function($num) { return sin($num); }));
    $this->assertEquals($stooges, __u($stooges)->sortBy(function($stooge) { return $stooge['age']; }), 'works with OO-style call');
  
    // docs
    $this->assertEquals(array(3, 2, 1), __u::sortBy(array(1, 2, 3), function($n) { return -$n; }));
  }
  
  public function testGroupBy() {
    // from js
    $parity = __u::groupBy(array(1,2,3,4,5,6), function($num) { return $num % 2; });
    $this->assertEquals(array(array(2,4,6), array(1,3,5)), $parity, 'created a group for each value');
      
    // extra
    $parity = __u(array(1,2,3,4,5,6))->groupBy(function($num) { return $num % 2; });
    $this->assertEquals(array(array(2,4,6), array(1,3,5)), $parity, 'created a group for each value using OO-style call');
    
    $vals = array(
      array('name'=>'rejected', 'yesno'=>'no'),
      array('name'=>'accepted', 'yesno'=>'yes'),
      array('name'=>'allowed', 'yesno'=>'yes'),
      array('name'=>'denied', 'yesno'=>'no')
    );
    $grouped = __u::groupBy($vals, 'yesno');
    $this->assertEquals('rejected denied', join(' ', __u::pluck($grouped['no'], 'name')), 'pulls no entries');
    $this->assertEquals('accepted allowed', join(' ', __u::pluck($grouped['yes'], 'name')), 'pulls yes entries');
    
    // docs
    $result = __u::groupBy(array(1, 2, 3, 4, 5), function($n) { return $n % 2; });
    $this->assertEquals(array(0=>array(2, 4), 1=>array(1, 3, 5)), $result);
    
    $values = array(
      array('name'=>'Apple',   'grp'=>'a'),
      array('name'=>'Bacon',   'grp'=>'b'),
      array('name'=>'Avocado', 'grp'=>'a')
    );
    $expected = array(
      'a'=>array(
        array('name'=>'Apple',   'grp'=>'a'),
        array('name'=>'Avocado', 'grp'=>'a')
      ),
      'b'=>array(
        array('name'=>'Bacon',   'grp'=>'b')
      )
    );
    $this->assertEquals($expected, __u::groupBy($values, 'grp'));
  }
  
  public function testSortedIndex() {
    // from js
    $numbers = array(10, 20, 30, 40, 50);
    $num = 35;
    $index = __u::sortedIndex($numbers, $num);
    $this->assertEquals(3, $index, '35 should be inserted at index 3');
    
    // extra
    $this->assertEquals(3, __u($numbers)->sortedIndex(35), '35 should be inserted at index 3 with OO-style call');
  
    // docs
    $this->assertEquals(3, __u::sortedIndex(array(10, 20, 30, 40), 35));
  }
  
  public function testShuffle() {
    // from js
    $numbers = __u::range(10);
    $shuffled = __u::shuffle($numbers);
    sort($shuffled);
    
    $this->assertEquals(join(',', $numbers), join(',', $shuffled), 'contains the same members before and after shuffle');
  }
  
  public function testToArray() {
    // from js
    $numbers = __u::toArray((object) array('one'=>1, 'two'=>2, 'three'=>3));
    $this->assertEquals('1, 2, 3', join(', ', $numbers), 'object flattened into array');
    
    // docs
    $stooge = new StdClass;
    $stooge->name = 'moe';
    $stooge->age = 40;
    $this->assertEquals(array('name'=>'moe', 'age'=>40), __u::toArray($stooge));
  }
  
  public function testSize() {
    // from js
    $items = (object) array(
      'one'   =>1,
      'two'   =>2,
      'three' =>3
    );
    $this->assertEquals(3, __u::size($items), 'can compute the size of an object');
    
    // extra
    $this->assertEquals(0, __u::size(array()));
    $this->assertEquals(1, __u::size(array(1)));
    $this->assertEquals(3, __u::size(array(1, 2, 3)));
    $this->assertEquals(6, __u::size(array(null, false, array(), array(1,2,array('a','b')), 1, 2)));
    $this->assertEquals(3, __u(array(1,2,3))->size(), 'works with OO-style calls');
  
    // docs
    $stooge = new StdClass;
    $stooge->name = 'moe';
    $stooge->age = 40;
    $this->assertEquals(2, __u::size($stooge));
  }
}