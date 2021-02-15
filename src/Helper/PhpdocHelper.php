<?php
declare(strict_types=1);

namespace Crmplease\Coder\Helper;

use Crmplease\Coder\Code;
use Crmplease\Coder\Constant;
use Crmplease\Coder\Rector\RectorException;
use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayItemNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFalseNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFloatNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNullNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprStringNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprTrueNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocNode;
use Rector\BetterPhpDocParser\PhpDocParser\BetterPhpDocParser;
use Rector\Core\Configuration\Option;
use Rector\PostRector\Collector\UseNodesToAddCollector;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use function explode;
use function gettype;
use function implode;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function ltrim;
use function strpos;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class PhpdocHelper
{
    private $parameterProvider;
    private $useNodesToAddCollector;
    private $phpDocParser;
    private $lexer;

    public function __construct(
        ParameterProvider $parameterProvider,
        UseNodesToAddCollector $useNodesToAddCollector,
        BetterPhpDocParser $phpDocParser,
        Lexer $lexer
    )
    {
        $this->parameterProvider = $parameterProvider;
        $this->useNodesToAddCollector = $useNodesToAddCollector;
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
    }

    /**
     * @param string $unionType
     * @param Node $node
     *
     * @return string
     */
    public function simplifyFqnForType(string $unionType, Node $node): string
    {
        if (!$this->parameterProvider->provideParameter(Option::AUTO_IMPORT_NAMES)) {
            return $unionType;
        }
        $types = explode('|', $unionType);
        foreach ($types as &$type) {
            if (strpos($type, '\\') === false) {
                continue;
            }

            $objectType = new FullyQualifiedObjectType(ltrim($type, '\\'));
            $this->useNodesToAddCollector->addUseImport($node, $objectType);
            $type = $objectType->getShortName();
        }
        unset($type);
        return implode('|', $types);
    }

    public function createTypeTagNodeByString(string $type) : TypeNode
    {
        if (!$type) {
            return new IdentifierTypeNode('mixed');
        }
        $input = "/** @var {$type} \$name */";
        $tokens = $this->lexer->tokenize($input);
        $tokenIterator = new TokenIterator($tokens);
        /** @var AttributeAwarePhpDocNode $phpDocNode */
        $phpDocNode = $this->phpDocParser->parse($tokenIterator);
        $varTagValue = $phpDocNode->getVarTagValues()[0] ?? null;
        if ($varTagValue === null) {
            return new IdentifierTypeNode('mixed');
        }
        return $varTagValue->type;
    }

    /**
     * @param string|float|int|bool|null $value
     *
     * @return ConstExprNode
     * @throws RectorException
     */
    public function simpleValueToAst($value): ConstExprNode
    {
        if ($value === null) {
            return new ConstExprNullNode();
        }
        if (is_bool($value)) {
            if ($value) {
                return new ConstExprTrueNode();
            }
            return new ConstExprFalseNode();
        }
        if (is_int($value)) {
            return new ConstExprIntegerNode((string)$value);
        }
        if (is_float($value)) {
            return new ConstExprFloatNode((string)$value);
        }
        if (is_string($value)) {
            return new ConstExprStringNode("'" . addcslashes($value, "'\\") . "'");
        }
        if ($value instanceof Constant) {
            return $this->constantToAst($value->getConstant());
        }
        if ($value instanceof Code) {
            // hack for insert code as is
            return new ConstFetchNode('', $value->getCode());
        }

        throw new RectorException("Value type '" . gettype($value) . "' isn't supported");
    }

    /**
     * @param array|string|float|int|bool|null $value
     *
     * @return ConstExprNode
     * @throws RectorException
     */
    public function simpleValueOrArrayToAst($value): ConstExprNode
    {
        if (is_array($value)) {
            return $this->arrayValueToAst($value);
        }
        return $this->simpleValueToAst($value);
    }

    /**
     * @param array $array
     *
     * @return ConstExprArrayNode
     * @throws RectorException
     */
    public function arrayValueToAst(array $array): ConstExprArrayNode
    {
        $items = [];
        $index = 0;
        $checkIndex = true;
        foreach ($array as $key => $value) {
            $keyNode = null;
            if (!$checkIndex || $index !== $key) {
                $checkIndex = false;
                $keyNode = $this->simpleValueToAst($key);
            }
            $items[] = new ConstExprArrayItemNode($keyNode, $this->simpleValueOrArrayToAst($value));
            $index++;
        }
        return new ConstExprArrayNode($items);
    }

    public function constantToAst(string $constant): ConstFetchNode
    {
        if (strpos($constant, '::') !== false) {
            [$className, $constant] = explode('::', $constant);
        } else {
            $className = '';
        }
        return new ConstFetchNode($className, $constant);
    }
}
