<?php

namespace Napse\BundleMaker;

use Napse\BundleMaker\Maker\MakeCustomBundleCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BundleMaker extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->register(MakeCustomBundleCommand::class)
            ->addTag('maker.command');
    }
}
