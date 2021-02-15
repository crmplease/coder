<?php
declare(strict_types=1);

namespace Crmplease\Coder\Helper;

use Crmplease\Coder\Rector\RectorException;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
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
