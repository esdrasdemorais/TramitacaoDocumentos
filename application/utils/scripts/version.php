<?php
/**
 * Verifica se a vers�o � igual ou maior � 5.1.4, m�nima requerida para Zend Framework
 */  
$version = (int)str_replace('.','',PHP_VERSION);
if ($version < 514)
{
	$message = '';
	$message .= '<center>';
	$message .= '<h1>';	
	$message .= htmlentities('Zend Framework requer no m�nimo PHP 5.1.4!');
	$message .= '</h1>';	
	$message .= '</center>';
	echo $message;	
	exit;
}
?>