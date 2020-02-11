<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Helper\AddToArrayByKeyHelper;
use CrmPlease\Coder\Helper\GetNodeArrayHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToPropertyArrayByKeyRector extends AbstractRector
{
    private $getNodeArrayHelper;
    private $addToArrayByKeyHelper;
    private $property = '';
    private $key;
    private $keyConstant;
    private $value;
    private $constant = '';

    public function __construct(GetNodeArrayHelper $getNodeArrayHelper, AddToArrayByKeyHelper $addToArrayByKeyHelper)
    {
        $this->getNodeArrayHelper = $getNodeArrayHelper;
        $this->addToArrayByKeyHelper = $addToArrayByKeyHelper;
    }

    /**
     * @param string $property
     *
     * @return $this
     */
    public function setProperty(string $property): self
    {
        $this->property = $property;
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
        return new RectorDefinition('Add to property "array" value "newValue" with check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    protected $array = [
        'existsKey' => 'existsValue',
    ];
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    protected $array = [
        'existsKey' => 'existsValue',
        'newKey' => 'newValue',
    ];
}
PHP
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [PropertyProperty::class];
    }

    /**
     * @param Node $node
     *
     * @return Node|null
     * @throws RectorException
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof PropertyProperty) {
            return null;
        }

        if ($node->name->name !== $this->property) {
            return null;
        }

        $arrayNode = $this->getNodeArrayHelper->getFromPropertyPropertyStatement($node);
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
