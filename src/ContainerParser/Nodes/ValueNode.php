<?php
/**
 * ClanCats Container
 *
 * @link      https://github.com/ClanCats/Container/
 * @copyright Copyright (c) 2016-2022 Mario Döring
 * @license   https://github.com/ClanCats/Container/blob/master/LICENSE (MIT License)
 */
namespace ClanCats\Container\ContainerParser\Nodes;

use ClanCats\Container\Exceptions\LogicalNodeException;

use ClanCats\Container\ContainerParser\{
    Token
};

class ValueNode extends BaseNode implements AssignableNode
{
    /**
     * Create value node from the given token
     *
     * @param Token         $token
     * @return ValueNode
     */
    public static function fromToken(Token $token) : ValueNode
    {
        if (!$token->isValue())
        {
            throw new LogicalNodeException('Can only create ValueNode from a value type Token.');
        }

        return new ValueNode($token->getValue(), $token->getType());
    }

    /**
	 * The value type
	 *
	 * @var int
	 */
	protected int $type = self::TYPE_UNKNOWN;
	
	/**
	 * The value value >.>
	 *
	 * @var mixed
	 */
	protected $value = null;

	/**
     * The value types
     * 
     * This MUST equal the token type raw values!
     */
    public const TYPE_UNKNOWN = -1;
    public const TYPE_STRING = 0;
    public const TYPE_NUMBER = 1;
    public const TYPE_BOOL_TRUE = 2;
    public const TYPE_BOOL_FALSE = 3;
    public const TYPE_NULL = 4;

	/**
     * Construct the value node
     *
     * @param mixed 			$value
     * @param int               $type
     *
     * @return void
     */
    public function __construct($value, int $type)
    {
    	$this->setRawValue($value);
        $this->setType($type);
    }

    /**
     * Get the current rawValue
     * 
     * @return mixed
     */
    public function getRawValue() 
    {
        return $this->value;
    }

    /**
     * Return the current type
     */
    public function getType() : int 
    {
        return $this->type;
    }

    /**
     * Set the values value
     * 
     * @param mixed 			$value
     * @return void
     */
    public function setRawValue($value)
    {
    	$this->value = $value;
    }

    /**
     * Validate if the given type and assing it
     * 
     * @param int 				$type
     * @return void
     */
    public function setType(int $type)
    {
    	if (!in_array($type, [
            self::TYPE_STRING, 
            self::TYPE_NUMBER, 
            self::TYPE_BOOL_TRUE, 
            self::TYPE_BOOL_FALSE,
            self::TYPE_NULL,
        ]))
        {
            throw new LogicalNodeException('Invalid value type assigned.');
        }

    	$this->type = $type;
    }
}

