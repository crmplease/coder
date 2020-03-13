<?php
declare(strict_types=1);

namespace Crmplease\Coder\Rector;

use Crmplease\Coder\Helper\ConvertToAstHelper;
use Crmplease\Coder\Helper\PhpdocHelper;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTextNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareReturnTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Core\Exception\NotImplementedException;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;
use function array_flip;
use function array_merge;
use function array_values;
use function ltrim;
use function strpos;
use function substr;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddMethodToClassRector extends AbstractRector
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
    private $phpDocInfoFactory;
    private $convertToAstHelper;
    private $method = '';
    private $visibility = self::VISIBILITY_PRIVATE;
    private $isStatic = false;
    private $isAbstract = false;
    private $isFinal = false;
    private $returnType = '';
    private $returnDescription = '';
    private $description = '';

    public function __construct(
        PhpdocHelper $phpdocHelper,
        PhpDocInfoFactory $phpDocInfoFactory,
        ConvertToAstHelper $convertToAstHelper
    )
    {
        $this->phpdocHelper = $phpdocHelper;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
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
     * @param bool $isAbstract
     *
     * @return $this
     */
    public function setIsAbstract(bool $isAbstract): self
    {
        $this->isAbstract = $isAbstract;

        return $this;
    }

    /**
     * @param bool $isFinal
     *
     * @return $this
     */
    public function setIsFinal(bool $isFinal): self
    {
        $this->isFinal = $isFinal;

        return $this;
    }

    /**
     * @param string $returnType
     *
     * @return $this
     */
    public function setReturnType(string $returnType): self
    {
        $this->returnType = $returnType;

        return $this;
    }

    /**
     * @param string $returnDescription
     *
     * @return $this
     */
    public function setReturnDescription(string $returnDescription): self
    {
        $this->returnDescription = $returnDescription;

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
        return new RectorDefinition('Add protected method "method" with return type "?string", "Method description" description with check duplicates', [
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
    /**
     * Method description
     * 
     * @return string|null
     */
    protected function method(): ?string
    {
        return null;
    }
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
     * @throws NotImplementedException
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        if ($this->isAbstract) {
            $node->flags |= Class_::MODIFIER_ABSTRACT;
        }

        $flags = static::VISIBILITIES_TO_AST_FLAGS[$this->visibility];
        $flags = $this->setBit($flags, $this->isStatic, Class_::MODIFIER_STATIC);
        $flags = $this->setBit($flags, $this->isAbstract, Class_::MODIFIER_ABSTRACT);
        $flags = $this->setBit($flags, $this->isFinal, Class_::MODIFIER_FINAL);

        $simpleReturnType = null;
        $returnTypeNode = null;
        $isReturnTypeNullable = $this->returnType && strpos($this->returnType, '?') === 0;
        if (strpos($this->returnType, '|') === false) {
            if ($this->returnType) {
                $returnTypeString = $isReturnTypeNullable ? substr($this->returnType, 1) : $this->returnType;
                if (strpos($returnTypeString, '\\') !== false) {
                    $returnTypeNode = new FullyQualified(ltrim($returnTypeString, '\\'));
                } else {
                    $simpleReturnType = $returnTypeString;
                    $returnTypeNode = new Identifier($returnTypeString);
                }
                if ($isReturnTypeNullable) {
                    $returnTypeNode = new NullableType($returnTypeNode);
                }
            } elseif ($this->method !== '__construct') {
                $returnTypeNode = new Identifier('void');
            }
        }
        $returnTypePhpDoc = null;
        if ($this->returnType) {
            if (strpos($this->returnType, '?') === 0) {
                $returnTypePhpDocString = substr($this->returnType, 1) . '|null';
            } else {
                $returnTypePhpDocString = $this->returnType;
            }
            $returnTypePhpDoc = $this->phpdocHelper->createTypeTagNodeByString($returnTypePhpDocString);
        } elseif ($this->method !== '__construct') {
            $returnTypePhpDoc = $this->phpdocHelper->createTypeTagNodeByString('void');
        }

        foreach ($node->getMethods() as $method) {
            if ($this->getName($method) === $this->method) {
                // clear visibility bits
                $method->flags &= ~Class_::VISIBILITY_MODIFIER_MASK;
                $method->flags |= $flags;
                $method->returnType = $returnTypeNode;

                if ($this->isAbstract) {
                    $method->stmts = null;
                }

                /** @var PhpDocInfo $phpDocInfo */
                $phpDocInfo = $method->getAttribute(AttributeKey::PHP_DOC_INFO);
                /** @var AttributeAwarePhpDocTextNode|null $textNode */
                $textNode = null;
                $textNodeNumber = null;
                /** @var ReturnTagValueNode $returnTagValue */
                $returnTagValue = null;
                $returnTagValueNodeNumber = null;
                foreach ($phpDocInfo->getPhpDocNode()->children as $childNumber => $child) {
                    if (!$textNode && $child instanceof AttributeAwarePhpDocTextNode) {
                        $textNode = $child;
                        $textNodeNumber = $childNumber;
                    }
                    if (!$returnTagValue && $child instanceof AttributeAwarePhpDocTagNode && $child->value instanceof ReturnTagValueNode) {
                        $returnTagValue = $child->value;
                        $returnTagValueNodeNumber = $childNumber;
                    }
                }
                if ($this->description) {
                    if ($textNode) {
                        $textNode->text = $this->description;
                    } else {
                        $textNode = new AttributeAwarePhpDocTextNode($this->description);
                        $phpDocInfo->getPhpDocNode()->children = array_merge([$textNode], $phpDocInfo->getPhpDocNode()->children);
                    }
                } elseif ($textNodeNumber !== null) {
                    unset($phpDocInfo->getPhpDocNode()->children[$textNodeNumber]);
                    $phpDocInfo->getPhpDocNode()->children = array_values($phpDocInfo->getPhpDocNode()->children);
                }
                if ($returnTypePhpDoc) {
                    if ($returnTagValue) {
                        $returnTagValue->type = $returnTypePhpDoc;
                        $returnTagValue->description = $this->returnDescription;
                    } else {
                        $returnTagValue = new AttributeAwareReturnTagValueNode($returnTypePhpDoc, $this->returnDescription);
                        $phpDocInfo->addTagValueNode($returnTagValue);
                    }
                } elseif ($returnTagValueNodeNumber !== null) {
                    $phpDocInfo->removeTagValueNodeFromNode($returnTagValue);
                }
                return $node;
            }
        }

        $returnStatement = null;
        if (!$this->isAbstract) {
            if ($isReturnTypeNullable) {
                $returnStatement = new Return_($this->convertToAstHelper->simpleValueOrArrayToAst(null));
            } elseif ($simpleReturnType) {
                switch ($simpleReturnType) {
                    case 'bool':
                    case 'boolean':
                        $returnStatement = new Return_($this->convertToAstHelper->simpleValueOrArrayToAst(false));
                        break;
                    case 'int':
                    case 'integer':
                        $returnStatement = new Return_($this->convertToAstHelper->simpleValueOrArrayToAst(0));
                        break;
                    case 'float':
                    case 'double':
                        $returnStatement = new Return_($this->convertToAstHelper->simpleValueOrArrayToAst(0.0));
                        break;
                    case 'string':
                        $returnStatement = new Return_($this->convertToAstHelper->simpleValueOrArrayToAst(''));
                        break;
                }
            }
        }
        $method = new ClassMethod(
            $this->method,
            [
                'flags' => $flags,
                'returnType' => $returnTypeNode,
                'stmts' => $returnStatement ? [$returnStatement] : ($this->isAbstract ? null : []),
            ]
        );
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($method);
        if ($this->description) {
            $phpDocInfo->getPhpDocNode()->children[] = new AttributeAwarePhpDocTextNode($this->description);
        }
        if ($returnTypePhpDoc) {
            $returnTagValue = new AttributeAwareReturnTagValueNode($returnTypePhpDoc, $this->returnDescription);
            $phpDocInfo->addTagValueNode($returnTagValue);
        }

        $node->stmts[] = $method;

        return $node;
    }

    protected function setBit(int $flags, bool $isBitSet, int $bit): int
    {
        if ($isBitSet) {
            return $flags | $bit;
        }
        return $flags & (~$bit);
    }
}
