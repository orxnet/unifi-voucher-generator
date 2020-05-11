<?PHP

abstract class Voucher {

	protected $voucher_id;
	protected $voucher_code;
	protected $owner_id;
	protected $duration;

	private static function lz($val) {
		return ($val<10?'0':'').$val;
	}
	
	public static function min2hm($min) {
		return Voucher::lz(floor($min/60)) . ':' . Voucher::lz($min%60) . 'u';
	}
	
	protected function formattedVoucherCode() {
		return substr($this->voucher_code, 0, 5) . ' ' . substr($this->voucher_code, -5);
	}

	abstract public function show($engine);
	
	abstract public function getType();
	
	abstract public function getSecRemaining();
	
	public function getId() {
		return $this->voucher_id;
	}
	
	public function getDuration() {
		return $this->duration;
	}
	
	public static function fromAuth($auth_json, $keep_history = 30) {
		if ($auth_json->end > time())
			return new ActiveVoucher($auth_json);
		return new UsedVoucher($auth_json, $keep_history);
	}
	
	public static function fromVoucher($voucher_json) {
		$embeddedJson = json_decode($voucher_json->note, true);
		if (isset($embeddedJson['Expires']))
			return new TempVoucher($voucher_json, $embeddedJson['Expires']);
		return new OpenVoucher($voucher_json);
	}
}

class UsedVoucher extends Voucher {

	private $auth_json;
	private $days_left;
	
	public function __construct($auth_json, $keep_history = 30) {
		$this->duration  = ($auth_json->end - $auth_json->start) / 60;
		$this->voucher_code = $auth_json->voucher_code;
		$this->voucher_id = $auth_json->voucher_id;
		
		$this->days_left = ceil($keep_history - (time() - $auth_json->end) / (3600 * 24));
		$this->auth_json = $auth_json;
	}
	
	public function show($engine) {
		$engine::show('usedVoucher', array(
			'voucherCode'    => $this->formattedVoucherCode(),
			'voucherLength'  => $this->min2hm($this->duration),
			'voucherExpires' => $this->days_left
		));
	}
	
	public function getType() {
		return 'used';
	}
	
	public function getSecRemaining() {
		return 0;
	}
}

class ActiveVoucher extends Voucher {

	private $auth_json;
	private $start;
	private $end;
	
	public function __construct($auth_json) {
		$this->duration  = ($auth_json->end - $auth_json->start)/60;
		$this->voucher_code = $auth_json->voucher_code;
		$this->voucher_id = $auth_json->voucher_id;
		$this->start = $auth_json->start;
		$this->end = $auth_json->end;

		$this->auth_json = $auth_json;
	}
	
	public function show($engine) {
		$clockDone = round(((time() - $this->start) / 60.0) / $this->duration * $engine::$CLOCK_DASH_TOTAL);
		$engine::show('activeVoucher', array(
			'voucherCode'   => $this->formattedVoucherCode(),
			'voucherLength' => $this->min2hm($this->duration),
			'clockDash'     => $clockDone.' '.($engine::$CLOCK_DASH_TOTAL-$clockDone),
			'secRemaining'  => $this->getSecRemaining(),
			'voucherId'     => $this->voucher_id
		));
	}
	
	public function getType() {
		return 'active';
	}
	
	public function getSecRemaining() {
		return $this->end - time();
	}
}

class OpenVoucher extends Voucher {

	private $voucher_json;
	
	public function __construct($voucher_json) {
		$this->duration = $voucher_json->duration;
		$this->voucher_code = $voucher_json->code;
		$this->voucher_id = $voucher_json->_id;
		
		$this->voucher_json = $voucher_json;
	}
	
	public function show($engine) {
		$engine::show('openVoucher', array(
			'voucherCode'   => $this->formattedVoucherCode(),
			'voucherLength' => $this->min2hm($this->duration),
			'voucherId'     => $this->voucher_id
		));
	}
	
	public function getType() {
		return 'open';
	}
	
	public function getSecRemaining() {
		return $this->duration * 60;
	}
}

class TempVoucher extends Voucher {

	private $voucher_json;
	private $start;
	private $end;
	
	public function __construct($voucher_json, $expires) {
		$this->duration = $voucher_json->duration;
		$this->voucher_code = $voucher_json->code;
		$this->voucher_id = $voucher_json->_id;
		$this->start = $voucher_json->create_time;
		$this->end = $expires;
		
		$this->voucher_json = $voucher_json;
	}
	
	public function show($engine) {
		$clockDone = round((time() - $this->start) / ($this->end - $this->start) * $engine::$CLOCK_DASH_TOTAL);
		$engine::show('tempVoucher', array(
			'voucherCode'   => $this->formattedVoucherCode(),
			'voucherLength' => $this->min2hm($this->duration),
			'clockDash'     => $clockDone.' '.($engine::$CLOCK_DASH_TOTAL-$clockDone),
			'secRemaining'  => $this->getSecRemaining(),
			'voucherId'     => $this->voucher_id
		));
	}
	
	public function getType() {
		return 'temp';
	}
	
	public function getSecRemaining() {
		return max(0, $this->end - time()); // * 60;
	}
}

?>