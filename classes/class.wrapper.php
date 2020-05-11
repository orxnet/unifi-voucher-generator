<?PHP
class UnifiWrapper {

	private $sEmbedJsonUserIdKey = 'VoucherSelfPortal';
	private $oUnifiApi;
	
	public function __construct() {
		$this->oUnifiApi = new Unifi();
		
		$this->oUnifiApi->login() || die('Unify login failed');
	}
	
	public function voucherList($sGoBack, $fnFilter, $fnDelete) {
		$sKeepHistory = substr($sGoBack, 0, strpos($sGoBack, ' '));
		// Get unused (open) vouchers
		$voucherJson = $this->oUnifiApi->stat_voucher();
		// Get authentications for used and active vouchers
		$usedAuthJson = $this->oUnifiApi->stat_auths(strtotime('-'.$sGoBack));
		$hashMapped = array();
		$vouchers = array();

		// Place found vouchers in hashMapped to easily find it by its _id
		foreach ($voucherJson as $voucher) {
			if (!isset($voucher->note)) continue;
			if (!$embeddedJson = json_decode($voucher->note, true)) continue;
			if ($fnFilter($embeddedJson, $voucher, null)) {
				$hashMapped[$voucher->_id] = $voucher;
			}
		}

		// Go through authentications, if they're authenticated by a voucher made by this user, base a Voucher object
		// on it. If the voucher _id was also present in the found vouchers above, remove it there to not make two
		// Voucher objects based on the same voucher _id
		foreach ($usedAuthJson as $auth) {
			if (!isset($auth->name)) continue;
			if (!$embeddedJson = json_decode($auth->name, true)) continue;
			if ($fnFilter($embeddedJson, null, $auth)) {
				if (array_key_exists($auth->voucher_id, $hashMapped)) {
					unset($hashMapped[$auth->voucher_id]);
				}
				$vouchers[] = Voucher::fromAuth($auth, $sKeepHistory);
			}
		}

		// Go through found open vouchers that weren't just removed from $hashMapped, revoke if the user clicked delete,
		// else base Voucher object on it.
		foreach ($hashMapped as $voucher) {
			$embeddedJson = json_decode($voucher->note, true);
			if ($fnDelete($embeddedJson, $voucher)) {
				$this->oUnifiApi->revoke_voucher($voucher->_id);
			} else {
				$vouchers[] = Voucher::fromVoucher($voucher);
			}
		}
		
		return $vouchers;
	}

	public function createVoucher($sUserId, $sExpiration, $iMinutes, $iQuantity) {
		$embeddedJson = array($this->sEmbedJsonUserIdKey => $sUserId);
		if ($sExpiration) {
			$embeddedJson['Expires'] = strtotime('+'.$sExpiration);
		}
		$newJson = $this->oUnifiApi->create_voucher($iMinutes, $iQuantity, '1', json_encode($embeddedJson), null, null, null, false);
		$returnVouchers = array();
		for ($n = 0; $n < count($newJson); $n++) {
			$returnVouchers[] = Voucher::fromVoucher($newJson[$n]);
		}
		return $returnVouchers;
	}
}
?>