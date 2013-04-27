<?php
/**
 * Verifica se a versão é igual ou maior à 5.1.4, mínima requerida para Zend Framework
 */  
$version = (int)str_replace('.','',PHP_VERSION);
if ($version < 514)
{
	$message = '';
	$message .= '<center>';
	$message .= '<h1>';	
	$message .= htmlentities('Zend Framework requer no mínimo PHP 5.1.4!');
	$message .= '</h1>';	
	$message .= '</center>';
	echo $message;	
	exit;
}
?>