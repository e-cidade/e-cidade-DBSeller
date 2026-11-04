<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

$parametros = JSON::requestParameters();
$retorno = (object)['erro' => false, 'mensagem' => ''];

$config = null;
$daoConfig = new cl_tfd_parametros();
$sql = $daoConfig->sql_query_file();
$rs = $daoConfig->sql_record($sql);
if ($daoConfig->numrows > 0) {
    $config = db_utils::fieldsmemory($rs, 0);
}

db_inicio_transacao();
try {
    switch ($parametros->acao) {
        case 'getParametros':
            if (!$config) {
                throw new Exception('Erro ao buscar parâmetros do módulo TFD.');
            }

            $retorno->utilizaGradeHorario = $config->tf11_i_utilizagradehorario == 1;
            $retorno->obrigaHoraSaida = $config->tf11_obriga_hora_saida == 't';
            break;
        case 'getVeiculosVinculados':
            $retorno->veiculosVinculados = [];
            $dao = new cl_tfd_pedidotfd();
            $campos = [];
            $campos[] = 'tfd_veiculodestino.tf18_i_codigo as id';
            $campos[] = 'cgm_motorista.z01_nome as motorista';
            $campos[] = 'tfd_destino.tf03_c_descr as destino';
            $campos[] = 'veiculos.ve01_codigo as veiculo';
            $campos[] = 'tfd_veiculodestino.tf18_c_horasaida as hora';
            $campos[] = 'cgs_und.z01_v_nome as paciente';
            $campos[] = 'cgmprest.z01_nome as prestadora';
            $campos = implode(', ', $campos);

            $where = [];
            $where[] = "tf18_i_veiculo = {$parametros->idVeiculo}";
            $where[] = "tf18_i_destino = {$parametros->idDestino}";
            $where[] = "tf18_d_datasaida = '{$parametros->data}'";
            $where[] = "not exists (select 1 from cancelamentoviagem where tf41_veiculodestino = tf18_i_codigo)";

            if (!empty($parametros->idMotorista)) {
                $where[] = "tf18_i_motorista = {$parametros->idMotorista}";
            }
            $where = implode(' AND ', $where);

            $sql = $dao->sql_query_pedido_saida('', $campos, '', $where);
            $rs = $dao->sql_record($sql);
            if ($dao->numrows > 0) {
                $veiculosVinculados = db_utils::getCollectionByRecord($rs);
                $retornoFormatado = [];
                foreach ($veiculosVinculados as $veiculoVinculado) {
                    if (!array_key_exists($veiculoVinculado->id, $retornoFormatado)) {
                        $dao = new cl_veiccadmodelo();
                        $sql = $dao->sql_query_file($veiculoVinculado->veiculo, 've22_descr');
                        $rs = $dao->sql_record($sql);
                        if (!$rs) {
                            throw new Exception('Erro ao buscar modelo do véiculo.');
                        }
                        $retornoFormatado[$veiculoVinculado->id] = (object)[
                            'id' => $veiculoVinculado->id,
                            'motorista' => $veiculoVinculado->motorista,
                            'destino' => $veiculoVinculado->destino,
                            'veiculo' => db_utils::fieldsMemory($rs, 0)->ve22_descr,
                            'hora' => $veiculoVinculado->hora,
                            'passageiros' => []
                        ];
                    }

                    $retornoFormatado[$veiculoVinculado->id]->passageiros[] = (object)[
                        'paciente' => $veiculoVinculado->paciente,
                        'prestadora' => $veiculoVinculado->prestadora
                    ];
                }
                $retorno->veiculosVinculados = array_values($retornoFormatado);
            }
            break;
        default:
            break;
    }
} catch (Exception $erro) {
    $retorno->mensagem = $erro->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);
