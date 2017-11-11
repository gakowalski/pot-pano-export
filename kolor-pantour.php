<?php

class Kolor_Pantour extends Converter {
  public function convert($target) {
    global $download_directory;
    global $output_directory;

    $hash = md5($target);
    $target_directory = "$download_directory/$hash";

    if (false === file_exists($target_directory)) {
      echo "$target_directory does not exist, creating...\n";
      mkdir($target_directory);
    } else {
      echo "$target_directory already exists\n";
    }
  }
}
