<?php
class SoapController extends Zend_Controller_Action
{
	private $_WSDL_URI = null; //"http://172.16.14.125:82/tramitacao_documentos/soap?wsdl";
	
 	public function init()
    {
    	//$this->_WSDL_URI = 'http://'.$_SERVER['HTTP_HOST'].$this->_request->getBaseUrl().'/'.$this->_request->getControllerName().'/index/wsdl'; // $_SERVER['SCRIPT_NAME'];
    	$this->_WSDL_URI = 'http://'.$_SERVER['HTTP_HOST'].$this->_request->getBaseUrl().'/'.$this->_request->getControllerName().'?wsdl'; // $_SERVER['SCRIPT_NAME'];

    	Zend_Loader::loadClass('Soaptest');
    	Zend_Loader::loadClass('Zend_Soap_AutoDiscover');
    	Zend_Loader::loadClass('Zend_Soap_Server');
    	Zend_Loader::loadClass('Zend_Soap_Client');
    }

    public function indexAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$wsdl = $this->_request->getParam('wsdl');
    	if(isset($wsdl))
    	{
    		//return the WSDL
    		$this->handleWSDL();
		}
		else
		{
			//handle SOAP request
    		$this->handleSOAP();
		}
    }

	private function handleWSDL()
	{
		$autodiscover = new Zend_Soap_AutoDiscover();
    	$autodiscover->setClass('Soaptest');
    	$autodiscover->handle();
	}
    
	private function handleSOAP()
	{
		//$soap = new Zend_Soap_Server($this->_WSDL_URI);
		$option = array(
		'uri' => 'http://'.$this->_request->getHttpHost().$this->_request->getRequestUri()
		);
		$soap = new Zend_Soap_Server(null, $option);
		
    	$soap->setClass('Soaptest');
    	
    	// Bind already initialized object to Soap Server
    	$soap->setObject(new Soaptest());

    	$soap->handle();
	}
    
    public function clientAction()
    {
    	// Compress requests using gzip with compression level 5
    	//$client = new Zend_Soap_Client($this->_WSDL_URI, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5));
    	$client = new Zend_Soap_Client($this->_WSDL_URI, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5));
    	
    	echo $client->math_add(1,1);exit;
    	
    	print_r($client);exit;
    	
    	$this->view->add_result 		= $client->math_add(11, 55);
    	$this->view->logical_not_result = $client->logical_not(true);
    	$this->view->sort_result 		= $client->simple_sort(array("d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple"));	
    }
}