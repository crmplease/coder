<?php
declare(strict_types=1);
/**
 * @author Mougrim <rinat@mougrim.ru>
 */

namespace Crmplease\Coder;

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTO_IMPORT_NAMES, false);

    $services = $containerConfigurator
        ->services()
        ->defaults()
        ->autowire();

    $services->set(Coder::class)->public();
    $services->set(Config::class)->public();
    $services->set(RectorRunner::class);

    // rector helpers
    $services->set(Helper\AddToArrayByKeyHelper::class);
    $services->set(Helper\AddToArrayByOrderHelper::class);
    $services->set(Helper\CheckMethodHelper::class);
    $services->set(Helper\ConvertFromAstHelper::class);
    $services->set(Helper\ConvertToAstHelper::class);
    $services->set(Helper\GetPropertyPropertyHelper::class);
    $services->set(Helper\NameNodeHelper::class);
    $services->set(Helper\NodeArrayHelper::class);
    $services->set(Helper\PhpdocHelper::class);
    $services->set(Helper\ReturnStatementHelper::class);

    // rectors
    $services->set(Rector\AddCodeToMethodRector::class);
    $services->set(Rector\AddMethodToClassRector::class);
    $services->set(Rector\AddParameterToMethodRector::class);
    $services->set(Rector\AddPhpdocMethodToClassRector::class);
    $services->set(Rector\AddPhpdocParamToMethodRector::class);
    $services->set(Rector\AddPhpdocPropertyToClassRector::class);
    $services->set(Rector\AddPropertyToClassRector::class);
    $services->set(Rector\AddToFileReturnArrayByKeyRector::class);
    $services->set(Rector\AddToFileReturnArrayByOrderRector::class);
    $services->set(Rector\AddToPropertyArrayByKeyRector::class);
    $services->set(Rector\AddToPropertyArrayByOrderRector::class);
    $services->set(Rector\AddToReturnArrayByKeyRector::class);
    $services->set(Rector\AddToReturnArrayByOrderRector::class);
    $services->set(Rector\AddTraitToClassRector::class);
    $services->set(Rector\ChangeClassParentRector::class);
    $services->set(Rector\RemoveTraitFromClassRector::class);
};
