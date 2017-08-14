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
		
		if ($db->user_id == 469) // Debug
			return new DefaultUser();
		
		if ($db->deleted || $db->user_id === null)
			return new DeletedUser();
		
		if ($db->restricted)
			return new RestrictedUser();
			
		// can give unlimited short vouchers (eg. dagcafe)
		if ($db->special_group == 5)
			return new CommissionUser();

		// can give longer vouchers than others (eg. bar)
                if ($db->special_group == 9)
			return new PrivilegedUser();
		
		// can give unlimited vouchers
		if ($db->special_group == 3)
			return new AdminUser();
		
		return new DefaultUser();
	}
	
	public function get_user_id() {
		return $this->user_id;
	}
	
	abstract public function max_minutes();
	
	abstract public function max_length();
	
	abstract public function max_quantity();
	
	abstract public function keep_history();
}

class DefaultUser extends User {
	
	public function max_minutes() { return DEFAULT_MAX_MINUTES; }

	public function max_length() { return DEFAULT_MAX_LENGTH; }

	public function max_quantity() { return DEFAULT_MAX_QUANTITY; }
	
	public function keep_history() { return DEFAULT_PERIOD; }
}

class AdminUser extends User {
	
	public function max_minutes() { return ADMIN_MAX_MINUTES; }

	public function max_length() { return ADMIN_MAX_LENGTH; }

	public function max_quantity() { return ADMIN_MAX_QUANTITY; }
	
	public function keep_history() { return ADMIN_PERIOD; }
}

class CommissionUser extends User {
	
	public function max_minutes() {	return COMMISSION_MAX_MINUTES; }

	public function max_length() { return COMMISSION_MAX_LENGTH; }

	public function max_quantity() { return COMMISSION_MAX_QUANTITY; }
	
	public function keep_history() { return COMMISSION_PERIOD; }
}

class RestrictedUser extends User {
	
	public function max_minutes() {	return RESTRICTED_MAX_MINUTES; }

	public function max_length() { return RESTRICTED_MAX_LENGTH; }

	public function max_quantity() { return RESTRICTED_MAX_QUANTITY; }
	
	public function keep_history() { return RESTRICTED_PERIOD; }
}

class PrivilegedUser extends User {
	
	public function max_minutes() {	return PRIVILEGED_MAX_MINUTES; }

	public function max_length() { return PRIVILEGED_MAX_LENGTH; }

	public function max_quantity() { return PRIVILEGED_MAX_QUANTITY; }
	
	public function keep_history() { return PRIVILEGED_PERIOD; }
}

class DeletedUser extends User {
	
	public function max_minutes() {	return 0; }

	public function max_length() { return 0; }

	public function max_quantity() { return 0; }
	
	public function keep_history() { return '0'; }
}
?>
