<?php

$app = new Core\Application(__DIR__ . '/../');

if(isset($_SERVER['REQUEST_URI'])) {
    $app->registerURI($_SERVER['REQUEST_URI']);
}
return $app;
