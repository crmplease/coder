<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Helper\CheckMethodHelper;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

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

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Add to method "addCode" code "$this->b = $b;" to the end of method', [
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

        $node->stmts[] = new ConstFetch(
            new Name([$this->code])
        );

        return $node;
    }
}
