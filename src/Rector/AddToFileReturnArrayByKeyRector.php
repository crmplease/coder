<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Code;
use Crmplease\Coder\Constant;
use Crmplease\Coder\Helper\AddToArrayByKeyHelper;
use Crmplease\Coder\Helper\NodeArrayHelper;
use Crmplease\Coder\Helper\ReturnStatementHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToFileReturnArrayByKeyRector extends AbstractRector
{
    private $nodeArrayHelper;
    private $addToArrayByKeyHelper;
    private $returnStatementHelper;
    private $path = [];
    private $key;
    private $value;

    public function __construct(NodeArrayHelper $nodeArrayHelper, AddToArrayByKeyHelper $addToArrayByKeyHelper, ReturnStatementHelper $returnStatementHelper)
    {
        $this->nodeArrayHelper = $nodeArrayHelper;
        $this->addToArrayByKeyHelper = $addToArrayByKeyHelper;
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add to file to return array value "newValue" by "newKey" with check duplicates', [
            new CodeSample(
                <<<'PHP'
return [
    'existsKey' => 'existsValue',
];
PHP
                ,
                <<<'PHP'
return [
    'existsKey' => 'existsValue',
    'newKey' => 'newValue',
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
        $this->addToArrayByKeyHelper->addToArrayByKey(
            $this->key,
            $this->value,
            $arrayNode
        );
        return $node;
    }
}
