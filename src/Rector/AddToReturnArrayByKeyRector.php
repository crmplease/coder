<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Helper\AddToArrayByKeyHelper;
use CrmPlease\Coder\Helper\CheckMethodHelper;
use CrmPlease\Coder\Helper\GetNodeArrayHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToReturnArrayByKeyRector extends AbstractRector
{
    private $checkMethodHelper;
    private $getNodeArrayHelper;
    private $addToArrayByKeyHelper;
    private $method = '';
    private $key;
    private $keyConstant;
    private $value;
    private $constant = '';

    public function __construct(
        CheckMethodHelper $checkMethodHelper,
        GetNodeArrayHelper $getNodeArrayHelper,
        AddToArrayByKeyHelper $addToArrayByKeyHelper
    )
    {
        $this->checkMethodHelper = $checkMethodHelper;
        $this->getNodeArrayHelper = $getNodeArrayHelper;
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
     * @param string|int $key
     *
     * @return $this
     * @throws RectorException
     */
    public function setKey($key): self
    {
        if ($this->keyConstant) {
            throw new RectorException('You should provide only key or only keyConstant to AddToReturnArrayByKeyRector');
        }
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $keyConstant
     *
     * @return $this
     * @throws RectorException
     */
    public function setKeyConstant(string $keyConstant): self
    {
        if ($this->key) {
            throw new RectorException('You should provide only key or only keyConstant to AddToReturnArrayByKeyRector');
        }
        $this->keyConstant = $keyConstant;
        return $this;
    }

    /**
     * @param string|float|int $value
     *
     * @return $this
     * @throws RectorException
     */
    public function setValue($value): self
    {
        if ($this->constant) {
            throw new RectorException('You should provide only value or only constant to AddToReturnArrayByKeyRector');
        }
        $this->value = $value;
        return $this;
    }

    /**
     * @param string $constant
     *
     * @return $this
     * @throws RectorException
     */
    public function setConstant(string $constant): self
    {
        if ($this->value) {
            throw new RectorException('You should provide only value or only constant to AddToReturnArrayByKeyRector');
        }
        $this->constant = $constant;
        return $this;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Add to method "getArray" to return array value "newValue" by "newKey" with check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    public function getArray()
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
    public function getArray()
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

        $arrayNode = $this->getNodeArrayHelper->getFromReturnStatement($node);
        $result = $this->addToArrayByKeyHelper->addToArrayByKey(
            $this->key,
            $this->keyConstant,
            $this->value,
            $this->constant,
            $arrayNode
        );
        return $result ? $node : null;
    }
}
