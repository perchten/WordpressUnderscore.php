<?php

class UnderscoreUtilityTest extends PHPUnit_Framework_TestCase {

  public function testIdentity() {
    // from js
    $moe = array('name'=>'moe');
    $moe_obj = (object) $moe;
    $this->assertEquals($moe, __u::identity($moe));
    $this->assertEquals($moe_obj, __u::identity($moe_obj));

    // extra
    $this->assertEquals($moe, __u($moe)->identity());
    $this->assertEquals($moe_obj, __u($moe_obj)->identity());
    
    // docs
    $moe = array('name'=>'moe');
    $this->assertTrue($moe === __u::identity($moe));
  }

  public function testUniqueId() {
    // docs
    $this->assertEquals(0, __u::uniqueId());
    $this->assertEquals('stooge_1', __u::uniqueId('stooge_'));
    $this->assertEquals(2, __u::uniqueId());
    
    // from js
    $ids = array();
    $i = 0;
    while($i++ < 100) array_push($ids, __u::uniqueId());
    $this->assertEquals(count($ids), count(__u::uniq($ids)));

    // extra
    $this->assertEquals('stooges', join('', (__u::first(__u::uniqueId('stooges'), 7))), 'prefix assignment works');
    $this->assertEquals('stooges', join('', __u(__u('stooges')->uniqueId())->first(7)), 'prefix assignment works in OO-style call');

    while($i++ < 100) array_push($ids, __u()->uniqueId());
    $this->assertEquals(count($ids), count(__u()->uniq($ids)));
  }

  public function testTimes() {
    // from js
    $vals = array();
    __u::times(3, function($i) use (&$vals) { $vals[] = $i; });
    $this->assertEquals(array(0,1,2), $vals, 'is 0 indexed');

    $vals = array();
    __u(3)->times(function($i) use (&$vals) { $vals[] = $i; });
    $this->assertEquals(array(0,1,2), $vals, 'works as a wrapper in OO-style call');
  
    // docs
    $result = '';
    __u::times(3, function() use (&$result) { $result .= 'a'; });
    $this->assertEquals('aaa', $result);
  }

  public function testMixin() {
    // from js
    __u::mixin(array(
      'myReverse' => function($string) {
        $chars = str_split($string);
        krsort($chars);
        return join('', $chars);
      }
    ));
    $this->assertEquals('aecanap', __u::myReverse('panacea'), 'mixed in a function to _');
    $this->assertEquals('pmahc', __u('champ')->myReverse(), 'mixed in a function to _ with OO-style call');
    
    // docs
    __u::mixin(array(
      'capitalize'=> function($string) { return ucwords($string); },
      'yell'      => function($string) { return strtoupper($string); }
    ));
    $this->assertEquals('Moe', __u::capitalize('moe'));
    $this->assertEquals('MOE', __u::yell('moe'));
  }

  public function testTemplate() {
    // from js
    $basicTemplate = __u::template('<%= $thing %> is gettin on my noives!');
    $this->assertEquals("This is gettin on my noives!", $basicTemplate(array('thing'=>'This')), 'can do basic attribute interpolation');
    $this->assertEquals("This is gettin on my noives!", $basicTemplate((object) array('thing'=>'This')), 'can do basic attribute interpolation');

    $backslashTemplate = __u::template('<%= $thing %> is \\ridanculous');
    $this->assertEquals('This is \\ridanculous', $backslashTemplate(array('thing'=>'This')));
    
    $escapeTemplate = __u::template('<%= $a ? "checked=\\"checked\\"" : "" %>');
    $this->assertEquals('checked="checked"', $escapeTemplate(array('a'=>true)), 'can handle slash escapes in interpolations');

    $fancyTemplate = __u::template('<ul><% foreach($people as $key=>$name) { %><li><%= $name %></li><% } %></ul>');
    $result = $fancyTemplate(array('people'=>array('moe'=>'Moe', 'larry'=>'Larry', 'curly'=>'Curly')));
    $this->assertEquals('<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>', $result, 'can run arbitrary php in templates');

    $namespaceCollisionTemplate = __u::template('<%= $pageCount %> <%= $thumbnails[$pageCount] %> <% __::each($thumbnails, function($p) { %><div class=\"thumbnail\" rel=\"<%= $p %>\"></div><% }); %>');
    $result = $namespaceCollisionTemplate((object) array(
      'pageCount' => 3,
      'thumbnails'=> array(
        1 => 'p1-thumbnail.gif',
        2 => 'p2-thumbnail.gif',
        3 => 'p3-thumbnail.gif'
      )
    ));
    $expected = '3 p3-thumbnail.gif <div class=\"thumbnail\" rel=\"p1-thumbnail.gif\"></div><div class=\"thumbnail\" rel=\"p2-thumbnail.gif\"></div><div class=\"thumbnail\" rel=\"p3-thumbnail.gif\"></div>';
    $this->assertEquals($expected, $result);

    $noInterpolateTemplate = __u::template("<div><p>Just some text. Hey, I know this is silly but it aids consistency.</p></div>");
    $result = $noInterpolateTemplate();
    $expected = "<div><p>Just some text. Hey, I know this is silly but it aids consistency.</p></div>";
    $this->assertEquals($expected, $result);

    $quoteTemplate = __u::template("It's its, not it's");
    $this->assertEquals("It's its, not it's", $quoteTemplate(new StdClass));

    $quoteInStatementAndBody = __u::template('<%
      if($foo == "bar"){
    %>Statement quotes and \'quotes\'.<% } %>');
    $this->assertEquals("Statement quotes and 'quotes'.", $quoteInStatementAndBody((object) array('foo'=>'bar')));

    $withNewlinesAndTabs = __u::template('This\n\t\tis: <%= $x %>.\n\tok.\nend.');
    $this->assertEquals('This\n\t\tis: that.\n\tok.\nend.', $withNewlinesAndTabs((object) array('x'=>'that')));
    
    $template = __u::template('<i><%- $value %></i>');
    $result = $template((object) array('value'=>'<script>'));
    $this->assertEquals('<i>&lt;script&gt;</i>', $result);

    __u::templateSettings(array(
      'evaluate'    => '/\{\{([\s\S]+?)\}\}/',
      'interpolate' => '/\{\{=([\s\S]+?)\}\}/'
    ));

    $custom = __u::template('<ul>{{ foreach($people as $key=>$name) { }}<li>{{= $people[$key] }}</li>{{ } }}</ul>');
    $result = $custom(array('people'=>array('moe'=>'Moe', 'larry'=>'Larry', 'curly'=>'Curly')));
    $this->assertEquals("<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>", $result, 'can run arbitrary php in templates using custom tags');

    $customQuote = __u::template("It's its, not it's");
    $this->assertEquals("It's its, not it's", $customQuote(new StdClass));

    $quoteInStatementAndBody = __u::template('{{ if($foo == "bar"){ }}Statement quotes and \'quotes\'.{{ } }}');
    $this->assertEquals("Statement quotes and 'quotes'.", $quoteInStatementAndBody(array('foo'=>'bar')));

    __u::templateSettings(array(
      'evaluate'    => '/<\?([\s\S]+?)\?>/',
      'interpolate' => '/<\?=([\s\S]+?)\?>/'
    ));

    $customWithSpecialChars = __u::template('<ul><? foreach($people as $key=>$name) { ?><li><?= $people[$key] ?></li><? } ?></ul>');
    $result = $customWithSpecialChars(array('people'=>array('moe'=>'Moe', 'larry'=>'Larry', 'curly'=>'Curly')));
    $this->assertEquals("<ul><li>Moe</li><li>Larry</li><li>Curly</li></ul>", $result, 'can run arbitrary php in templates');

    $customWithSpecialCharsQuote = __u::template("It's its, not it's");
    $this->assertEquals("It's its, not it's", $customWithSpecialCharsQuote(new StdClass));

    $quoteInStatementAndBody = __u::template('<? if($foo == "bar"){ ?>Statement quotes and \'quotes\'.<? } ?>');
    $this->assertEquals("Statement quotes and 'quotes'.", $quoteInStatementAndBody(array('foo'=>'bar')));

    __u::templateSettings(array(
      'interpolate' => '/\{\{(.+?)\}\}/'
    ));

    $mustache = __u::template('Hello {{$planet}}!');
    $this->assertEquals("Hello World!", $mustache(array('planet'=>'World')), 'can mimic mustache.js');

    // extra
    __u::templateSettings(); // reset to default
    $basicTemplate = __u::template('<%= $thing %> is gettin\' on my <%= $nerves %>!');
    $this->assertEquals("This is gettin' on my noives!", $basicTemplate(array('thing'=>'This', 'nerves'=>'noives')), 'can do basic attribute interpolation for multiple variables');

    $result = __u('hello: <%= $name %>')->template(array('name'=>'moe'));
    $this->assertEquals('hello: moe', $result, 'works with OO-style call');

    $result = __u('<%= $thing %> is gettin\' on my <%= $nerves %>!')->template(array('thing'=>'This', 'nerves'=>'noives'));
    $this->assertEquals("This is gettin' on my noives!", $result, 'can do basic attribute interpolation for multiple variables with OO-style call');
  
    $result = __u('<%
      if($foo == "bar"){
    %>Statement quotes and \'quotes\'.<% } %>')->template((object) array('foo'=>'bar'));
    $this->assertEquals("Statement quotes and 'quotes'.", $result);
    
    // docs
    $compiled = __u::template('hello: <%= $name %>');
    $result = $compiled(array('name'=>'moe'));
    $this->assertEquals('hello: moe', $result);
    
    $list = '<% __::each($people, function($name) { %><li><%= $name %></li><% }); %>';
    $result = __u::template($list, array('people'=>array('moe', 'curly', 'larry')));
    $this->assertEquals('<li>moe</li><li>curly</li><li>larry</li>', $result);
    
    __u::templateSettings(array(
      'interpolate' => '/\{\{(.+?)\}\}/'
    ));
    $mustache = __u::template('Hello {{$planet}}!');
    $result = $mustache(array('planet'=>'World'));
    $this->assertEquals('Hello World!', $result);
    
    $template = __u::template('<i><%- $value %></i>');
    $result = $template(array('value'=>'<script>'));
    $this->assertEquals('<i>&lt;script&gt;</i>', $result);
    
    $sans = __u::template('A <% $this %> B');
    $this->assertEquals('A  B', $sans());
  }
  
  public function testEscape() {
    // from js
    $this->assertEquals('Curly &amp; Moe', __u::escape('Curly & Moe'));
    $this->assertEquals('Curly &amp;amp; Moe', __u::escape('Curly &amp; Moe'));
    
    // extra
    $this->assertEquals('Curly &amp; Moe', __u('Curly & Moe')->escape());
    $this->assertEquals('Curly &amp;amp; Moe', __u('Curly &amp; Moe')->escape());
  }
}