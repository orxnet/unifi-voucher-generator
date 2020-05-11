function lz(val) {
	return val<10?'0'+val:val;
}
function min2hm(min) {
	return lz(Math.floor(min/60)) + ':' + lz(min%60);
}
function voucherUsed(voucher, secs) {
	setTimeout(function () {
		if ($('#'+voucher).hasClass('active')) {
			$('#'+voucher)
				.toggleClass('active')
				.toggleClass('used')
				.children('svg')
				.replaceWith('<div class="cal">' + keepHistory + '</div>');
		} else if ($('#'+voucher).hasClass('temp')) {
			$('#'+voucher+' .del')
				.off('click')
				.get(0)
				.click();
		}
	}, secs*1000);
}
$(function() {
	$('#help').click(function() {
		$('#explanation').toggleClass('hide');
		$('#help').toggleClass('hide');
	});
/*	$('#add-button').click(function() {
		$('#plus').toggleClass('clicked');
		$('form').toggle('slide', {'direction':'up'});
	});*/
	$('#duration-slider').slider({
		min: 0,
		max: maxAvailable,
		disabled: disableSubmit,
		value: minAvailable,
		step: 30,
		slide: function(event, ui) {
			$('form button').prop('disabled', ui.value <= 0);
			$('#duration').val(min2hm(ui.value));
		}
	});
	$('#duration').val(min2hm($('#duration-slider').slider('value')));
	$('.del').click(function(e) {
		if (!confirm('Voucher zeker weten weggooien?')) {
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
	});
	for (var voucher in timeouts) {
		voucherUsed(voucher, timeouts[voucher]);
	}
});