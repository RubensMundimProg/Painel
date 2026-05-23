<?php

namespace Application\Service;

use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\RendererInterface;

class PdfGenerator
{
    private $viewRenderer;
    private $dompdfFactory;

    public function __construct(RendererInterface $viewRenderer, callable $dompdfFactory)
    {
        $this->viewRenderer = $viewRenderer;
        $this->dompdfFactory = $dompdfFactory;
    }

    public function renderTemplate($template, array $variables, array $options = array())
    {
        $model = new ViewModel($variables);
        $model->setTemplate($template);
        $model->setTerminal(true);

        $html = $this->viewRenderer->render($model);

        $dompdf = call_user_func($this->dompdfFactory);
        $dompdf->setPaper(
            isset($options['paperSize']) ? $options['paperSize'] : 'A4',
            isset($options['paperOrientation']) ? $options['paperOrientation'] : 'portrait'
        );

        if (!empty($options['basePath'])) {
            $dompdf->setBasePath($options['basePath']);
        }

        $dompdf->loadHtml($html, isset($options['encoding']) ? $options['encoding'] : 'UTF-8');
        $dompdf->render();

        return $dompdf->output();
    }
}
