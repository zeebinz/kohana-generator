<?php $pad = max(array_map('strlen', array_keys($errors))) ?>

Parameter errors:

<?php foreach ($errors as $parameter => $error): ?>
    <info><?php echo sprintf("%-{$pad}s", $parameter); ?></info> : <alert><?php echo $error; ?></alert> 
<?php endforeach; ?>

For more help, run:

    <info>minion <?php echo $task?> --help</info>
