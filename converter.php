<?php

use Cocur\Slugify\Slugify;

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

  public function get_title($target, $language) {
    global $translations_directory;
    global $translations_cache;

    $path = "$translations_directory/$language.xml";

    if (!isset($translations_cache[$language])) {
      if (true === file_exists($path)) {
        $xml = new SimpleXMLElement(file_get_contents($path));
        if ($xml) {
          foreach ($xml->item as $item) {
            $translations_cache[$language][] = array(
              'url' => $item->url->__toString(),
              'desc' => $item->desc->__toString(),
            );
          }
        } else {
          throw new Exception("Cannot process $path as SimpleXMLElement");
        }
      } else {
        throw new Exception("$path not found");
      }
    }

    $needle = strtr($target, array($this->options['language_token'] => $language));

    foreach ($translations_cache[$language] as $item) {
      if (false !== strstr($item['url'], $needle)) {
        return $item['desc'];
      }
    }

    return null;
  }

  public function prepare_output($target) {
    global $download_directory;
    global $output_directory;

    $title = $this->get_title($target, 'pl');
    $slugify = new Slugify();
    $slug = $slugify->slugify($title);
    $this->prepare_folder("$output_directory/$slug");

    $hash = md5($target);
    $target_directory = "$download_directory/$hash";

    $to_copy = array(
      'back.jpg',
      'front.jpg',
      'right.jpg',
      'up.jpg',
      'down.jpg',
      'left.jpg',
      'music.mp3',
    );

    foreach ($to_copy as $file) {
      $this->prepare_file("$output_directory/$slug/$file", "$target_directory/$file");
    }
  }
}
