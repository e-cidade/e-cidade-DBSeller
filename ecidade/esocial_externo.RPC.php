<?php

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Service\ProcessamentoExternoService;

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');


$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$dadosGet = filter_input_array(INPUT_GET, FILTER_DEFAULT);
$dadosClass = new stdClass();

if (!empty($dados['evento']) && !empty($dados['matricula']) && !empty( $dados['cgm'])) {
    $dadosClass->instituicao = $dados['instituicao'];
    $dadosClass->evento = $dados['evento'];
    $dadosClass->matricula = $dados['matricula'];
    $dadosClass->empregador = $dados['empregador'];
    $dadosClass->cgm = $dados['cgm'];
    switch ($dadosClass->evento) {
        case 'S2200':
            ProcessamentoExternoService::processamentoExterno(Tipo::S2200, $dadosClass);
        break;
        default:
        
        break;
    }
}

if (!empty($dadosGet['matricula'])) {
    $vinculo = ProcessamentoExternoService::vinculo($dadosGet['matricula']);
    echo json_encode($vinculo);
    exit;
} 

