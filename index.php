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
/** Configura o formato para moeda */
setlocale(LC_MONETARY,'ptb');

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
$path = $pathSeparator.$documentRoot.$bar.'tramitacao_documentos'.PATH_SEPARATOR.$pathSeparator.$documentRoot.$bar.'tramitacao_documentos'.$bar.'library'.PATH_SEPARATOR.'application'.$bar.'models';
set_include_path(get_include_path().$path);

/*// configura o caminho
require('application/utils/scripts/path.php');
configurePath(basename(getcwd()));

// carrega classe que fará a inicialização do Zend Framework
require('application/utils/classes/Bootstrap.php');
new Bootstrap($_SERVER['PHP_SELF']);*/

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
 * 
 * Carregando módulos necessários
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

Zend_Loader::loadClass('Zend_Controller_Front');  	/** Classe de controladores */
Zend_Loader::loadClass('Zend_Session'); 			/** Inclui o suporte a sessões. Só é necessário caso seja usado. */
Zend_Loader::loadClass('Zend_Session_Namespace'); 	/** Classe usada para armazenar e recuperar dados da sessão */
Zend_Loader::loadClass("Zend_View"); 				/* Classe das visões */
Zend_Loader::loadClass('Zend_Config_Ini'); 			/** Classe usada para configurações */
Zend_Loader::loadClass('Zend_Db'); 					/** Classe para acesso a base de dados */
Zend_Loader::loadClass('Zend_Db_Table'); 			/** Classe para usar as tabelas como objetos */
Zend_Loader::loadClass('Zend_Filter_Input');		/** Classe usada para filtrar os dados */
Zend_Loader::loadClass("Zend_Auth");
Zend_Loader::loadClass("Zend_Controller_Plugin_Abstract");
Zend_Loader::loadClass("SecurityPlugin");
Zend_Loader::loadClass('Zend_Form');
Zend_Loader::loadClass('LoginForm');

/** O método set é responsável por armazenar variáveis que podem ser usadas
 * pelos aplicativos. Aqui, registrando os arrays post e get com dados vindos do usuário.
 * o Zend_Filter limpa os dados.
 */
Zend_Registry::set('post', new Zend_Filter_Input(NULL,NULL,$_POST));
Zend_Registry::set('get', new Zend_Filter_Input(NULL,NULL,$_GET));

/** Inicia a sessão global */
Zend_Session::start();

/** Cria o manipulador da dessão */
Zend_Registry::set('session', new Zend_Session_Namespace());

/** Parte das visões (Views) */
$objView = new Zend_View(); 					/** Cria um novo objeto do tipo view */
$objView->setEncoding('UTF-8');					/** Configura a codificação das páginas */
$objView->setEscape('htmlentities');			/** Escapar entradas HTML */
$objView->setBasePath('./application/views/');	/** Define o diretório onde estarão as visões */
Zend_Registry::set('view', $objView); 			/** Registra na memória a variável view que indica a visão */

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
$objControlador = Zend_Controller_Front::getInstance();

/** Configura o controlador do projeto.
 * O Controlador, por acaso, é o index.php
 */
$baseUrl = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/index.php'));

/** Configura o endereço do controlador do projeto */
$objControlador->setbaseUrl($baseUrl);

/* Mostrar exceções (apenas para testes) */
$objControlador->throwExceptions(TRUE);

$objControlador->setControllerDirectory('./application/controllers'); // seta diretório com nossos controllers

$objControlador->registerPlugin(new SecurityPlugin());

/** Configurações da base de dados.
 * Indica onde estão as configurações do projeto.
 * Estão no arquivo config.ini na seção database.
 */
$objConfig = new Zend_Config_Ini('./application/config.ini', 'database');

/** Registra na memória a variável config */
Zend_Registry::set('config', $objConfig);

/** Configura a conexão com a base de dados, pegando as variáveis do arquivo
 * de configuração.
 */
try
{
	/*
	$objConfig->db->config->toArray() = array(
    'host'     => 'server1',
    'username' => 'sa',
    'password' => '',
    'dbname'   => 'Documentos'
	));
	*/
	
	$db = Zend_Db::factory($objConfig->db->adapter, $objConfig->db->config->toArray());

    /*$db = Zend_Db::factory('Pdo_Mssql', array(
    'host'     => 'server1',
    'username' => 'sa',
    'password' => '',
    'dbname'   => 'Documentos'
	));*/
	
    #$db->getConnection();
}
catch(Zend_Db_Adapter_Exception $e)
{
    $e->getMessage();
}
catch(Zend_Exception $e)
{
   $e->getMessage();
}
#$sql = 'SELECT * FROM tb_unidade';
#$result = $db->fetchAll($sql);
#echo "----------------<pre>" . var_dump($result) . "</pre>";

Zend_Db_Table_Abstract::setDefaultAdapter($db);

/** Registra a variável db */
Zend_Registry::set('db', $db);

/**
 * 
 * Inicializando o sistema
 */
$objControlador->dispatch();

/**
 * 
 * Não se põe a tag de fechamento do PHP, para evitar mensagens de erros
 */