<?php 
	/**
	 * 
	 * Sessão de configuração de paths e erros
	 */
	error_reporting(E_ALL|E_STRICT); // para mostrar os erros, apenas nos testes(precisa estar setado no PHP.ini)// Seta o timezone pra são paulo (>=PHP 5.1)
 	setlocale (LC_ALL, 'pt_BR');
 	date_default_timezone_set('America/Sao_Paulo'); /* Seta include path para o funcionamento correto do framework ***OBRIGATÓRIO*** */
 	
	#set_include_path('.' . PATH_SEPARATOR . './library' . PATH_SEPARATOR . './application/models' . PATH_SEPARATOR.get_include_path());
	
	$operatingSystem = stripos($_SERVER['SERVER_SOFTWARE'],'win32')!== FALSE ? 'WINDOWS' : 'LINUX';
	$bar = $operatingSystem == 'WINDOWS' ? '\\' : '/';
	$pathSeparator = $operatingSystem == 'WINDOWS' ? ';' : ':';
	$documentRoot =  $operatingSystem == 'WINDOWS' ? str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']) : $_SERVER['DOCUMENT_ROOT'];
	
	// Seta o diretório das libs ZEND
	$path = $pathSeparator.$documentRoot.$bar.'teste'.$bar.'library'.PATH_SEPARATOR.'application'.$bar.'models';;
 	set_include_path(get_include_path().$path);
	
	/**
	 * 
	 * Include do Loader do Zend, OBRIGATÓRIO para todas as aplicações
	*/
 	require_once("Zend/Loader.php");
 	
 	/**
 	 * 
 	 * Carregando módulos necessários
	*/
	Zend_Loader::loadClass("Zend_Auth");
	Zend_Loader::loadClass('Zend_Controller_Front');
	Zend_Loader::loadClass('Zend_Form');
	Zend_Loader::loadClass('LoginForm');
	 
	/**
	 * 
	 * Essa classe se encontra em Zend/Controller/Front.php
	 * Pode ser loadado diretamente pelo nome se preferir
	 * Para começar nós precisamos loadar primeiro o front controller
	 * Ele faz um controle automático para detectar a base URL e fazer o redirecionamento correto
	*/ 
	$controlador = Zend_Controller_Front::getInstance(); 
	$controlador->throwExceptions(true); // mostrar excessões(apenas para testes) 
	$controlador->setControllerDirectory('./application/controllers'); // seta diretório com nossos controllers

	/**
	 * 
	 * Inicializando o sistema
	*/
	$controlador->dispatch();