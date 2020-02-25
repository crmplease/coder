<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use function array_merge;
use function array_slice;
use function ltrim;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddTraitToClassRector extends AbstractRector
{
    private $trait;

    /**
     * @param string $trait
     *
     * @return $this
     */
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
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        $traitName = ltrim($this->trait, '\\');

        foreach ($node->getTraitUses() as $traitUseNode) {
            foreach ($traitUseNode->traits as $traitNameNode) {
                if ($traitNameNode->toString() === $traitName) {
                    return null;
                }
            }
        }

        $traitUseNode = new TraitUse([new FullyQualified($traitName)]);
        $lastTraitStatementNumber = null;
        foreach ($node->stmts as $statementNumber => $statement) {
            if (!$statement instanceof TraitUse) {
                continue;
            }
            $lastTraitStatementNumber = $statementNumber;
        }

        if ($lastTraitStatementNumber === null) {
            // if there is no trait uses, then add to begin of class
            $node->stmts = array_merge([$traitUseNode], $node->stmts);
        } else {
            // add after last trait use
            $node->stmts = array_merge(
                array_slice($node->stmts, 0, $lastTraitStatementNumber + 1),
                [$traitUseNode],
                array_slice($node->stmts, $lastTraitStatementNumber + 1)
            );
        }
        return $node;
    }
}
