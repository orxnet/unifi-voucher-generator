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
$oUnifi = new UnifiWrapper();

$vouchers = $oUnifi->voucherList($oUser->keep_history(),
	function($embeddedJson, $voucher, $auth) use ($oUser) {
		return $embeddedJson['VoucherSelfPortal'] === $oUser->get_user_id() && ($voucher == null || $voucher->status != 'EXPIRED');
	},
	function($embeddedJson, $voucher) use ($oUser) {
		return isset($_GET['delVoucher']) && $voucher->_id == $_GET['delVoucher']
			|| isset($embeddedJson['Expires']) && $embeddedJson['Expires'] < time();
	});

// Go through all Voucher objects and collect total minutes, and get seconds remaining in case of Active Voucher
$minutes = array('used'=>0, 'active'=>0, 'temp'=>0, 'open'=>0);
$maxAvailable = $oUser->max_minutes();

foreach ($vouchers as $voucher) {
	$minutes[$voucher->getType()] += $voucher->getDuration();
	$maxAvailable -= $voucher->getDuration();
}

$maxAvailable = max(0, $maxAvailable);

// If $_POST is filled, user just made a new voucher
if (isset($_POST['duration'])) {
	// Conver hours:minutes format to total number of minutes
	$hm = preg_split('/\D+/', $_POST['duration']);
	$newMinutes = 60 * intval($hm[0]) + intval($hm[1]);
	if ($newMinutes > 0) {
		// Set quantity of vouchers requested (only available for certain user groups)
		$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
		// If quantity of vouchers with this length would go over the limit, revert to quantity of 1
		if ($quantity < 1 || $quantity > $oUser->max_quantity() || $quantity * $newMinutes > $maxAvailable) {
			$quantity = 1;
		}
		if ($newMinutes <= $maxAvailable) {
			$newVouchers = $oUnifi->createVoucher(
				$oUser->get_user_id(),
				$oUser->voucher_expires(),
				$newMinutes,
				$quantity);
			for ($n = 0; $n < count($newVouchers); $n++) {
				$newVoucher = $newVouchers[$n];
				$minutes[$newVoucher->getType()] += $newVoucher->getDuration();
				$maxAvailable -= $newVoucher->getDuration();
				$vouchers[] = $newVoucher;
			}
		}
	}
}

// Calculate percentage of minutes used
$percentages = array();
$totalPercentage = 0;
foreach ($minutes as $type => $minute) {
	$percentage = $oUser->max_minutes() == 0 ? 0 : floor((100 / $oUser->max_minutes()) * $minute);
	$percentage = ($totalPercentage + $percentage > 100) ? (100 - $totalPercentage) : $percentage;
	$totalPercentage += $percentage;
	$percentages[$type] = $percentage;
}

$timeouts = array();
foreach ($vouchers as $voucher) {
	if ($voucher->getType() == 'active' || $voucher->getType() == 'temp') {
		$timeouts['v'.$voucher->getId()] = $voucher->getSecRemaining();
	}
}

Template::show('main', array(
	'explanationClass' => (count($vouchers) > 0)?'hide':'',
	'minAvailable'     => min(30, $maxAvailable),
	'maxAvailable'     => min($maxAvailable, $oUser->max_length()),
	'disableSubmit'    => (($maxAvailable <= 0) ? true : false),
	'percentages'      => $percentages,
	'availableTime'    => Voucher::min2hm($maxAvailable),
	'maxQuantity'      => $oUser->max_quantity(),
	'listVouchers'     => function() use ($vouchers) {
		foreach(array_reverse($vouchers) as $voucher) {
			$voucher->show('Template');
		}
	},
	'keepHistory'     => substr($oUser->keep_history(), 0, strpos($oUser->keep_history(), ' ')),
	'timeoutsJson'    => json_encode($timeouts))
);
echo('<!-- DEV -->');
?>
