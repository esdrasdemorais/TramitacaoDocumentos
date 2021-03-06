<?php
class GatewayController extends Zend_Controller_Action
{
 
    public function init()
	{		
		//Adiciona o autoloader do Zend Framework
		require_once "Zend/Loader/Autoloader.php";
		Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
		
		//linha adicionada para evitar que o layout seja exibido
        //$this->_helper->layout()->disableLayout();
        
		//remove a renderiza��o do layout da p�gina
        $this->getHelper('viewRenderer')->setNoRender();
        //carrega a classe
        Zend_Loader::loadClass('Zend_Amf_Server');
		
    }
 
    public function amfAction()
	{
        //criamos a instancia do componente Zend AMF
        $server = new Zend_Amf_Server();
		
		$server->addDirectory(APPLICATION_PATH . '/application/services/');
        echo(trim($server->handle()));
    }
 
}