<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Helper\NameNodeHelper;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;
use function array_merge;
use function ltrim;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddTraitToClassRector extends AbstractRector
{
    private $nameNodeHelper;
    private $trait;

    public function __construct(NameNodeHelper $nameNodeHelper)
    {
        $this->nameNodeHelper = $nameNodeHelper;
    }

    public function setTrait(string $trait): self
    {
        $this->trait = $trait;
        return $this;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Add trait "\Some\MyTrait" class with check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
}
PHP
                ,
                <<<'PHP'
use Some\MyTrait;

class SomeClass
{
    use MyTrait;
}
PHP
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }
    
    /**
     * @param Node $node
     *
     * @return Node|null
     * @throws RectorException
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        $traitName = ltrim($this->trait, '\\');

        foreach ($node->getTraitUses() as $traitUseNode) {
            foreach ($traitUseNode->traits as $traitNameNode) {
                $name = $this->nameNodeHelper->getNameByNodeName($traitNameNode);
                if (ltrim($name, '\\') === $traitName) {
                    return null;
                }
            }
        }

        $traitUseNode = new TraitUse([new FullyQualified($traitName)]);
        $node->stmts = array_merge([$traitUseNode], $node->stmts);
        return $node;
    }
}
