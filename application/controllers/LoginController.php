<?php
/**
 * Controlador de Login
 * @author Esdras
 * @copyright FGSL 2009
 * @license New SDB
 * @package application
 * @subpackage controllers
 * @filesource
 */
class LoginController extends Zend_Controller_Action
{
	/**
	 * Inits
	 * 
	 */
	public function init()
	{
		$this->initView();
		$this->view->baseUrl 		= $this->_request->getBaseUrl();
		$this->view->actionName 	= $this->getRequest()->getActionName();
		$this->view->controllerName	= $this->getRequest()->getControllerName();
		if(Zend_Auth::getInstance() && Zend_Auth::getInstance()->hasIdentity() && Zend_Auth::getInstance()->hasIdentity())
		{
			$this->view->userLogin	= @Zend_Auth::getInstance()->getIdentity()->us_login;
			$this->view->userUnitId	= @Zend_Auth::getInstance()->getIdentity()->un_id;
			$this->view->userUnit 	= @Zend_Auth::getInstance()->getIdentity()->un_descricao;
			$this->view->userId 	= @Zend_Auth::getInstance()->getIdentity()->us_id;
			$this->view->userTipoId = @Zend_Auth::getInstance()->getIdentity()->tu_id;
		}
		
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
		#Sem cookie ativo não loga
		#echo (Zend_Auth::getInstance()->hasIdentity() ? "ok" : "nok");
        if(Zend_Auth::getInstance()->hasIdentity()) 
        {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if($this->getRequest()->getActionName() != 'logout' && $this->getRequest()->getActionName() != 'edit')
            {		
            	$this->_helper->redirector('index', 'index');
            }
        }
        else
        {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if($this->getRequest()->getActionName() == 'logout')
            {
                $this->_helper->redirector('index');
            }
        }
    }
    
    /**
     * Actions
     */
    public function indexAction()
    {
		$this->view->detail = "in LoginController::indexAction() at " . $this->view->baseUrl;
        $this->view->form  = $this->getForm();
		$this->view->title = "Login";
		$this->render();
    }
    
	public function processAction()
    {
		#Zend_Loader::loadClass('Zend_Auth_Storage_Session');
		Zend_Loader::loadClass('Unidade');
	
		$this->view->title = "Login";
	
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost())
		{
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->getForm();

        if (!$form->isValid($request->getPost()))
		{
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
		
        if(!$result->isValid())
		{
            // Invalid credentials
            $form->setDescription('Login ou senha inválidos'); // Invalid credentials provided
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }
		
		/** success : store database row to auth's storage system (not the password though!) */
		$data = $adapter->getResultRowObject(null, 'us_senha');
		
		/** Obtém a descrição da Unidade */
		$objUnidade 		= new Unidade();
		$_whereUnidade 		= 'un_id = '.$data->un_id;
		$resUnidade 		= $objUnidade->fetchRow($_whereUnidade);
		$data->un_id		= $resUnidade->un_id;
		$data->un_descricao = $resUnidade->un_descricao;
		
		$auth->getStorage()->write($data);
        
        // We're authenticated! Redirect to the home page  //$this->_redirect('/');
        $this->_helper->redirector('index', 'index');
    }
    
	public function editAction()
	{
		Zend_Loader::loadClass('Usuario');
		Zend_Loader::loadClass('LoginForm');
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		
		$this->view->title = "Alterar Login";
		$usuario = new Usuario();
		
		// Get our form
        #$form = $this->getForm();
		$form = $this->getForm('edit'); #$this->view->form->setAction($this->view->baseUrl.'/'.$this->view->controllerName.'/edit');
		// additional view fields required by form	
		$objButton = $form->getElement('send');
		$objButton->setValue('Atualizar');
		
		$filter 	= new Zend_Filter_StripTags();
		$us_id 		= (int) $this->view->userId; // (int) $this->_request->getPost('us_id');
		
		$this->view->usuario = $usuario->fetchRow('us_id = '.$us_id);
		
		if(isset($this->view->usuario))
		{
			$form->setDefault('us_login', $this->view->usuario->us_login);
			
			$usId = new Zend_Form_Element_Hidden('us_id');
			$usId->setValue($this->view->usuario->us_id);
			$usId->removeDecorator('label');
			$usId->removeDecorator('HtmlTag');
			$form->addElement($usId);
		}
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			$request = $this->getRequest();
			// Check if we have a POST request
			if (!$request->isPost())
			{
				return $this->_helper->redirector('index');
			}
		
		print_r($request->getPost());
		
			// Validate form
			if(!$form->isValid($request->getPost()))
			{
				// Invalid entries
				$this->view->form = $form;
				return $this->render('index'); // re-render the login form
			}
			
			$us_login 				= (string) trim($filter->filter($this->_request->getPost('us_login')));
			$us_senha				= (string) trim($filter->filter($this->_request->getPost('us_senha')));
			$us_nova_senha			= (string) trim($filter->filter($this->_request->getPost('us_nova_senha')));
			$us_confirma_nova_senha = (string) trim($filter->filter($this->_request->getPost('us_confirma_nova_senha')));
			
			if($us_id > 0 && strlen($us_login) && strlen($us_senha) && strlen($us_nova_senha) && strlen($us_confirma_nova_senha))
			{
				//[us_login] => admin [us_senha] => 1234567
				$arrData = array(
				'us_login' => $us_login,
				'us_senha' => $us_senha
				);
				
		        // Get our authentication adapter and check credentials
				$adapter = $this->getAuthAdapter($arrData);
				$auth    = Zend_Auth::getInstance();
				$result  = $auth->authenticate($adapter);
				
				if(!$result->isValid())
				{
					// Invalid credentials
					$form->setDescription('Login ou senha inválidos'); // Invalid credentials provided
					#$this->view->form = $form;
					#return $this->render('index'); // re-render the login form
				}
				elseif($us_nova_senha != $us_confirma_nova_senha)
				{
					$form->setDescription('A nova senha e a confirmação dela não são iguais.'); // Invalid new or confirm password provided	
				}
				else
				{
					$arrData['us_senha'] = $us_nova_senha;
					
					$whereUsuario = 'us_id = '.$us_id;
					$usuario->update($arrData, $whereUsuario);
					
					// Efetua logoff
					$this->logoutAction();
					#$this->_redirect('/');
					return;
				}
			}
		}
		else
		{
			// usuario id should be $params['id']
			#$us_id = (int) $this->_request->getParam('id',0);
			$us_id = (int) $this->view->userId;
			if($us_id > 0)
			{
				$this->view->usuario = $usuario->fetchRow('us_id = '.$us_id);
			}
		}
	
		$this->view->form = $form;
		
		$this->render('index'); // render login form
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
    public function getForm($type=null)
    {
		$action = ($type == 'edit') ? 'edit' : 'process';
        return new LoginForm(
        	array(
            	'action' => $this->view->baseUrl . '/login/'.$action, //$this->view->baseUrl.'/'.$this->view->controllerName.'/process');
            	'method' => 'post'
        	)
        );
    }

    public function getAuthAdapter(array $params)
    {
    	/**
    	 * Carregando configurações do arquivo de configuração
    	 */
		$objConfig   = new Zend_Config_Ini('./application/config.ini','database');
        $dbAdapter   = Zend_Db::factory($objConfig->db->adapter, $objConfig->db->config->toArray());
        
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        /**
         * Setando valores para a classe de Autenticação poder realizar a consulta
         */
        $authAdapter->setTableName($objConfig->db->auth->tableName); // Nome da tabela de usuários
		
        $authAdapter->setIdentityColumn($objConfig->db->auth->identityColumn); // Campo na tabela com o login do usuário
        $authAdapter->setCredentialColumn($objConfig->db->auth->credentialColumn); // Campo na tabela com a senha do usuário
        //$authAdapter->setCredentialTreatment('PASSWORD(?)'); //Configura o método que será aplicado ao campo de senha, médo PASSWORD somente MySQL
		
        $authAdapter->setIdentity($params[$objConfig->db->auth->identityColumn]); // Valor de entrada do usuários
		$authAdapter->setCredential($params[$objConfig->db->auth->credentialColumn]); // Valor de entrada de senha
		
		/*echo "<pre>";
		print_r($authAdapter);
		echo "</pre>";
		exit;*/
		
        return $authAdapter;
    }
}