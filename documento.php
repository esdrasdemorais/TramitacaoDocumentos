<?php 
/**
 * 
 * Sessão de configuração de paths e erros
 */

/** Configura as mensagens de erro que devem ser apresentadas para mostrar os erros apenas nos testes (precisa estar setado no PHP.ini) */
error_reporting(E_ALL|E_STRICT);

/** Seta o timezone pra são paulo (>=PHP 5.1) */
setlocale(LC_ALL, 'pt_BR');
date_default_timezone_set('America/Sao_Paulo');

/**
 * Configura o caminho a ser procurado em todos os includes.
 * Irá procurar no diretório ../library, no application/models
 * e no caminho original do PHP.
 */
/**
 * É interessante utilizar set_include_path para definir onde se encontram
 * todos os arquivos do projeto, pois assim se evita que o mesmo código
 * seja escrito várias vezes, gerando menos linhas e facilitando qualquer
 * alteração de path.
 */
/**
 * Seta include path para o funcionamento correto do framework ZEND e o modelo da aplicação (application/models)
 ***OBRIGATÓRIO***
 */
#set_include_path('.' . PATH_SEPARATOR . './library' . PATH_SEPARATOR . './application/models/' . PATH_SEPARATOR.get_include_path());
$operatingSystem = stripos($_SERVER['SERVER_SOFTWARE'],'win32')!== FALSE ? 'WINDOWS' : 'LINUX';
$bar 			= ($operatingSystem == 'WINDOWS') ? '\\' : '/';
$pathSeparator  = ($operatingSystem == 'WINDOWS') ? ';' : ':';
$documentRoot   = ($operatingSystem == 'WINDOWS') ? str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']) : $_SERVER['DOCUMENT_ROOT'];
/**
 * Seta o path separando os paths por PATH_SEPARATOR ou :
 * $path = ':'.$pathSeparator.$documentRoot.$bar.'teste'.$bar.'library';
 * $path+= ':'.$pathSeparator.$documentRoot.$bar.'teste'.$bar.'application'.$bar.'models';
 */
$path = $pathSeparator.$documentRoot.$bar.'tramitacao_documentos'.$bar.'library'.PATH_SEPARATOR.'application'.$bar.'models';
set_include_path(get_include_path().$path);

// configura o caminho
/*require('application/path.php');
configurePath(basename(getcwd()));*/

/**
 * Faz o include do componente Zend_Loader.
 * Este include é OBRIGATÓRIO.
 * Zend_Loader carrega arquivos, classes e recursos
 * dinamicamente em sua aplicação PHP.
 * => suporta autocarregamento da SPL (Standard PHP Library)
 * => suporta include_path
 * => fornece mecanismo de falha baseado em exceção
 */
require_once("Zend/Loader.php");

/**
 * O método loadClass é responsável por incluir o arquivo responsável pela classe.
 * O acesso a níveis dos diretórios do framework ZEND é feito através do "_" não da "/".
 */

/**
* O registro (Zend_Registry) é um contâiner para armazenar objetos e valores
* no espaço da aplicação. Armazenar um objeto ou valor
* no registro torna o mesmo sempre disponível ao longo
* da aplicação durante o tempo de vida da requisição.
* Este mecanismo é freqüentemente uma alternativa aceitável
* ao uso de variáveis globais.
* => fornece armazenamento acessível globalmente para objetos
* e valores
* => fornece os padrões iterator, array e indexed access
*/
Zend_Loader::loadClass('Zend_Registry');

/** Inclui o suporte a sessões. Só é necessário caso seja usado. */
Zend_Loader::loadClass('Zend_Session');

/**
 * 
 * Carregando módulos necessários
 */
/* Classe de controladores */
Zend_Loader::loadClass('Zend_Controller_Front');
/* Classe das visões */
Zend_Loader::loadClass("Zend_View");

Zend_Loader::loadClass("Zend_Auth");
Zend_Loader::loadClass("Zend_Controller_Plugin_Abstract");
#Zend_Loader::loadClass("SecurityPlugin");
Zend_Loader::loadClass('Zend_Form');
#Zend_Loader::loadClass('LoginForm');
Zend_Loader::loadClass('DocumentoForm');

/**
* Este método tenta carregar o arquivo passado como parâmetro procurando no path
* definido com a função set_include_path. Caso ele não consiga encontrar o arquivo,
* é gerada uma exceção que indica arquivo inexistente ou sem acesso. O método
* considera os underscores no nome do arquivo como subdiretórios. Por exemplo, o
* comando Zend::loadClass('Zend_Controller_Front') faz a importação do arquivo
* ../zendframework/library/Zend/Controller/Front.php.
* Seguir esse padrão facilita o entendimento da estrutura do projeto.
* Essa classe se encontra em Zend/Controller/Front.php
* Pode ser "loadado" diretamente pelo nome se preferir
* Para começar nós precisamos "loadar" primeiro o front controller
* Ele faz um controle automático para detectar a base URL e fazer o redirecionamento correto
*/
$controlador = Zend_Controller_Front::getInstance();

/* Mostrar exceções (apenas para testes) */
$controlador->throwExceptions(TRUE);

$controlador->setControllerDirectory('./application/controllers'); // seta diretório com nossos controllers

#$controlador->registerPlugin(new SecurityPlugin());

/**
 * 
 * Inicializando o sistema
 */
$controlador->dispatch();

/**
 * 
 * Não se põe a tag de fechamento do PHP, para evitar mensagens de erros
 */