<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Code;
use Crmplease\Coder\Constant;
use Crmplease\Coder\Helper\AddToArrayByKeyHelper;
use Crmplease\Coder\Helper\CheckMethodHelper;
use Crmplease\Coder\Helper\NodeArrayHelper;
use Crmplease\Coder\Helper\ReturnStatementHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToReturnArrayByKeyRector extends AbstractRector
{
    private $checkMethodHelper;
    private $nodeArrayHelper;
    private $addToArrayByKeyHelper;
    private $returnStatementHelper;
    private $method = '';
    private $path = [];
    private $key;
    private $value;

    public function __construct(
        CheckMethodHelper $checkMethodHelper,
        NodeArrayHelper $nodeArrayHelper,
        AddToArrayByKeyHelper $addToArrayByKeyHelper,
        ReturnStatementHelper $returnStatementHelper
    )
    {
        $this->checkMethodHelper = $checkMethodHelper;
        $this->nodeArrayHelper = $nodeArrayHelper;
        $this->addToArrayByKeyHelper = $addToArrayByKeyHelper;
        $this->returnStatementHelper = $returnStatementHelper;
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
     * @param string[]|int[]|Constant[] $path
     *
     * @return $this
     */
    public function setPath($path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param string|int|Constant $key
     *
     * @return $this
     */
    public function setKey($key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string|float|int|array|Constant|Code $value
     *
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Add to method "getArray" to return array value "newValue" by "newKey" with check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    public function getArray(): array
    {
        return [
            'existsKey' => 'existsValue',
        ];
    }
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    public function getArray(): array
    {
        return [
            'existsKey' => 'existsValue',
            'newKey' => 'newValue',
        ];
    }
}
PHP
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param Node $node
     *
     * @return Node|null
     * @throws RectorException
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }
        if (!$this->checkMethodHelper->checkMethod($this->method, $node)) {
            return null;
        }
        $returnStatement = $this->returnStatementHelper->getLastReturnForClassMethod($node);
        if ($returnStatement === null) {
            return null;
        }

        $arrayNode = $this->nodeArrayHelper->getFromReturnStatement($returnStatement);
        $arrayNode = $this->nodeArrayHelper->findOrAddArrayByPath($this->path, $arrayNode);
        $this->addToArrayByKeyHelper->addToArrayByKey(
            $this->key,
            $this->value,
            $arrayNode
        );
        return $node;
    }
}
