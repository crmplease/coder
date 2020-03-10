<?php
declare(strict_types=1);

namespace Crmplease\Coder\Helper;

use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocNode;
use Rector\CodingStyle\Application\UseAddingCommander;
use Rector\Core\Configuration\Option;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\PHPStan\Type\FullyQualifiedObjectType;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use function count;
use function explode;
use function implode;
use function ltrim;
use function strpos;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class PhpdocHelper
{
    private $parameterProvider;
    private $useAddingCommander;
    private $phpDocParser;
    private $lexer;

    public function __construct(
        ParameterProvider $parameterProvider,
        UseAddingCommander $useAddingCommander,
        PhpDocParser $phpDocParser,
        Lexer $lexer
    )
    {
        $this->parameterProvider = $parameterProvider;
        $this->useAddingCommander = $useAddingCommander;
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
    }

    /**
     * @param string $unionType
     * @param Node $node
     *
     * @return string
     * @throws ShouldNotHappenException
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

            $this->useAddingCommander->addUseImport($node, new FullyQualifiedObjectType(ltrim($type, '\\')));
            $parts = explode('\\', $type);
            $type = $parts[count($parts) - 1];
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
        $phpDocNode = $this->phpDocParser ->parse($tokenIterator);
        $varTagValue = $phpDocNode->getVarTagValues()[0] ?? null;
        if ($varTagValue === null) {
            return new IdentifierTypeNode('mixed');
        }
        return $varTagValue->type;
    }
}
