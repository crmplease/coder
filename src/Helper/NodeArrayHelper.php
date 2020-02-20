<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use CrmPlease\Coder\Constant;
use CrmPlease\Coder\Rector\RectorException;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Return_;
use function array_shift;
use function get_class;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class NodeArrayHelper
{
    private $convertFromAstHelper;
    private $convertToAstHelper;

    public function __construct(ConvertFromAstHelper $convertFromAstHelper, ConvertToAstHelper $convertToAstHelper)
    {
        $this->convertFromAstHelper = $convertFromAstHelper;
        $this->convertToAstHelper = $convertToAstHelper;
    }
    /**
     * @param Return_ $node
     *
     * @return Array_
     * @throws RectorException
     */
    public function getFromReturnStatement(Return_ $node): Array_
    {
        $nodeArray = $node->expr;
        if (!$nodeArray) {
            throw new RectorException('Return statement is without value');
        }

        if (!$nodeArray instanceof Array_) {
            throw new RectorException("Return value isn't array, node class: " . get_class($nodeArray));
        }
        return $nodeArray;
    }

    /**
     * @param PropertyProperty $node
     *
     * @return Array_
     * @throws RectorException
     */
    public function getFromPropertyPropertyStatement(PropertyProperty $node): Array_
    {
        $defaultNode = $node->default;

        if (!$defaultNode) {
            throw new RectorException("Property doesn't have default value");
        }


        if (!$defaultNode instanceof Array_) {
            throw new RectorException("Property isn't array, node class: " . get_class($defaultNode));
        }
        return $defaultNode;
    }

    /**
     * @param array $path
     * @param Array_ $node
     *
     * @return Array_
     * @throws RectorException
     */
    public function findOrAddArrayByPath(array $path, Array_ $node): Array_
    {
        if (!$path) {
            return $node;
        }
        foreach ($node->items as $itemNodeNumber => $itemNode) {
            if (!$itemNode instanceof ArrayItem) {
                throw new RectorException("Can't get key from item, class '".get_class($itemNode)."' isn't supported");
            }
            $keyNode = $itemNode->key;
            if (!$keyNode) {
                continue;
            }
            $part = $path[0];
            if ($part instanceof Constant) {
                $part = $part->getValue();
            }
            $key = $this->convertFromAstHelper->simpleValueFromAst($keyNode);
            if ($key instanceof Constant) {
                $key = $key->getValue();
            }
            if ($part !== $key) {
                continue;
            }
            /** @var Array_ $valueNode */
            $valueNode = $itemNode->value;
            if (!$valueNode instanceof Array_) {
                $valueNode = new Array_();
                $itemNode->value = $valueNode;
            }
            array_shift($path);
            return $this->findOrAddArrayByPath($path, $valueNode);
        }
        $part = $path[0];
        $result = new Array_();
        $keyNode = $this->convertToAstHelper->simpleValueToAst($part);
        $arrayItemNode = new ArrayItem(
            $result,
            $keyNode
        );
        $node->items[] = $arrayItemNode;
        array_shift($path);
        return $this->findOrAddArrayByPath($path, $result);
    }
}
