<?php
/**
 * Classe modelo de formulário de Documentos
 */
class TipoDocumentoForm extends Zend_Form
{
    public function init()
    {
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		
		// Complemento do assunto do documento
        $dc_compl_assunto = $this->addElement('text', 'td_descricao', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'StringLength',
                array('StringLength', false, array(3, 150)),
            ),
            'required'   => true,
            'label'      => "* Descrição:",
			'size'		 => 70
        ));
		
		// Botão de envio do formulário
        $submit = $this->createElement('submit', 'save', array(
            'required' => true,
            'ignore'   => true,
            'label'    => 'Salvar'
        ));
        $submit->removeDecorator('Label');
		$submit->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
        $submit->setDecorators(
		array(
			array("decorator" => "ViewHelper"), //This one is required...
            array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($submit);
		
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
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($cancel);
 
		/*$tdId = new Zend_Form_Element_Hidden('td_id');
		if(isset($view->dados))
			$tdId->setValue($view->dados->dc_id);
		$this->addElements(array($dc_id, $artista, $titulo, $submit));
		$this->addElement($tdId);*/
		
        /**
         * Esse método serve para exibir mensagens de erro no submit do formulário
         */
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
}