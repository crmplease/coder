<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Code;
use CrmPlease\Coder\Constant;
use CrmPlease\Coder\Helper\AddToArrayByOrderHelper;
use CrmPlease\Coder\Helper\NodeArrayHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToPropertyArrayByOrderRector extends AbstractRector
{
    private $nodeArrayHelper;
    private $addToArrayByOrderHelper;
    private $property = '';
    private $path = [];
    private $value;

    public function __construct(NodeArrayHelper $nodeArrayHelper, AddToArrayByOrderHelper $addToArrayByOrderHelper)
    {
        $this->nodeArrayHelper = $nodeArrayHelper;
        $this->addToArrayByOrderHelper = $addToArrayByOrderHelper;
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
        return new RectorDefinition('Add to property "array" value "newValue" with check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    protected $array = [
        'existsValue',
    ];
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    protected $array = [
        'existsValue',
        'newValue',
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
        $this->addToArrayByOrderHelper->addToArrayByOrder($this->value, $arrayNode);
        return $node;
    }
}
