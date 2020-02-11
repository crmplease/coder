<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use CrmPlease\Coder\Rector\RectorException;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use function explode;
use function get_class;
use function gettype;
use function implode;
use function in_array;
use function is_float;
use function is_int;
use function is_string;
use function strpos;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToArrayByOrderHelper
{
    /**
     * @param string|float|int $value
     * @param string $constant
     * @param Array_ $node
     *
     * @throws RectorException
     */
    public function arrayToArrayByOrder($value, string $constant, Array_ $node): void
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
                $constants[] = $this->getNameByNodeName($valueNode->name);
            } elseif ($valueNode instanceof ClassConstFetch) {
                $classNode = $valueNode->class;
                if (!$classNode instanceof Name) {
                    throw new RectorException("Can't get class name from class const value, class class '" . get_class($valueNode->class) . "' isn't supported");
                }
                $className = $this->getNameByNodeName($classNode);
                $constants[] = "{$className}::{$valueNode->name->name}";
            } else {
                throw new RectorException("Can't get value from value node, class '" . get_class($valueNode) . "' isn't supported");
            }
        }

        if ($value && !in_array((string)$value, $values, true)) {
            if (is_int($value)) {
                $node->items[] = new LNumber($value, ['kind' => LNumber::KIND_DEC]);
            } elseif (is_float($value)) {
                $node->items[] = new DNumber($value);
            } elseif (is_string($value)) {
                $node->items[] = new String_($value, ['kind' => String_::KIND_SINGLE_QUOTED]);
            } else {
                throw new RectorException("Value type '" . gettype($value) . "' isn't supported");
            }
        }

        if ($constant && !in_array($constant, $constants, true)) {
            if (strpos($constant, '::') !== false) {
                [$className, $constant] = explode('::', $constant);
                $node->items[] = new ClassConstFetch($this->createNodeName($className), new Identifier($constant));
            } else {
                $node->items[] = new ConstFetch($this->createNodeName($constant));
            }
        }
    }

    protected function getNameByNodeName(Name $node) : string
    {
        return implode('\\', $node->parts);
    }

    protected function createNodeName(string $name): Name
    {
        $parts = explode('\\', $name);
        return new Name($parts);
    }
}
