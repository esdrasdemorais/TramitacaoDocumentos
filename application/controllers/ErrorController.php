<?php
/**
 * Controlador de Erros
 * @author Flávio Gomes da Silva Lisboa
 * @copyright FGSL 2008
 * @license New SDB
 * @package application
 * @subpackage controllers
 * @filesource 
 */
class ErrorController extends Zend_Controller_Action 
{
	/**
	 * Método default
	 * Code Sample from http://framework.zend.com/docs/quickstart/create-your-project
	*/
	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');
        
        switch ($errors->type)
		{
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
		}
		
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
	}
}
?>