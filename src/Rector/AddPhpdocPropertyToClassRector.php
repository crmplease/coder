<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Helper\PhpdocHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareInvalidTagValueNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePropertyTagValueNode;
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
class AddPhpdocPropertyToClassRector extends AbstractRector
{
    private $phpdocHelper;
    private $symfonyStyle;
    private $parameterProvider;
    private $phpDocParser;
    private $lexer;
    private $showProgressBar = true;
    private $property = '';
    private $propertyType = '';
    private $description = '';

    public function __construct(
        PhpdocHelper $phpDocHelper,
        SymfonyStyle $symfonyStyle,
        ParameterProvider $parameterProvider,
        PhpDocParser $phpDocParser,
        Lexer $lexer
    )
    {
        $this->phpdocHelper = $phpDocHelper;
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
     * @param string $property
     *
     * @return $this
     */
    public function setProperty(string $property): self
    {
        $this->property = $property;
        return $this;
    }

    /**
     * @param string $propertyType class name started with '\\', scalar type, collections, union type
     *
     * @return $this
     */
    public function setPropertyType(?string $propertyType): self
    {
        $this->propertyType = $propertyType;
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
        return new RectorDefinition('Add to phpdoc @property property "property2" with type "string" and description "description" to class with check duplicates', [
            new CodeSample(
                <<<'PHP'
/**
 * @property int $property1
 */
class SomeClass
{
}
PHP
                ,
                <<<'PHP'
/**
 * @property int $property1
 * @property string $property2 description
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

        $propertyType = $this->propertyType;
        $propertyType = $this->phpdocHelper->simplifyFqnForType($propertyType, $node);
        $typeTagNode = $this->phpdocHelper->createTypeTagNodeByString($propertyType);

        /** @var PhpDocInfo $phpDocInfo */
        $phpDocInfo = $node->getAttribute(AttributeKey::PHP_DOC_INFO);
        $propertyTagNodes = $phpDocInfo->getTagsByName('property');
        foreach ($propertyTagNodes as $propertyTagNode) {
            /** @ var AttributeAwarePropertyTagValueNode $value */
            $value = $propertyTagNode->value;
            if ($value instanceof AttributeAwareInvalidTagValueNode) {
                if ($this->showProgressBar) {
                    $this->symfonyStyle->warning("Invalid property {$value->value} Phpdoc: {$value->exception->getMessage()}");
                }
                continue;
            }
            if (!$value instanceof AttributeAwarePropertyTagValueNode) {
                if ($this->showProgressBar) {
                    $this->symfonyStyle->warning('Unknown property Phpdoc class: '. get_class($value));
                }
                continue;
            }
            if ($value->propertyName === "\${$this->property}") {
                $value->description = $this->description;
                $value->type = $typeTagNode;
                return $node;
            }
        }

        $phpDocInfo->addPhpDocTagNode(
            new AttributeAwarePhpDocTagNode(
                '@property',
                new AttributeAwarePropertyTagValueNode(
                    $typeTagNode,
                    "\${$this->property}",
                    $this->description
                )
            )
        );
        return $node;
    }
}
