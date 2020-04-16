<?php

$app = \OC::$server->query(\OCA\Versions_Ignore\AppInfo\Application::class);
$app->registerEvents();
