## [<?php echo $menu ?>]()

<?php if ( ! empty($pages)) foreach ($pages as $title => $file): ?>
 - [<?php echo $title ?>](<?php echo $file ?>)
<?php endforeach; ?>