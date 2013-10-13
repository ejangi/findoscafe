<!DOCTYPE html>
<html>
<head>
	<title><?php echo @$title ?></title>
	<link rel="shortcut icon" href="/images/favicon.png">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1">
	<base href="<?php echo @$base; ?>" target="_self">
	<meta name="apple-mobile-web-app-capable" content="no">
	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="viewport" content="width=width=device-width, user-scalable=no">
	<link rel="stylesheet" href="<?php echo @$base; ?>/master.css" type="text/css">
	<link rel="apple-touch-icon" href="<?php echo @$base; ?>/images/apple-touch-icon.png">
	<link rel="image_src" href="<?php echo @$base; ?>/images/thumbnail.jpg"> 
	<meta property="og:site_name" content="<?php echo @$title ?>"> 
	<meta property="og:type" content="website">
	<?php /* ?><meta property="fb:app_id" content="202923026424081"><?php */ ?>
	<meta property="og:country-name" content="AU" /> 
	<meta property="og:image" content="<?php echo @$base; ?>/images/thumbnail.jpg"> 
	<meta property="og:title" content="<?php echo @$title ?>">
	<meta property="og:url" content="<?php echo @$base; ?>">
</head>
<body id="home">
	<div id="page">
		<div id="container">
			<?php echo @$yield; ?>
		</div>
	</div>
	<script src="<?php echo @$base; ?>/jquery.js"></script>
	<script src="<?php echo @$base; ?>/app.js"></script>
</body>
</html>