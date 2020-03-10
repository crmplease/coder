<?php
declare(strict_types=1);

namespace Crmplease\Coder\Helper;

use Crmplease\Coder\Code;
use Crmplease\Coder\Constant;
use Crmplease\Coder\Rector\RectorException;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use function explode;
use function gettype;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function strpos;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class ConvertToAstHelper
{
    private $nameNodeHelper;

    public function __construct(NameNodeHelper $nameNodeHelper)
    {
        $this->nameNodeHelper = $nameNodeHelper;
    }

    /**
     * @param Code|Constant|string|float|int|bool|null $value
     *
     * @return Expr
     * @throws RectorException
     */
    public function simpleValueToAst($value): Expr
    {
        if ($value === null) {
            return new ConstFetch(new Name(['null']));
        }
        if (is_bool($value)) {
            if ($value) {
                return new ConstFetch(new Name(['true']));
            }
            return new ConstFetch(new Name(['false']));
        }
        if (is_int($value)) {
            return new LNumber($value, ['kind' => LNumber::KIND_DEC]);
        }
        if (is_float($value)) {
            return new DNumber($value);
        }
        if (is_string($value)) {
            return new String_($value, ['kind' => String_::KIND_SINGLE_QUOTED]);
        }
        if ($value instanceof Constant) {
            return $this->constantToAst($value->getConstant());
        }
        if ($value instanceof Code) {
            // hack for insert code as is
            return new ConstFetch(new Name([$value->getCode()]));
        }

        throw new RectorException("Value type '" . gettype($value) . "' isn't supported");
    }

    /**
     * @param array|Code|Constant|string|float|int|bool|null $value
     *
     * @return Expr
     * @throws RectorException
     */
    public function simpleValueOrArrayToAst($value): Expr
    {
        if (is_array($value)) {
            return $this->arrayValueToAst($value);
        }
        return $this->simpleValueToAst($value);
    }

    /**
     * @param array $array
     *
     * @return Array_
     * @throws RectorException
     */
    public function arrayValueToAst(array $array): Array_
    {
        $node = new Array_();
        $index = 0;
        $checkIndex = true;
        foreach ($array as $key => $value) {
            $keyNode = null;
            if (!$checkIndex || $index !== $key) {
                $checkIndex = false;
                $keyNode = $this->simpleValueToAst($key);
            }
            $node->items[] = new ArrayItem($this->simpleValueOrArrayToAst($value), $keyNode);
            $index++;
        }
        return $node;
    }

    public function constantToAst(string $constant): Expr
    {
        if (strpos($constant, '::') !== false) {
            [$className, $constant] = explode('::', $constant);
            return new ClassConstFetch($this->nameNodeHelper->createNodeName($className), new Identifier($constant));
        }

        return new ConstFetch($this->nameNodeHelper->createNodeName($constant));
    }
}
