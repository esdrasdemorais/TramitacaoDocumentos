<?php
/**
 * Classe modelo de formulário de confirmação de edição
 */
class EditConfirmationForm extends Zend_Form
{
    public function init()
    {
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		Zend_Loader::loadClass('Zend_Form_Decorator_HtmlTag');
		
		// Set the decorators needly
        /*$this->setElementDecorators(array(
            'ViewHelper',
            'Label', //
            'Error', //Deseja realmente editar este registro?
            new Zend_Form_Decorator_HtmlTag(array('tag' => 'li')) //wrap elements in <li>'s
        ));*/
		
		// Botãde confirmação de exclusão
        $salvar = $this->addElement('submit', 'edit0', array(
            'required' => true,
            'ignore'   => true,
            'label'    => 'Sim',
			'value'	   => 'Sim',
			'name'	   => 'edit'
        ));
        
		// Botão de cancelamento de exclusão
        $salvar = $this->addElement('submit', 'edit1', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Não',
			'value'	   => 'Não',
			'name'	   => 'edit'
        ));
		
		/*	
		// Botão de Confirmação da edição
		$sim = $this->createElement('submit', 'edit0', array(
			'required' => true,
			'ignore'   => true,
			'label'    => 'Sim',
			'value'	   => 'Sim',
			'name'	   => 'edit'
		));
		$sim->removeDecorator('Label');
		$sim->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
		$sim->setDecorators(
		array(
			array("decorator" => "ViewHelper"), //This one is required...
			array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($sim);
		
		// Botão de cancelamento da exclusão
		$nao = $this->createElement('submit', 'edit1', array(
			'required' => true,
			'ignore'   => true,
			'label'    => 'Não',
			'value'	   => 'Não',
			'name'	   => 'edit'
		));
		$nao->removeDecorator('Label');
		$nao->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
		$nao->setDecorators(array(
		array("decorator" => "ViewHelper"), //This one is required...
			array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($nao);
		*/
		
		/*$tdId = new Zend_Form_Element_Hidden('id');
		if(isset($view->dados))
			$tdId->setValue($view->dados->dc_id);
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