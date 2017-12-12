<?php
  $dev = true;

  if ($dev) $path = '../output/baltow/';
  else $path = '';

  $lang = (isset($_GET['lang']))? $_GET['lang'] : 'pl';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="ltr">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
  <link href="style.css" rel="stylesheet"/>
  <script src="three.min.js"></script>
  <script src="panolens.min.js"></script>
</head>
<body>
  <div id="progress">
    <div id="bar"></div>
  </div>
  <div id="info">
    Lorem Ipsum jest tekstem stosowanym jako przykładowy wypełniacz w przemyśle poligraficznym. Został po raz pierwszy użyty w XV w. przez nieznanego drukarza do wypełnienia tekstem próbnej książki. Pięć wieków później zaczął być używany przemyśle elektronicznym, pozostając praktycznie niezmienionym. Spopularyzował się w latach 60. XX w. wraz z publikacją arkuszy Letrasetu, zawierających fragmenty Lorem Ipsum, a ostatnio z zawierającym różne wersje Lorem Ipsum oprogramowaniem przeznaczonym do realizacji druków na komputerach osobistych, jak Aldus PageMaker
    <button id="read-text"></button>
    <button id="stop-reading"></button>
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

    function make_button(viewer, background_image, on_tap_function) {
      // based on https://codepen.io/pchen66/pen/vZVyYr
      var control = {
        style: {
          backgroundImage: 'url(' + background_image + ')',
          width: '3rem'
        },
        onTap: on_tap_function
      };

      viewer.appendControlItem(control);
    }

    make_button(viewer, 'volume-up.svg', function () {
      alert('turn down');
    });

    make_button(viewer, 'info-circle.svg', function () {
      var display = document.getElementById('info').style.display;

      if (display == 'block') {
        display = 'none';
      } else {
        display = 'block';
      }
      document.getElementById('info').style.display = display;
    });

    make_button(viewer, 'search-minus.svg', function () {
      var event = new Event('mousewheel');
      event.wheelDelta = 120;
      event.detail = 2;
      viewer.getControl().domElement.dispatchEvent(event);
    });

    make_button(viewer, 'search-plus.svg', function () {
      var event = new Event('mousewheel');
      event.wheelDelta = -120;
      event.detail = -2;
      viewer.getControl().domElement.dispatchEvent(event);
    });

    make_button(viewer, 'arrow-circle-down.svg', function () {
      var event = new Event('keydown');
      event.keyCode = viewer.getControl().keys.BOTTOM;
      window.dispatchEvent(event);
      var event = new Event('keyup');
      event.keyCode = viewer.getControl().keys.BOTTOM;
      window.dispatchEvent(event);
    });

    make_button(viewer, 'arrow-circle-up.svg', function () {
      var event = new Event('keydown');
      event.keyCode = viewer.getControl().keys.UP;
      window.dispatchEvent(event);
      var event = new Event('keyup');
      event.keyCode = viewer.getControl().keys.UP;
      window.dispatchEvent(event);
    });

    make_button(viewer, 'arrow-circle-right.svg', function () {
      var event = new Event('keydown');
      event.keyCode = viewer.getControl().keys.RIGHT;
      window.dispatchEvent(event);
      var event = new Event('keyup');
      event.keyCode = viewer.getControl().keys.RIGHT;
      window.dispatchEvent(event);
    });

    make_button(viewer, 'arrow-circle-left.svg', function () {
      var event = new Event('keydown');
      event.keyCode = viewer.getControl().keys.LEFT;
      window.dispatchEvent(event);
      var event = new Event('keyup');
      event.keyCode = viewer.getControl().keys.LEFT;
      window.dispatchEvent(event);
    });

  </script>
</body>
