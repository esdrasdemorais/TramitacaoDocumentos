<?php
/**
 * Controlador Default
 * @author Flávio Gomes da Silva Lisboa
 * @copyright FGSL 2008
 * @license New SDB
 * @package application
 * @subpackage controllers
 * @filesource 
 */
class IndexController extends Zend_Controller_Action 
{
	/**
	 * Inits
	 * 
	 */
	function init()
	{
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->welcomeMsg = "Ol&aacute; " . Zend_Auth::getInstance()->getIdentity();
		$this->initView();
	}

	/**
	 * Método default
	 *
	 */
	public function indexAction()
	{
		$this->view->title = "PÃ¡gina Inicial";
    	$this->view->detail = "in IndexController::indexAction() at " . $this->view->baseUrl;
		$this->render();
		#echo "<pre>";
		#print_r($this->view);
		#echo "</pre>";
		#exit;
	}
	
	function listuserAction()
    {
    	$this->view->title = "Lista de usuÃ¡rios";
    	$this->view->detail = "in IndexController::listuserAction() at " . $this->view->baseUrl;
		
    	if ( $this->_request->isPost() )
    	{
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
			$id = $this->_request->getParam('id', 0);
			if ($id > 0) {
				echo "vish... funciona mesmo e o id Ã©=" . $id;
			}
		}
    }
}
?>