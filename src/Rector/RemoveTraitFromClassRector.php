<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function count;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class RemoveTraitFromClassRector extends AbstractRector
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove trait "\Some\MyTrait" from class', [
            new CodeSample(
                <<<'PHP'
use Some\MyTrait;

class SomeClass
{
    use MyTrait;
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
}
PHP
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        $traitName = ltrim($this->trait, '\\');

        foreach ($node->getTraitUses() as $traitUseNode) {
            foreach ($traitUseNode->traits as $traitNameNode) {
                if ($traitNameNode->toString() === $traitName) {
                    if (count($traitUseNode->traits) > 1) {
                        $this->removeNode($traitNameNode);
                    } else {
                        $this->removeNode($traitUseNode);
                    }
                    return $node;
                }
            }
        }

        return null;
    }
}

