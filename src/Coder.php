<?php
declare(strict_types=1);

namespace CrmPlease\Coder;

use CrmPlease\Coder\Rector\AddCodeToMethodRector;
use CrmPlease\Coder\Rector\AddParameterToMethodRector;
use CrmPlease\Coder\Rector\AddPhpdocParamToMethodRector;
use CrmPlease\Coder\Rector\AddPropertyToClassRector;
use CrmPlease\Coder\Rector\AddToFileReturnArrayByKeyRector;
use CrmPlease\Coder\Rector\AddToFileReturnArrayByOrderRector;
use CrmPlease\Coder\Rector\AddToPropertyArrayByKeyRector;
use CrmPlease\Coder\Rector\AddToPropertyArrayByOrderRector;
use CrmPlease\Coder\Rector\AddToReturnArrayByKeyRector;
use CrmPlease\Coder\Rector\AddToReturnArrayByOrderRector;
use CrmPlease\Coder\Rector\AddTraitToClassRector;
use CrmPlease\Coder\Rector\ChangeClassParentRector;
use CrmPlease\Coder\Rector\RectorException;
use Rector\Core\Exception\ShouldNotHappenException;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class Coder
{
    private $rectorRunner;
    private $addToFileReturnArrayByOrderRector;
    private $addToReturnArrayByOrderRector;
    private $addToPropertyArrayByOrderRector;
    private $addToFileReturnArrayByKeyRector;
    private $addToReturnArrayByKeyRector;
    private $addToPropertyArrayByKeyRector;
    private $addPropertyToClassRector;
    private $addParameterToMethodRector;
    private $addCodeToMethodRector;
    private $addTraitToClassRector;
    private $addPhpdocParamToMethodRector;
    private $changeClassParentRector;

    /**
     * @return static
     * @throws FileNotFoundException
     */
    public static function create(): self
    {
        $containerConfigurator = new RectorContainerConfigurator();
        $container = $containerConfigurator->configureContainer();
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $container->get(__CLASS__);
    }

    public function __construct(
        RectorRunner $rectorRunner,
        AddToFileReturnArrayByOrderRector $addToFileReturnArrayByOrderRector,
        AddToReturnArrayByOrderRector $addToReturnArrayByOrderRector,
        AddToPropertyArrayByOrderRector $addToPropertyArrayByOrderRector,
        AddToFileReturnArrayByKeyRector $addToFileReturnArrayByKeyRector,
        AddToReturnArrayByKeyRector $addToReturnArrayByKeyRector,
        AddToPropertyArrayByKeyRector $addToPropertyArrayByKeyRector,
        AddPropertyToClassRector $addPropertyToClassRector,
        AddParameterToMethodRector $addParameterToMethodRector,
        AddCodeToMethodRector $addCodeToMethodRector,
        AddTraitToClassRector $addTraitToClassRector,
        AddPhpdocParamToMethodRector $addPhpdocParamToMethodRector,
        ChangeClassParentRector $changeClassParentRector
    )
    {
        $this->rectorRunner = $rectorRunner;
        $this->addToFileReturnArrayByOrderRector = $addToFileReturnArrayByOrderRector;
        $this->addToReturnArrayByOrderRector = $addToReturnArrayByOrderRector;
        $this->addToPropertyArrayByOrderRector = $addToPropertyArrayByOrderRector;
        $this->addToFileReturnArrayByKeyRector = $addToFileReturnArrayByKeyRector;
        $this->addToReturnArrayByKeyRector = $addToReturnArrayByKeyRector;
        $this->addToPropertyArrayByKeyRector = $addToPropertyArrayByKeyRector;
        $this->addPropertyToClassRector = $addPropertyToClassRector;
        $this->addParameterToMethodRector = $addParameterToMethodRector;
        $this->addCodeToMethodRector = $addCodeToMethodRector;
        $this->addTraitToClassRector = $addTraitToClassRector;
        $this->addPhpdocParamToMethodRector = $addPhpdocParamToMethodRector;
        $this->changeClassParentRector = $changeClassParentRector;
    }

    /**
     * @param bool $showProgressBar
     *
     * @return $this
     */
    public function setShowProgressBar(bool $showProgressBar): self
    {
        $this->rectorRunner->setShowProgressBar($showProgressBar);

        return $this;
    }

    /**
     * @param string $file
     * @param string[]|int[]|Constant[] $path
     * @param string|float|int|array|Constant|Code $value
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addToFileReturnArrayByOrder(string $file, array $path, $value): void
    {
        $this->addToFileReturnArrayByOrderRector
            ->setPath($path)
            ->setValue($value);
        $this->rectorRunner->run($file, $this->addToFileReturnArrayByOrderRector);
    }

    /**
     * @param string $file
     * @param string $method
     * @param string[]|int[]|Constant[] $path
     * @param string|float|int|array|Constant|Code $value
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addToReturnArrayByOrder(string $file, string $method, array $path, $value): void
    {
        $this->addToReturnArrayByOrderRector
            ->setMethod($method)
            ->setPath($path)
            ->setValue($value);
        $this->rectorRunner->run($file, $this->addToReturnArrayByOrderRector);
    }

    /**
     * @param string $file
     * @param string $property
     * @param string[]|int[]|Constant[] $path
     * @param string|float|int|array|Constant|Code $value
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addToPropertyArrayByOrder(string $file, string $property, array $path, $value): void
    {
        $this->addToPropertyArrayByOrderRector
            ->setProperty($property)
            ->setPath($path)
            ->setValue($value);
        $this->rectorRunner->run($file, $this->addToPropertyArrayByOrderRector);
    }

    /**
     * @param string $file
     * @param string[]|int[]|Constant[] $path
     * @param string|int|Constant $key
     * @param string|float|int|array|Constant|Code $value
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addToFileReturnArrayByKey(string $file, array $path, $key, $value): void
    {
        $this->addToFileReturnArrayByKeyRector
            ->setPath($path)
            ->setKey($key)
            ->setValue($value);
        $this->rectorRunner->run($file, $this->addToFileReturnArrayByKeyRector);
    }

    /**
     * @param string $file
     * @param string $method
     * @param string[]|int[]|Constant[] $path
     * @param string|int|Constant $key
     * @param string|float|int|array|Constant|Code $value
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addToReturnArrayByKey(string $file, string $method, array $path, $key, $value): void
    {
        $this->addToReturnArrayByKeyRector
            ->setMethod($method)
            ->setPath($path)
            ->setKey($key)
            ->setValue($value);
        $this->rectorRunner->run($file, $this->addToReturnArrayByKeyRector);
    }

    /**
     * @param string $file
     * @param string $property
     * @param string[]|int[]|Constant[] $path
     * @param string|int|Constant $key
     * @param string|float|int|array|Constant|Code $value
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addToPropertyArrayByKey(string $file, string $property, array $path, $key, $value): void
    {
        $this->addToPropertyArrayByKeyRector
            ->setProperty($property)
            ->setPath($path)
            ->setKey($key)
            ->setValue($value);
        $this->rectorRunner->run($file, $this->addToPropertyArrayByKeyRector);
    }

    /**
     * @see AddPropertyToClassRector::VISIBILITIES_TO_AST_FLAGS
     *
     * @param string $file
     * @param string $property
     * @param bool $isStatic
     * @param string $visibility AddPropertyToClassRector::VISIBILITY_*
     * @param string|float|int|array|Constant|Code|null $value default value for property, skip it or pass null if isn't needed
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addPropertyToClass(
        string $file,
        string $property,
        bool $isStatic,
        string $visibility,
        $value = null
    ): void
    {
        $this->addPropertyToClassRector
            ->setProperty($property)
            ->setIsStatic($isStatic)
            ->setVisibility($visibility)
            ->setValue($value);
        $this->rectorRunner->run($file, $this->addPropertyToClassRector);
    }

    /**
     * @param string $file
     * @param string $method
     * @param string $parameter
     * @param string $parameterType
     * @param bool $hasValue
     * @param string|float|int|array|Constant|Code|null $value
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addParameterToMethod(
        string $file,
        string $method,
        string $parameter,
        string $parameterType,
        bool $hasValue = false,
        $value = null
    ): void
    {
        $this->addParameterToMethodRector
            ->setMethod($method)
            ->setParameter($parameter)
            ->setParameterType($parameterType)
            ->setHasValue($hasValue)
            ->setValue($value);
        $this->rectorRunner->run($file, $this->addParameterToMethodRector);
    }

    /**
     * @param string $file
     * @param string $method
     * @param string $code
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addCodeToMethod(string $file, string $method, string $code): void
    {
        $this->addCodeToMethodRector
            ->setMethod($method)
            ->setCode($code);
        $this->rectorRunner->run($file, $this->addCodeToMethodRector);
    }

    /**
     * @param string $file
     * @param string $trait
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addTraitToClass(string $file, string $trait): void
    {
        $this->addTraitToClassRector
            ->setTrait($trait);
        $this->rectorRunner->run($file, $this->addTraitToClassRector);
    }

    /**
     * @param string $file
     * @param string $method
     * @param string $parameter
     * @param string $parameterType
     * @param string $description
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addPhpdocParamToMethod(
        string $file,
        string $method,
        string $parameter,
        string $parameterType,
        string $description = ''
    ): void
    {
        $this->addPhpdocParamToMethodRector
            ->setMethod($method)
            ->setParameter($parameter)
            ->setParameterType($parameterType)
            ->setDescription($description);
        $this->rectorRunner->run($file, $this->addPhpdocParamToMethodRector);
    }

    /**
     * @param string $file
     * @param string $parentClass
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function changeClassParent(string $file, string $parentClass): void
    {
        $this->changeClassParentRector
            ->setParentClass($parentClass);
        $this->rectorRunner->run($file, $this->changeClassParentRector);
    }
}
