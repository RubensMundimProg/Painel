<?php

namespace Application\Factory;

use Application\Service\PdfGenerator;
use Dompdf\Dompdf;

class PdfGeneratorFactory
{
    public function __invoke($container, $requestedName = null, $options = null)
    {
        return new PdfGenerator(
            $container->get('ViewRenderer'),
            function () use ($container) {
                return $container->get(Dompdf::class);
            }
        );
    }

    public function createService($serviceLocator)
    {
        return $this($serviceLocator);
    }
}
