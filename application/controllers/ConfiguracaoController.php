<?php
/**
 * Controlador de Configuração do Sistema
 * @author Esdras
 * @copyright FGSL 2009
 * @license New SDB
 * @package application
 * @subpackage controllers
 * @filesource 
 */
class ConfiguracaoController extends Zend_Controller_Action 
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
		
		Zend_Loader::loadClass('Configuracao');
	}
	
	public function editAction()
	{
		$this->view->title = "Efetuar esta ConfiguraÃ§Ã£o?";
		
		$objConfiguracao = new Configuracao();
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			
			$filter = new Zend_Filter_Alpha();
			$id 	= (int) $this->_request->getPost('id');
			$edit = $filter->filter($this->_request->getPost('edit'));
			
			if(strlen($edit) && $this->view->userTipoId == 1)
			{
				$cf_valor = ($edit == 'Sim') ? '1' : '0';
				
				$arrData = array(
				'cf_valor' => $cf_valor
				);
				
				$where = 'cf_id = ' . $id;
				
				$objConfiguracao->update($arrData, $where);
				#$rowsAffected = $objTramitacao->delete($_where);
			}
		}
		else
		{
			$id = (int) $this->_request->getParam('id');
			if($id > 0)
			{
				$this->view->configuracao = $objConfiguracao->fetchRow('cf_id='.$id);
				if($this->view->configuracao && $this->view->configuracao->cf_id > 0)
				{
					Zend_Loader::loadClass('EditConfirmationForm');
					
					$this->view->form = new EditConfirmationForm(
						array(
							'action' => $this->view->baseUrl.'/configuracao/edit',
							'method' => 'post'
						)
					);
					
					// additional view fields required by form
					$cfId = new Zend_Form_Element_Hidden('id');
					$cfId->setValue($this->view->configuracao->cf_id);
					$this->view->form->addElement($cfId);
					
					return $this->render();
				}
			}
		}
		
		// redireciona à listagem novamente
		return $this->_helper->redirector('index', 'index');
		#$this->_redirect('list');
	}
}