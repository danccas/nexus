<?php

namespace App\Traits;

trait hasFillable {
  private $denyFillable = [];
  function __validData($row = null) {
    if($this->exists) {
      if(method_exists($this, 'dinamicFillable')) {
        $cc = $this->dinamicFillable();
        if(is_array($cc)) {
          foreach($cc as $k => $v) {
            if(!$v) {
              $this->__denyFill($k);
            }
          }
        }
      }
    }
    $this->__options = [
      'denyFill' => $this->denyFillable,
    ];
    return $this;
  }
  private function __denyFill($column) {
    $todos = $this->getFillable();
    if (($key = array_search($column, $todos)) !== false) {
      $this->denyFillable[] = $column;
      unset($todos[$key]);
    }
    $todos = array_values($todos);
    $this->fillable($todos);
    return $this;
  }
}
