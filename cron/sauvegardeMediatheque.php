<?php

$timestamp = time();

exec('cd '.__DIR__.'/../public; zip -r '.__DIR__.'/../sauvegardes/mediatheque/mediatheque'.$timestamp.'.zip uploads');