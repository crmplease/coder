<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Code;
use CrmPlease\Coder\Constant;
use CrmPlease\Coder\Helper\AddToArrayByOrderHelper;
use CrmPlease\Coder\Helper\NodeArrayHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToFileReturnArrayByOrderRector extends AbstractRector
{
    private $nodeArrayHelper;
    private $addToArrayByOrderHelper;
    private $method = '';
    private $path = [];
    private $value;

    public function __construct(NodeArrayHelper $nodeArrayHelper, AddToArrayByOrderHelper $addToArrayByOrderHelper)
    {
        $this->nodeArrayHelper = $nodeArrayHelper;
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
        return new RectorDefinition('Add to file to return array value "newValue" with check duplicates', [
            new CodeSample(
                <<<'PHP'
return [
    'existsValue',
];
PHP
                ,
                <<<'PHP'
return [
    'existsValue',
    'newValue',
];
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

        $arrayNode = $this->nodeArrayHelper->getFromReturnStatement($node);
        $arrayNode = $this->nodeArrayHelper->findOrAddArrayByPath($this->path, $arrayNode);
        $this->addToArrayByOrderHelper->addToArrayByOrder($this->value, $arrayNode);
        return $node;
    }
}
