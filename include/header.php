<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Chat</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="main.css">
    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
	<script src="js/jQuery.js"></script>
	<script>
		if ( window.history.replaceState ) {
			window.history.replaceState( null, null, window.location.href );
		}
	</script>
	<script>
		$( document ).ready(function( $ ) {
			$(window).on('popstate', function() {
				location.reload(true);
			});

		});
	</script>
