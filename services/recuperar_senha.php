<?php
session_start()
require_once: '../config/db.php';
require_once: '../models/usuario.php';

$_REQUEST['email'];

$user = new Usuario();
if ($user->verifyEmail($_REQUEST['email']

?>
