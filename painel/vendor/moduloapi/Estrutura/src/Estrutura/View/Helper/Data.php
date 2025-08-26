<?php
namespace Estrutura\View\Helper;
use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public function __invoke($data)
    {
		$data = date_create_from_format("Y-m-d", $data);
        return $data->format('d/m/Y');
    }
}