<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />	
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<title><?php echo $title; ?></title>
		<!--<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/normalize/2.1.3/normalize.min.css" />-->
		<!--<link href='//fonts.googleapis.com/css?family=Source+Code+Pro:300|Roboto:300,300italic,500' rel='stylesheet' type='text/css'>-->
		<link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/css/style.css'); ?>" />
	</head>
	<body>
		<div class="wrap">
			<?php if ($title): ?>
				<h1><?php echo $title; ?></h1>
			<?php endif; ?>
