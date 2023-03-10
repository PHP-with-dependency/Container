<?php
namespace ClanCats\Container\Tests\ContainerParser\Parser;

use ClanCats\Container\Tests\TestCases\ParserTestCase;

use ClanCats\Container\ContainerParser\{
    Parser\ArgumentArrayParser,
    Nodes\ArgumentArrayNode,
    Nodes\ValueNode,
    Nodes\ArrayNode,
    Nodes\ParameterReferenceNode,
    Nodes\ServiceReferenceNode,
    Token as T
};

class ArgumentArrayParserTest extends ParserTestCase
{
    protected function argumentsArrayParserFromCode(string $code) : ArgumentArrayParser 
    {
        return $this->parserFromCode(ArgumentArrayParser::class, $code);
    }

    protected function argumentsArrayNodeFromCode(string $code) : ArgumentArrayNode 
    {
        return $this->argumentsArrayParserFromCode($code)->parse();
    }

	public function testConstruct()
    {
    	$this->assertInstanceOf(ArgumentArrayParser::class, $this->argumentsArrayParserFromCode(''));
    }

    public function testArgumentArrayOfValues()
    {
        $arguments = $this->argumentsArrayNodeFromCode('"hello", "world"');

        $this->assertCount(2, $arguments->getArguments());

        $arguments = $arguments->getArguments();

        foreach(['hello', 'world'] as $k => $word) {

            $this->assertEquals(ValueNode::TYPE_STRING, $arguments[$k]->getType());
            $this->assertEquals($word, $arguments[$k]->getRawValue());
        }

        // check the value types
        $arguments = $this->argumentsArrayNodeFromCode('"galaxy", 42, true, false, null');
        $this->assertCount(5, $arguments->getArguments());
        $arguments = $arguments->getArguments();

        $this->assertEquals(ValueNode::TYPE_STRING, $arguments[0]->getType());
        $this->assertEquals('galaxy', $arguments[0]->getRawValue());
        $this->assertEquals(ValueNode::TYPE_NUMBER, $arguments[1]->getType());
        $this->assertEquals(42, $arguments[1]->getRawValue());
        $this->assertEquals(ValueNode::TYPE_BOOL_TRUE, $arguments[2]->getType());
        $this->assertEquals(true, $arguments[2]->getRawValue());
        $this->assertEquals(ValueNode::TYPE_BOOL_FALSE, $arguments[3]->getType());
        $this->assertEquals(false, $arguments[3]->getRawValue());
        $this->assertEquals(ValueNode::TYPE_NULL, $arguments[4]->getType());
        $this->assertEquals(null, $arguments[4]->getRawValue());
    }

    public function testArgumentArrayOfParameters()
    {
        $arguments = $this->argumentsArrayNodeFromCode(':hello, :world');

        $this->assertCount(2, $arguments->getArguments());

        $arguments = $arguments->getArguments();

        foreach(['hello', 'world'] as $k => $word) {

            $argument = $arguments[$k];

            $this->assertInstanceOf(ParameterReferenceNode::class, $argument);
            $this->assertEquals($word, $argument->getName());
        }
    }

    public function testArgumentArrayWithLinebreak()
    {
        $arguments = $this->argumentsArrayNodeFromCode(":hello,\n:world");
        $this->assertCount(2, $arguments->getArguments());
    }

    public function testArgumentArrayOfServices()
    {
        $arguments = $this->argumentsArrayNodeFromCode('@hello, @world');

        $this->assertCount(2, $arguments->getArguments());

        $arguments = $arguments->getArguments();

        foreach(['hello', 'world'] as $k => $word) {

            $argument = $arguments[$k];

            $this->assertInstanceOf(ServiceReferenceNode::class, $argument);
            $this->assertEquals($word, $argument->getName());
        }
    }

    public function testOnlyOneItem()
    {
        $arguments = $this->argumentsArrayNodeFromCode('@hello');
        $argument = $arguments->getArguments()[0];

        $this->assertCount(1, $arguments->getArguments());
        $this->assertInstanceOf(ServiceReferenceNode::class, $argument);
        $this->assertEquals('hello', $argument->getName());
    }

    public function testEmpty()
    {
        $arguments = $this->argumentsArrayNodeFromCode('');
        $this->assertCount(0, $arguments->getArguments());
    }

    public function testArrayInArgumentArray()
    {
        $arguments = $this->argumentsArrayNodeFromCode('{"A"}');
        $argument = $arguments->getArguments()[0];

        $this->assertCount(1, $arguments->getArguments());
        $this->assertInstanceOf(ArrayNode::class, $argument);
        $this->assertEquals(['A'], $argument->convertToNativeArray());

        // both?
        $arguments = $this->argumentsArrayNodeFromCode('{"A"}, {1, 2, 3}');
        $argument = $arguments->getArguments()[1];

        $this->assertCount(2, $arguments->getArguments());
        $this->assertInstanceOf(ArrayNode::class, $argument);
        $this->assertEquals([1, 2, 3], $argument->convertToNativeArray());
    }

    public function testInvalid() 
    {
        $this->expectException(\ClanCats\Container\Exceptions\ContainerParserException::class);
        $arguments = $this->argumentsArrayNodeFromCode('@foo,,');
    }

    public function testInvalidIdentifier() 
    {
        $this->expectException(\ClanCats\Container\Exceptions\ContainerParserException::class);
        $arguments = $this->argumentsArrayNodeFromCode('@foo,bar');
    }


    public function testClassNameArgument()
    {
        $arguments = $this->argumentsArrayNodeFromCode('Foo::class, Bar::class');
        $argument1 = $arguments->getArguments()[0];
        $argument2 = $arguments->getArguments()[1];

        $this->assertCount(2, $arguments->getArguments());

        $this->assertInstanceOf(ValueNode::class, $argument1);
        $this->assertEquals('\\Foo', $argument1->getRawValue());
        
        $this->assertInstanceOf(ValueNode::class, $argument2);
        $this->assertEquals('\\Bar', $argument2->getRawValue());
    }
}
