<?php
/**
 * Classe modelo de formul�rio de busca de �lbuns
 */
class SearchAlbumForm extends Zend_Form
{
    public function init()
    {    	
        $id = $this->addElement('text', 'id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alnum'
            ),
            'required'   => false,
            'label'      => 'Id:',
        ));

        $title = $this->addElement('text', 'title', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(3, 20)),
            ),
            'required'   => false,
            'label'      => 'Title:',
        ));

        $login = $this->addElement('submit', 'search', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Search',
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