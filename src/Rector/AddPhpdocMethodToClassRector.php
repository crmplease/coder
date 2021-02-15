<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Config;
use Crmplease\Coder\Helper\PhpdocHelper;
use Crmplease\Coder\PhpdocMethodParameter;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareInvalidTagValueNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareMethodTagValueNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractRector;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function get_class;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddPhpdocMethodToClassRector extends AbstractRector
{
    private $config;
    private $phpdocHelper;
    private $symfonyStyle;
    private $method = '';
    private $returnType = '';
    private $isStatic = false;
    /**
     * @var PhpdocMethodParameter[]
     */
    private $parameters = [];
    private $description = '';

    public function __construct(
        Config $config,
        PhpdocHelper $phpdocHelper,
        SymfonyStyle $symfonyStyle
    )
    {
        $this->config = $config;
        $this->phpdocHelper = $phpdocHelper;
        $this->symfonyStyle = $symfonyStyle;
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
    public function setReturnType(string $returnType): self
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add to phpdoc @method method "method2" with return type "string" and description "description" to class with check duplicates', [
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

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        $phpDocInfo->markAsChanged();

        $methodTagNodes = $phpDocInfo->getTagsByName('method');
        foreach ($methodTagNodes as $methodTagNode) {
            $value = $methodTagNode->value;
            if ($value instanceof AttributeAwareInvalidTagValueNode) {
                if ($this->config->doShowProgressBar()) {
                    $this->symfonyStyle->warning("Invalid method {$value->value} Phpdoc: {$value->exception->getMessage()}");
                }
                continue;
            }
            if (!$value instanceof AttributeAwareMethodTagValueNode) {
                if ($this->config->doShowProgressBar()) {
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
