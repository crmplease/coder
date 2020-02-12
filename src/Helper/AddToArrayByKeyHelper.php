<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use CrmPlease\Coder\Rector\RectorException;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use function get_class;
use function in_array;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToArrayByKeyHelper
{
    private $convertToAstHelper;
    private $nameNodeHelper;

    public function __construct(ConvertToAstHelper $convertToAstHelper, NameNodeHelper $nameNodeHelper)
    {
        $this->convertToAstHelper = $convertToAstHelper;
        $this->nameNodeHelper = $nameNodeHelper;
    }

    /**
     * @param string|int $key
     * @param string $keyConstant
     * @param string|float|int $value
     * @param string $constant
     * @param Array_ $node
     *
     * @return bool
     * @throws RectorException
     */
    public function addToArrayByKey(
        $key,
        ?string $keyConstant,
        $value,
        ?string $constant,
        Array_ $node
    ): bool
    {
        $keys = [];
        $keyConstants = [];
        $index = 0;
        foreach ($node->items as $itemNode) {
            if (!$itemNode instanceof ArrayItem) {
                throw new RectorException("Can't get key from item, class '" . get_class($itemNode) . "' isn't supported");
            }
            $keyNode = $itemNode->key;
            if (!$keyNode) {
                $keys[] = $index;
            } elseif ($keyNode instanceof LNumber) {
                $keys[] = (string)$keyNode->value;
            } elseif ($keyNode instanceof DNumber) {
                $keys[] = (string)$keyNode->value;
            } elseif ($keyNode instanceof String_) {
                $keys[] = (string)$keyNode->value;
            } elseif ($keyNode instanceof ConstFetch) {
                $keyConstants[] = $this->nameNodeHelper->getNameByNodeName($keyNode->name);
            } elseif ($keyNode instanceof ClassConstFetch) {
                $classNode = $keyNode->class;
                if (!$classNode instanceof Name) {
                    throw new RectorException("Can't get class name from class const key, class '" . get_class($keyNode->class) . "' isn't supported");
                }
                $className = $this->nameNodeHelper->getNameByNodeName($classNode);
                $keyConstants[] = "{$className}::{$keyNode->name->name}";
            } else {
                throw new RectorException("Can't get value from key node, class '" . get_class($keyNode) . "' isn't supported");
            }
        }

        if ($value) {
            $valueNode = $this->convertToAstHelper->simpleValueToAst($value);
        } elseif ($constant) {
            $valueNode = $this->convertToAstHelper->constantToAst($constant);
        } else {
            throw new RectorException('You should provide value or constant');
        }
        $keyNode = null;
        if ($key) {
            if (!in_array((string)$key, $keys, true)) {
                $keyNode = $this->convertToAstHelper->simpleValueToAst($key);
            }
        } elseif ($keyConstant) {
            if (!in_array($keyConstant, $keyConstants, true)) {
                $keyNode = $this->convertToAstHelper->constantToAst($keyConstant);
            }
        } else {
            throw new RectorException('You should provide key or keyConstant');
        }
        if (!$keyNode) {
            return false;
        }
        $node->items[] = new ArrayItem($valueNode, $keyNode);
        return true;
    }
}
