<?php

class Converter {
  protected $options;

  public function __construct($options) {
    $this->options = $options;
  }

  public function convert($target) {
    echo "Converting $target\n";
  }

  protected function prepare_folder($path) {
    if (false === file_exists($path)) {
      echo "$path does not exist, creating...\n";
      mkdir($path);
      if (false === file_exists($path)) {
        throw new Exception("Couldn't create folder $path");
      }
    } else {
      echo "$path already exists\n";
    }
  }

  protected function prepare_file($path, $url) {
    if (false === file_exists($path)) {
      echo "$path does not exist, downloading...\n";
      file_put_contents($path, file_get_contents($url));
      if (false === file_exists($path)) {
        throw new Exception("Couldn't write from $url to $path");
      }
    } else {
      echo "$path already exists\n";
    }
  }

  public function target_url($target, $language) {
    $url = "{$this->options['target_base']}$target";
    return strtr($url, array($this->options['language_token'] => $language));
  }
}
