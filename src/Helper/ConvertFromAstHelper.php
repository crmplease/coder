<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use CrmPlease\Coder\Constant;
use CrmPlease\Coder\Rector\RectorException;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use Rector\NodeTypeResolver\Node\AttributeKey;
use function get_class;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class ConvertFromAstHelper
{
    /**
     * @param Expr $expression
     *
     * @return bool|float|int|string|null
     * @throws RectorException
     */
    public function simpleValueFromAst(Expr $expression)
    {
        if ($expression instanceof ConstFetch) {
            $name = $expression->name->toString();
            if ($name === 'null') {
                return null;
            }
            if ($name === 'true') {
                return true;
            }
            if ($name === 'false') {
                return false;
            }
            return new Constant($name);
        }
        if ($expression instanceof ClassConstFetch) {
            $className = $expression->class->toString();
            if ($className === 'self') {
                $className = $expression->getAttribute(AttributeKey::CLASS_NAME);
            }
            return new Constant("{$className}::{$expression->name->toString()}");
        }
        if ($expression instanceof LNumber) {
            return $expression->value;
        }
        if ($expression instanceof DNumber) {
            return $expression->value;
        }
        if ($expression instanceof String_) {
            return $expression->value;
        }

        throw new RectorException("Expression class '" . get_class($expression) . "' isn't supported");
    }
}
