<?php
class LoginController extends Zend_Controller_Action
{
	/**
	 * Inits
	 * 
	 */
	public function init()
	{
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->initView();
		
		Zend_Loader::loadClass('Zend_Config_Ini');
		Zend_Loader::loadClass('Zend_Registry');
		Zend_Loader::loadClass('Zend_Db');
		Zend_Loader::loadClass('Zend_Db_Table');
		
		Zend_Loader::loadClass('Zend_Auth');
		Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
	}
	
	/**
     * Esse método é chamado antes das actions (indexAction)
     * 
     */
    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) 
        {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ( $this->getRequest()->getActionName() != 'logout' ) 
            {
            	$this->_helper->redirector('index', 'index');
            }
        }
        else 
        {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if ( $this->getRequest()->getActionName() == 'logout' ) 
            {
                $this->_helper->redirector('index');
            }
        }
    }
    
    /**x
     * Actions
     */
    public function indexAction()
    {
        $this->view->form = $this->getForm();
		$this->render();
    }
    
	public function processAction()
    {
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->getForm();

        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($form->getValues());
        $auth    = Zend_Auth::getInstance();
		$result  = $auth->authenticate($adapter);
			
		#echo "<pre>";print_r($adapter);echo "</pre>";
		#echo "<pre>";print_r($auth);echo "</pre>";
		#exit();
		
        /*echo "<pre>";
        $auth->authenticate($adapter);
		print_r($form->getValues());
		echo "</pre>";*/
		
		/*assert($request instanceof Zend_Controller_Request_Http);
		assert($response instanceof Zend_Controller_Response_Http);*;
		$adapter->setRequest($request);
		$adapter->setResponse($response);
		$result = $adapter->authenticate();*/
		
		
        if (!$result->isValid())
		{
			
			print_r($this->view->form);exit;
		
            // Invalid credentials
            $form->setDescription('Login ou senha inválidos'); // Invalid credentials provided
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }
			
        // We're authenticated! Redirect to the home page
        $this->_helper->redirector('index', 'index');
    }
    
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index'); // back to login page
    }
    
    /**
     * Outros métodos
     * 
     */    
    public function getForm()
    {
        return new LoginForm(
        	array(
            	'action' => $this->view->baseUrl . '/login/process',
            	'method' => 'post',
        	)
        );
    }

    public function getAuthAdapter(array $params)
    {
    	/**
    	 * Carregando configurações do arquivo de configuração
    	 */
		$objConfig = new Zend_Config_Ini('./application/config.ini','database');
        $dbAdapter = Zend_Db::factory($objConfig->db->adapter, $objConfig->db->config->toArray());
        $authAdapter =  new Zend_Auth_Adapter_DbTable($dbAdapter);
        
        /**
         * Setando valores para a classe de Autenticação poder realizar a consulta
         */
        $authAdapter->setTableName($objConfig->db->auth->tableName); // Nome da tabela de usuários
		
        $authAdapter->setIdentityColumn($objConfig->db->auth->identityColumn); // Campo na tabela com o login do usuário
        $authAdapter->setCredentialColumn($objConfig->db->auth->credentialColumn); // Campo na tabela com a senha do usuário
        //$authAdapter->setCredentialTreatment('PASSWORD(?)'); //Configura o método que será aplicado ao campo de senha, médo PASSWORD somente MySQL
		
        $authAdapter->setIdentity( $params[$objConfig->db->auth->identityColumn] ); // Valor de entrada do usuários
		$authAdapter->setCredential( $params[$objConfig->db->auth->credentialColumn] ); // Valor de entrada de senha
		
		/*echo "<pre>";
		print_r($authAdapter);
		echo "</pre>";
		exit;*/
		
        return $authAdapter;
    }
}