<?php
/**
 * Classe modelo de formulário de busca de álbuns
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