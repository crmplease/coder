<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Helper\CheckMethodHelper;
use CrmPlease\Coder\Helper\ConvertToAstHelper;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;
use function ltrim;
use function strpos;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddParameterToMethodRector extends AbstractRector
{
    private $checkMethodHelper;
    private $convertToAstHelper;
    private $method = '';
    private $parameter = '';
    private $parameterType = '';
    private $hasValue = true;
    private $value;

    public function __construct(CheckMethodHelper $checkMethodHelper, ConvertToAstHelper $convertToAstHelper)
    {
        $this->checkMethodHelper = $checkMethodHelper;
        $this->convertToAstHelper = $convertToAstHelper;
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
     * @param string $parameter
     *
     * @return $this
     */
    public function setParameter(string $parameter): self
    {
        $this->parameter = $parameter;
        return $this;
    }

    /**
     * @param string $parameterType class name started with '\\' or scalar type
     *
     * @return $this
     */
    public function setParameterType(string $parameterType): self
    {
        $this->parameterType = $parameterType;
        return $this;
    }

    public function setHasValue(bool $hasValue): self
    {
        $this->hasValue = $hasValue;
        return $this;
    }

    /**
     * @param string[]|float[]|int[]|string|float|int|null $value
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
        return new RectorDefinition('Add parameter "parameter2" with type "string" value "defaultValue" to method "foo" with check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    public function foo(int $parameter1 = 0): void 
    {}
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    public function foo(int $parameter1 = 0, string $parameter2 = 'defaultValue'): void 
    {}
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
     * @throws RectorException
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }

        if (!$this->checkMethodHelper->checkMethod($this->method, $node)) {
            return null;
        }

        foreach ($node->params as $parameterNode) {
            if ($parameterNode->var->name === $this->parameter) {
                if ((bool)$parameterNode->type !== (bool)$this->parameterType) {
                    if ($parameterNode->type) {
                        throw new RectorException("Parameter '{$this->parameter}' already exist, but has type");
                    }
                    throw new RectorException("Parameter '{$this->parameter}' already exist, but hasn't type");
                }
                if ($this->getName($parameterNode->type) !== ltrim($this->parameterType, '\\')) {
                    throw new RectorException("Parameter '{$this->parameter}' already exist, but has type '{$this->getName($parameterNode->type)}'");
                }
                if ((bool)$parameterNode->default !== $this->hasValue) {
                    if ($parameterNode->default) {
                        throw new RectorException("Parameter '{$this->parameter}' already exist, but has default value");
                    }
                    throw new RectorException("Parameter '{$this->parameter}' already exist, but hasn't default value");
                }
                // compare value isn't implemented
                return null;
            }
        }

        $typeNode = null;
        if ($this->parameterType) {
            if (strpos($this->parameterType, '\\') !== false) {
                $typeNode = new FullyQualified(ltrim($this->parameterType, '\\'));
            } else {
                $typeNode = new Identifier($this->parameterType);
            }
        }

        $node->params[] = new Param(
            new Variable($this->parameter),
            $this->hasValue ? $this->convertToAstHelper->simpleValueOrArrayToAst($this->value) : null,
            $typeNode
        );

        return $node;
    }
}
