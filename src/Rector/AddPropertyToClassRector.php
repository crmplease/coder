<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Code;
use Crmplease\Coder\Constant;
use Crmplease\Coder\Helper\ConvertToAstHelper;
use Crmplease\Coder\Helper\GetPropertyPropertyHelper;
use Crmplease\Coder\Helper\PhpdocHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareVarTagValueNode;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function array_merge;
use function array_slice;
use function count;
use function get_class;
use function gettype;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddPropertyToClassRector extends AbstractRector
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PROTECTED = 'protected';
    public const VISIBILITY_PRIVATE = 'private';

    public const VISIBILITIES_TO_AST_FLAGS = [
        self::VISIBILITY_PUBLIC => Class_::MODIFIER_PUBLIC,
        self::VISIBILITY_PROTECTED => Class_::MODIFIER_PROTECTED,
        self::VISIBILITY_PRIVATE => Class_::MODIFIER_PRIVATE,
    ];

    private $phpdocHelper;
    private $getPropertyPropertyHelper;
    private $convertToAstHelper;
    private $phpDocTagRemover;
    private $property = '';
    private $visibility = self::VISIBILITY_PRIVATE;
    private $isStatic = false;
    private $value;
    private $type = '';
    private $description = '';

    public function __construct(
        PhpdocHelper $phpdocHelper,
        GetPropertyPropertyHelper $getPropertyPropertyHelper,
        ConvertToAstHelper $convertToAstHelper,
        PhpDocTagRemover $phpDocTagRemover
    )
    {
        $this->phpdocHelper = $phpdocHelper;
        $this->getPropertyPropertyHelper = $getPropertyPropertyHelper;
        $this->convertToAstHelper = $convertToAstHelper;
        $this->phpDocTagRemover = $phpDocTagRemover;
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
     * @see AddPropertyToClassRector::VISIBILITIES_TO_AST_FLAGS
     * @param string $visibility self::VISIBILITY_*
     *
     * @return $this
     * @throws RectorException
     */
    public function setVisibility(string $visibility): self
    {
        if (!isset(static::VISIBILITIES_TO_AST_FLAGS[$visibility])) {
            throw new RectorException("Unknown visibility '{$visibility}'");
        }
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * @param bool $isStatic
     *
     * @return $this
     */
    public function setIsStatic(bool $isStatic): self
    {
        $this->isStatic = $isStatic;

        return $this;
    }

    /**
     * @param string|float|int|array|Constant|Code|null $value
     *
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add protected property "property" with value "defaultValue" with check duplicates', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    protected $property = 'defaultValue';
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
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        $flags = static::VISIBILITIES_TO_AST_FLAGS[$this->visibility];
        if ($this->isStatic) {
            $flags |= Class_::MODIFIER_STATIC;
        }

        if (!$this->type && $this->value !== null) {
            $value = $this->value;
            if ($value instanceof Constant) {
                $value = $value->getValue();
            }
            switch (gettype($value)) {
                case 'boolean':
                    $this->type = 'bool';
                    break;
                case 'integer':
                    $this->type = 'int';
                    break;
                case 'double':
                    $this->type = 'float';
                    break;
                case 'string':
                    $this->type = 'string';
                    break;
                case 'array':
                    $this->type = 'array';
                    break;
            }
        }
        $typePhpDoc = null;
        if ($this->type || $this->description) {
            $typePhpDoc = $this->phpdocHelper->createTypeTagNodeByString($this->type);
        }

        $propertyPropertyNode = $this->getPropertyPropertyHelper->getPropertyProperty($node, $this->property);
        if ($propertyPropertyNode) {
            $propertyNode = $propertyPropertyNode->getAttribute(AttributeKey::PARENT_NODE);
            if (!$propertyNode) {
                throw new RectorException("Can't get property property parent node");
            }
            if (!$propertyNode instanceof Property) {
                throw new RectorException("Can't get property node from property property node, got class: " . get_class($propertyNode));
            }
            if (count($propertyNode->props) > 1) {
                throw new RectorException("Multiple properties aren't supported");
            }
            // clear visibility bits
            $propertyNode->flags &= ~Class_::VISIBILITY_MODIFIER_MASK;
            $propertyNode->flags |= $flags;
            if ($this->value === null) {
                $propertyPropertyNode->default = null;
            } else {
                $propertyPropertyNode->default = $this->convertToAstHelper->simpleValueOrArrayToAst($this->value);
            }

            $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($propertyNode);

            $varTagValueNode = $phpDocInfo->getVarTagValueNode();
            if ($typePhpDoc) {
                if ($varTagValueNode) {
                    $varTagValueNode->type = $typePhpDoc;
                    $varTagValueNode->description = $this->description;
                } else {
                    $varTagValueNode = new AttributeAwareVarTagValueNode($typePhpDoc, '', $this->description);
                    $phpDocInfo->addTagValueNode($varTagValueNode);
                }
                $phpDocInfo->markAsChanged();
            } elseif ($varTagValueNode !== null) {
                $this->phpDocTagRemover->removeByName($phpDocInfo, 'var');
                $phpDocInfo->markAsChanged();
            }

            return $node;
        }
        $valueNode = null;
        if ($this->value !== null) {
            $valueNode = $this->convertToAstHelper->simpleValueOrArrayToAst($this->value);
        }

        $propertyNode = new Property(
            $flags,
            [
                new PropertyProperty(new Node\VarLikeIdentifier($this->property), $valueNode),
            ]
        );

        if ($typePhpDoc) {
            $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($propertyNode);
            $phpDocInfo->markAsChanged();
            $varTagValueNode = new AttributeAwareVarTagValueNode($typePhpDoc, '', $this->description);
            $phpDocInfo->addTagValueNode($varTagValueNode);
        }

        $constructorMethodNode = $node->getMethod('__construct');
        $constructorMethodStatementNumber = null;
        $lastPropertyStatementNumber = null;
        foreach ($node->stmts as $statementNumber => $statement) {
            if ($statement === $constructorMethodNode) {
                $constructorMethodStatementNumber = $statementNumber;
            }
            if ($statement instanceof Property) {
                $lastPropertyStatementNumber = $statementNumber;
            }
        }

        if ($lastPropertyStatementNumber === null && $constructorMethodStatementNumber === null) {
            // if no constructor or any property found, then put new property to begin of class
            $node->stmts = array_merge(
                [$propertyNode],
                $node->stmts
            );
        } else {
            if ($lastPropertyStatementNumber === null) {
                // if constructor found, then put new property before constructor
                $putToStatement = $constructorMethodStatementNumber;
            } else {
                // if any properties found, then put new property after last property
                $putToStatement = $lastPropertyStatementNumber + 1;
            }
            $node->stmts = array_merge(
                array_slice($node->stmts, 0, $putToStatement),
                [$propertyNode],
                array_slice($node->stmts, $putToStatement)
            );
        }
        return $node;
    }
}
