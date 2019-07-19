<?php
// Evita que usuários acesse este arquivo diretamente
if ( ! defined('ABSPATH')) exit;
 
// Inicia a sessão
session_start();

// Verifica o modo para debugar
if ( ! defined('DEBUG') || DEBUG == false ) {

	// Esconde todos os erros
	error_reporting(0);
	ini_set("display_errors", 0); 
	
} else {

	// Mostra todos os erros
	error_reporting(E_ALL);
	ini_set("display_errors", 1); 
	
}

// Configura o email
ini_set("SMTP", '/usr/sbin/sendmail');
ini_set("smtp_port", '587');
ini_set("sendmail_from", 'sistemapegai@conepe.info');

// Funções globais

require_once ABSPATH . '/functions/global-functions.php';

//if (!isset($_POST['usuario'])) {
	//require_once ABSPATH . '/views/login/login-view.php';
//}
//else
//{
// Carrega a aplicação
$conepe_app = new ConepeApp();
//}
?>

