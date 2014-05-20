<?php

class UnderscoreFunctionsTest extends PHPUnit_Framework_TestCase {
  
  public function testMemoize() {
    // from js
    $fib = function($n) use (&$fib) {
      return $n < 2 ? $n : $fib($n - 1) + $fib($n - 2);
    };
    $fastFib = __u::memoize($fib);
    $this->assertEquals(55, $fib(10), 'a memoized version of fibonacci produces identical results');
    $this->assertEquals(55, $fastFib(10), 'a memoized version of fibonacci produces identical results');
    
    $o = function($str) { return $str; };
    $fastO = __u::memoize($o);
    $this->assertEquals('toString', $o('toString'), 'checks hasOwnProperty');
    $this->assertEquals('toString', $fastO('toString'), 'checks hasOwnProperty');
  
    // extra
    $name = function() { return 'moe'; };
    $fastName = __u::memoize($name);
    $this->assertEquals('moe', $name(), 'works with no parameters');
    $this->assertEquals('moe', $fastName(), 'works with no parameters');
    
    $names = function($one, $two, $three) {
      return join(', ', array($one, $two, $three));
    };
    $fastNames = __u::memoize($names);
    $this->assertEquals('moe, larry, curly', $names('moe', 'larry', 'curly'), 'works with multiple parameters');
    $this->assertEquals('moe, larry, curly', $fastNames('moe', 'larry', 'curly'), 'works with multiple parameters');
  
    $foo = function() { return 'foo'; };
    $fastFoo = __u($foo)->memoize();
    $this->assertEquals('foo', $foo(), 'can handle OO-style calls');
    $this->assertEquals('foo', $fastFoo(), 'can handle OO-style calls');
    
    $bar = function() { return 'bar'; };
    $fastBar = __u::memoize($bar, function($function, $args) {
      return sha1(join('x', array(
        var_export($function, 1),
        var_export($args, 1)
      )));
    });
    $this->assertEquals('bar', $bar(), 'can custom hash function');
    $this->assertEquals('bar', $fastBar(), 'can use custom hash function');
  
    // docs
    $fibonacci = function($n) use (&$fibonacci) {
      return $n < 2 ? $n : $fibonacci($n - 1) + $fibonacci($n - 2);
    };
    $fastFibonacci = __u::memoize($fibonacci);
    $this->assertEquals($fibonacci(2), $fastFibonacci(2));
  }
  
  public function testThrottle() {
    // from js
    $counter = 0;
    $incr = function() use (&$counter) { $counter++; };
    $throttledIncr = __u::throttle($incr, 100);
    $throttledIncr(); $throttledIncr(); $throttledIncr();
    usleep(120 * 1000); $throttledIncr();
    usleep(140 * 1000); $throttledIncr();
    usleep(220 * 1000); $throttledIncr();
    usleep(240 * 1000); $throttledIncr();
    $this->assertEquals(5, $counter, 'incr was throttled');
    
    usleep(500 * 1000);
    $counter = 0;
    $incr = function() use (&$counter) { $counter++; };
    $throttledIncr = __u::throttle($incr, 100);
    $throttledIncr();
    usleep(220 * 1000);
    $this->assertEquals(1, $counter, 'incr called once');
    
    usleep(500 * 1000);
    $counter = 0;
    $incr = function() use (&$counter) { $counter++; };
    $throttledIncr = __u::throttle($incr, 100);
    $throttledIncr(); $throttledIncr();
    usleep(220 * 1000);
    $this->assertEquals(1, $counter, 'incr called twice');
    
    // extra
    $counter = 0;
    $incr = function() use (&$counter) { $counter++; };
    $throttledIncr = __u($incr)->throttle(100);
    $throttledIncr(); $throttledIncr(); $throttledIncr();
    usleep(120 * 1000); $throttledIncr();
    usleep(140 * 1000); $throttledIncr();
    usleep(220 * 1000); $throttledIncr();
    usleep(240 * 1000); $throttledIncr();
    $this->assertEquals(5, $counter, 'incr was throttled with OO-style call');
  }
  
  public function testOnce() {
    // from js + docs
    $num = 0;
    $increment = __u::once(function() use (&$num) { return $num++; });
    $increment();
    $increment();
    $this->assertEquals(1, $num);
    
    // extra
    $num = 0;
    $increment = __u(function() use (&$num) { return $num++; })->once();
    $increment();
    $increment();
    $this->assertEquals(1, $num);
  }
  
  public function testWrap() {
    // from js
    $greet = function($name) { return 'hi: ' . $name; };
    $backwards = __u::wrap($greet, function($func, $name) { return $func($name) . ' ' . strrev($name); });
    $this->assertEquals('hi: moe eom', $backwards('moe'), 'wrapped the salutation function');
    
    $inner = function() { return 'Hello '; };
    $arr = array('name'=>'Moe');
    $arr['hi'] = __u::wrap($inner, function($fn) use ($arr) { return $fn() . $arr['name']; });
    $this->assertEquals('Hello Moe', $arr['hi']());
    
    $noop = function() {};
    $wrapped = __u::wrap($noop, function($fn) { return func_get_args(); });
    $ret = $wrapped(array('whats', 'your'), 'vector', 'victor');
    $this->assertEquals(array($noop, array('whats', 'your'), 'vector', 'victor'), $ret);
    
    // extra
    $inner = function() { return 'Hello '; };
    $arr = array('name'=>'Curly');
    $arr['hi'] = __u($inner)->wrap(function($fn) use ($arr) { return $fn() . $arr['name']; });
    $this->assertEquals('Hello Curly', $arr['hi']());
    
    // docs
    $hello = function($name) { return 'hello: ' . $name; };
    $hi = __u::wrap($hello, function($func) {
      return 'before, ' . $func('moe') . ', after'; 
    });
    $this->assertEquals('before, hello: moe, after', $hi());
  }
  
  public function testCompose() {
    // from js
    $greet = function($name) { return 'hi: ' . $name; };
    $exclaim = function($sentence) { return $sentence . '!'; };
    $composed = __u::compose($exclaim, $greet);
    $this->assertEquals('hi: moe!', $composed('moe'), 'can compose a function that takes another');
    
    $composed = __u::compose($greet, $exclaim);
    $this->assertEquals('hi: moe!', $composed('moe'), 'in this case, the functions are also commutative');
    
    // extra
    $composed = __u($greet)->compose($exclaim);
    $this->assertEquals('hi: moe!', $composed('moe'), 'in this case, the functions are also commutative');
  
    // docs
    $greet = function($name) { return 'hi: ' . $name; };
    $exclaim = function($statement) { return $statement . '!'; };
    $welcome = __u::compose($exclaim, $greet);
    $this->assertEquals('hi: moe!', $welcome('moe'));
  }
  
  public function testAfter() {
    // from js
    $testAfter = function($afterAmount, $timesCalled) {
      $afterCalled = 0;
      $after = __u::after($afterAmount, function() use (&$afterCalled) {
        $afterCalled++;
      });
      while($timesCalled--) $after();
      return $afterCalled;
    };
    $this->assertEquals(1, $testAfter(5, 5), 'after(N) should fire after being called N times');
    $this->assertEquals(0, $testAfter(5, 4), 'after(N) should not fire unless called N times');
    $this->assertEquals(1, $testAfter(0, 0), 'after(0) should fire immediately');
    
    // extra
    $testAfterAgain = function($afterAmount, $timesCalled) {
      $afterCalled = 0;
      $after = __u($afterAmount)->after(function() use (&$afterCalled) {
        $afterCalled++;
      });
      while($timesCalled--) $after();
      return $afterCalled;
    };
    $this->assertEquals(1, $testAfterAgain(5, 5), 'after(N) should fire after being called N times in OO-style call');
    $this->assertEquals(0, $testAfterAgain(5, 4), 'after(N) should not fire unless called N times in OO-style call');
  
    // docs
    $str = '';
    $func = __u::after(3, function() use(&$str) { $str = 'x'; });
    $func();
    $func();
    $func();
    $this->assertEquals('x', $str);
  }
}