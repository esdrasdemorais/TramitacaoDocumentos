<?php
/**
 * Classe modelo do formulário de Documentos
 */
class DocumentoForm extends Zend_Form
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
		Zend_Loader::loadClass('Zend_Form_Element_Radio');
		
		$objRegistry = Zend_Registry::getInstance();
		$view  		 = $objRegistry->view;
		
		// Dojo-enable the form:
        #Zend_Dojo::enableForm($this);

		$this->setName('formDocumento');
		
        // ... continue form definition from here
		
		// Número do Documento
		$dcNumero = $this->addElement('text', 'dc_numero', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'StringLength',
                array('StringLength', false, array(1, 20)),
            ),
            'required'   => true,
            'label'      => '* Número Documento:'
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
		$tdId->setLabel('* Tipo Documento:')
         ->setRequired(true);
		$tdId->addMultiOption("", "Selecione");
		
		$tableTipoDocumento = new TipoDocumento();		
		$whereTipoDocumento = 'td_excluido IS NULL';
		$orderTipoDocumento = 'td_descricao ASC';
		foreach($tableTipoDocumento->fetchAll($whereTipoDocumento, $orderTipoDocumento) as $tTD)
		{
			$tdId->addMultiOption($tTD->td_id, $tTD->td_descricao);
		}
		
		$this->addElement($tdId);
		
		// Data Elaboração do Documento
        /*$element = $this->createElement('text', 'calendar'); //dc_data_elaboracao
        $element->setLabel('* Data de Elaboração:')
                ->setAttrib('dojoType', array('dijit.form.DateTextBox'))
                ->setAttrib('constraints', "{datePattern:'dd-MM-yyyy'}")
                ->setRequired(true)
                ->addFilter('stringTrim')
                ->addValidator('date');
        $this->addElement($element);*/
		
		$dcDataElaboracao = new Zend_Dojo_Form_Element_DateTextBox("calendar");
        $dcDataElaboracao
         ->setLabel('* Data de Elaboração:')
         ->setAttribs(array("dojoType"=>"dijit.form.DateTextBox"))
		 ->setAttrib('constraints', "{datePattern:'dd-MM-yyyy'}")
         ->setRequired(true)
         ->addFilter('stringTrim')
		 ->setValue('dd/mm/aaaa')
		 ->setAttrib('onfocus', "this.value = (this.value == 'dd/mm/aaaa') ? '' : this.value")
		 ->setAttrib('onblur', "this.value = (this.value.length > 0) ? this.value : 'dd/mm/aaaa'")
         /*->addValidator('date')*/;
        /*'invalidMessage' => 'Invalid date specified.',
        'formatLength'   => 'long',*/
		
		$this->addElements(array($dcDataElaboracao)); 
		
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
		$asId->setLabel('* Assunto:')
         ->setRequired(true);
		$asId->addMultiOption("", "Selecione");
		$tableAssunto = new Assunto();
		foreach($tableAssunto->fetchAll('as_excluido IS NULL', 'as_descricao ASC') as $tA)
			$asId->addMultiOption($tA->as_id, $tA->as_descricao);
		
		$this->addElement($asId);
		
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
			'rows'		 => 10,
        ));
		
		// ID da Ordem Externa do documento se houver (DEFAULT NULL)
		/*$oe_id = $this->addElement('select', 'oe_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alnum'
            ),
            'required'   => true,
            'label'      => 'OrgãoExterno:'
        ));*/
		
		/*$documentoExterno = new Zend_Form_Element_Radio('documentoExterno');
		$documentoExterno->setLabel('Documento Externo?');
		$documentoExterno->addMultiOption('Sim', 'Sim');
		$documentoExterno->addMultiOption('Não', 'Não');
		$documentoExterno->setAttrib('onclick', "displayOrgaoExterno(this.value)");
		$documentoExterno->setSeparator('');
		$this->addElement($documentoExterno);
		// Seta valor default
		$this->setDefault('documentoExterno', 'Sim');*/
		
		/* Orgão Externo */
		$oeId = new Zend_Form_Element_Select('oe_id');
		$oeId->setLabel('Orgão Origem Externa:');
		$oeId->setRequired(false);
		$oeId->addMultiOption("", "Selecione");
		$tableOrgaoExterno = new OrgaoExterno();
		foreach($tableOrgaoExterno->fetchAll('oe_excluido IS NULL', 'oe_descricao ASC') as $tOE)
		{
			$oeId->addMultiOption($tOE->oe_id, $tOE->oe_descricao);
		}
		$this->addElement($oeId);

        $daArquivo = new Zend_Form_Element_File('da_arquivo'); //App_Form_Element_File('filePdf');
        $daArquivo->setLabel('Documento em PDF');
		$daArquivo->setIgnore(true);
        $daArquivo->setRequired(false);
		$daArquivo->addValidator('Extension', true, 'pdf','odt','doc','xls'); //jpg,jpeg,tif,tiff,gif,png,tga,psd,bmp,doc,zip,gz,rar
		$daArquivo->addValidator('FilesSize', true, '20MB'); //max upload size is 20MB (20971520 bytes)
		$daArquivo->addValidator('Size', false, 20971520); //max upload size is 20MB (20971520 bytes)
		$daArquivo->setMaxFileSize(20971520);
		$daArquivo->addValidator(new Zend_Validate_File_Extension(array('pdf','odt','doc','xls')));
		$this->addElement($daArquivo);
		
		/** automatically sets form encoding on upload */
		#$this->setAttrib('enctype', 'multipart/form-data');
		// or
		#$this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
		
		/** Caso não for edição, for inclusão (action index ou add) adiciona campo para permitir informar a unidade de destino do documento (tramitação pré-cadastrada) */
		if(strpos($this->getAction(), 'edit') === false)
		{
			// ID Unidade de destino do documento a tramitá-lo em lote
			$unId = new Zend_Form_Element_Select('un_id');
			$unId->setLabel('Unidade Destino:')
			 ->setRequired(false);
			/** Tabela da Prefeitura a integrar */
			$unId->addMultiOption("", "Selecione");
			$tableUnidade = new Unidade();
			$whereUnidade = "un_id <> ".$view->userUnitId;
			$orderUnidade = "un_descricao ASC";
			foreach($tableUnidade->fetchAll($whereUnidade, $orderUnidade) as $tU)
				$unId->addMultiOption($tU->un_id, utf8_encode($tU->un_descricao));
			$this->addElement($unId);
		}
		
		// Botão de envio do formuláo
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
		
		/** Caso não for edição, for inclusão (action index ou add) adiciona botão para permitir tramitação pré-cadastrada (informada a unidade de destino do documento ) */
		if(strpos($this->getAction(), 'edit') === false)
		{
			// Botão de início da tramitação em lote dos Documentos salvos com um unidade de destino já especificada (tramitação pré-cadastrada)
			$tramite = $this->createElement('button', 'tramite', array(
				'required' => false,
				'ignore'   => true,
				'label'    => 'Tramitar Documentos',
				'attribs' => array(
					'onclick' => "window.location.href='".$view->baseUrl."/tramitacao/batch'"
					//'onclick' => "window.location.href='/tramitacao_documentos/batch'"
				)
			));
			$tramite->removeDecorator('Label');
			$tramite->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
			$tramite->setDecorators(array(
			array("decorator" => "ViewHelper"), //This one is required...
				array("decorator" =>"HtmlTag", "options" => 
				array('tag' => "span", "class" =>"formbutton")))
			); //Beware that I can set the attributes of the span element via options...
			$this->addElement($tramite);
		}
		
		// Botão de cancelamento da ação
		$cancel = $this->createElement('button', 'cancel', array(
			'required' => false,
			'ignore'   => true,
			'label'    => 'Cancelar',
			'attribs' => array(
				'onclick' => "window.location.href='".$view->baseUrl.'/'.$view->controllerName."'"
				//'onclick' => "window.location.href='/tramitacao_documentos'"
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
		
		// Hidden ID documento
	  	/*$td_id = $this->addElement('hidden', 'dc_id', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Int'
            ),
            'required'   => false
        ));*/
		
		/*$dcId = new Zend_Form_Element_Hidden('dc_id');
		if(isset($view->dados))
			$dcId->setValue($view->dados->dc_id);
		//$this->addElements(array($dc_id, $artista, $titulo, $submit));
		$this->addElement($dcId)*/
          /*->addElement($artista)
          ->addElement($titulo)
          ->addElement($sub)
          ->addElement($bt_voltar)*/;
		
		// Data Elaboração
		#$objDate 		= new Zend_Date();
		#$objLocale  	= new Zend_Locale('pt_BR');
		#Zend_Date::setOptions(array('format_type' => 'php'));
		#echo $objDate->get(); /** Output of the desired Timestamp date */
		
		#$dcDataElaboracao = new Zend_Form_Element_Hidden('dc_data_elaboracao');
		#$dcDataElaboracao->setValue($objDate->toString('d-m-Y H:i:s')); /** pt_BR format */
		
		#$this->addElement($dcDataElaboracao);
		
        /**
         * Esse método serve para exibir mensagens de erro no submit do formulário
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