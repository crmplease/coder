<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Code;
use Crmplease\Coder\Constant;
use Crmplease\Coder\Helper\AddToArrayByOrderHelper;
use Crmplease\Coder\Helper\NodeArrayHelper;
use Crmplease\Coder\Helper\ReturnStatementHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToFileReturnArrayByOrderRector extends AbstractRector
{
    private $nodeArrayHelper;
    private $addToArrayByOrderHelper;
    private $returnStatementHelper;
    private $path = [];
    private $value;

    public function __construct(NodeArrayHelper $nodeArrayHelper, AddToArrayByOrderHelper $addToArrayByOrderHelper, ReturnStatementHelper $returnStatementHelper)
    {
        $this->nodeArrayHelper = $nodeArrayHelper;
        $this->addToArrayByOrderHelper = $addToArrayByOrderHelper;
        $this->returnStatementHelper = $returnStatementHelper;
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
        if ($this->returnStatementHelper->isReturnNodeForClosure($node)) {
            return null;
        }

        $arrayNode = $this->nodeArrayHelper->getFromReturnStatement($node);
        $arrayNode = $this->nodeArrayHelper->findOrAddArrayByPath($this->path, $arrayNode);
        $this->addToArrayByOrderHelper->addToArrayByOrder($this->value, $arrayNode);
        return $node;
    }
}
