<?PHP
abstract class User {
	
	protected $user_id;
	
	public function __construct() {
		$this->user_id = $_SESSION['user_id'];
		$this->special_group = $_SESSION['special_group'];
		$this->deleted = $_SESSION['deleted'];
		$this->restricted = $_SESSION['restricted'];
	}
	
	public static function get_user($db) {
		if (!isset($_SESSION['user_id'])) {
			$_SESSION['user_id'] = $db->user_id;
			$_SESSION['special_group'] = $db->special_group;
			$_SESSION['deleted'] = $db->deleted;
			$_SESSION['restricted'] = $db->restricted;
		}
		
		if ($db->user_id == 469) // Herman
			return new DefaultUser();
		
		if ($db->deleted || $db->user_id === null)
			return new DeletedUser();
		
		if ($db->restricted)
			return new RestrictedUser();
			
		if ($db->special_group == 5)
			return new CommissionUser();
		
		if ($db->special_group == 3)
			return new AdminUser();
		
		return new DefaultUser();
	}
	
	public function get_user_id() {
		return $this->user_id;
	}
	
	abstract public function max_quantity();
	
	abstract public function max_minutes();
	
	abstract public function keep_history();
}

class DefaultUser extends User {

	public function max_quantity() { return DEFAULT_MAX_QUANTITY; }
	
	public function max_minutes() { return DEFAULT_MAX_MINUTES; }
	
	public function keep_history() { return DEFAULT_PERIOD; }
}

class AdminUser extends User {

	public function max_quantity() { return 50; }
	
	public function max_minutes() { return DEFAULT_MAX_MINUTES; }
	
	public function keep_history() { return '0'; }
}

class CommissionUser extends User {

	public function max_quantity() { return 20; }
	
	public function max_minutes() {	return COMMISSION_MAX_MINUTES; }
	
	public function keep_history() { return '0'; }
}

class RestrictedUser extends User {

	public function max_quantity() { return RESTRICTED_MAX_QUANTITY; }
	
	public function max_minutes() {	return RESTRICTED_MAX_MINUTES; }
	
	public function keep_history() { return RESTRICTED_PERIOD; }
}

class DeletedUser extends User {

	public function max_quantity() { return 0; }
	
	public function max_minutes() {	return 0; }
	
	public function keep_history() { return '0'; }
}
?>