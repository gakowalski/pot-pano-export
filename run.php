<?php

require 'config.php';

foreach ($targets as $format) {
  $converter = null;

  switch ($format) {
    case 'Flash Panorama Player':
      require 'flash-panorama-player.php';
      break;
    case 'Kolor Pantour 1.7.2':
      require 'kolor-pantour.php';
      break;
    default:
      die("Unsupported pano format: $converter\n");
  }
}
