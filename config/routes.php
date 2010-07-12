<?php

// $Map->connect('/item/:id/', array('controller' => 'welcome', 'action' => 'show'));

$Map->connect('/startpage', array('controller' => 'welcome', 'action' => 'startpage'));

$Map->connect('/:controller/:action/:id', array('controller' => 'welcome', 'action' => 'index'));
?>