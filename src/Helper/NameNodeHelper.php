<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use PhpParser\Node\Name;
use function explode;
use function implode;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class NameNodeHelper
{
    public function getNameByNodeName(Name $node) : string
    {
        return implode('\\', $node->parts);
    }

    public function createNodeName(string $name): Name
    {
        $parts = explode('\\', $name);
        return new Name($parts);
    }
}
