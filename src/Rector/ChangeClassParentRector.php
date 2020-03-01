<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Helper\NameNodeHelper;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use function ltrim;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class ChangeClassParentRector extends AbstractRector
{
    private $nameNodeHelper;
    private $parentClass = '';

    public function __construct(NameNodeHelper $nameNodeHelper)
    {
        $this->nameNodeHelper = $nameNodeHelper;
    }

    /**
     * @param string $parentClass
     * @return $this
     */
    public function setParentClass(string $parentClass): self
    {
        $this->parentClass = $parentClass;
        return $this;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Change class parent to "\Some\ParentClass" class', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
}
PHP
                ,
                <<<'PHP'
use Some\ParentClass;

class SomeClass extends ParentClass
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

        $parentClassName = ltrim($this->parentClass, '\\');
        if ($node->extends && $this->nameNodeHelper->getNameByNodeName($node->extends) === $parentClassName) {
            return null;
        }

        $node->extends = new FullyQualified($parentClassName);
        return $node;
    }
}
