<?PHP
session_start();

require('settings.php');

// I wanted to have 1 file `voucher` with all voucher classes so decided to have the
// autoload filename of a class be the last CamelCase "word" of the class name
spl_autoload_register(function ($class_name) {
	$match = array();
	preg_match('/[A-Z][a-z]*$/', $class_name, $match);
    include('classes/class.'.strtolower($match[0]).'.php');
});

$oUser = User::get_user(new LmsMysql());
$oUnifiApi = new Unifi();

$oUnifiApi->login() || die('Unify login failed');

$voucherJson = $oUnifiApi->stat_voucher();
$usedAuthJson = $oUnifiApi->stat_auths(strtotime('-'.$oUser->keep_history()));
$hashMapped = array();
$vouchers = array();

foreach ($voucherJson as $voucher) {
	if (!isset($voucher->note)) continue;
	if (!$embeddedJson = json_decode($voucher->note, true)) continue;
	if ($embeddedJson['VoucherSelfPortal'] === $oUser->get_user_id() && $voucher->status != 'EXPIRED') {
	//	var_dump($voucher);
		$hashMapped[$voucher->_id] = $voucher;
	}
}

foreach ($usedAuthJson as $auth) {
	if (!isset($auth->name)) continue;
	if (!$embeddedJson = json_decode($auth->name, true)) continue;
	if ($embeddedJson['VoucherSelfPortal'] == $oUser->get_user_id()) {
		if (array_key_exists($auth->voucher_id, $hashMapped)) {
			unset($hashMapped[$auth->voucher_id]);
		}
		$vouchers[] = Voucher::fromAuth($auth);
	}
}

foreach ($hashMapped as $voucher) {
	if (isset($_GET['delVoucher']) && $voucher->_id == $_GET['delVoucher']) {
		$oUnifiApi->revoke_voucher($voucher->_id);
	} else {
		$vouchers[] = Voucher::fromVoucher($voucher);
	}
}

$minutes = array('open'=>0, 'active'=>0, 'used'=>0);
$maxAvailable = $oUser->max_minutes(); // MAX_MINUTES;
$timeouts = array();

foreach ($vouchers as $voucher) {
	$minutes[$voucher->getType()] += $voucher->getDuration();
	$maxAvailable -= $voucher->getDuration();
	if ($voucher->getType() == 'active') {
		$timeouts['v'.$voucher->getId()] = $voucher->getSecRemaining();
	}
}

$maxAvailable = max(0, $maxAvailable);

if (isset($_POST['duration'])) {
	$hm = preg_split('/\D+/', $_POST['duration']);
	$newMinutes = 60 * intval($hm[0]) + intval($hm[1]);
	$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
	if ($quantity < 1 || $quantity > $oUser->max_quantity() || $quantity * $newMinutes > $maxAvailable) {
		$quantity = 1;
	}
	if ($newMinutes <= $maxAvailable) {
		$newJson = $oUnifiApi->create_voucher($newMinutes, $quantity, '1', json_encode(array('VoucherSelfPortal'=>$oUser->get_user_id())), null, null, null, false);
		for ($n = 0; $n < count($newJson); $n++) {
			$newVoucher = Voucher::fromVoucher($newJson[$n]);
			$minutes[$newVoucher->getType()] += $newVoucher->getDuration();
			$maxAvailable -= $newVoucher->getDuration();
			$vouchers[] = $newVoucher;
		}
	}
}
	
Template::show('main', array(
	'explanationClass' => (count($vouchers) > 0)?'hide':'',
	'minAvailable'     => min(30, $maxAvailable),
	'maxAvailable'     => min($maxAvailable, $oUser->max_length()),
	'disableSubmit'    => (($maxAvailable == 0) ? true : false),
	'usedPercentage'   => $oUser->max_minutes() == 0 ? 0 : floor((100 / $oUser->max_minutes()) * $minutes['used']),
	'activePercentage' => $oUser->max_minutes() == 0 ? 0 : floor((100 / $oUser->max_minutes()) * $minutes['active']),
	'openPercentage'   => $oUser->max_minutes() == 0 ? 0 : floor((100 / $oUser->max_minutes()) * $minutes['open']),
	'availableTime'    => Voucher::min2hm($maxAvailable),
	'maxQuantity'      => $oUser->max_quantity(),
	'listVouchers'     => function() use ($vouchers) {
		foreach(array_reverse($vouchers) as $voucher) {
			$voucher->show('Template');
		}
	},
	'timeoutsJson'    => json_encode($timeouts))
);

?>