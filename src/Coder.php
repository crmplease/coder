<?php
declare(strict_types=1);

namespace Crmplease\Coder;

use Crmplease\Coder\Rector\AddCodeToMethodRector;
use Crmplease\Coder\Rector\AddParameterToMethodRector;
use Crmplease\Coder\Rector\AddPhpdocParamToMethodRector;
use Crmplease\Coder\Rector\AddPhpdocPropertyToClassRector;
use Crmplease\Coder\Rector\AddPropertyToClassRector;
use Crmplease\Coder\Rector\AddToFileReturnArrayByKeyRector;
use Crmplease\Coder\Rector\AddToFileReturnArrayByOrderRector;
use Crmplease\Coder\Rector\AddToPropertyArrayByKeyRector;
use Crmplease\Coder\Rector\AddToPropertyArrayByOrderRector;
use Crmplease\Coder\Rector\AddToReturnArrayByKeyRector;
use Crmplease\Coder\Rector\AddToReturnArrayByOrderRector;
use Crmplease\Coder\Rector\AddTraitToClassRector;
use Crmplease\Coder\Rector\ChangeClassParentRector;
use Crmplease\Coder\Rector\RectorException;
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
    private $addPhpdocPropertyToClassRector;
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
        AddPhpdocPropertyToClassRector $addPhpdocPropertyToClassRector,
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
        $this->addPhpdocPropertyToClassRector = $addPhpdocPropertyToClassRector;
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
        $this->addPhpdocPropertyToClassRector->setShowProgressBar($showProgressBar);

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
     * @param string $file
     * @param array $path
     * @param array $array
     *
     * @throws RectorException
     * @throws ShouldNotHappenException
     * @throws FileNotFoundException
     */
    public function addToFileReturnArray(string $file, array $path, array $array): void
    {
        $index = 0;
        foreach ($array as $key => $value) {
            if ($key === $index) {
                $index++;
                $this->addToFileReturnArrayByOrder(
                    $file,
                    $path,
                    $value
                );
            } else {
                $this->addToFileReturnArrayByKey(
                    $file,
                    $path,
                    $key,
                    $value
                );
            }
        }
    }

    /**
     * @param string $file
     * @param string $method
     * @param array $path
     * @param array $array
     *
     * @throws RectorException
     * @throws ShouldNotHappenException
     * @throws FileNotFoundException
     */
    public function addToReturnArray(string $file, string $method, array $path, array $array): void
    {
        $index = 0;
        foreach ($array as $key => $value) {
            if ($key === $index) {
                $index++;
                $this->addToReturnArrayByOrder(
                    $file,
                    $method,
                    $path,
                    $value
                );
            } else {
                $this->addToReturnArrayByKey(
                    $file,
                    $method,
                    $path,
                    $key,
                    $value
                );
            }
        }
    }

    /**
     * @param string $file
     * @param string $property
     * @param array $path
     * @param array $array
     *
     * @throws FileNotFoundException
     * @throws RectorException
     * @throws ShouldNotHappenException
     */
    public function addToPropertyArray(string $file, string $property, array $path, array $array): void
    {
        $index = 0;
        foreach ($array as $key => $value) {
            if ($key === $index) {
                $index++;
                $this->addToPropertyArrayByOrder(
                    $file,
                    $property,
                    $path,
                    $value
                );
            } else {
                $this->addToPropertyArrayByKey(
                    $file,
                    $property,
                    $path,
                    $key,
                    $value
                );
            }
        }
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
     * @param PhpdocProperty $property
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addPhpdocPropertyToClass(string $file, PhpdocProperty $property): void
    {
        $this->addPhpdocPropertyToClassRector
            ->setProperty($property->getName())
            ->setPropertyType($property->getType())
            ->setDescription($property->getDescription());
        $this->rectorRunner->run($file, $this->addPhpdocPropertyToClassRector);
    }

    /**
     * @param string $file
     * @param PhpdocProperty[] $properties
     *
     * @throws FileNotFoundException
     * @throws ShouldNotHappenException
     * @throws RectorException
     */
    public function addPhpdocPropertiesToClass(string $file, array $properties): void
    {
        foreach ($properties as $property) {
            $this->addPhpdocPropertyToClass(
                $file,
                $property
            );
        }
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
