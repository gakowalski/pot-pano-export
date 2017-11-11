<?php

class Converter {
  public $options;

  public function __construct($options) {
    $this->options = $options;
  }

  public function convert($target) {
    echo "Converting $target\n";
  }
}
