<?PHP

require('settings.php');

// I wanted to have 1 file `voucher` with all voucher classes so decided to have the
// autoload filename of a class be the last CamelCase "word" of the class name
spl_autoload_register(function ($class_name) {
	$match = array();
	preg_match('/[A-Z][a-z]*$/', $class_name, $match);
    include('classes/class.'.strtolower($match[0]).'.php');
});


$oUnifiApi = new Unifi();

$oUnifiApi->login() || die('Unify login failed');

$voucherJson = $oUnifiApi->stat_voucher();

echo('<pre>');
foreach ($voucherJson as $voucher) {
	if (!isset($voucher->note)) continue;
	if (!$embeddedJson = json_decode($voucher->note, true)) continue;
	if (!isset($embeddedJson['Expires'])) continue;
	if ($embeddedJson['Expires'] <= time() && $voucher->status != 'EXPIRED') {
		echo($embeddedJson['Expires'].' <= '.time().'. Deleting '.substr($voucher->code, 0, 5) . ' ' . substr($voucher->code, -5)."\n");
		$oUnifiApi->revoke_voucher($voucher->_id);
	}
}

?>
</pre>