
Usage
=======
<?php echo $usage ?> 

Details
=======
<?php foreach($tags as $tag_name => $tag_content): ?>
<?php echo ucfirst($tag_name) ?>: <?php echo $tag_content ?>

<?php endforeach; ?>

Description
===========
<?php echo $description ?>

