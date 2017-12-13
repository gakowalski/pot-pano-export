<?php
  if (isset($_GET['dev'])) $path = '../output/wolinski-park-narodowy-wybrzeze-klifowe/';
  else $path = '';

  $lang = (isset($_GET['lang']))? $_GET['lang'] : 'pl';
  $lang_2 = $lang;
  $text_direction = 'ltr'; //< default value, can be changed later

  switch ($lang) {
    case 'sv': $lang = 'se'; break;
    case 'zh': $lang = 'cn'; break;
    case 'uk': $lang = 'ua'; break;
    case 'he':
      $lang = 'il';
    case 'il':
      $text_direction = 'rtl';
      break;
    case 'ja': $lang = 'jp'; break;
    default:
  }

  $title_translations = json_decode(file_get_contents($path . 'title_translations.json'), true);
  $desc_translations = json_decode(file_get_contents($path . 'desc_translations.json'), true);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_2; ?>" dir="<?php echo $text_direction; ?>">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
  <link href="style.css" rel="stylesheet"/>
  <script src="three.min.js"></script>
  <script src="panolens.min.js"></script>
</head>
<body class="<?php echo $text_direction; ?>">
  <div id="progress">
    <div id="bar"></div>
  </div>
  <div id="text">
    <div id="title"><?php echo $title_translations['title'][$lang]; ?></div>
    <div id="info">
      <?php echo $desc_translations[$lang]; ?>
      <?php if (($lang == 'pl' && file_exists($path.'voice_pl.mp3') || ($lang != 'pl' && file_exists($path.'voice_en.mp3')))): ?>
      <button id="read-text" class="play"></button>
      <?php endif; ?>
    </div>
  </div>
  <div id="container"></div>
  <script>
    var panorama, viewer;

    PANOLENS.DataImage.FullscreenEnter = 'expand-arrows-alt.svg';
    PANOLENS.DataImage.FullscreenLeave = 'expand-arrows-alt.svg';

    panorama = new PANOLENS.CubePanorama( [
          '<?php echo $path; ?>right.jpg', '<?php echo $path; ?>left.jpg',
          '<?php echo $path; ?>up.jpg', '<?php echo $path; ?>down.jpg',
          '<?php echo $path; ?>front.jpg', '<?php echo $path; ?>back.jpg',
    ] );

    // based on: https://codepen.io/pchen66/pen/RgxeJM
    var bar = document.querySelector( '#bar' );

    function onProgressUpdate ( event ) {
      var percentage = event.progress.loaded/ event.progress.total * 100;
      bar.style.width = percentage + "%";
      if (percentage >= 100){
        bar.classList.add( 'hide' );
        setTimeout(function(){
          bar.style.width = 0;
        }, 1000);
      }
    }

    panorama.addEventListener( 'progress', onProgressUpdate );

    viewer = new PANOLENS.Viewer({
      container: document.querySelector( '#container' ), //< A DOM Element container
      controlBar: true,         //< Vsibility of bottom control bar
      // Buttons array in the control bar. Default to ['fullscreen', 'setting', 'video']
      controlButtons: ['fullscreen'],
      autoHideControlBar: false, //< Auto hide control bar
      autoHideInfospot: true,    //< Auto hide infospots
      horizontalView: false,     //< Allow only horizontal camera control
      cameraFov: 60,             //< Camera field of view in degree
      reverseDragging: false,    //< Reverse orbit control direction
      enableReticle: false,      //< Enable reticle for mouseless interaction
      dwellTime: 1500,           //< Dwell time for reticle selection in millisecond
      autoReticleSelect: true,   //< Auto select a clickable target after dwellTime
      viewIndicator: false,      //< Adds an angle view indicator in upper left corner
      indicatorSize: 30,         //< Size of View Indicator
      output: 'console',         //< Whether and where to output infospot position. Could be 'console' or 'overlay'

      /* undocumented, unofficial, potentially broken */
      // https://codepen.io/pchen66/pen/rGpoPv
      /*
      autoRotate: true,
      autoRotateSpeed: 1,
      autoRotateActivationDuration: 5000,
      */
    });

    viewer.add(panorama);

    /*
    viewer.addUpdateCallback(function(){ });
    */

    /* AUDIO */

    var audio_listener = new THREE.AudioListener();

    viewer.getCamera().add( audio_listener );

    var music = new THREE.Audio( audio_listener );
    var audioLoader = new THREE.AudioLoader();

    audioLoader.load( '<?php echo $path; ?>music.mp3', function( buffer ) {
      music.setBuffer( buffer );
      music.setLoop( true );
      music.setVolume( 0.5 );
      music.play();
    });

    /* CONTROLS */

    function toggle(element) {
      var display = element.style.display;

      if (display == 'block') {
        display = 'none';
      } else {
        display = 'block';
      }
      element.style.display = display;
    }

    function toggle_music() {
      if (music.isPlaying === true) {
        music.pause();
      } else {
        music.play();
      }
    }

    function make_button(viewer, background_image, on_tap_function) {
      // based on https://codepen.io/pchen66/pen/vZVyYr
      var control = {
        style: {
          backgroundImage: 'url(' + background_image + ')',
          //width: '3rem'
        },
        onTap: on_tap_function
      };

      viewer.appendControlItem(control);
    }

    function press_key(key) {
      var event = new Event('keydown');
      event.keyCode = key;
      window.dispatchEvent(event);
      var event = new Event('keyup');
      event.keyCode = key;
      window.dispatchEvent(event);
    }

    make_button(viewer, 'volume-up.svg', toggle_music);

    make_button(viewer, 'info-circle.svg', function () {
      toggle(document.getElementById('info'));
    });

    make_button(viewer, 'search-minus.svg', function () {
      var zoom = viewer.getCamera().zoom;
      if (zoom > 1) {
        viewer.getCamera().zoom -= 0.5;
      }
      viewer.getCamera().updateProjectionMatrix();
    });

    make_button(viewer, 'search-plus.svg', function () {
      var zoom = viewer.getCamera().zoom;
      if (zoom < 2) {
        viewer.getCamera().zoom += 0.5;
      }
      viewer.getCamera().updateProjectionMatrix();
    });

    make_button(viewer, 'arrow-circle-down.svg', function () {
      press_key(viewer.getControl().keys.BOTTOM);
    });

    make_button(viewer, 'arrow-circle-up.svg', function () {
      press_key(viewer.getControl().keys.UP);
    });

    make_button(viewer, 'arrow-circle-<?php echo $text_direction == 'ltr'? 'right' : 'left'; ?>.svg', function () {
      press_key(viewer.getControl().keys.<?php echo strtoupper($text_direction == 'ltr'? 'right' : 'left'); ?>);
    });

    make_button(viewer, 'arrow-circle-<?php echo $text_direction == 'ltr'? 'left' : 'right'; ?>.svg', function () {
      press_key(viewer.getControl().keys.<?php echo strtoupper($text_direction == 'ltr'? 'left' : 'right'); ?>);
    });

    <?php if (($lang == 'pl' && file_exists($path.'voice_pl.mp3') || ($lang != 'pl' && file_exists($path.'voice_en.mp3')))): ?>
    var voice = new THREE.Audio( audio_listener );

    audioLoader.load( '<?php echo $path; ?>voice_<?php echo ($lang == 'pl')? 'pl' : 'en'; ?>.mp3', function( buffer ) {
      voice.setBuffer( buffer );
      voice.setLoop( false );
      voice.setVolume( 0.5 );
    });

    var music_state;

    function toggle_voice() {
      var e = document.getElementById('read-text');
      if (e.classList.contains('play')) {
        e.classList.remove('play');
        e.classList.add('pause');
      } else {
        e.classList.remove('pause');
        e.classList.add('play');
      }

      if (voice.isPlaying === true) {
        voice.pause();
        if (music_state == true) {
          music.play();
        }
      } else {
        if (music.isPlaying === true) {
          music_state = true;
          music.pause();
        } else {
          music_state = false;
        }
        voice.play();
      }
    }
    document.getElementById('read-text').addEventListener('click', toggle_voice, false);
    <?php endif; ?>
  </script>
</body>
