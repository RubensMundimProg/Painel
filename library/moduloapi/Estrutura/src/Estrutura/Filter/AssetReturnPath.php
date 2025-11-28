<?php

namespace Estrutura\Filter;

use RiskManager\Organization\Service\Asset;
use Zend\Filter\AbstractFilter;

class AssetReturnPath extends AbstractFilter
{
    public function filter($id)
    {
        if(!$id)return '';
        $asset = new Asset();
        $asset->setId($id);
        $asset->load();
        $path = $asset->getPath();
        return $path;
    }
}
