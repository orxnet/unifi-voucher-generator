<!DOCTYPE html>
<html>
	<head>
		<title>WiFi-code self-service</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="format-detection" content="telephone=no">
		<link rel="stylesheet" href="templates/css/main.css?v=2.0">
		<link rel="stylesheet" href="jquery/jquery-ui.css">
	</head>

	<body>
		<div id="container">
			<div id="help" class="<?=$_['explanationClass']?>"></div>
			<div id="explanation" class="<?=$_['explanationClass']?>">
				<?PHP $_['showExplanation'](); ?>
			</div>
			<div class="empty">
				<?PHP foreach ($_['percentages'] as $type => $percentage) { ?>
					<div class="<?=$type?> minutes" style="width:<?=$percentage?>%"></div>
				<?PHP } ?>
				<div class="available minutes">Nog te besteden: <?=$_['availableTime']?></div>
			</div>
			<div class="new voucher" id="add-button">
				<span id="plus"></span>WiFi-code aanmaken
			</div>
			<form method="post" action="?">
				Toegang voor <input type="text" id="duration" name="duration" readonly size="5"> uur
				<div id="duration-slider"></div>
				<?PHP if ($_['maxQuantity'] > 1) { ?>
				Aantal: <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?=$_['maxQuantity']?>" pattern="\d*">
				<?PHP } ?>
				<button type="submit" <?=($_['disableSubmit']?'disabled':'')?>>Aanmaken</button>
			</form>
			<?PHP $_['listVouchers'](); ?>
		</div>
		<script src="jquery/jquery.min.js"></script>
		<script src="jquery/jquery-ui.min.js"></script>
		<script src="jquery/jquery.ui.touch-punch.min.js"></script>
		<script>
			var minAvailable = <?=$_['minAvailable']?>;
			var maxAvailable = <?=$_['maxAvailable']?>;
			var disableSubmit = <?=($_['disableSubmit']?'true':'false')?>;
			var timeouts = <?=$_['timeoutsJson']?>;
			var keepHistory = <?=$_['keepHistory']?>;
		</script>
		<script src="templates/js/main.js?v=2.0"></script>
	</body>
</html>