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

    $this->prepare_archive("$target_directory/object0.swf", $target_directory);

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

  protected function archive_directory($path) {
    return md5($path);
  }

  protected function prepare_archive($path, $destination) {
    global $java;
    global $decompiler;
    if (true === file_exists($path)) {
      $uncompressed = "$destination/" . $this->archive_directory($path);
      if (false === file_exists($uncompressed)) {
        echo "Decompressing $path...\n";
        $decompile_command = "$java -jar $decompiler -cli -export sound,text $uncompressed $path";
        //var_dump($decompile_command);
        system($decompile_command);
      } else {
        echo "$path already decompressed\n";
      }
    } else {
      throw new Exception("$path not found");
    }
  }
}
