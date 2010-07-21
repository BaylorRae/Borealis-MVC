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
		<?php foreach( $flash as $f ) : ?>
			<p class="<%= $f->type %>"><%= $f->message %></p>
		<?php endforeach ?>
	<?php endif ?>
	
{PAGE_CONTENT}
</body>
</html>