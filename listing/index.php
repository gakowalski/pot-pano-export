<!DOCTYPE html>
<html>
<body>
  <h1>Panoramas</h1>
  <ul><?php foreach (scandir('.') as $folder): ?>
    <?php if ($folder == '.' || $folder == '..' || $folder == 'index.php') continue; ?>
    <li>
      Folder <a href="<?php echo $folder; ?>"><?php echo $folder; ?></a>
      <ul>
      <?php
        $path = "$folder/title_translations.json";
        if (true === file_exists($path)):
          $title_translations = json_decode(file_get_contents($path), true);
          foreach ($title_translations['title'] as $language_code => $translation):
      ?>
      <li>
        <?php echo $language_code; ?> -
        <a href="<?php echo "$folder/?lang=$language_code"; ?>"><?php echo $translation; ?></a>
      </li>
      <?php endforeach; ?>
      <?php endif; ?>
      </ul>
    </li>
  <?php endforeach; ?></ul>
</body>
</html>
