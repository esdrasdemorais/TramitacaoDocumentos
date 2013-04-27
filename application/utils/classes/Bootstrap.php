<?php
require('Zend/Loader.php');
/**
 * Classe de inicialização
 *
 */
class Bootstrap
{
	/**
	 * O construtor deve configurar o framework
	 * e iniciar o controlador frontal
	 *
	 * @param string $baseURl
	 */
	public function __construct($baseURl)
	{
		// Carrega os componentes do ZF que serão utilizados pela aplicaãão
		Zend_Loader::loadClass('Zend_Controller_Front');
		Zend_Loader::loadClass('Zend_Controller_Router_Route');		
		
		$frontController = Zend_Controller_Front::getInstance();
		
		$frontController->setBaseUrl($baseUrl);
		
		$frontController->setControllerDirectory('./application/controllers');
		
		// Altera o sufixo dos templates
		//$this->changeViewSuffix('phd');		
		
		// Cria uma nova rota
		
		//$defaults = array(
		//					'controller' => 'news',
		//					'action'	=> 'view'
		//);
		//$requirements = array(
		//					'id' => '\d+' // permite somente 1 ou mais dígitos
		//); 
		//$route = $this->createRoute('news/view/:id,$defaults,$requirements);
		//$this->addNewRoute($frontController,$route,$name);		
		
		$frontController->dispatch();
		/*
		Os dois métodos acima podem ser substituídos por: 
		$frontController->run($controllerDirectory);
		*/
	}
	
	/**
	 * Altera o sufixo dos templates
	 *
	 * @param string $suffix
	 */
	private function changeViewSuffix($suffix)
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
		$viewRenderer->setViewSuffix('phd'); 
		
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);		
	}

	/**
	 * Cria um novo objeto roteador
	 *
	 * @param string $expression
	 * @param array $defaults
	 * @param array $requirement
	 * @return Zend_Controller_Router_Route
	 */
	private function createRoute($expression,array $defaults,array $requirement)
	{
		return new Zend_Controller_Router_Route($expression,$defaults,$requirement);			
	}
	
	/**
	 * Adiciona uma nova rota a um controlador frontal
	 *
	 * @param Zend_Controller_Front $frontController
	 * @param Zend_Controller_Router_Route $route
	 * @param string $name
	 */
	private function addNewRoute($frontController,$route,$name)
	{		
		// Zend_Controller_Router_Rewrite
		$router = $frontController->getRouter();
		
		$router->addRoute($name,$route);
		
		$frontController->setRouter($router);		
	}
}