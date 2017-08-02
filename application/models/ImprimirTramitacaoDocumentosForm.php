<?php
/**
 * Classe modelo de formulário de Impressão da Tramitação de Documentos
 */
class ImprimirTramitacaoDocumentosForm extends Zend_Form
{
	#$this->disableLoadDefaultDecorators(true);

	public function init()
	{
		$this->setAttrib('id', 'frmFinalizarTramitacao');
		$this->setAction('/tramitacao_documentos/tramitacao/printguia');
	
		// Botão de impressão da guia
		$print = $this->createElement('button', 'print', array(
			'required' => true,
			'ignore'   => true,
			'label'    => 'Imprimir',
			'attribs'  => array(
				'onclick'  => "openPrint(document.getElementById('frmFinalizarTramitacao'), 'printguia');"
			)
		));
		$print->removeDecorator('Label');
		$print->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
		$print->setDecorators(
		array(
			array("decorator" => "ViewHelper"), //This one is required...
			array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($print);
		
		// Botão de alteração dos dados complementares da tramitação
		/*$edit = $this->createElement('button', 'editar', array(
			'required' => false,
			'ignore'   => true,
			'label'    => 'Alterar',
			'attribs'  => array(
				'onclick' => "document.getElementById(\'frmFinalizarTramitacao\').action = \'/tramitacao_documentos/tramitacao/edit\'; document.getElementById(\'frmFinalizarTramitacao\').submit();"
			)
		));
		$edit->removeDecorator('Label');
		$edit->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
		$edit->setDecorators(array(
		array("decorator" => "ViewHelper"), //This one is required...
			array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($edit);*/
	}
}