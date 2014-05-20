<?php

// @see testFunctions()
class FunctionsTestClass {
  const FOO = 'BAR';
  public static $_foo = 'bar';
  public static function methodA() {}
  public static function methodB() {}
  private function _methodC() {}
}

class First {
  public $value = 1;
}

class Second {
  public $value = 1;
}

class UnderscoreObjectsTest extends PHPUnit_Framework_TestCase {
  
  public function testKeys() {
    // from js
    $this->assertEquals(array('one', 'two'), __u::keys((object) array('one'=>1, 'two'=>2)), 'can extract the keys from an object');
    
    $a = array(1=>0);
    $this->assertEquals(array(1), __u::keys($a), 'is not fooled by sparse arrays');
    
    $actual = 'underscore';
    try { $actual = __u::keys(null); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for null values');
    
    $actual = 'underscore';
    try { $actual = __u::keys(UNDERSCORE_FOO); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for undefined values');
    
    $actual = 'underscore';
    try { $actual = __u::keys(1); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for number primitives');
    
    $actual = 'underscore';
    try { $actual = __u::keys('a'); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for string primitives');
    
    $actual = 'underscore';
    try { $actual = __u::keys(true); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for boolean primitives');
    
    // extra
    $this->assertEquals(array('one', 'two'), __u::keys(array('one'=>1, 'two'=>2)), 'can extract the keys from an array');
    $this->assertEquals(array('three', 'four'), __u(array('three'=>3, 'four'=>4))->keys(), 'can extract the keys from an array using OO-style call');
  
    // docs
    $this->assertEquals(array('name', 'age'), __u::keys((object) array('name'=>'moe', 'age'=>40)));
  }
  
  public function testValues() {
    // from js
    $items = array('one'=>1, 'two'=>2);
    $this->assertEquals(array(1,2), __u::values((object) $items), 'can extract the values from an object');
    
    // extra
    $this->assertEquals(array(1,2), __u::values($items));
    $this->assertEquals(array(1), __u::values(array(1)));
    $this->assertEquals(array(1,2), __u($items)->values());
    
    // docs
    $this->assertEquals(array('moe', 40), __u::values((object) array('name'=>'moe', 'age'=>40)));
  }
  
  public function testExtend() {
    // from js
    $result = __u::extend(array(), array('a'=>'b'));
    $this->assertEquals(array('a'=>'b'), $result, 'can extend an array with the attributes of another');
    
    $result = __u::extend((object) array(), (object) array('a'=>'b'));
    $this->assertEquals((object) array('a'=>'b'), $result, 'can extend an object with the attributes of another');
    
    $result = __u::extend(array('a'=>'x'), array('a'=>'b'));
    $this->assertEquals(array('a'=>'b'), $result, 'properties in source override destination');
    
    $result = __u::extend(array('x'=>'x'), array('a'=>'b'));
    $this->assertEquals(array('x'=>'x', 'a'=>'b'), $result, "properties not in source don't get overriden");
    
    $result = __u::extend(array('x'=>'x'), array('a'=>'b'), array('b'=>'b'));
    $this->assertEquals(array('x'=>'x', 'a'=>'b', 'b'=>'b'), $result, 'can extend from multiple sources');
    
    $result = __u::extend(array('x'=>'x'), array('a'=>'a', 'x'=>2), array('a'=>'b'));
    $this->assertEquals(array('x'=>2, 'a'=>'b'), $result, 'extending from multiple source objects last property trumps');
    
    // extra
    $result = __u(array('x'=>'x'))->extend(array('a'=>'a', 'x'=>2), array('a'=>'b'));
    $this->assertEquals(array('x'=>2, 'a'=>'b'), $result, 'extending from multiple source objects last property trumps');
    
    // docs
    $expected = (object) array('name'=>'moe', 'age'=>50);
    $result = __u::extend((object) array('name'=>'moe'), (object) array('age'=>50));
    $this->assertEquals($expected, $result);
  }
  
  public function testDefaults() {
    // from js
    $options = array('zero'=>0, 'one'=>1, 'empty'=>'', 'nan'=>acos(8), 'string'=>'string');
    $options = __u::defaults($options, array('zero'=>1, 'one'=>10, 'twenty'=>20));
    $this->assertEquals(0, $options['zero'], 'value exists');
    $this->assertEquals(1, $options['one'], 'value exists');
    $this->assertEquals(20, $options['twenty'], 'default applied');
    
    $options_obj = (object) array('zero'=>0, 'one'=>1, 'empty'=>'', 'nan'=>acos(8), 'string'=>'string');
    $options_obj = __u::defaults($options_obj, (object) array('zero'=>1, 'one'=>10, 'twenty'=>20));
    $this->assertEquals(0, $options_obj->zero, 'value exists');
    $this->assertEquals(1, $options_obj->one, 'value exists');
    $this->assertEquals(20, $options_obj->twenty, 'default applied');
    
    $options = __u::defaults($options, array('empty'=>'full'), array('nan'=>'nan'), array('word'=>'word'), array('word'=>'dog'));
    $this->assertEquals('', $options['empty'], 'value exists');
    $this->assertTrue(__u::isNaN($options['nan']), 'NaN is not overridden');
    $this->assertEquals('word', $options['word'], 'new value is added, first one wins');
    
    $options_obj = __u::defaults($options_obj, (object) array('empty'=>'full'), (object) array('nan'=>'nan'), (object) array('word'=>'word'), (object) array('word'=>'dog'));
    $this->assertEquals('', $options_obj->empty, 'value exists');
    $this->assertTrue(__u::isNaN($options_obj->nan), 'NaN is not overridden');
    $this->assertEquals('word', $options_obj->word, 'new value is added, first one wins');
  
    // extra
    $options = array('zero'=>0, 'one'=>1, 'empty'=>'', 'nan'=>acos(8), 'string'=>'string');
    $options = __u($options)->defaults(array('zero'=>1, 'one'=>10, 'twenty'=>20));
    $this->assertEquals(0, $options['zero'], 'value exists');
    $this->assertEquals(1, $options['one'], 'value exists');
    $this->assertEquals(20, $options['twenty'], 'default applied');
    
    // docs
    $food = (object) array('dairy'=>'cheese');
    $defaults = (object) array('meat'=>'bacon');
    $expected = (object) array('dairy'=>'cheese', 'meat'=>'bacon');
    $this->assertEquals($expected, __u::defaults($food, $defaults));
  }
  
  public function testFunctions() {
    // from js doesn't really apply here because in php function aren't truly first class citizens
    
    // extra
    $this->assertEquals(array('methodA', 'methodB'), __u::functions(new FunctionsTestClass));
    $this->assertEquals(array('methodA', 'methodB'), __u(new FunctionsTestClass)->functions());
    $this->assertEquals(array('methodA', 'methodB'), __u::methods(new FunctionsTestClass));
    $this->assertEquals(array('methodA', 'methodB'), __u(new FunctionsTestClass)->methods());
  }
  
  public function testClon() {
    // from js
    $moe = array('name'=>'moe', 'lucky'=>array(13, 27, 34));
    $clone = __u::clon($moe);
    $this->assertEquals('moe', $clone['name'], 'the clone as the attributes of the original');
    
    $moe_obj = (object) $moe;
    $clone_obj = __u::clon($moe_obj);
    $this->assertEquals('moe', $clone_obj->name, 'the clone as the attributes of the original');

    $clone['name'] = 'curly';
    $this->assertTrue($clone['name'] === 'curly' && $moe['name'] === 'moe', 'clones can change shallow attributes without affecting the original');
    
    $clone_obj->name = 'curly';
    $this->assertTrue($clone_obj->name === 'curly' && $moe_obj->name === 'moe', 'clones can change shallow attributes without affecting the original');
    
    $clone['lucky'][] = 101;
    $this->assertEquals(101, __u::last($moe['lucky']), 'changes to deep attributes are shared with the original');
    
    $clone_obj->lucky[] = 101;
    $this->assertEquals(101, __u::last($moe_obj->lucky), 'changes to deep attributes are shared with the original');
    
    $val = 1;
    $this->assertEquals(1, __u::clon($val), 'non objects should not be changed by clone');
    
    $val = null;
    $this->assertEquals(null, __u::clon($val), 'non objects should not be changed by clone');
  
    // extra
    $foo = array('name'=>'Foo');
    $bar = __u($foo)->clon();
    $this->assertEquals('Foo', $bar['name'], 'works with OO-style call');
    
    // docs
    $stooge = (object) array('name'=>'moe');
    $this->assertEquals((object) array('name'=>'moe'), __u::clon($stooge));
  }
  
  public function testHas() {
    // extra
    $input = array('a'=>1, 'b'=>2, 'c'=>3);
    $this->assertTrue(__u::has($input, 'a'));
    $this->assertFalse(__u::has($input, 'A'));
    $this->assertFalse(__u::has($input, 'ab'));
    $this->assertTrue(__u::has((object) $input, 'a'));
    $this->assertFalse(__u::has((object) $input, 'A'));
    $this->assertFalse(__u::has((object) $input, 'ab'));
    $this->assertTrue(__u((object) $input)->has('a'), 'works in OO-style call');
    
    // docs
    $this->assertTrue(__u::has($input, 'b'));
  }
  
  public function testIsEqual() {
    // from js
    $moe = (object) array(
      'name' => 'moe',
      'lucky'=> array(13, 27, 34)
    );
    $clone = (object) array(
      'name' => 'moe',
      'lucky'=> array(13, 27, 34)
    );
    $this->assertFalse($moe === $clone, 'basic equality between objects is false');
    $this->assertTrue(__u::isEqual($moe, $clone), 'deep equality is true');
    $this->assertTrue(__u($moe)->isEqual($clone), 'OO-style deep equality works');
    $this->assertFalse(__u::isEqual(5, acos(8)), '5 is not equal to NaN');
    $this->assertTrue(acos(8) != acos(8), 'NaN is not equal to NaN (native equality)');
    $this->assertTrue(acos(8) !== acos(8), 'NaN is not equal to NaN (native identity)');
    $this->assertFalse(__u::isEqual(acos(8), acos(8)), 'NaN is not equal to NaN');
    
    if(class_exists('DateTime')) {
      $timezone = new DateTimeZone('America/Denver');
      $this->assertTrue(__u::isEqual(new DateTime(null, $timezone), new DateTime(null, $timezone)), 'identical dates are equal');
    }
    
    $this->assertFalse(__u::isEqual(null, array(1)), 'a falsy is never equal to a truthy');
    $this->assertEquals(true, __u(array('x'=>1, 'y'=>2))->chain()->isEqual(__u(array('x'=>1, 'y'=>2))->chain())->value(), 'wrapped objects are equal');
    $getTrue = function() { return true; };
    $this->assertTrue(__u::isEqual(array('isEqual'=>$getTrue), array()));
    $this->assertTrue(__u::isEqual(array(), array('isEqual'=>$getTrue)));
    
    $this->assertEquals(new First, new First, 'Object instances are equal');
    $this->assertNotEquals(new First, new Second, 'Objects with different constors and identical own properties are not equal');
    $this->assertNotEquals((object) array('value'=>1), new First, 'Object instances and objects sharing equivalent properties are not equal');
    $this->assertNotEquals((object) array('value'=>2), new Second);    
    
    // docs
    $stooge = (object) array('name'=>'moe');
    $clon = __u::clon($stooge);
    $this->assertFalse($stooge === $clon);
    $this->assertTrue(__u::isEqual($stooge, $clon));
    
    // @todo Lower memory usage on these
    //$this->assertFalse(__::isEqual(array('x'=>1, 'y'=>null), array('x'=>1, 'z'=>2)), 'objects with the same number of undefined keys are not equal');
    //$this->assertFalse(__::isEqual(__(array('x'=>1, 'y'=>null))->chain(), __(array('x'=>1, 'z'=>2))->chain()), 'wrapped objects are not equal');
  }
  
  public function testIsEmpty() {
    // from js
    $this->assertFalse(__u::isEmpty(array(1)), 'array(1) is not empty');
    $this->assertTrue(__u::isEmpty(array()), 'array() is empty');
    $this->assertFalse(__u::isEmpty((object) array('one'=>1), '(object) array("one"=>1) is not empty'));
    $this->assertTrue(__u::isEmpty(new StdClass), 'new StdClass is empty');
    $this->assertTrue(__u::isEmpty(null), 'null is empty');
    $this->assertTrue(__u::isEmpty(''), 'the empty string is empty');
    $this->assertFalse(__u::isEmpty('moe'), 'but other strings are not');
    
    $obj = (object) array('one'=>1);
    unset($obj->one);
    $this->assertTrue(__u::isEmpty($obj), 'deleting all the keys from an object empties it');
  
    // extra
    $this->assertFalse(__u(array(1))->isEmpty(), 'array(1) is not empty with OO-style call');
    $this->assertTrue(__u(array())->isEmpty(), 'array() is empty with OO-style call');
    $this->assertTrue(__u(null)->isEmpty(), 'null is empty with OO-style call');
  
    // docs
    $stooge = (object) array('name'=>'moe');
    $this->assertFalse(__u::isEmpty($stooge));
    $this->assertTrue(__u::isEmpty(new StdClass));
    $this->assertTrue(__u::isEmpty((object) array()));
  }
  
  public function testIsObject() {
    // from js
    $this->assertTrue(__u::isObject((object) array(1, 2, 3)));
    $this->assertTrue(__u::isObject(function() {}), 'and functions');
    $this->assertFalse(__u::isObject(null), 'but not null');
    $this->assertFalse(__u::isObject('string'), 'and not string');
    $this->assertFalse(__u::isObject(12), 'and not number');
    $this->assertFalse(__u::isObject(true), 'and not boolean');
    if(class_exists('DateTimeZone')) {
      $this->assertTrue(__u::isObject(new DateTimeZone('America/Denver')), 'objects are');
    }
    
    // extra
    $this->assertTrue(__u::isObject(new StdClass), 'empty objects work');
    $this->assertTrue(__u(new StdClass)->isObject(), 'works with OO-style call');
    $this->assertFalse(__u(2)->isObject());
  }
  
  public function testIsArray() {
    // from js
    $this->assertTrue(__u::isArray(array(1,2,3)), 'arrays are');
    
    // extra
    $this->assertFalse(__u::isArray(null));
    $this->assertTrue(__u::isArray(array()));
    $this->assertTrue(__u::isArray(array(array(1,2))));
    $this->assertFalse(__u(null)->isArray());
    $this->assertTrue(__u(array())->isArray());
    
    // docs
    $this->assertTrue(__u::isArray(array(1, 2)));
    $this->assertFalse(__u::isArray((object) array(1, 2)));
  }
  
  public function testIsString() {
    // from js
    $this->assertTrue(__u::isString(join(', ', array(1,2,3))), 'strings are');
    
    // extra
    $this->assertFalse(__u::isString(1));
    $this->assertTrue(__u::isString(''));
    $this->assertTrue(__u::isString('1'));
    $this->assertFalse(__u::isString(array()));
    $this->assertFalse(__u::isString(null));
    $this->assertFalse(__u(1)->isString());
    $this->assertTrue(__u('1')->isString());
    $this->assertTrue(__u('')->isString());
    
    // docs
    $this->assertTrue(__u::isString('moe'));
    $this->assertTrue(__u::isString(''));
  }
  
  public function testIsNumber() {
    // from js
    $this->assertFalse(__u::isNumber('string'), 'a string is not a number');
    $this->assertFalse(__u::isNumber(null), 'null is not a number');
    $this->assertTrue(__u::isNumber(3 * 4 - 7 / 10), 'but numbers are');
    
    // extra
    $this->assertFalse(__u::isNumber(acos(8)), 'invalid calculations (nan) are not numbers');
    $this->assertFalse(__u::isNumber('1'), 'strings of numbers are not numbers');
    $this->assertFalse(__u::isNumber(log(0)), 'infinite values are not numbers');
    $this->assertTrue(__u::isNumber(pi()));
    $this->assertTrue(__u::isNumber(M_PI));
    $this->assertFalse(__u(acos(8))->isNumber());
    $this->assertFalse(__u('1')->isNumber());
    $this->assertFalse(__u(log(0))->isNumber());
    $this->assertTrue(__u(pi())->isNumber());
    $this->assertTrue(__u(M_PI)->isNumber());
    $this->assertTrue(__u(1)->isNumber());
    
    // docs
    $this->assertTrue(__u::isNumber(1));
    $this->assertTrue(__u::isNumber(2.5));
    $this->assertFalse(__u::isNumber('5'));
  }
  
  public function testIsBoolean() {
    // from js
    $this->assertFalse(__u::isBoolean(2), 'a number is not a boolean');
    $this->assertFalse(__u::isBoolean('string'), 'a string is not a boolean');
    $this->assertFalse(__u::isBoolean('false'), 'the string "false" is not a boolean');
    $this->assertFalse(__u::isBoolean('true'), 'the string "true" is not a boolean');
    $this->assertFalse(__u::isBoolean(null), 'null is not a boolean');
    $this->assertFalse(__u::isBoolean(acos(8)), 'nan values are not booleans');
    $this->assertTrue(__u::isBoolean(true), 'but true is');
    $this->assertTrue(__u::isBoolean(false), 'and so is false');
    
    // extra
    $this->assertFalse(__u::isBoolean(array()));
    $this->assertFalse(__u::isBoolean(1));
    $this->assertFalse(__u::isBoolean(0));
    $this->assertFalse(__u::isBoolean(-1));
    $this->assertFalse(__u(array())->isBoolean());
    $this->assertTrue(__u(true)->isBoolean());
    $this->assertTrue(__u(false)->isBoolean());
    $this->assertFalse(__u(0)->isBoolean());
    
    // docs
    $this->assertFalse(__u::isBoolean(null));
    $this->assertTrue(__u::isBoolean(true));
    $this->assertFalse(__u::isBoolean(0));
  }
  
  public function testIsFunction() {
    // from js
    $func = function() {};
    $this->assertFalse(__u::isFunction(array(1,2,3)), 'arrays are not functions');
    $this->assertFalse(__u::isFunction('moe'), 'strings are not functions');
    $this->assertTrue(__u::isFunction($func), 'but functions are');
    
    // extra
    $this->assertFalse(__u::isFunction('array_search'), 'strings with names of functions are not functions');
    $this->assertFalse(__u::isFunction(new __u));
    $this->assertFalse(__u(array(1,2,3))->isFunction());
    $this->assertFalse(__u('moe')->isFunction());
    $this->assertTrue(__u($func)->isFunction());
    $this->assertFalse(__u('array_search')->isFunction());
    $this->assertFalse(__u(new __u)->isFunction());
    
    // docs
    $this->assertTrue(__u::isFunction(function() {}));
    $this->assertFalse(__u::isFunction('trim'));
  }
  
  public function testIsDate() {
    // from js
    $this->assertFalse(__u::isDate(1), 'numbers are not dates');
    $this->assertFalse(__u::isDate(new StdClass), 'objects are not dates');
    
    if(class_exists('DateTime')) {
      $timezone = new DateTimeZone('America/Denver');
      $this->assertTrue(__u::isDate(new DateTime(null, $timezone)), 'but dates are');
    }
    
    // extra
    $this->assertFalse(__u::isDate(time()), 'timestamps are not dates');
    $this->assertFalse(__u::isDate('Y-m-d H:i:s'), 'date strings are not dates');
    $this->assertFalse(__u(time())->isDate());
    
    if(class_exists('DateTime')) {
      $timezone = new DateTimeZone('America/Denver');
      $this->assertTrue(__u(new DateTime(null, $timezone))->isDate(), 'dates are dates with OO-style call');
    }
    
    // docs
    $this->assertFalse(__u::isDate(null));
    $this->assertFalse(__u::isDate('2011-06-09 01:02:03'));
    if(class_exists('DateTime')) {
      $timezone = new DateTimeZone('America/Denver');
      $this->assertTrue(__u::isDate(new DateTime(null, $timezone)));
    }
  }
  
  public function testIsNaN() {
    // from js
    $this->assertFalse(__u::isNaN(null), 'null not not NaN');
    $this->assertFalse(__u::isNaN(0), '0 is not NaN');
    $this->assertTrue(__u::isNaN(acos(8)), 'but invalid calculations are');
    
    // extra
    $this->assertFalse(__u(null)->isNan(), 'null is not NaN with OO-style call');
    $this->assertFalse(__u(0)->isNan(), '0 is not NaN with OO-style call');
    $this->assertTrue(__u(acos(8))->isNaN(), 'but invalid calculations are with OO-style call');
  
    // docs
    $this->assertFalse(__u::isNaN(null));
    $this->assertTrue(__u::isNaN(acos(8)));
  }
  
  public function testTap() {
    // from js
    $intercepted = null;
    $interceptor = function($obj) use (&$intercepted) { $intercepted = $obj; };
    $returned = __u::tap(1, $interceptor);
    $this->assertEquals(1, $intercepted, 'passed tapped object to interceptor');
    $this->assertEquals(1, $returned, 'returns tapped object');
    
    $returned = __u(array(1,2,3))->chain()
      ->map(function($n) { return $n * 2; })
      ->max()
      ->tap($interceptor)
      ->value();
    $this->assertTrue($returned === 6 && $intercepted === 6, 'can use tapped objects in a chain');
    
    $returned = __u::chain(array(1,2,3))->map(function($n) { return $n * 2; })
                                       ->max()
                                       ->tap($interceptor)
                                       ->value();
    $this->assertTrue($returned === 6 && $intercepted === 6, 'can use tapped objects in a chain with static call');
  
    // docs
    $interceptor = function($obj) { return $obj * 2; };
    $result = __u(array(1, 2, 3))->chain()
      ->max()
      ->tap($interceptor)
      ->value();
    $this->assertEquals(3, $result);
  }
}