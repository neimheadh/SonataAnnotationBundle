<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle;

use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AccessCompilerPass;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AddChildCompilerPass;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AutoRegisterCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Marko Kunic <kunicmarko20@gmail.com>
 */
final class SonataAnnotationBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AutoRegisterCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new AccessCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1);
        $container->addCompilerPass(new AddChildCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1);
    }
}
