<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;
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
class AddToReturnArrayByOrderRector extends AbstractRector
{
    private $method = '';
    private $value;
    private $constant = '';

    /**
     * @param string $method
     *
     * @return $this
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param string|float|int $value
     *
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param string $constant
     *
     * @return $this
     */
    public function setConstant(string $constant): self
    {
        $this->constant = $constant;
        return $this;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Add to method "getArray" to return array value "newValue" with check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    public function getArray()
    {
        return [
            'existsValue',
        ];
    }
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    public function getArray()
    {
        return [
            'existsValue',
            'newValue',
        ];
    }
}
PHP
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Return_::class];
    }

    /**
     * @param Node $node
     *
     * @return Node|null
     * @throws RectorException
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Return_) {
            return null;
        }
        $methodNode = $node->getAttribute(AttributeKey::METHOD_NODE);
        if (!$methodNode instanceof ClassMethod) {
            return null;
        }
        if ($methodNode->name->name !== $this->method) {
            return null;
        }

        $nodeArray = $node->expr;
        if (!$nodeArray || !$nodeArray instanceof Array_) {
            return null;
        }

        $values = [];
        $constants = [];
        foreach ($nodeArray->items as $itemNode) {
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

        if ($this->value && !in_array((string)$this->value, $values, true)) {
            if (is_int($this->value)) {
                $nodeArray->items[] = new LNumber($this->value, ['kind' => LNumber::KIND_DEC]);
            } elseif (is_float($this->value)) {
                $nodeArray->items[] = new DNumber($this->value);
            } elseif (is_string($this->value)) {
                $nodeArray->items[] = new String_($this->value, ['kind' => String_::KIND_SINGLE_QUOTED]);
            } else {
                throw new RectorException("Value type '" . gettype($this->value) . "' isn't supported");
            }
        }

        if ($this->constant && !in_array($this->constant, $constants, true)) {
            if (strpos($this->constant, '::') !== false) {
                [$className, $constant] = explode('::', $this->constant);
                $nodeArray->items[] = new ClassConstFetch($this->createNodeName($className), new Identifier($constant));
            } else {
                $nodeArray->items[] = new ConstFetch($this->createNodeName($this->constant));
            }
        }

        return $node;
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
