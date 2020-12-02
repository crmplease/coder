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
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
};
