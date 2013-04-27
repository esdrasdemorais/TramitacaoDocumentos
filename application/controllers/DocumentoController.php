<?php
/**
 * Exemplo de Controlador de Página
 * @author Flávio Gomes da Silva Lisboa
 * @copyright FGSL 2008
 * @license New SDB
 * @package application
 * @subpackage controllers
 * @filesource 
 */
class DocumentoController extends Zend_Controller_Action 
{
	/**
	 * Inits
	 * 
	 */
	public function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('DocumentoForm');
	}
	
	/**
	 * Método default
	 *
	 */
	public function indexAction()
	{
		$this->view->title = "Cadastro Novo Documento";
		$this->view->form = $this->getForm();
		$this->render();
		#$this->view->welcomeMsg = "Olá " . Zend_Auth::getInstance()->getIdentity(); Zend_Auth
		
		//Zend_Loader::loadClass('DocumentoForm');
		//Zend_Loader::loadClass("Zend_Paginator");
		//Zend_Loader::loadClass("Zend_Paginator_Adapter_Array");
		
		#Zend_Loader::loadClass("Zend_Form");
    	#Zend_Loader::loadClass("SearchAlbumForm");
    	
    	
    	/*$this->view->formSearch = $this->getSearchAlbumForm();    	
    	$this->view->title = "Página de teste de álbuns";
    	
    	$album = new Album();
    	$_where = null;
    	
    	if ( $this->_request->isPost() )
    	{
    		$_where	= $this->ParsePostFilter();
    	}*/
    	
    	/*
    	echo "where = " . $_where;
    	exit();
    	*/
    	
    	/*$this->view->albuns = $album->fetchAll($_where);
    	
    	Zend_Loader::loadClass("Zend_Paginator");
    	Zend_Loader::loadClass("Zend_Paginator_Adapter_Array");
    	    
    	$paginator = Zend_Paginator::factory($this->view->albuns);
    	$paginator->setItemCountPerPage(1);
    	$paginator->getItemsByPage(2);
    	$paginator->setCurrentPageNumber($this->_getParam("page"));
    	
    	Zend_Paginator::setDefaultScrollingStyle('Sliding');
    	$this->view->albuns = $paginator;
    	$this->view->paginator = $paginator;*/
	}

	public function addAction()
	{
		Zend_Loader::loadClass('Documento');
		
		$this->view->title = "Salvar Novo Documento";
		$request = $this->getRequest();
	
        // Check if we have a POST request
        if(!$request->isPost())
		{
            return $this->_helper->redirector($this->baseUrl.'/index');
        }

        // Get our form and validate it
        $form = $this->getForm();
        if(!$form->isValid($request->getPost()))
		{
            // Invalid entries
            $this->view->form = $form;
		    return $this->render('index'); // re-render the documento form
        }
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			
			$objFilter  		= new Zend_Filter_StripTags();
			$td_id 				= (int) trim($objFilter->filter($this->_request->getPost('td_id')));
			$as_id 				= (int) trim($objFilter->filter($this->_request->getPost('as_id')));
			$dc_compl_assunto 	= (string) trim($objFilter->filter($this->_request->getPost('dc_compl_assunto')));
			$oe_id 				= (int) trim($objFilter->filter($this->_request->getPost('oe_id')));
			#$dc_data_elaboracao = (string) trim($objFilter->filter($this->_request->getPost('dc_data_elaboracao')));
			
			if (($td_id > 0) && ($as_id > 0))// && strlen($dc_data_elaboracao))
			{
				$dc_compl_assunto 	= (strlen($dc_compl_assunto)) ? $dc_compl_assunto : null;
				$oe_id 			  	= ($oe_id > 0) ? $oe_id : null;
				$dc_data_elaboracao = new Zend_Db_Expr('NOW()');
				
				$arrData = array(
				'td_id' => $td_id,
				'as_id' => $as_id,
				'dc_data_elaboracao' => $dc_data_elaboracao,
				'dc_compl_assunto' => $dc_compl_assunto,
				'oe_id' => $oe_id
				);
				
				$objDocumento = new Documento();
				$objDocumento->insert($arrData);
				
				$this->_redirect('/');
				return;
			}
			else
			{
				$this->view->detail = "Informe todos os campos obrigatÃ³rios.";
			}
		}
		
		// set up an "empty" album
		$this->view->documento = new stdClass();
		$this->view->documento->td_id = null;
		$this->view->documento->as_id = null;
		$this->view->documento->dc_compl_assunto = '';
		
		// additional view fields required by form
		$this->view->action = 'add';
		$this->view->buttonText = 'Add';
	}
	
	public function editAction()
	{
		$this->view->title = "Atualizar Documento";
		$request = $this->getRequest();
		
        // Check if we have a POST request
        if(!$request->isPost())
		{
            return $this->_helper->redirector($this->baseUrl.'/index/'.$this->action);
        }

        // Get our form and validate it
        $form = $this->getForm();
        if(!$form->isValid($request->getPost()))
		{
            // Invalid entries
            $this->view->form = $form;
		    return $this->render('index'); // re-render the documento form
        }
	}
	
	public function deleteAction()
	{
	}
	
	/**
     * Outros métodos
     * 
     */    
    public function getForm()
    {
        return new DocumentoForm(
        	array(
            	'action' => $this->view->baseUrl . '/documento/add',
            	'method' => 'post'
        	)
        );
    }
	
    public function getSearchDocumentoForm()
    {
        return new SearchDocumentoForm(
        	array(
            	'action' => $this->view->baseUrl . '/album/',
            	'method' => 'post',
        	)
        );
    }
    
    private function parsePostFilter()
    {
    	if ( $this->_request->isPost() )
    	{
    		return "id>3";
    		/*
    		Zend_Loader::loadClass('Zend_Filter_StripTags');
    		$filter = new Zend_Filter_StripTags();
    		
    		$id = (int)$this->_request->getPost('id');
    		$artist = $filter->filter($this->_request->getPost('artist'));
    		$artist = trim($artist);
    		$title = $filter->filter($this->_request->getPost('title'));
    		$title = trim($title);    		
    		
    		if ($id !== false) 
    		{
    		    if ( $artist != '' && $title != '' )
	    		{
	    			$data = array(
	    				'artist' => $artist,
	    				'title' => $title
	    			);
	    			
	    			$where = 'id = ' . $id;
	    			
	    			$album->update($data, $where);
	    			
	    			$this->_redirect('/index/edit/id/' . $id);
	    			return;
	    		}
	    		else 
	    		{
	    			$this->view->album = $album->fetchRow('id = ' . $id);
	    		}
    		}
    		*/
    		echo "Is post";
    	}
		else 
		{
			/*
			$id = $this->_request->getParam('id', 0);
			if ($id > 0) {
				echo "vish... funciona mesmo e o id é " . $id;
			}
			*/
			return "";
		}
    }
}
?>