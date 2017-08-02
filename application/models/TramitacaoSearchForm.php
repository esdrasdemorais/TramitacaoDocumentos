<?php
/**
 * Classe modelo de formulário de busca de tramitações
 */
class TramitacaoSearchForm extends Zend_Form
{
    public function init()
    {
		$this->setName('tramitacaoSearchForm');
		
		Zend_Loader::loadClass('Unidade');
		Zend_Loader::loadClass('Zend_Dojo');
		Zend_Loader::loadClass('Zend_Dojo_Form_Element_DateTextBox');
		Zend_Loader::loadClass('Zend_Form_Element_Select');
		
        $dcNumero = $this->addElement('text', 'dc_numero', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'StringLength'
            ),
            'required'   => false,
            'label'      => 'Nº Documento:'
        ));
		
		$unId = new Zend_Form_Element_Select('un_id');
		$unId->setLabel('Unidade:');
        $unId->setRequired(false);
		
		/** Tabela de Unidades da Prefeitura a integrar */
		$tableUnidade = new Unidade();
		$unId->addMultiOption("", "Selecione");
		foreach($tableUnidade->fetchAll() as $tU)
		{
			$unId->addMultiOption($tU->un_id, utf8_encode($tU->un_descricao));
		}
		$this->addElement($unId);
		
		// Data de Tramitação Inicial para Filtro
		$dcDataInicial = new Zend_Dojo_Form_Element_DateTextBox("data_inicial");
        $dcDataInicial->setLabel('Data Inicial:');
        $dcDataInicial->setAttribs(array("dojoType"=>"dijit.form.DateTextBox"));
		$dcDataInicial->setAttrib('constraints', "{datePattern:'dd-MM-yyyy'}");
        $dcDataInicial->setRequired(false);
        $dcDataInicial->addFilter('stringTrim');
		$dcDataInicial->setValue('dd/mm/aaaa');
		$dcDataInicial->setAttrib('onfocus', "this.value = (this.value == 'dd/mm/aaaa') ? '' : this.value");
		$dcDataInicial->setAttrib('onblur', "this.value = (this.value.length > 0) ? this.value : 'dd/mm/aaaa'");
        //$dcDataInicial->addValidator('date');
		$this->addElements(array($dcDataInicial));
		
		// Data de Tramitação Final para Filtro
		$dcDataFinal = new Zend_Dojo_Form_Element_DateTextBox("data_final");
        $dcDataFinal->setLabel('Data Final:');
        $dcDataFinal->setAttribs(array("dojoType"=>"dijit.form.DateTextBox"));
		$dcDataFinal->setAttrib('constraints', "{datePattern:'dd-MM-yyyy'}");
        $dcDataFinal->setRequired(false);
        $dcDataFinal->addFilter('stringTrim');
		$dcDataFinal->setValue('dd/mm/aaaa');
		$dcDataFinal->setAttrib('onfocus', "this.value = (this.value == 'dd/mm/aaaa') ? '' : this.value");
		$dcDataFinal->setAttrib('onblur', "this.value = (this.value.length > 0) ? this.value : 'dd/mm/aaaa'");
        //$dcDataInicial->addValidator('date');
		$this->addElements(array($dcDataFinal));
		
        // Botão de envio do formulário da busca
        $search = $this->createElement('submit', 'search', array(
            'required' => true,
            'ignore'   => true,
            'label'    => 'Buscar'
        ));
		$search->removeDecorator('Label');
		$search->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
        $search->setDecorators(
		array(
			array("decorator" => "ViewHelper"), //This one is required...
            array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($search);
		
		// Botão de cancelamento da ação
		$cancel = $this->createElement('button', 'cancel', array(
			'required' => false,
			'ignore'   => true,
			'label'    => 'Cancelar',
			'attribs' => array(
				'onclick' => "window.location.href='/tramitacao_documentos/tramitacao/list';"
			)
		));
		$cancel->removeDecorator('Label');
		$cancel->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
		$cancel->setDecorators(
		array(
			array("decorator" => "ViewHelper"), //This one is required...
            array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($cancel);
        
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