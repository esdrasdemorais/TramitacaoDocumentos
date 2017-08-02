<?php
/**
 * O objetivo deste arquivo é determinar qual o sistema operacional do servidor web
 * e, com essa informação, configurar o caminho de busca do interpretador PHP.
 * @author Flávio Gomes da Silva Lisboa
 * @filesource 
 */
define('WINDOWS','WINDOWS');
define('LINUX','LINUX');

function configurePath($applicationName)
{
	#set_include_path('.' . PATH_SEPARATOR . './library' . PATH_SEPARATOR . './application/models/' . PATH_SEPARATOR.get_include_path());
	/*
	$operatingSystem = stripos($_SERVER['SERVER_SOFTWARE'],'win32')!== FALSE ? 'WINDOWS' : 'LINUX';
	$bar 			 = ($operatingSystem == 'WINDOWS') ? '\\' : '/';
	$pathSeparator   = ($operatingSystem == 'WINDOWS') ? ';' : ':';
	$documentRoot    = ($operatingSystem == 'WINDOWS') ? str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']) : $_SERVER['DOCUMENT_ROOT'];
	*/
	/**
	 * Seta o path separando os paths por PATH_SEPARATOR ou :
	 * $path = ':'.$pathSeparator.$documentRoot.$bar.'teste'.$bar.'library';
	 * $path+= ':'.$pathSeparator.$documentRoot.$bar.'teste'.$bar.'application'.$bar.'models';
	 */
	
	$documentRoot = $_SERVER['DOCUMENT_ROOT'];
	#echo "<!--".$documentRoot."-->";
	// o teste abaixo irá variar dependendo das opções de sistema disponíveis
	//$operatingSystem = strpos('WIN32',strtoupper($_SERVER['SERVER_SOFTWARE'])) === FALSE ? LINUX : WINDOWS;
	//$operatingSystem = stripos($_SERVER['SERVER_SOFTWARE'], 'win32') !== FALSE ? 'WINDOWS' : 'LINUX';	
	/*
    	 *@Author:Esdras
	 *@Date:23/04/2012
	 *@Describe:Alterada Variável Global do Servidor para obter SO do Web Server (SERVER_SOFTWARE ou SystemRoot), não do cliente
  	 */
	#echo "<!--";print_r($_SERVER)."-->";
	//$operatingSystem = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') === FALSE ? LINUX : WINDOWS;
	$operatingSystem = (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'win') === FALSE || strpos(strtolower($_SERVER['SystemRoot']), 'windows') === FALSE) ? 
				LINUX : 
				WINDOWS;

	#echo "<!--".$operatingSystem."-->";
	// configuração padrão: sistema de arquivos do UNIX
	$bar 			= '/';
	$pathSeparator  = ':';
	
	if($operatingSystem == WINDOWS)
	{
		$bar = '\\';
		$pathSeparator = ';';
		$documentRoot = str_replace('/','\\',$documentRoot);
	}
	
	$path = $pathSeparator.$documentRoot.$bar.$applicationName.$bar.'library';
	$path.= $pathSeparator.$documentRoot.$bar.$applicationName.$bar.'application'.$bar.'models';
	$path.= $pathSeparator.$documentRoot.$bar.$applicationName.$bar.'application'.$bar.'utils';
	$path.= $pathSeparator.$documentRoot.$bar.$applicationName.$bar.'application'.$bar.'services';
	#echo "<!--".$path."-->";
	set_include_path(get_include_path().$path);
#echo "<!--".$path."-->";
/*require ("Soaptest.php");
$sp = new Soaptest();
echo "<pre>";
print_r($sp);
echo "</pre>";*/
}
?>
