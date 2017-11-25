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
      $contents = file_get_contents($url);
      if ($contents === false) {
        throw new Exception("Couldn't read from $url");
      }
      $bytes_written = file_put_contents($path, $contents);
      if (false === $bytes_written || false === file_exists($path)) {
        throw new Exception("Couldn't write from $url to $path");
      }
      if (0 === $bytes_written) {
        throw new Exception("Zero bytes downloaded from $url to $path");
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
