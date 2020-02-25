<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Code;
use CrmPlease\Coder\Constant;
use CrmPlease\Coder\Helper\AddToArrayByKeyHelper;
use CrmPlease\Coder\Helper\CheckMethodHelper;
use CrmPlease\Coder\Helper\NodeArrayHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;
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
    private $method = '';
    private $path = [];
    private $key;
    private $value;

    public function __construct(
        CheckMethodHelper $checkMethodHelper,
        NodeArrayHelper $nodeArrayHelper,
        AddToArrayByKeyHelper $addToArrayByKeyHelper
    )
    {
        $this->checkMethodHelper = $checkMethodHelper;
        $this->nodeArrayHelper = $nodeArrayHelper;
        $this->addToArrayByKeyHelper = $addToArrayByKeyHelper;
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
        if (!$this->checkMethodHelper->checkMethod($this->method, $node)) {
            return null;
        }

        $arrayNode = $this->nodeArrayHelper->getFromReturnStatement($node);
        $arrayNode = $this->nodeArrayHelper->findOrAddArrayByPath($this->path, $arrayNode);
        $this->addToArrayByKeyHelper->addToArrayByKey(
            $this->key,
            $this->value,
            $arrayNode
        );
        return $node;
    }
}
