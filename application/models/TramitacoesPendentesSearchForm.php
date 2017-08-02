<?php
/**
 * Classe modelo de formul�rio de busca de tramita��es
 */
class TramitacoesPendentesSearchForm extends Zend_Form
{
    public function init()
    {
		$this->setName('tramitacaoSearchForm');
		
		Zend_Loader::loadClass('Unidade');
		Zend_Loader::loadClass('Zend_Dojo');
		Zend_Loader::loadClass('Zend_Dojo_Form_Element_DateTextBox');
		Zend_Loader::loadClass('Zend_Form_Element_Select');
		
		
    	$trNumeroGuia = $this->addElement('text', 'tr_numero_guia', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'StringLength'
            ),
            'required'   => false,
            'label'      => 'N� Guia:'
        ));
		
        $dcNumero = $this->addElement('text', 'dc_numero', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'StringLength'
            ),
            'required'   => false,
            'label'      => 'N� Documento:'
        ));
		
		/*$unId = new Zend_Form_Element_Select('un_id');
		$unId->setLabel('Unidade:');
        $unId->setRequired(false);*/
		/** Tabela de Unidades da Prefeitura a integrar */
		/*$tableUnidade = new Unidade();
		$unId->addMultiOption("", "Selecione");
		foreach($tableUnidade->fetchAll() as $tU)
		{
			$unId->addMultiOption($tU->un_id, utf8_encode($tU->un_descricao));
		}
		$this->addElement($unId);*/
		
		// Data de Tramita��o Inicial para Filtro
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
		
		// Data de Tramita��o Final para Filtro
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
		
        // Bot�o de envio do formul�rio da busca
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
		
		// Bot�o de cancelamento da a��o
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
         * Esse m�todo serve para exibir mensagens de erro no submit do formul�rio
         */
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
}