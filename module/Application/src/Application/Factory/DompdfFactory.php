<?php

namespace Application\Factory;

use Dompdf\Dompdf;
use Dompdf\Options;

class DompdfFactory
{
    public function __invoke($container, $requestedName = null, $options = null)
    {
        $config = $container->get('config');
        $dompdfConfig = isset($config['dompdf']) ? $config['dompdf'] : array();

        $this->ensureDirectory($dompdfConfig, 'temp_dir');
        $this->ensureDirectory($dompdfConfig, 'font_dir');
        $this->ensureDirectory($dompdfConfig, 'font_cache');

        $options = new Options();
        $options->setTempDir($this->option($dompdfConfig, 'temp_dir', sys_get_temp_dir()));
        $options->setFontDir($this->option($dompdfConfig, 'font_dir', null));
        $options->setFontCache($this->option($dompdfConfig, 'font_cache', null));
        $options->setChroot($this->option($dompdfConfig, 'chroot', BASE_PATCH));
        $options->setDefaultPaperSize($this->option($dompdfConfig, 'default_paper_size', 'A4'));
        $options->setDefaultFont($this->option($dompdfConfig, 'default_font', 'Arial'));
        $options->setDpi((int) $this->option($dompdfConfig, 'dpi', 96));
        $options->setIsRemoteEnabled((bool) $this->option($dompdfConfig, 'is_remote_enabled', true));
        $options->setIsHtml5ParserEnabled((bool) $this->option($dompdfConfig, 'is_html5_parser_enabled', true));
        $options->setIsFontSubsettingEnabled((bool) $this->option($dompdfConfig, 'is_font_subsetting_enabled', false));

        return new Dompdf($options);
    }

    public function createService($serviceLocator)
    {
        return $this($serviceLocator);
    }

    private function option(array $config, $key, $default)
    {
        return array_key_exists($key, $config) ? $config[$key] : $default;
    }

    private function ensureDirectory(array $config, $key)
    {
        if (empty($config[$key]) || is_dir($config[$key])) {
            return;
        }

        mkdir($config[$key], 0777, true);
    }
}
