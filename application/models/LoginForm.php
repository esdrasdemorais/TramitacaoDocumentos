<?php
/**
 * Classe modelo de formulário de login
 */
class LoginForm extends Zend_Form
{
    public function init()
    {
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		
		$this->addDecorator("HtmlTag", array('tag' => "div", 'class' => 'zend_form_login')); //With this you are wrapping your form with a div instead of dl
		
		$usLogin = $this->createElement('text', 'us_login', array(
			'filters'    => array('StringTrim'),
			'validators' => array(
				'Alpha',
				array('StringLength', false, array(3, 18)),
			),
			'required'   => true,
			'label'      => 'Informe seu nome de usário:'
		));
		/** Caso for edição de senha disabilita o login */
		if(strpos($this->getAction(), 'edit') !== false)
		{
			$usLogin->setAttrib('readonly','readonly');
		}
		$this->addElement($usLogin);
		
        $password = $this->addElement('password', 'us_senha', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(4, 20)),
            ),
            'required'   => true,
            'label'      => 'Informe sua senha de acesso:'
        ));
		
		/** Caso for edição de senha exibe cria os campos de nova senha e confirmação de senha */
		if(strpos($this->getAction(), 'edit') !== false)
		{	
			$newPassword = $this->addElement('password', 'us_nova_senha', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(4, 20)),
            ),
            'required'   => true,
            'label'      => 'Nova Senha:'
			));
			
			$newPassword = $this->addElement('password', 'us_confirma_nova_senha', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(4, 20)),
            ),
            'required'   => true,
            'label'      => 'Corfirmação Senha:'
			));
		}
		
		// Botão de envio do formuláo
        $submit = $this->createElement('submit', 'send', array(
            'required' => true,
            'ignore'   => true,
            'label'    => 'Entrar'
        ));
        $submit->removeDecorator('Label');
		$submit->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
        $submit->setDecorators(
		array(
			array("decorator" => "ViewHelper"), //This one is required...
            array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "div", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($submit);
		
		/** Caso for edição de senha cria o botão de cancelamento da ação */
		if(strpos($this->getAction(), 'edit') !== false)
		{
			// Botão de cancelamento da ação
			$cancel = $this->createElement('button', 'cancel', array(
				'required' => false,
				'ignore'   => true,
				'label'    => 'Cancelar',
				'attribs' => array(
					#'onclick' => "window.location.href='".$view->baseUrl.'/'.$view->controllerName."'"
					'onclick' => "window.location.href='/tramitacao_documentos'"
				)
			));
			$cancel->removeDecorator('Label');
			$cancel->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
			$cancel->setDecorators(array(
				array("decorator" => "ViewHelper"), //This one is required...
				array("decorator" =>"HtmlTag", "options" => 
				array('tag' => "div", "class" =>"formbutton")))
			); //Beware that I can set the attributes of the span element via options...
			$this->addElement($cancel);
		}
			
        /**
         * Esse método serve para exibir mensagens de erro no submit do formulário
         */
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form_login')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
}