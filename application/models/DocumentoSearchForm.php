<?php
/**
 * Classe modelo do formulário de busca de Documentos
 */
class DocumentoSearchForm extends Zend_Form
{
    public function init()
    {
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		Zend_Loader::loadClass('Zend_Form_Element_Select');
		Zend_Loader::loadClass('TipoDocumento');
		Zend_Loader::loadClass('Assunto');
		Zend_Loader::loadClass('OrgaoExterno');
		Zend_Loader::loadClass('Unidade');
		Zend_Loader::loadClass('Zend_Date');
		Zend_Loader::loadClass('Zend_Locale');
		Zend_Loader::loadClass('Zend_Dojo');
		Zend_Loader::loadClass('Zend_Dojo_Form_Element_DateTextBox');
		Zend_Loader::loadClass('Zend_Registry');		
		Zend_Loader::loadClass('Zend_Form_Element_File');
		Zend_Loader::loadClass('Zend_Validate_File_Extension');
		
		$objRegistry = Zend_Registry::getInstance();
		$view  		 = $objRegistry->view;
		
		$this->setName('documentoSearchForm');
		
		// Dojo-enable the form:
        #Zend_Dojo::enableForm($this);

        // ... continue form definition from here
		
		// Número do Documento
		$dcNumero = $this->addElement('text', 'dc_numero', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'StringLength',
                array('StringLength', false, array(1, 20)),
            ),
            'required'   => false,
            'label'      => 'Nº Documento:'
        ));
		
		// ID do tipo do documento
        /*$td_id = $this->addElement('select', 'td_id', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum'
            ),
            'required'   => true,
            'label'      => 'Tipo de Documento:'
        ));
		$objDocumentoForm = new BrowseForm();
		$objDocumentoForm->getElement('td_id')
						   ->setMultiOptions(aray(1=>'form 1',2=>'form 2'));
		if ($browseForm->isValid($this->getRequest()->getQuery())) {
		// etc
		}
		echo $browseForm; */
		$tdId = new Zend_Form_Element_Select('td_id');
		$tdId->setLabel('Tipo Documento:');
        $tdId->setRequired(false);
        $tdId->addMultiOption("", "Selecione");
		$tableTipoDocumento = new TipoDocumento();
		foreach($tableTipoDocumento->fetchAll('td_excluido IS NULL', 'td_descricao ASC') as $tTD)
		{
			$tdId->addMultiOption($tTD->td_id, $tTD->td_descricao);
		}
		$this->addElement($tdId);
		
		/** Unidade de Origem do Documento - Tabela de Unidades da Prefeitura a integrar */
		/*$unId = new Zend_Form_Element_Select('un_id');
		$unId->setLabel('Unidade de Origem:');
        $unId->setRequired(true);
		$tableUnidade = new Unidade();
		$unId->addMultiOption("", "Selecione");
		foreach($tableUnidade->fetchAll() as $tU)
		{
			$unId->addMultiOption($tU->un_id, utf8_encode($tU->un_descricao));
		}
		$this->addElement($unId);
		*/		

			// ID da Ordem Externa do documento se houver (DEFAULT NULL)
		/*$oe_id = $this->addElement('select', 'oe_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alnum'
            ),
            'required'   => true,
            'label'      => 'Orgão Externo:'
        ));*/
		$oeId = new Zend_Form_Element_Select('oe_id');
		$oeId->setLabel('Orgão Origem:');
        $oeId->setRequired(false);
		$tableOrgaoExterno = new OrgaoExterno();
		$oeId->addMultiOption("", "Selecione");
		foreach($tableOrgaoExterno->fetchAll('oe_excluido IS NULL', 'oe_descricao ASC') as $tOE)
			$oeId->addMultiOption($tOE->oe_id, $tOE->oe_descricao);
		$this->addElement($oeId);
		
		// Data Elaboração do Documento
        /*$element = $this->createElement('text', 'calendar'); //dc_data_elaboracao
        $element->setLabel('Data de Elaboração:')
                ->setAttrib('dojoType', array('dijit.form.DateTextBox'))
                ->setAttrib('constraints', "{datePattern:'dd-MM-yyyy'}")
                ->setRequired(true)
                ->addFilter('stringTrim')
                ->addValidator('date');
        $this->addElement($element);*/
		
		/*$dcDataElaboracao = new Zend_Dojo_Form_Element_DateTextBox("data_elaboracao");
        $dcDataElaboracao
         ->setLabel('Data de Elaboração:')
         ->setAttribs(array("dojoType"=>"dijit.form.DateTextBox"))
		 ->setAttrib('constraints', "{datePattern:'dd-MM-yyyy'}")
         ->setRequired(false)
         ->addFilter('stringTrim')
		 ->setValue('dd/mm/aaaa')
		 ->setAttrib('onfocus', "this.value = (this.value == 'dd/mm/aaaa') ? '' : this.value")
		 ->setAttrib('onblur', "this.value = (this.value.length > 0) ? this.value : 'dd/mm/aaaa'")
         /*->addValidator('date')*/;
        /*'invalidMessage' => 'Invalid date specified.',
        'formatLength'   => 'long',*/
		//$this->addElements(array($dcDataElaboracao)); 
		
		// ID do assunto do documento
		/*$as_id = $this->addElement('select', 'as_id', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum'
            ),
            'required'   => true,
            'label'      => 'Assunto:'
        ));*/
		$asId = new Zend_Form_Element_Select('as_id');
		$asId->setLabel('Assunto:');
        $asId->setRequired(false);
		$tableAssunto = new Assunto();
		$asId->addMultiOption("", "Selecione");
		foreach($tableAssunto->fetchAll('as_excluido IS NULL', 'as_descricao ASC') as $tA)
			$asId->addMultiOption($tA->as_id, $tA->as_descricao);
		$this->addElement($asId);
		
		// Data de Cadastro do Documento Inicial para Filtro
		$dcDataInicial = new Zend_Dojo_Form_Element_DateTextBox("data_inicial");
        $dcDataInicial->setLabel('Data Cadastro Inicial:');
        $dcDataInicial->setAttribs(array("dojoType"=>"dijit.form.DateTextBox"));
		$dcDataInicial->setAttrib('constraints', "{datePattern:'dd-MM-yyyy'}");
        $dcDataInicial->setRequired(false);
        $dcDataInicial->addFilter('stringTrim');
		$dcDataInicial->setValue('dd/mm/aaaa');
		$dcDataInicial->setAttrib('onfocus', "this.value = (this.value == 'dd/mm/aaaa') ? '' : this.value");
		$dcDataInicial->setAttrib('onblur', "this.value = (this.value.length > 0) ? this.value : 'dd/mm/aaaa'");
        //$dcDataInicial->addValidator('date');
		$this->addElements(array($dcDataInicial));
		
		// Data de Cadastro do Documento Final para Filtro
		$dcDataFinal = new Zend_Dojo_Form_Element_DateTextBox("data_final");
        $dcDataFinal->setLabel('Data Cadastro Final:');
        $dcDataFinal->setAttribs(array("dojoType"=>"dijit.form.DateTextBox"));
		$dcDataFinal->setAttrib('constraints', "{datePattern:'dd-MM-yyyy'}");
        $dcDataFinal->setRequired(false);
        $dcDataFinal->addFilter('stringTrim');
		$dcDataFinal->setValue('dd/mm/aaaa');
		$dcDataFinal->setAttrib('onfocus', "this.value = (this.value == 'dd/mm/aaaa') ? '' : this.value");
		$dcDataFinal->setAttrib('onblur', "this.value = (this.value.length > 0) ? this.value : 'dd/mm/aaaa'");
        //$dcDataInicial->addValidator('date');
		$this->addElements(array($dcDataFinal));
		
		// Data de Elaboração do Documento para Filtro
		$dcDataElaboracao = new Zend_Dojo_Form_Element_DateTextBox("dc_data_elaboracao");
        $dcDataElaboracao->setLabel('Data de Elaboração:');
        $dcDataElaboracao->setAttribs(array("dojoType"=>"dijit.form.DateTextBox"));
		$dcDataElaboracao->setAttrib('constraints', "{datePattern:'dd-MM-yyyy'}");
        $dcDataElaboracao->setRequired(false);
        $dcDataElaboracao->addFilter('stringTrim');
		$dcDataElaboracao->setValue('dd/mm/aaaa');
		$dcDataElaboracao->setAttrib('onfocus', "this.value = (this.value == 'dd/mm/aaaa') ? '' : this.value");
		$dcDataElaboracao->setAttrib('onblur', "this.value = (this.value.length > 0) ? this.value : 'dd/mm/aaaa'");
        //$dcDataInicial->addValidator('date');
		$this->addElements(array($dcDataElaboracao));
		
		// Complemento do assunto do documento
        $dcCmplAssunto = $this->addElement('textarea', 'dc_compl_assunto', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'StringLength',
                array('StringLength', false, array(2, 500)),
            ),
            'required'   => false,
            'label'      => 'Complemento Assunto:',
			'cols' 		 => 70,
			'rows'		 => 1,
        ));
		
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
				'onclick' => "window.location.href='/tramitacao_documentos/documento/list';"
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
		
        // Dojo-enable all sub forms:
        #foreach($this->getSubForms() as $subForm)
        #    Zend_Dojo::enableForm($subForm);
		
		#echo "<pre>";
		#print_r($this);
		#echo "</pre>";
    }
}