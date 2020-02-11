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
class AddToArrayByOrderHelper
{
    private $convertToAstHelper;
    private $nameNodeHelper;

    public function __construct(ConvertToAstHelper $convertToAstHelper, NameNodeHelper $nameNodeHelper)
    {
        $this->convertToAstHelper = $convertToAstHelper;
        $this->nameNodeHelper = $nameNodeHelper;
    }

    /**
     * @param string|float|int $value
     * @param string $constant
     * @param Array_ $node
     *
     * @return bool
     * @throws RectorException
     */
    public function addToArrayByOrder($value, string $constant, Array_ $node): bool
    {
        $values = [];
        $constants = [];
        foreach ($node->items as $itemNode) {
            if (!$itemNode instanceof ArrayItem) {
                throw new RectorException("Can't get value from item, class '" . get_class($itemNode) . "' isn't supported");
            }
            $valueNode = $itemNode->value;
            if ($valueNode instanceof LNumber) {
                $values[] = (string)$valueNode->value;
            } elseif ($valueNode instanceof DNumber) {
                $values[] = (string)$valueNode->value;
            } elseif ($valueNode instanceof String_) {
                $values[] = (string)$valueNode->value;
            } elseif ($valueNode instanceof ConstFetch) {
                $constants[] = $this->nameNodeHelper->getNameByNodeName($valueNode->name);
            } elseif ($valueNode instanceof ClassConstFetch) {
                $classNode = $valueNode->class;
                if (!$classNode instanceof Name) {
                    throw new RectorException("Can't get class name from class const value, class class '" . get_class($valueNode->class) . "' isn't supported");
                }
                $className = $this->nameNodeHelper->getNameByNodeName($classNode);
                $constants[] = "{$className}::{$valueNode->name->name}";
            } else {
                throw new RectorException("Can't get value from value node, class '" . get_class($valueNode) . "' isn't supported");
            }
        }

        $isChanged = false;
        if ($value && !in_array((string)$value, $values, true)) {
            $node->items[] = new ArrayItem($this->convertToAstHelper->valueToAst($value));
            $isChanged = true;
        }

        if ($constant && !in_array($constant, $constants, true)) {
            $node->items[] = new ArrayItem($this->convertToAstHelper->constantToAst($constant));
            $isChanged = true;
        }
        return $isChanged;
    }
}
