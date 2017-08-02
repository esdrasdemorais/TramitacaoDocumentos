<?php
/**
 * Classe modelo de formul�rio de TramitacaoForm
 */
class UnidadeForm extends Zend_Form
{
    public function init()
    {
		// ID do tipo do documento
        $td_id = $this->addElement('select', 'dc_id', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'num'
            ),
            'required'   => true,
            'label'      => 'Documento:'
        ));
		
		// Cota da tramita��o se houver (DEFAULT NULL)
        $un_descricao = $this->addElement('text', 'un_descricao', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(1, 200)),
            ),
            'required'   => false,
            'label'      => 'Cota:'
        ));		
		
		// Bot�o de envio do formul�rio
        $salvar = $this->addElement('submit', 'save', array(
            'required' => true,
            'ignore'   => true,
            'label'    => 'Salvar'
        ));
        
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