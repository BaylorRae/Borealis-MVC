<!DOCTYPE html>
<html lang="en">
<head>
<title>Page title</title>

	<!-- CSS -->
	<?= get_stylesheets('reset, application') ?>
	
	<!-- JS -->
	<?= get_javascripts('jquery') ?>
</head>
<body>
	
	<?php if( count($flash) ) : ?>
		<?php foreach( $flash as $msg ) : ?>
			<p class="notice"><%= $msg %></p>
		<?php endforeach ?>
	<?php endif ?>
	
{PAGE_CONTENT}
</body>
</html>