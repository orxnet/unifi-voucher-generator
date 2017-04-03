		<div id="v<?=$_['voucherId']?>" class="active voucher">
			<div class="tearoff"><span>voucher</span></div>
			<?=$_['voucherCode']?>
			<span class="val"><?=$_['voucherLength']?></span>
			<svg class="clock" width="30" height="30">
				<circle r="7.5" cx="15" cy="15" style="stroke-dasharray: 0 <?=$_['clockDash']?>; animation: count-down <?=$_['secRemaining']?>s forwards;">
			</svg>
		</div>
