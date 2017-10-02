
<?php
class Strategy{
	public $pid;
	public $response;
	private $strategy;
	public function __construct($pid, $strategy){
		$this->pid = $pid;
		$this->strategy = $strategy;
		$this->response = true;
	}
}
require_once '../play/board.php';
require_once '../play/json_handle.php';
$NEW_STRATEGY = $_GET['strategy'];
$NEW_PID_KEY = 1546849651523584;
$NEW_PID_RANGE  = 4294967295;
$NEW_PID_SESS = dechex($NEW_PID_KEY+rand(0, $NEW_PID_RANGE));
/*
$NEW_FILE = fopen("../writable/$NEW_PID_SESS", "w");
fputs($NEW_FILE, json_encode(new Grid()));
fclose($NEW_FILE);*/
$JSON_HANDLE = new JsonHandle();
$JSON_HANDLE->encodeToFile($NEW_PID_SESS, new Grid($NEW_STRATEGY));
//echo json_encode(new Strategy($NEW_PID_SESS, $NEW_STRATEGY));
echo $JSON_HANDLE->getEncoding(new Strategy($NEW_PID_SESS, $NEW_STRATEGY));
?>
