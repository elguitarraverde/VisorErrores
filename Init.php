<?php

namespace FacturaScripts\Plugins\VisorErrores;

use FacturaScripts\Core\Kernel;
use FacturaScripts\Core\Template\InitClass;
use FacturaScripts\Dinamic\Controller\ApiRoot;
use FacturaScripts\Plugins\VisorErrores\Controller\ApiVisorErrores;

class Init extends InitClass
{
    public function init(): void
    {
        Kernel::addRoute('/api/3/visorerrores', ApiVisorErrores::class, -1);
        Kernel::addRoute('/api/3/visorerrores/*', ApiVisorErrores::class, -1);
        ApiRoot::addCustomResource('visorerrores');
    }

    public function update(): void
    {
    }

    public function uninstall(): void
    {
    }
}
