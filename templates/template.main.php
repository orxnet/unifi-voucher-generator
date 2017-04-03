<!DOCTYPE html>
<html>
	<head>
		<title>Voucher zelf-service</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="format-detection" content="telephone=no">
		<link rel="stylesheet" href="templates/css/main.css">
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	</head>

	<body>
		<div id="container">
			<div id="help" class="<?=$_['explanationClass']?>"></div>
			<div id="explanation" class="<?=$_['explanationClass']?>">
				<center>Voucher zelf-service</center>
				<p>
					Met de Voucher zelf-service kun je zelf vouchers aanmaken voor als je iemand op bezoek hebt of voor een nieuw apparaat wat nog niet is aangesloten. Je kunt deze pagina gebruiken op elk apparaat wat bij ORXnet is aangemeld.
				</p>
				<p>
					Klik hieronder op <span class="new"> Voucher Aanmaken</span> kies hoe lang de voucher toegang tot internet moet geven en klik op <button>Aanmaken</button> De aangemaakte voucher kan gebruikt worden door te verbinden met Wi-Fi netwerk [Hutspot RKZ] en de code in te voeren.
				</p>
				<p>
					Je kunt <?=DEFAULT_MAX_MINUTES/60?> uur per maand opnemen aan vouchers, de <span class="empty">balk</span> hieronder loopt vol naar mate je vouchers hebt aangemaakt en geeft aan hoeveel uur je nog beschikbaar hebt. Nieuw aangemaakte en nog niet gebruikte vouchers zijn <span class="open">groen</span> en kunnen nog verwijderd worden om de uren anders te gebruiken (langere of kortere voucher). Vouchers die zijn ingezet zijn <span class="active">oranje</span> en hebben een aflopend klokje dat aangeeft wanneer de voucher is afgelopen. Vouchers die zijn afgelopen zijn <span class="used">rood</span> en geven met een kalendertje aan hoeveel dagen totdat die uren weer vrij komen.
				</p>
			</div>
			<div class="empty">
				<div class="used minutes" style="width:<?=$_['usedPercentage']?>%"></div>
				<div class="active minutes" style="width:<?=$_['activePercentage']?>%"></div>
				<div class="open minutes" style="width:<?=$_['openPercentage']?>%"></div>
				<div class="available minutes"><?=$_['availableTime']?></div>
			</div>
			<?PHP $_['listVouchers'](); ?>
			<div class="new voucher" id="add-button">
				<span id="plus"></span> Voucher Aanmaken
			</div>
			<form style="display:none;" method="post" action="?">
				Lengte: <input type="text" id="duration" name="duration" readonly size="5"> uur
				<div id="duration-slider"></div>
				<?PHP if ($_['maxQuantity'] > 1) { ?>
				Aantal: <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?=$_['maxQuantity']?>" pattern="\d*">
				<?PHP } ?>
				<button type="submit" <?=($_['disableSubmit']?'disabled':'')?>>Aanmaken</button>
			</form>
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
		<script>
			var minAvailable = <?=$_['minAvailable']?>;
			var maxAvailable = <?=$_['maxAvailable']?>;
			var disableSubmit = <?=($_['disableSubmit']?'true':'false')?>;
			var timeouts = <?=$_['timeoutsJson']?>;
		</script>
		<script src="templates/js/main.js"></script>
	</body>
</html>