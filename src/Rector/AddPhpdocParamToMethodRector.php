<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Helper\CheckMethodHelper;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareParamTagValueNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\Core\Configuration\Option;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PHPStan\Type\FullyQualifiedObjectType;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use function count;
use function explode;
use function implode;
use function ltrim;
use function strpos;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddPhpdocParamToMethodRector extends AbstractRector
{
    private $parameterProvider;
    private $checkMethodHelper;
    private $phpDocParser;
    private $lexer;
    private $method = '';
    private $parameter = '';
    private $parameterType = '';
    private $description = '';

    public function __construct(
        ParameterProvider $parameterProvider,
        CheckMethodHelper $checkMethodHelper,
        PhpDocParser $phpDocParser,
        Lexer $lexer
    )
    {
        $this->parameterProvider = $parameterProvider;
        $this->checkMethodHelper = $checkMethodHelper;
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
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
     * @param string $parameterType class name started with '\\', scalar type, collections, union type
     *
     * @return $this
     */
    public function setParameterType(string $parameterType): self
    {
        $this->parameterType = $parameterType;
        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Add to phpdoc @param parameter "parameter2" with type "string" and description "description" to method "foo" with check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    /**
     * @param int $parameter1
     */
    public function foo(int $parameter1 = 0, string $parameter2 = 'defaultValue'): void 
    {}
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    /**
     * @param int $parameter1
     * @param string $parameter2 description
     */
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
     * @throws ShouldNotHappenException
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }

        if (!$this->checkMethodHelper->checkMethod($this->method, $node)) {
            return null;
        }

        $parameterType = $this->parameterType;
        $parameterNode = null;
        foreach ($node->params as $currentParameterNode) {
            if ($currentParameterNode->var->name === $this->parameter) {
                if (!$parameterType && $currentParameterNode->type) {
                    $parameterType = (string)$this->getName($currentParameterNode->type);
                }
                $parameterNode = $currentParameterNode;
                break;
            }
        }
        if (!$parameterNode) {
            throw new RectorException("Can't get parameter '{$this->parameter}' from method '{$this->method}'");
        }

        $parameterType = $this->simplifyFqn($parameterType, $node);

        $typeTagNode = $this->createTypeTagNodeByString($parameterType);
        $parameterTagNode = $this->createPhpDocParamNode($typeTagNode, $parameterNode, $this->description);

        /** @var PhpDocInfo $phpDocInfo */
        $phpDocInfo = $node->getAttribute(AttributeKey::PHP_DOC_INFO);

        $paramTagNodes = $phpDocInfo->getTagsByName('param');
        foreach ($paramTagNodes as $paramTagNode) {
            /** @var AttributeAwareParamTagValueNode $value */
            $value = $paramTagNode->value;
            if (ltrim($value->parameterName, '$') !== $this->parameter) {
                continue;
            }
            $phpDocInfo->removeTagValueNodeFromNode($paramTagNode->value);
            break;
        }

        $phpDocInfo->addPhpDocTagNode($parameterTagNode);
        return $node;
    }

    protected function simplifyFqn(string $unionType, Node $node): string
    {
        if (!$this->parameterProvider->provideParameter(Option::AUTO_IMPORT_NAMES)) {
            return $unionType;
        }
        $types = explode('|', $unionType);
        foreach ($types as &$type) {
            if (strpos($type, '\\') === false) {
                continue;
            }

            $this->addUseType(new FullyQualifiedObjectType(ltrim($type, '\\')), $node);
            $parts = explode('\\', $type);
            $type = $parts[count($parts) - 1];
        }
        unset($type);
        return implode('|', $types);
    }

    protected function createTypeTagNodeByString(string $type) : TypeNode
    {
        if (!$type) {
            return new IdentifierTypeNode('mixed');
        }
        $input = "/** @var {$type} \$name */";
        $tokens = $this->lexer->tokenize($input);
        $tokenIterator = new TokenIterator($tokens);
        /** @var AttributeAwarePhpDocNode $phpDocNode */
        $phpDocNode = $this->phpDocParser ->parse($tokenIterator);
        $varTagValue = $phpDocNode->getVarTagValues()[0] ?? null;
        if ($varTagValue === null) {
            return new IdentifierTypeNode('mixed');
        }
        return $varTagValue->type;
    }

    protected function createPhpDocParamNode(TypeNode $typeNode, Param $parameterNode, string $description): AttributeAwarePhpDocTagNode
    {
        $paramTagValueNode = new AttributeAwareParamTagValueNode(
            $typeNode,
            $parameterNode->variadic,
            '$' . $this->getName($parameterNode),
            $description,
            $parameterNode->byRef
        );

        return new AttributeAwarePhpDocTagNode('@param', $paramTagValueNode);
    }
}
