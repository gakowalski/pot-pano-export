<?php $dev = (isset($_GET['dev'])? '&dev' : ''); ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    body {
      line-height: 1.5;
    }
    body > ul > li {
      margin-bottom: 25px;
    }
    img {
      border: 1px solid black;
    }
  </style>
</head>
<body>
  <h1>Panoramas</h1>
  <ul><?php foreach (scandir('.') as $folder): ?>
    <?php if ($folder == '.' || $folder == '..' || $folder == 'index.php') continue; ?>
    <li>
      Folder <a href="<?php echo "$folder"; ?>"><?php echo $folder; ?></a>
      <ul>
      <li>
        <?php if (true === file_exists("$folder/cover.jpg")): ?>
          <img src="<?php echo "$folder/cover.jpg"; ?>">
        <?php endif; ?>
      </li>
      <?php
        $path = "$folder/title_translations.json";
        if (true === file_exists($path)):
          $title_translations = json_decode(file_get_contents($path), true);
          foreach ($title_translations['title'] as $language_code => $translation):
      ?>
      <li>
        <?php echo $language_code; ?> -
        <a href="<?php echo "$folder/?lang=$language_code$dev"; ?>"><?php echo $translation; ?></a>
      </li>
      <?php endforeach; ?>
      <?php endif; ?>
      </ul>
    </li>
  <?php endforeach; ?></ul>
</body>
</html>
