<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use CrmPlease\Coder\Rector\RectorException;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use function explode;
use function gettype;
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
     * @param string|float|int $value
     *
     * @return Expr
     * @throws RectorException
     */
    public function valueToAst($value): Expr
    {
        if (is_int($value)) {
            return new LNumber($value, ['kind' => LNumber::KIND_DEC]);
        }
        if (is_float($value)) {
            return new DNumber($value);
        }
        if (is_string($value)) {
            return new String_($value, ['kind' => String_::KIND_SINGLE_QUOTED]);
        }

        throw new RectorException("Value type '" . gettype($value) . "' isn't supported");
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
