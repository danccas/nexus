<?php
namespace App\Helpers;

class Chartjs {
  private $formatData = [];
  private $width  = 500;
  private $height = 500;
  private $quick  = null;

  function __construct($width = 500, $height = 500) {
    $this->width = $width;
    $this->height = $height;
    $this->quick = new QuickChart([
      'width' => $width,
      'height' => $height
    ]);
  }
  function input($data) {
    $this->formatData = $data;
    return $this;
  }
  function size($width, $height) {
    $this->width = $width;
    $this->height = $height;
  }
  private function mprocess() {
//    echo "CONFIt:";
//    print_r(json_encode($this->formatData));
    $this->quick->setConfig(json_encode($this->formatData));
  }
  function toFile($path) {
    $this->mprocess();
    return $this->quick->toFile($path);
  }
  static function line($listado, $labels, $type = 'line') {
    $listado = array_map(function($n) { return (array) $n; }, $listado);
    $estadisticas = array();
    $_est = array();
    array_walk($labels, function(&$v, $k) { $v['collection'] = $k; });
    $default = array_map(function($n) { return 0; }, $labels);
    if(!empty($listado) && is_array($listado)) {
      foreach($listado as $m) {
        if(!isset($estadisticas[$m['eje_x']])) {
          $estadisticas[$m['eje_x']] = $default;
        }
        $estadisticas[$m['eje_x']][$m['collection']] = $m['eje_y'];
      }
    }
    foreach($estadisticas as $f => $j) {
      foreach($j as $t => $m) {
        $_est[$t][] = $m;
      }
    }
    //    dd($_est);
    $tiempos = array_keys($estadisticas);
    $labels = array_map(function($n) use ($_est) {
      return array(
        'label'           => $n['rotulo'],
        'fill'            => false,
        'backgroundColor' => $n['color'],
        'borderColor'     => $n['color'],
        'data'            => !empty($_est[$n['collection']]) ? $_est[$n['collection']] : array(),
      );
    }, $labels);
    $labels = array_values($labels);
    return array(
      'type'     => $type,
      'data'     => [
        'labels'   => $tiempos,
        'datasets' => $labels,
      ],
    );
    return $this;
  }
  function pie($listado, $labels) {
    $estadisticas = array();
    array_walk($labels, function(&$v, $k) { $v['tipo'] = $k; });
    $default = array_map(function($n) { return 0; }, $labels);
    foreach($listado as $m) {
      $estadisticas[$m['collection']] = $m['eje_y'];
    }
    $datasets = array(
      'data'            => array_values($estadisticas),
      'backgroundColor' => array_values(array_map(function($n) { return $n['color']; }, $labels)),
      'label'           => 'PIE',
    );
    $this->formatData = array(
      'type'     => 'pie',
      'labels'   => array_values(array_map(function($n) { return !empty($n['rotulo']) ? $n['rotulo'] : $n['collection']; }, $labels)),
      'datasets' => array($datasets),
    );
    return $this;
  }
}
