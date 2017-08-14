<!DOCTYPE html>
<html>
	<head>
		<title>WiFi-code super-self-service</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="format-detection" content="telephone=no">
		<link rel="stylesheet" href="templates/css/main.css">
		<link rel="stylesheet" href="jquery/jquery-ui.css">
	</head>

	<body>
		<div id="container">
			<div id="help" class="<?=$_['explanationClass']?>"></div>
			<div id="explanation" class="<?=$_['explanationClass']?>">
				<center>WiFi-code super-self-service</center>
				<p>
					Met de WiFi-code self-service kun je zelf WiFi-codes aanmaken om apparaten van je visite tijdelijk op internet te laten. Je kunt deze pagina alleen gebruiken op apparaten die bij ORXnet zijn aangemeld.
				</p>
				<p>
					Kies onder <span class="new"> WiFi-code aanmaken</span> hoe lang de WiFi-code toegang tot internet moet geven en klik op <button>Aanmaken</button> De aangemaakte WiFi-code kan gebruikt worden door te verbinden met Wi-Fi netwerk [Hutspot RKZ] en de code in te voeren. Je kunt <?=DEFAULT_MAX_MINUTES/60;?> uur per maand opnemen aan WiFi-codes, in de <span class="empty">balk</span> hieronder zie je hoeveel uren je gebruikt hebt en hoeveel je nog te besteden hebt.
				</p>
				<p>
					Nieuw aangemaakte en nog niet gebruikte WiFi-codes zijn <span class="open">groen</span> en kun je nog verwijderen. WiFi-codes die in gebruik zijn zijn <span class="active">oranje</span> en hebben een aflopend klokje dat aangeeft wanneer de WiFi-toegang is afgelopen. WiFi-codes die zijn afgelopen zijn <span class="used">rood</span> en geven met een kalendertje aan hoeveel dagen totdat die uren weer vrij komen.
				</p>
			</div>
			<div class="empty">
				<div class="used minutes" style="width:<?=$_['usedPercentage']?>%"></div>
				<div class="active minutes" style="width:<?=$_['activePercentage']?>%"></div>
				<div class="open minutes" style="width:<?=$_['openPercentage']?>%"></div>
				<div class="available minutes">Nog te besteden: <?=$_['availableTime']?></div>
			</div>
			<?PHP $_['listVouchers'](); ?>
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
		</div>
		<script src="jquery/jquery.min.js"></script>
		<script src="jquery/jquery-ui.min.js"></script>
		<script src="jquery/jquery.ui.touch-punch.min.js"></script>
		<script>
			var minAvailable = <?=$_['minAvailable']?>;
			var maxAvailable = <?=$_['maxAvailable']?>;
			var disableSubmit = <?=($_['disableSubmit']?'true':'false')?>;
			var timeouts = <?=$_['timeoutsJson']?>;
		</script>
		<script src="templates/js/main.js"></script>
	</body>
</html>
