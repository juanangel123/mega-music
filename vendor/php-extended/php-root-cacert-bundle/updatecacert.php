<?php

require __DIR__.'/src/CacertUpdate.php';
require __DIR__.'/src/CacertBundle.php';

$update = new PhpExtended\RootCacert\CacertUpdate();
$update->doUpdate();
