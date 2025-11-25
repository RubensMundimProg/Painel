<?php
/**
 * Created by PhpStorm.
 * User: bruno.rosa
 * Date: 18/07/2016
 * Time: 14:51
 */

namespace Application\Service;

/**
 * vendor/jpgraph/jpgraph.php:248 - comentado para funcionar no php versão 7
 *
 * Deprecated: Methods with the same name as their class will not be constructors in a future version of PHP;
 * jpgraph_bar.php on line 679 - \GroupBarPlot::GroupBarPlot(plots) -> changed to __construct because its deprecated on php 7
 * jpgraph_line.php on line 24 - \LinePlot::LinePlot -> changed to __construct because its deprecated on php 7
 *
 **/
require_once (BASE_PATCH.'/vendor/jpgraph/jpgraph.php');
require_once (BASE_PATCH.'/vendor/jpgraph/jpgraph_pie.php');
require_once (BASE_PATCH.'/vendor/jpgraph/jpgraph_pie3d.php');
require_once (BASE_PATCH.'/vendor/jpgraph/jpgraph_bar.php');
require_once (BASE_PATCH.'/vendor/jpgraph/jpgraph_line.php');

class Charts {

    public $path = 'public/report/charts/';
    public $type = '';
    public $value = [];
    public $name = '';
    public $subtitle = [];
    public $colors = [];


    public function __construct($type = 'Pie',$value = [],$name = 'chart', $subtitle = [],$colors = ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4', '#1E90FF','#2E8B57','#ADFF2F','#DC143C','#BA55D3'])
    {
//        ['#1E90FF','#2E8B57','#ADFF2F','#DC143C','#BA55D3']
        $this->type = $type;
        $this->value = $value;
        $this->name = $name;
        $this->subtitle = $subtitle;
        $this->colors = $colors;
    }

    public function build()
    {
        if(count($this->value) > 0){

            if($this->type == 'Pie'){
                $graph = new \PieGraph(530, 420);
                $pieGraph = new \PiePlot($this->value);

                if($this->subtitle){
                    $pieGraph->setLegends($this->subtitle);
                    $graph->legend->SetPos(0.5,0.97,'center','bottom');
                    $graph->legend->SetColumns(2);
                }
                $graph->Add($pieGraph);
                $pieGraph->SetSliceColors($this->colors);
            }

            if($this->type == 'Bar'){
                $graph = new \Graph(750,360,'auto');
//                $graph->SetScale("textlin",0,50);
                $graph->SetScale("textlin",0,max($this->value)+5);
                $graph->yaxis->SetLabelFormatCallback('number_format');

                $graph->SetBox(false);
                $graph->ygrid->SetFill(false);
                $graph->yaxis->HideLine(false);
                $graph->yaxis->HideTicks(false,false);
                $graph->xaxis->SetTickLabels($this->subtitle);
                $graph->xaxis->SetLabelAngle(15);

                $plot = new \BarPlot($this->value);
                $graph->Add($plot);

                $plot->value->Show();
                $plot->value->SetFormat('%d');
                $plot->value->SetFont(FF_ARIAL,FS_BOLD);
                $plot->value->SetColor("#222222","#444444");

            }

            if($this->type == 'Line'){
                $graph = new \Graph(750,360,'auto');
                $graph->SetScale("intlin");

                $graph->SetBox(false);
                $graph->ygrid->SetFill(false);
                $graph->yaxis->HideLine(false);
                $graph->yaxis->HideTicks(false,false);
                $graph->xaxis->SetTickLabels($this->subtitle);

                if(count($this->value) > 1){
                    $color = 0;
                    foreach ($this->value as $key => $item) {
                        $plot = new \LinePlot($item);
                        $graph->Add($plot);
                        $plot->SetColor($this->colors[$color]);
                        $color++;
                        $plot->SetLegend($key);

                        $plot->value->Show();
                        $plot->value->SetFormat('%d');
                        $plot->value->SetFont(FF_ARIAL,FS_BOLD);
                        $plot->value->SetColor("#222222","#444444");
                    }
                }else{
                    $plot = new \LinePlot($this->value);
                    $graph->Add($plot);
                    $plot->SetColor($this->colors[0]);
                    $plot->SetLegend($this->subtitle[0]);

                    $plot->value->Show();
                    $plot->value->SetFormat('%d');
                    $plot->value->SetFont(FF_ARIAL,FS_BOLD);
                    $plot->value->SetColor("#222222","#444444");
                }

                $graph->legend->SetFrameWeight(1);

            }

            @unlink($this->path . $this->name . '.png');
            $graph->Stroke($this->path . $this->name . '.png');

            return BASE_PATCH . '/' . $this->path . $this->name . '.png';

        }else{
            return '';
        }
    }

}
