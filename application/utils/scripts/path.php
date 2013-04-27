<?php
/**
 * O objetivo deste arquivo � determinar qual o sistema operacional do servidor web
 * e, com essa informa��o, configurar o caminho de busca do interpretador PHP.
 * @author Fl�vio Gomes da Silva Lisboa
 * @filesource 
 */
define('WINDOWS','WINDOWS');
define('LINUX','LINUX');

function configurePath($applicationName)
{
	$documentRoot = $_SERVER['DOCUMENT_ROOT'];
	// o teste abaixo ir� variar dependendo das op��es de sistema dispon�veis
	$operatingSystem = strpos('WIN32',strtoupper($_SERVER['SERVER_SOFTWARE'])) === FALSE ? LINUX : WINDOWS;

	// configura��o padr�o: sistema de arquivos do UNIX
	$bar = '/';
	$pathSeparator = ':';

	if ($operatingSystem == WINDOWS)
	{
		$bar = '/';
		$pathSeparator = ':';
		$documentRoot = str_replace('/','\\',$documentRoot);
	}
		
	$path = $pathSeparator.$documentRoot.$bar.'library';
	$path .= $pathSeparator.$documentRoot.$bar.$applicationName.$bar.'application'.$bar.'models';
	$path .= $pathSeparator.$documentRoot.$bar.$applicationName.$bar.'application'.$bar.'utils';

	set_include_path(get_include_path().$path);
}
?>