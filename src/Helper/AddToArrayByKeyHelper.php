<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use CrmPlease\Coder\Code;
use CrmPlease\Coder\Constant;
use CrmPlease\Coder\Rector\RectorException;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use function get_class;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToArrayByKeyHelper
{
    private $convertFromAstHelper;
    private $convertToAstHelper;

    public function __construct(ConvertFromAstHelper $convertFromAstHelper, ConvertToAstHelper $convertToAstHelper)
    {
        $this->convertFromAstHelper = $convertFromAstHelper;
        $this->convertToAstHelper = $convertToAstHelper;
    }

    /**
     * @param string|int|Constant $key
     * @param string|float|int|array|Constant|Code $value
     * @param Array_ $node
     *
     * @throws RectorException
     */
    public function addToArrayByKey(
        $key,
        $value,
        Array_ $node
    ): void
    {
        if ($key instanceof Constant) {
            $keyValue = $key->getValue();
        } else {
            $keyValue = $key;
        }
        $index = 0;
        foreach ($node->items as $itemNode) {
            if (!$itemNode instanceof ArrayItem) {
                throw new RectorException("Can't get key from item, class '" . get_class($itemNode) . "' isn't supported");
            }
            $keyNode = $itemNode->key;
            if (!$keyNode) {
                $currentKey = $index;
                $index++;
            } else {
                $currentKey = $this->convertFromAstHelper->simpleValueFromAst($keyNode);
            }
            if ($currentKey instanceof Constant) {
                $currentKeyValue = $currentKey->getValue();
            } else {
                $currentKeyValue = $currentKey;
            }
            if ($currentKeyValue !== $keyValue) {
                continue;
            }
            $itemNode->value = $this->convertToAstHelper->simpleValueOrArrayToAst($value);
            return;
        }
        $node->items[] = new ArrayItem(
            $this->convertToAstHelper->simpleValueOrArrayToAst($value),
            $this->convertToAstHelper->simpleValueToAst($key)
        );
    }
}
