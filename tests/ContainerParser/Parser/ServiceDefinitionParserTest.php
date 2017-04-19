<?php
namespace ClanCats\Container\Tests\ContainerParser\Parser;

use ClanCats\Container\Tests\TestCases\ParserTestCase;

use ClanCats\Container\ContainerParser\{
    Parser\ServiceDefinitionParser,
    Nodes\ServiceDefinitionNode,
    Nodes\ValueNode,
    Token as T
};

class ServiceDefinitionParserTest extends ParserTestCase
{
    protected function serviceDefnitionParserFromCode(string $code) : ServiceDefinitionParser 
    {
        return $this->parserFromCode(ServiceDefinitionParser::class, $code);
    }

    protected function serviceDefnitionNodeFromCode(string $code) : ServiceDefinitionNode 
    {
        return $this->serviceDefnitionParserFromCode($code)->parse();
    }

	public function testConstruct()
    {
    	$this->assertInstanceOf(ServiceDefinitionParser::class, $this->serviceDefnitionParserFromCode(''));
    }
    
    public function testSimpleAssign()
    {
        $def = $this->serviceDefnitionNodeFromCode('@logger: Acme\\Log()');

        $this->assertEquals('logger', $def->getName());
        $this->assertEquals('Acme\\Log', $def->getClassName());

        $this->assertFalse($def->hasArguments());
    }
}