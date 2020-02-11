<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Helper\AddToArrayByOrderHelper;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;
use function get_class;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToReturnArrayByOrderRector extends AbstractRector
{
    private $addToArrayByOrderHelper;
    private $method = '';
    private $value;
    private $constant = '';

    public function __construct(AddToArrayByOrderHelper $addToArrayByOrderHelper)
    {
        $this->addToArrayByOrderHelper = $addToArrayByOrderHelper;
    }

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
        if (!$nodeArray) {
            throw new RectorException("Method {$this->method} return statement is without value");
        }

        if (!$nodeArray instanceof Array_) {
            throw new RectorException("Method {$this->method} return value isn't array, node class: " . get_class($nodeArray));
        }

        $this->addToArrayByOrderHelper->arrayToArrayByOrder($this->value, $this->constant, $nodeArray);

        return $node;
    }
}
