<?php
  $dev = true;

  if ($dev) $path = '../output/bialka-tatrzanska/';
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
  <script>
    var panorama, viewer;

    panorama = new PANOLENS.CubePanorama( [
          '<?php echo $path; ?>right.jpg', '<?php echo $path; ?>left.jpg',
          '<?php echo $path; ?>up.jpg', '<?php echo $path; ?>down.jpg',
          '<?php echo $path; ?>front.jpg', '<?php echo $path; ?>back.jpg',
    ] );

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

    //panorama = new PANOLENS.ImagePanorama( 'building.jpg' );

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
      output: 'console'          //< Whether and where to output infospot position. Could be 'console' or 'overlay'
    });

    viewer.add(panorama);
  </script>
  <div id="progress">
    <div id="bar"></div>
  </div>
  <div id="container"></div>
</body>
