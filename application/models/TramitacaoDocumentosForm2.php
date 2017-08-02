<?php
/**
 * Classe modelo de formulário de TramitacaoForm
 */
class TramitacaoDocumentosForm2 extends Zend_Form
{
	#private $userUnitId;
	
	#public function __construct()
	#{
		/** Obtendo o objeto view registrado no bootstrap index.php **/
		#$objRegistry 	  = Zend_Registry::getInstance();
		#$this->userUnitId =	$objRegistry->view->userUnitId;
	#}*/
	
    public function init()
    {
		Zend_Loader::loadClass('Unidade');
		Zend_Loader::loadClass('Zend_Form_Element_Select');
		
		//$this->setAction($this->view->baseUrl . '/tramitacao/add');
		//$this->setMethod('post');
		
		/**
		 * Esse método serve para exibir mensagens de erro no submit do formulário
		 */

		// Cota da tramitação se houver (DEFAULT NULL)
		$this->addElement('textarea', 'tr_cota', array(
			'filters'    => array('StringTrim'),
			'validators' => array(
				'StringLength',
				array('StringLength', false, array(2, 250)),
			),
			'required'   => false,
			'label'      => 'Cota:',
			'cols' 		 => 70,
			'rows'		 => 10,
		));
		
		// ID Unidade de destino
		$unId = new Zend_Form_Element_Select('un_id');
		$unId->setLabel('Unidade Destino:')
		 ->setRequired(true);
		/** Tabela da Prefeitura a integrar */
		$unId->addMultiOption("", "Selecione");
		$tableUnidade = new Unidade();
		$objRegistry 	   = Zend_Registry::getInstance();
		#$this->userUnitId =	$objRegistry->view->userUnitId;
		$whereUnidade = "un_id <> ".$objRegistry->view->userUnitId;
		foreach($tableUnidade->fetchAll($whereUnidade) as $tU)
			$unId->addMultiOption($tU->un_id, utf8_encode($tU->un_descricao));
		$this->addElement($unId);
		
		// BotÃ£o de envio do formulÃ¡rio
		$submit = $this->createElement('submit', 'save', array(
			'required' => true,
			'ignore'   => true,
			'label'    => 'Iniciar TramitaÃ§Ã£o'
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
		
		// BotÃ£o de cancelamento da aÃ§Ã£o
		$cancel = $this->createElement('button', 'cancel', array(
			'required' => false,
			'ignore'   => true,
			'label'    => 'Cancelar',
			'attribs' => array(
				#'onclick' => "window.location.href='".$this->view->baseUrl.'/'.$this->view->controllerName."'"
				'onclick' => "window.location.href='/tramitacao_documentos/tramitacao'"
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
		
		$this->setDecorators(array(
			#array('ViewHelper'),
			#array('Errors'),
			#array('Description', array('tag' => 'p', 'class' => 'description')),
			#array('HtmlTag', array('tag' => 'dd')),
			#array('Label', array('tag' => 'dt'))
			
			'FormElements',
			array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
			array('Description', array('tag' => 'p', 'class' => 'description')), #array('Description', array('placement' => 'prepend')),
			'Form'
		));
	}
}