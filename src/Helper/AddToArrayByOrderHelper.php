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
class AddToArrayByOrderHelper
{
    private $convertFromAstHelper;
    private $convertToAstHelper;

    public function __construct(
        ConvertFromAstHelper $convertFromAstHelper,
        ConvertToAstHelper $convertToAstHelper
    )
    {
        $this->convertFromAstHelper = $convertFromAstHelper;
        $this->convertToAstHelper = $convertToAstHelper;
    }

    /**
     * @param string|float|int|array|Constant|Code $value
     * @param Array_ $node
     *
     * @throws RectorException
     */
    public function addToArrayByOrder($value, Array_ $node): void
    {
        if ($value instanceof Constant) {
            $realValue = $value->getValue();
        } else {
            $realValue = $value;
        }
        foreach ($node->items as $itemNode) {
            if (!$itemNode instanceof ArrayItem) {
                throw new RectorException("Can't get value from item, class '" . get_class($itemNode) . "' isn't supported");
            }
            $valueNode = $itemNode->value;
            try {
                $currentValue = $this->convertFromAstHelper->simpleValueFromAst($valueNode);
            } catch (RectorException $exception) {
                // ignore values, which not simple, don't check duplicates for them
                continue;
            }
            if ($currentValue instanceof Constant) {
                $currentValue = $currentValue->getValue();
            }
            if ($currentValue === $realValue) {
                return;
            }
        }

        $node->items[] = new ArrayItem($this->convertToAstHelper->simpleValueOrArrayToAst($value));
    }
}
