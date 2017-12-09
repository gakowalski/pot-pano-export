<?php

require 'config.php';
require 'vendor/autoload.php';
require 'converter.php';

foreach ($targets as $format => $targets) {
  $converter = null;

  switch ($format) {
    case 'Flash Panorama Player':
      require 'flash-panorama-player.php';
      $converter = new Flash_Panorama_Player($options[$format]);
      break;
    case 'Kolor Pantour 1.7.2':
      require 'kolor-pantour.php';
      $converter = new Kolor_Pantour($options[$format]);
      break;
    default:
      die("Unsupported pano format: $converter\n");
  }

  foreach ($targets as $target) {
    $converter->convert($target);
    $converter->prepare_output($target);
  }
}
