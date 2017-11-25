<?php

class Kolor_Pantour extends Converter {
  public function convert($target) {
    global $download_directory;
    global $output_directory;

    parent::convert($target);

    $hash = md5($target);
    $target_directory = "$download_directory/$hash";
    $this->prepare_folder($target_directory);

    $common_files = array(
      'virtualtour.xml' => 'virtualtour.xml',
      'virtualtour0.xml' => 'virtualtour0.xml',
      'virtualtourdata/sounds/sound0.mp3' => 'sound0.mp3',
      'virtualtourdata/graphics/spots/object0.swf' => 'object0.swf',
      'mapa.png' => 'mapa.png',
    );

    foreach ($common_files as $server_path => $file) {
      $this->prepare_file("$target_directory/$file", $this->target_url("$target/$server_path", $this->options['languages'][0]));
    }

    $language_dependent = array(
      
    );

    foreach ($this->options['languages'] as $language) {
      $language_directory = "$target_directory/$language";
      $this->prepare_folder($language_directory);
      foreach ($language_dependent as $server_path => $file) {
        $this->prepare_file("$language_directory/$file", $this->target_url("$target/$server_path", $language));
      }
    }
  }
}
