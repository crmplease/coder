<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Helper\PhpdocHelper;
use Crmplease\Coder\PhpdocMethodParameter;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareInvalidTagValueNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareMethodTagValueNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use function get_class;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddPhpdocMethodToClassRector extends AbstractRector
{
    private $phpdocHelper;
    private $symfonyStyle;
    private $parameterProvider;
    private $phpDocParser;
    private $lexer;
    private $showProgressBar = true;
    private $method = '';
    private $returnType = '';
    private $isStatic = false;
    /**
     * @var PhpdocMethodParameter[]
     */
    private $parameters = [];
    private $description = '';

    public function __construct(
        PhpdocHelper $phpdocHelper,
        SymfonyStyle $symfonyStyle,
        ParameterProvider $parameterProvider,
        PhpDocParser $phpDocParser,
        Lexer $lexer
    )
    {
        $this->phpdocHelper = $phpdocHelper;
        $this->symfonyStyle = $symfonyStyle;
        $this->parameterProvider = $parameterProvider;
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
    }

    /**
     * @param bool $showProgressBar
     *
     * @return $this
     */
    public function setShowProgressBar(bool $showProgressBar): self
    {
        $this->showProgressBar = $showProgressBar;

        return $this;
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
     * @param string $returnType class name started with '\\', scalar type, collections, union type
     *
     * @return $this
     */
    public function setReturnType(?string $returnType): self
    {
        $this->returnType = $returnType;
        return $this;
    }

    /**
     * @param bool $isStatic true if method should be static
     *
     * @return $this
     */
    public function setIsStatic(bool $isStatic): self
    {
        $this->isStatic = $isStatic;
        return $this;
    }

    /**
     * @param PhpdocMethodParameter[] $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description = ''): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Add to phpdoc @method method "method2" with return type "string" and description "description" to class with check duplicates', [
            new CodeSample(
                <<<'PHP'
/**
 * @method int method1()
 */
class SomeClass
{
}
PHP
                ,
                <<<'PHP'
/**
 * @method int method1()
 * @method string method2() description
 */
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

    /**
     * @param Node $node
     *
     * @return Node|null
     * @throws RectorException
     * @throws ShouldNotHappenException
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        $returnType = $this->returnType;
        $returnType = $this->phpdocHelper->simplifyFqnForType($returnType, $node);
        $returnTypeTagNode = $this->phpdocHelper->createTypeTagNodeByString($returnType);
        $parameters = [];
        foreach ($this->parameters as $parameter) {
            $parameters[] = new MethodTagValueParameterNode(
                $this->phpdocHelper->createTypeTagNodeByString(
                    $this->phpdocHelper->simplifyFqnForType(
                        $parameter->getType(),
                        $node
                    )
                ),
                false,
                false,
                "\${$parameter->getName()}",
                $parameter->hasDefault() ? $this->phpdocHelper->simpleValueOrArrayToAst($parameter->getDefault()) : null
            );
        }

        /** @var PhpDocInfo $phpDocInfo */
        $phpDocInfo = $node->getAttribute(AttributeKey::PHP_DOC_INFO);
        $methodTagNodes = $phpDocInfo->getTagsByName('method');
        foreach ($methodTagNodes as $methodTagNode) {
            $value = $methodTagNode->value;
            if ($value instanceof AttributeAwareInvalidTagValueNode) {
                if ($this->showProgressBar) {
                    $this->symfonyStyle->warning("Invalid method {$value->value} Phpdoc: {$value->exception->getMessage()}");
                }
                continue;
            }
            if (!$value instanceof AttributeAwareMethodTagValueNode) {
                if ($this->showProgressBar) {
                    $this->symfonyStyle->warning('Unknown method Phpdoc class: '. get_class($value));
                }
                continue;
            }
            if ($value->methodName === $this->method) {
                $value->returnType = $returnTypeTagNode;
                $value->isStatic = $this->isStatic;
                $value->parameters = $parameters;
                $value->description = $this->description;
                return $node;
            }
        }

        $phpDocInfo->addPhpDocTagNode(
            new AttributeAwarePhpDocTagNode(
                '@method',
                new AttributeAwareMethodTagValueNode(
                    $this->isStatic,
                    $returnTypeTagNode,
                    $this->method,
                    $parameters,
                    $this->description
                )
            )
        );
        return $node;
    }
}
