		<div id="v<?=$_['voucherId']?>" class="temp voucher">
			<div class="tearoff"><span>WiFi-code</span></div>
			<?=$_['voucherCode']?>
			<span class="val"><?=$_['voucherLength']?></span>
			<a class="del" href="?delVoucher=<?=$_['voucherId']?>"></a>
			<svg class="clock" width="30" height="30">
				<circle r="7.5" cx="15" cy="15" style="stroke-dasharray: 0 <?=$_['clockDash']?>; animation: count-down <?=$_['secRemaining']?>s forwards;">
			</svg>
		</div>
