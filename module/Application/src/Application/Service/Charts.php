<?php

namespace Application\Service;

class Charts
{
    public $path = 'public/report/charts/';
    public $type = '';
    public $value = array();
    public $name = '';
    public $subtitle = array();
    public $colors = array();

    public function __construct(
        $type = 'Pie',
        $value = array(),
        $name = 'chart',
        $subtitle = array(),
        $colors = array('#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4', '#1E90FF', '#2E8B57', '#ADFF2F', '#DC143C', '#BA55D3')
    ) {
        $this->type = $type;
        $this->value = array_values((array) $value);
        $this->name = $name;
        $this->subtitle = array_values((array) $subtitle);
        $this->colors = $colors;
    }

    public function build()
    {
        if (count($this->value) === 0) {
            return '';
        }

        if (!extension_loaded('gd')) {
            throw new \RuntimeException('A extensao GD do PHP e necessaria para gerar os graficos do relatorio.');
        }

        $directory = BASE_PATCH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $this->path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->name) . '.png';
        $filePath = $directory . $fileName;

        $image = $this->createBarChart();
        imagepng($image, $filePath);
        imagedestroy($image);

        return BASE_PATCH . '/' . $this->path . $fileName;
    }

    private function createBarChart()
    {
        $width = 900;
        $height = 430;
        $marginLeft = 70;
        $marginRight = 25;
        $marginTop = 30;
        $marginBottom = 95;
        $plotWidth = $width - $marginLeft - $marginRight;
        $plotHeight = $height - $marginTop - $marginBottom;

        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $white = imagecolorallocate($image, 255, 255, 255);
        $axis = imagecolorallocate($image, 60, 60, 60);
        $grid = imagecolorallocate($image, 225, 230, 235);
        $text = imagecolorallocate($image, 35, 35, 35);
        $barColor = imagecolorallocate($image, 79, 129, 189);
        $barBorder = imagecolorallocate($image, 52, 86, 126);

        imagefill($image, 0, 0, $white);

        $maxValue = max(array_map('intval', $this->value));
        $maxValue = max(1, $maxValue);
        $yMax = $this->roundUpScale($maxValue);

        for ($i = 0; $i <= 5; $i++) {
            $y = (int) round($marginTop + $plotHeight - ($plotHeight * $i / 5));
            imageline($image, $marginLeft, $y, $width - $marginRight, $y, $grid);
            $label = (string) (int) round($yMax * $i / 5);
            imagestring($image, 3, 8, $y - 7, $label, $text);
        }

        imageline($image, $marginLeft, $marginTop, $marginLeft, $marginTop + $plotHeight, $axis);
        imageline($image, $marginLeft, $marginTop + $plotHeight, $width - $marginRight, $marginTop + $plotHeight, $axis);

        $count = count($this->value);
        $slotWidth = $plotWidth / max(1, $count);
        $barWidth = max(10, min(45, (int) floor($slotWidth * 0.55)));

        foreach ($this->value as $index => $rawValue) {
            $value = max(0, (int) $rawValue);
            $xCenter = (int) round($marginLeft + ($slotWidth * $index) + ($slotWidth / 2));
            $x1 = $xCenter - (int) floor($barWidth / 2);
            $x2 = $xCenter + (int) floor($barWidth / 2);
            $barHeight = (int) round($plotHeight * ($value / $yMax));
            $y1 = $marginTop + $plotHeight - $barHeight;
            $y2 = $marginTop + $plotHeight;

            imagefilledrectangle($image, $x1, $y1, $x2, $y2, $barColor);
            imagerectangle($image, $x1, $y1, $x2, $y2, $barBorder);
            imagestring($image, 3, $xCenter - (strlen((string) $value) * 3), max(5, $y1 - 16), (string) $value, $text);

            $label = isset($this->subtitle[$index]) ? $this->normalizeLabel($this->subtitle[$index]) : '';
            $label = $this->wrapLabel($label, 16);
            $labelY = $marginTop + $plotHeight + 10;
            foreach ($label as $line) {
                imagestringup($image, 2, $xCenter - 4, $labelY + 70, $line, $text);
                $labelY += 8;
                break;
            }
        }

        return $image;
    }

    private function roundUpScale($value)
    {
        $value = max(1, (int) $value);
        $magnitude = pow(10, max(0, strlen((string) $value) - 1));
        return (int) (ceil($value / $magnitude) * $magnitude);
    }

    private function normalizeLabel($label)
    {
        $label = (string) $label;
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $label);
            if ($converted !== false) {
                return $converted;
            }
        }

        return $label;
    }

    private function wrapLabel($label, $length)
    {
        $label = trim($label);
        if ($label === '') {
            return array('');
        }

        return explode("\n", wordwrap($label, $length, "\n", true));
    }
}
