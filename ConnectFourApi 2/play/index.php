<?php
//TODOFIXSTRATEGY
require_once './board.php';
require_once './json_handle.php';
session_start();
$PLAY_PID = $_GET['pid'];
$PLAY_MOVE = $_GET['move'];
if($PLAY_PID==NULL||$PLAY_MOVE==NULL||$PLAY_MOVE>6){
	$reason = $PLAY_PID==NULL? 'Pid not specefied':'invalid/not specefied move';
	$resonOut = new Invalid($reason);
	echo json_encode($reason);
}
else{
$JSON_HANDLE = new JsonHandle();
$board = new Board($JSON_HANDLE->decodeFromFile($PLAY_PID));
$ack_move = $board->addToken($PLAY_MOVE, 'o', false);
$move = $board->addToken(0,'x',true);
$JSON_HANDLE->encodeToFile($PLAY_PID, $board->getGrid());
echo $JSON_HANDLE->getEncoding(new Response($ack_move, $move));
}
?>