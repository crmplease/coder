<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use CrmPlease\Coder\Rector\RectorException;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use function explode;
use function get_class;
use function implode;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class NameNodeHelper
{
    public function getNameByNodeName(Name $node) : string
    {
        return $node->toString();
    }

    public function createNodeName(string $name): Name
    {
        $parts = explode('\\', $name);
        return new Name($parts);
    }

    /**
     * @param Identifier|Name|NullableType|UnionType $node
     *
     * @return string
     * @throws RectorException
     */
    public function getNameByTypeNode($node): string
    {
        if ($node instanceof Identifier) {
            return $node->name;
        }
        if ($node instanceof Name) {
            return $node->toString();
        }
        if ($node instanceof NullableType) {
            return "?{$this->getNameByTypeNode($node->type)}";
        }
        if ($node instanceof UnionType) {
            $names = [];
            foreach ($node->types as $type) {
                $names[] = $this->getNameByTypeNode($type);
            }
            return implode('|', $names);
        }
        throw new RectorException("Unknown type node class '" . get_class($node) . "'");
    }
}
