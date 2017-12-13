<?php

/* GENERAL OPTIONS */

$download_directory = 'download';
$output_directory = 'output';
$translations_directory = 'translations';
$translations_cache = array();
$translations_cache2 = array();

/* EXTERNAL TOOLS */

/* required for both Flash Panorama Player and Kolor Pantour panoramas */
$irfan_view = "C:/\"Program Files\"/IrfanView/i_view64.exe";

/* required to process Kolor Pantour panoramas */
$java = "C:/\"Program Files\"/Java/jdk1.8.0_131/bin/java";
$decompiler = "f:/\"Program Files (x86)\"/FFDec/ffdec.jar";
$record_separator = "\r\n--- RECORDSEPARATOR ---\r\n";

/* OPTIONS */
$options = array(
  'Flash Panorama Player' => array(
    'target_base' => 'https://www.example.com/panoramas/',
    'languages' => array('en', 'fr', 'de'),
    'language_token' => '??',
  ),
  'Kolor Pantour 1.7.2' => array(
    'target_base' => 'https://www.example.com/panoramas/',
    'languages' => array('en', 'fr', 'de'),
    'language_token' => '??',
  ),
);

/* TARGETS */
$targets = array(
  'Flash Panorama Player' => array(
    //'unique-places/some_unique_place_??.html',
  ),
  'Kolor Pantour 1.7.2' => array(
    //'great-places/??/2',
  ),
);
