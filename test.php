<?php
session_start();
session_destroy();
spl_autoload_register(function ($class_name) {
	$match = array();
	preg_match('/[A-Z][a-z]*$/', $class_name, $match);
    include('classes/class.'.strtolower($match[0]).'.php');
});

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_DEPRECATED);

require_once('settings.php');

$oUnifiApi = new Unifi();

$oUnifiApi->login() || die('Unify login failed');

$voucherJson = $oUnifiApi->stat_voucher();
$usedAuthJson = $oUnifiApi->stat_auths(strtotime('-30 days'));
$hashMapped = array();
$vouchers = array();

foreach ($voucherJson as $voucher) {
	if (!isset($voucher->note)) continue;
	if (!$embeddedJson = json_decode($voucher->note, true)) continue;
	if ($voucher->status != 'EXPIRED') {
	//	var_dump($voucher);
		$hashMapped[$voucher->_id] = $voucher;
	}
}

foreach ($usedAuthJson as $auth) {
	if (!isset($auth->name)) continue;
	if (!$embeddedJson = json_decode($auth->name, true)) continue;
	if (array_key_exists($auth->voucher_id, $hashMapped)) {
		unset($hashMapped[$auth->voucher_id]);
	}
	$vouchers[$embeddedJson['VoucherSelfPortal']][] = Voucher::fromAuth($auth);
}

foreach ($hashMapped as $voucher) {
	$embeddedJson = json_decode($voucher->note, true);
	$vouchers[$embeddedJson['VoucherSelfPortal']][] = Voucher::fromVoucher($voucher);
}

$oLms = new LmsMysql();

$temp = array();
$temp[] = 378; // 485;

//$users = $oLms->get_stats(array_keys($vouchers));
$users = $oLms->get_stats($temp);

var_dump($users[0]['cutoffstop']>time());
/*
$data = array_map(function($user) use ($vouchers) {
	$user['vouchers'] = $vouchers[$user['ownerid']];
	return $user;
}, $users);

var_dump($data);
*/
?>