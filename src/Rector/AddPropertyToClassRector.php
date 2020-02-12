<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Rector;

use CrmPlease\Coder\Helper\ConvertToAstHelper;
use CrmPlease\Coder\Helper\GetPropertyPropertyHelper;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;
use function array_merge;
use function get_class;

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

    private $getPropertyPropertyHelper;
    private $convertToAstHelper;
    private $property = '';
    private $visibility = self::VISIBILITY_PRIVATE;
    private $isStatic = false;
    private $value;

    public function __construct(GetPropertyPropertyHelper $getPropertyPropertyHelper, ConvertToAstHelper $convertToAstHelper)
    {
        $this->getPropertyPropertyHelper = $getPropertyPropertyHelper;
        $this->convertToAstHelper = $convertToAstHelper;
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
     * @param string $visibility
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
        return new RectorDefinition('Add protected property "property" with value "defaultValue" with check duplicates', [
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

        $propertyPropertyNode = $this->getPropertyPropertyHelper->getPropertyProperty($node, $this->property);
        if ($propertyPropertyNode) {
            $propertyNode = $propertyPropertyNode->getAttribute(AttributeKey::PARENT_NODE);
            if (!$propertyNode) {
                throw new RectorException("Can't get property property parent node");
            }
            if (!$propertyNode instanceof Property) {
                throw new RectorException("Can't get property node from property property node, got class: " . get_class($propertyNode));
            }
            switch ($this->visibility) {
                case static::VISIBILITY_PRIVATE:
                    if (!$propertyNode->isPrivate()) {
                        throw new RectorException("Property {$this->property} already exists, but isn't private");
                    }
                    break;
                case static::VISIBILITY_PROTECTED:
                    if (!$propertyNode->isProtected()) {
                        throw new RectorException("Property {$this->property} already exists, but isn't protected");
                    }
                    break;
                case static::VISIBILITY_PUBLIC:
                    if (!$propertyNode->isPublic()) {
                        throw new RectorException("Property {$this->property} already exists, but isn't public");
                    }
                    break;
            }
            if ($propertyNode->isStatic() !== $this->isStatic) {
                if ($propertyNode->isStatic()) {
                    throw new RectorException("Property {$this->property} already exists, but is static");
                }
                throw new RectorException("Property {$this->property} already exists, but isn't static");
            }
            // compare value isn't implemented
            return null;
        }
        $valueNode = null;
        if ($this->value !== null) {
            $valueNode = $this->convertToAstHelper->simpleValueOrArrayToAst($this->value);
        }

        $flags = static::VISIBILITIES_TO_AST_FLAGS[$this->visibility];
        if ($this->isStatic) {
            $flags |= Class_::MODIFIER_STATIC;
        }
        $propertyNode = new Property(
            $flags,
            [
                new PropertyProperty(new Node\VarLikeIdentifier($this->property), $valueNode),
            ]
        );
        $node->stmts = array_merge(
            [$propertyNode],
            $node->stmts
        );
        return $node;
    }
}
