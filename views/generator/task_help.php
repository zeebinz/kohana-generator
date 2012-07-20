
<comment>Usage</comment>
=======
<?php echo $usage ?> 

<comment>Details</comment>
=======
<?php foreach($tags as $tag_name => $tag_content): ?>
<?php echo ucfirst($tag_name) ?>: <?php echo $tag_content ?>

<?php endforeach; ?>

<comment>Description</comment>
===========
<?php echo $description ?>

