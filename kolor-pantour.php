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
      'mapa.png' => 'mapa.png',
    );

    foreach ($common_files as $server_path => $file) {
      $this->prepare_file("$target_directory/$file", $this->target_url("$target/$server_path", $this->options['languages'][0]));
    }

    $language_dependent = array();

    $vt = $this->process_vt("$target_directory/virtualtour.xml");
    $this->process_vt0("$target_directory/virtualtour0.xml", $target, $this->options['languages'][0]);

    $language_dependent = $language_dependent + $vt;

    foreach ($this->options['languages'] as $language) {
      $language_directory = "$target_directory/$language";
      $this->prepare_folder($language_directory);
      foreach ($language_dependent as $server_path => $file) {
        if ($language == 'pl' && $file == 'infodata.swf') {
          continue;
        }
        $this->prepare_file("$language_directory/$file", $this->target_url("$target/$server_path", $language));
      }

      if ($language == 'pl') {
        $server_path = 'virtualtourdata/graphics/spots/object0.swf';
        $this->prepare_file("$language_directory/infodata.swf", $this->target_url("$target/$server_path", $language));
      }

      $this->prepare_archive("$language_directory/infodata.swf", $language_directory);
    }
  }

  protected function process_vt($path = 'virtualtour.xml') {
    if (true === file_exists($path)) {
      $vt = new SimpleXMLElement(file_get_contents($path));
      if ($vt) {
        $to_download = array();

        foreach ($vt->plugin as $plugin) {
    			if ($plugin['name'] == 'maps') {
    				echo '[.] ' . $plugin['lat'] . ',' . $plugin['lng'] . "\n";
    				echo '[.] ' . $plugin->spot['lat'] . ',' . $plugin->spot['lng'] . "\n";
    			}
          if ($plugin['name'] == 'helpScreen') {
            $to_download[$plugin['url']->__toString()] = 'infodata.swf';
    			}
    		}

        return $to_download;
      } else {
        throw new Exception("Cannot process $path as SimpleXMLElement");
      }
    } else {
      throw new Exception("$path not found");
    }
  }

  protected function process_vt0($path = 'virtualtour0.xml', $target, $language) {
    if (true === file_exists($path)) {
      $vt0 = new SimpleXMLElement(file_get_contents($path));
      if ($vt0) {
        $image = $vt0->image;
        $tile_size = $image['tilesize'];
        echo "[.] Tile size: $tile_size\n";
        $highest_level = $image->level[0];
        echo "[.] Wall width: ". $highest_level['tiledimagewidth'] . "\n";
        echo "[.] Wall height: ". $highest_level['tiledimageheight'] . "\n";

        $left = $highest_level->left['url'];
        $right = $highest_level->right['url'];
        $up = $highest_level->up['url'];
        $down = $highest_level->down['url'];
        $front = $highest_level->front['url'];
        $back = $highest_level->back['url'];

        $left = $this->horizontal_merge_list($left, 'left', dirname($path), $target, $language);
        $right = $this->horizontal_merge_list($right, 'right', dirname($path), $target, $language);
        $up = $this->horizontal_merge_list($up, 'up', dirname($path), $target, $language);
        $down = $this->horizontal_merge_list($down, 'down', dirname($path), $target, $language);
        $front = $this->horizontal_merge_list($front, 'front', dirname($path), $target, $language);
        $back = $this->horizontal_merge_list($back, 'back', dirname($path), $target, $language);
      } else {
        throw new Exception("Cannot process $path as SimpleXMLElement");
      }
    } else {
      throw new Exception("$path not found");
    }
  }

  protected	function horizontal_merge_list($pattern, $position = 'tile', $target_directory, $target, $language) {
		global $irfan_view;

		for ($v = 0; $v < 4; $v++) {
			echo "[$position] Merge horizontal \n";
			$files = array();
			for ($u = 0; $u < 4; $u++) {
        $path = strtr($pattern, array('%v' => $v, '%u' => $u));
        $this->prepare_folder("$target_directory/$position");
        $this->prepare_file("$target_directory/$position/{$v}_$u.jpg", $this->target_url("$target/$path", $language));
				$path = realpath("$target_directory/$position/{$v}_$u.jpg");
				$files[] = $path;
			}
			$result = str_replace('_0', '_merged', $files[0]);
			$files = implode(',', $files);
			if (false === file_exists($result)) {
				system("$irfan_view /panorama=(1,$files) /convert=$result");
			} else {
				echo "[$position] Already merged\n";
			}
		}

		echo "[$position] Merge vertical \n";
		$files = array();
		for ($v = 0; $v < 4; $v++) {
			$path = realpath("$target_directory/$position/{$v}_merged.jpg");
			$files[] = $path;
		}
		$result = realpath($target_directory) . "\\$position.jpg";
		$files = implode(',', $files);

		if (false === file_exists($result)) {
			system("$irfan_view /panorama=(2,$files) /convert=$result");
		} else {
			echo "[$position] Already merged\n";
		}

		return $result;
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
        system($decompile_command);
      } else {
        echo "$path already decompressed\n";
      }
    } else {
      throw new Exception("$path not found");
    }
  }
}
