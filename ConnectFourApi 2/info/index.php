<?php
require_once '../play/json_handle.php';
$JSON_HANDLE = new JsonHandle();
echo json_encode(new Info());
?>
