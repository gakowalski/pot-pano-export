<?php

class Flash_Panorama_Player extends Converter {
  public function convert($target) {
    global $download_directory;
    global $output_directory;

    parent::convert($target);

    $hash = md5($target);
    $target_directory = "$download_directory/$hash";
    $this->prepare_folder($target_directory);

    foreach ($this->options['languages'] as $language) {
      $language_directory = "$target_directory/$language";
      $this->prepare_folder($language_directory);
      $this->prepare_file("$language_directory/index.html", $this->target_url($target, $language));
    }
  }
}
