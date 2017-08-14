<?PHP

class LmsMysql {

	private $server = LMS_MYSQL_SERVER;
	private $username = LMS_MYSQL_USER;
	private $password = LMS_MYSQL_PASS;
	private $database = LMS_MYSQL_DB;

	public $user_id;
	public $special_group;
	public $deleted;
	public $restricted;

	public function __construct() {
		$db = new mysqli($this->server, $this->username, $this->password, $this->database);
		if ($db->connect_errno) {
			  die('could not connect: ' . $db->connect_error());
		}

		$result = $db->query(
			'select m.ownerid, m.warning, c.cutoffstop, c.deleted, a.tariffid
			from vmacs m
			inner join customers c on c.id = m.ownerid 
			left join assignments a on a.customerid = c.id and a.tariffid in (3, 5, 9)
			where m.ipaddr='.ip2long($_SERVER['REMOTE_ADDR']).'
			limit 1');
		$info = $result->fetch_assoc();
		$this->user_id = $info['ownerid'];
		$this->special_group = $info['tariffid'];
		$this->deleted = $info['deleted'];
		$this->restricted = ($info['warning'] == 1 && $info['cutoffstop'] <= time());
	}
	
	public function get_stats($user_ids) {
		$db = new mysqli($this->server, $this->username, $this->password, $this->database);
		if ($db->connect_errno) {
			  die('could not connect: ' . $db->connect_error());
		}
		
		$in = implode(', ', array_map(function($uid) {
			return intval($uid);
		}, $user_ids));

		$result = $db->query(
			'select m.ownerid, m.warning, c.cutoffstop, c.deleted, c.name, c.lastname, min(a.tariffid) tariffid
			from vmacs m
			inner join customers c on c.id = m.ownerid 
			left join assignments a on a.customerid = c.id and a.tariffid in (3, 5)
			where c.id in ('.$in.')
			group by m.ownerid, m.warning, c.deleted, c.name, c.lastname');
		
		$ret = array();
		
		while ($user = $result->fetch_assoc()) {
			$ret[] = $user;
		}
		
		return $ret;
	}
}

?>
