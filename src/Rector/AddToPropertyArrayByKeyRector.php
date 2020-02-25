<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Code;
use CrmPlease\Coder\Constant;
use CrmPlease\Coder\Helper\AddToArrayByKeyHelper;
use CrmPlease\Coder\Helper\NodeArrayHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToPropertyArrayByKeyRector extends AbstractRector
{
    private $nodeArrayHelper;
    private $addToArrayByKeyHelper;
    private $property = '';
    private $path = [];
    private $key;
    private $value;

    public function __construct(NodeArrayHelper $nodeArrayHelper, AddToArrayByKeyHelper $addToArrayByKeyHelper)
    {
        $this->nodeArrayHelper = $nodeArrayHelper;
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
        return new RectorDefinition('Add to property "array" value "newValue" by "newKey" with check duplicates', [
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

        $arrayNode = $this->nodeArrayHelper->getFromPropertyPropertyStatement($node);
        $arrayNode = $this->nodeArrayHelper->findOrAddArrayByPath($this->path, $arrayNode);
        $this->addToArrayByKeyHelper->addToArrayByKey(
            $this->key,
            $this->value,
            $arrayNode
        );
        return $node;
    }
}
