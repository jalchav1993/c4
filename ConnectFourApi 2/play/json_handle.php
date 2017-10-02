<?php
class Grid{
	public $tokenSet;
	public $topSlots;
	public $nextSlots;
	public $cpuPlayer;
	public $humanPlayer;
	public $strategy;
	public function __construct($strategy){
		$this->topSlots = array('_','_','_','_','_','_','_',);
		$this->tokenSet = $this->buildSet();
		$this->cpuPlayer = 'x';
		$this->humanPlayer = 'o';
		$this->strategy = $strategy;
		$this->nextSlots= array(0,1,2,3,4,5,6);
	}
	public function buildSet(){
		$tokenSet = array();
		for($i = 0; $i <42; $i++){
			$tokenSet[$i]='_';
		}
		return $tokenSet;
	}
}
class Info{
	public $width = 7;
	public $height = 6;
	public $strategies = array("Smart", "Random");
}
class Invalid{
	public $response;
	public $reason;
	public function __construct($reason){
		$this->reason = $reason;
	}
}
class Response{
	public $response;
	public $ack_move;
	public $move;
 	public function __construct($ack_move, $move){
 		$this->response = true;
 		$this->ack_move=$ack_move;
 		$this->move = $move;
 	}
}
class JsonHandle{
	private $FILE_URL = "../writable/";
	public function getEncoding($obj){
		return json_encode($obj);
	}
	public function encodeToFile($pid, $obj){
		$pidFile= fopen("$this->FILE_URL$pid", "w");
		fputs($pidFile, json_encode($obj));
		fclose($pidFile);
	}
	public function decodeFromFile($pid){
		$pidFile= fopen("$this->FILE_URL$pid", "r");
		$state = fread($pidFile, filesize("$this->FILE_URL$pid"));
		fclose($pidFile);
		return json_decode($state);
	}
}
?>