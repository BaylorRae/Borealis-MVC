<?php

$Map->connect('/:controller/:action/:id', array('controller' => 'welcome', 'action' => 'index'));
$Map->connect('/', array('controller' => 'welcome', 'action' => 'index'));
?>