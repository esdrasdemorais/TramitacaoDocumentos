<?php
/**
 * O objetivo deste arquivo щ determinar qual o sistema operacional do servidor web
 * e, com essa informaчуo, configurar o caminho de busca do interpretador PHP.
 * @author Flсvio Gomes da Silva Lisboa
 * @filesource 
 */
define('WINDOWS','WINDOWS');
define('LINUX','LINUX');

function configurePath($applicationName)
{
	$documentRoot = $_SERVER['DOCUMENT_ROOT'];
	// o teste abaixo irс variar dependendo das opчѕes de sistema disponэveis
	$operatingSystem = strpos('WIN32',strtoupper($_SERVER['SERVER_SOFTWARE'])) === FALSE ? LINUX : WINDOWS;

	// configuraчуo padrуo: sistema de arquivos do UNIX
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