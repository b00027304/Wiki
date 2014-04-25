<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script type="text/javascript" src="<?php echo $wgScriptPath?>/includes/include/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $wgScriptPath; ?>/include/jquery.scrollTo-1.4.2-min.js"></script>
	<script type="text/javascript" src="<?php echo $wgScriptPath; ?>/include/tiny_mce/jquery.tinymce.js"></script>
	<script type="text/javascript" src="<?php echo $wgScriptPath; ?>/include/eqiat.js.php"></script>
	
	<?php if (isset($GLOBALS["headerjs"])) { ?>
		<script type="text/javascript">
			<?php echo $GLOBALS["headerjs"]; ?>
		</script>
	<?php } ?>
	
	<link rel="stylesheet" href="<?php echo $wgScriptPath; ?>/includes/include/styles.css">
</head>
<body>

<div id="body"> </div>

