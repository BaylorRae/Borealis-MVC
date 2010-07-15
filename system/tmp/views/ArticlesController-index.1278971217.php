<!DOCTYPE html>
<html lang="en">
<head>
<title>Page title</title>

</head>
<body>
<?php foreach( $articles as $article ) : ?>
	
	<div class="article">
		<h2><%= $article->title %></h2>
		<p class="meta">Category <%= $article->category->name %></p>
	</div>
	
<?php endforeach ?>
</body>
</html>