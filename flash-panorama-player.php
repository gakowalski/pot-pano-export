<?php

class Flash_Panorama_Player extends Converter {
  public function convert($target) {
    global $download_directory;
    global $output_directory;

    parent::convert($target);

    $hash = md5($target);
    $target_directory = "$download_directory/$hash";
    $this->prepare_folder($target_directory);

    $common_files = array();
    $language_dependent = array();
    $target_base = '';

    foreach ($this->options['languages'] as $language) {
      $language_directory = "$target_directory/$language";
      $this->prepare_folder($language_directory);
      $this->prepare_file("$language_directory/index.html", $this->target_url($target, $language));

      $str = file_get_contents("$language_directory/index.html");
      $re = '/embedSWF\([\"\']([a-zA-Z0-9]+)\/pano\.swf[\"\']/m';
      preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
      $data_directory = $matches[0][1];

      $re = '/xml_file:\s+[\"\']([a-zA-Z0-1_]+.xml)[\"\']/m';
      preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
      $xml = $matches[0][1];

      $target_base = dirname($target) . "/$data_directory";

      $this->prepare_file("$language_directory/data.xml", $this->target_url("$target_base/$xml", $language));
      $to_download = $this->process_data("$language_directory/data.xml", $common_files, $language);

      $common_files = $to_download['common_files'];
      $language_dependent = $to_download['language_dependent'];
    }

    foreach ($common_files as $server_path => $file) {
      $this->prepare_file("$target_directory/$file", $this->target_url("$target_base/$server_path", ''));
    }

  }

  protected function process_data($path, $common_files, $language) {
    if (true === file_exists($path)) {
      $vt = new SimpleXMLElement(file_get_contents($path));
      if ($vt) {
        $to_download = array();

        foreach ($vt->hotspots->global->spot as $spot) {
          if ($spot['id'] == 'description_general') {
            echo '[.] ' . $spot['text'] . "\n";
          }
          if ($spot['id'] == 'bar-title') {
            echo '[.] ' . $spot['url'] . "\n";
          }
        }

        $parameters = parse_ini_string($vt->parameters);
        if (!isset($common_files[$parameters['panoName'].'.jpg'])) {
          $common_files[$parameters['panoName'].'.jpg'] = 'data.jpg';
        }

        $music = parse_ini_string($vt->music);
        if (!isset($common_files['mp3/' . $music['name']. '_loop_1.mp3'])) {
          $common_files['mp3/' . $music['name']. '_loop_1.mp3'] = 'music.mp3';
        }

        return array('common_files' => $common_files, 'language_dependent' => $to_download);
      } else {
        throw new Exception("Cannot process $path as SimpleXMLElement");
      }
    } else {
      throw new Exception("$path not found");
    }
  }
}
