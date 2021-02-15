<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Helper\CheckMethodHelper;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddCodeToMethodRector extends AbstractRector
{
    private $checkMethodHelper;
    private $method = '';
    private $code = '';

    public function __construct(CheckMethodHelper $checkMethodHelper)
    {
        $this->checkMethodHelper = $checkMethodHelper;
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
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add to method "addCode" code "$this->b = $b;" to the end of method with trying check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    public function addCode(int $a, int $b) : void
    {
        $this->a = $a;
    }
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    public function addCode(int $a, int $b) : void
    {
        $this->a = $a;
        $this->b = $b;
    }
}
PHP
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param Node $node
     *
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }

        if (!$this->checkMethodHelper->checkMethod($this->method, $node)) {
            return null;
        }

        // try to check duplicates in simple cases
        foreach ($node->stmts as $statement) {
            $code = $this->betterStandardPrinter->print($statement);
            if ($code === $this->code) {
                return null;
            }
        }

        $node->stmts[] = new ConstFetch(
            new Name([$this->code])
        );

        return $node;
    }
}
