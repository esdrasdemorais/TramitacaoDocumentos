<?php
/**
 * Classe modelo de formulário de confirmação de exclusão
 */
class DeleteConfirmationForm extends Zend_Form
{
    public function init()
    {
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		Zend_Loader::loadClass('Zend_Form_Decorator_HtmlTag');
		
		// Set the decorators needly
        /*$this->setElementDecorators(array(
            'ViewHelper',
            'Label', //
            'Error', //Deseja realmente excluir este registro?
            new Zend_Form_Decorator_HtmlTag(array('tag' => 'li')) //wrap elements in <li>'s
        ));*/
		
		// Botão de confirmação de exclusão
        $salvar = $this->addElement('submit', 'delete0', array(
            'required' => true,
            'ignore'   => true,
            'label'    => 'Sim',
			'value'	   => 'Sim',
			'name'	   => 'delete'
        ));
        
		// Botão de cancelamento de exclusão
        $salvar = $this->addElement('submit', 'delete1', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Não',
			'value'	   => 'Não',
			'name'	   => 'delete'
        ));
		
		/*
		// Botão de Confirmação de exclusão
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