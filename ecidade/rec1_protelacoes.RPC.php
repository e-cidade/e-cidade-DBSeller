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

use ECidade\RecursosHumanos\Pessoal\Repository\PontoFixoRepository;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("std/db_stdClass.php"));

$oJson  = new services_json();
$oParametro = $oJson->decode(str_replace("\\", "", $_POST['json']));

$oRetorno = new stdClass();
$oRetorno->erro = false;
$oRetorno->sMensagem = "";
db_inicio_transacao();
try {

    switch ($oParametro->exec) {
        case "processar":
            $iAnoCompetencia = $oParametro->ano;
            $iMesCompetencia = $oParametro->mes;
            $iInstituicao    = db_getsession("DB_instit");
        
            $rubrica = $oParametro->rubrica;

            if (empty($rubrica)) {
                throw new BusinessException("Rubrica não informada.");
            }
            $trienioProcessado = false;
            $progressaoProcessada = false;
        
            $dados = novaDataTrienio($iAnoCompetencia, $iMesCompetencia, $iInstituicao, $rubrica);
            $dados = novaDataProgressao($iAnoCompetencia, $iMesCompetencia, $iInstituicao, $dados);

            ksort($dados);

            $oRetorno->dados = [];
            foreach ($dados as $dado) {
                $oRetorno->dados[] = $dado;
            }
            db_fim_transacao(true);
        break;
    }
} catch (Exception $oErro) {

    db_fim_transacao(true);
    $oRetorno->erro     = true;
    $oRetorno->sMensagem = $oErro->getMessage();
}
  
$oRetorno->sMensagem = urlencode($oRetorno->sMensagem);
echo $oJson->encode($oRetorno);


function novaDataTrienio($ano, $mes, $iInstituicao, $rubrica)
{
    $dataAtual = new DateTime("{$ano}-{$mes}-01");
    $dataAtual->modify('last day of this month');
    $queryTrienio = <<<SQL
        SELECT 
            h19_op01 as operador,
            SUM(h16_quant) as quantidade,
            rh01_admiss as data_admissao,
            rh02_regist as matricula,
            sum(
                case when h16_assent is not null and h19_assent is not null then
                    case when (h16_dtterm is null or h16_dtterm >= '{$dataAtual->format('Y-m-d')}') then
                        1
                    else 0 end
                else 0 end
            ) as afastado  
        FROM rhpessoal
            LEFT JOIN assenta ON h16_regist = rh01_regist
            LEFT JOIN tipoasse ON h12_codigo = h16_assent
            left join protelac on CAST(h12_codigo AS VARCHAR) = h19_assent and h19_tipo = 'T'
            INNER JOIN rhpessoalmov ON rh01_regist = rh02_regist and rh01_instit = rh02_instit
            INNER JOIN rhregime     ON rh02_codreg = rh30_codreg and rh02_instit = rh30_instit
            LEFT JOIN rhpesrescisao ON rh02_seqpes = rh05_seqpes
        WHERE rh02_anousu = $ano
            AND rh02_mesusu = $mes
            AND rh01_instit = $iInstituicao
            AND rh30_regime in (1,2)
            AND rh30_vinculo = 'A'
            AND rh05_recis is null
        GROUP BY 1,3,4
SQL;
    $rsTrienio = db_query($queryTrienio);
    $existeCalculo = (pg_num_rows($rsTrienio)) > 0;
    $relatorio = [];

    if ($existeCalculo) {
        $calculoTrienio = db_utils::getCollectionByRecord($rsTrienio);

        if (!empty($calculoTrienio)) {
            foreach ($calculoTrienio as $trienio) {
                $operador = $trienio->operador;
                $quantidade = $trienio->quantidade;
                $data_admissao = $trienio->data_admissao;
                $matricula = $trienio->matricula;
                
                $servidor = ServidorRepository::getInstanciaByCodigo($matricula);
                if (!empty($relatorio[$matricula])) {
                    $dados = $relatorio[$matricula];
                } else {
                    $dados = novoRegistro($servidor);
                }
                if (!empty($trienio->afastado)) {
                    $dados->afastado = "S";
                }
                $dados->datatrienioatual = $servidor->getDataTrienio()->getDate(DBDate::DATA_PTBR);
                $valorPorcentagem = PontoFixoRepository::find($matricula, $ano, $mes, $rubrica);

                if (!empty($valorPorcentagem)) {
                    $dados->percentualtrienioatual = "{$valorPorcentagem->getQuantidade()}%";
                }

                if (!isset($dias_direito_total_por_matricula[$matricula])) {
                    $dias_direito_total_por_matricula[$matricula] = 0;
                }

                if (!empty($operador)) {
                    if ($operador === '+') {
                        $dias_direito_total_por_matricula[$matricula] += $quantidade;
                    } else {
                        $dias_direito_total_por_matricula[$matricula] -= $quantidade;
                    }    
                } else {
                    $dias_direito_total_por_matricula[$matricula] = 0;
                    if (!empty($relatorio[$matricula])) {
                        if ($dados->afastado == "S") {
                            $relatorio[$matricula]->afastado = "S";
                        }
                        continue;
                    }
                }

                $nova_data_trienio = new DateTime($data_admissao);
                $nova_data_trienio->modify("{$dias_direito_total_por_matricula[$matricula]} days");
                $nova_data_trienio_formatado = $nova_data_trienio->format('Y-m-d');
                $dados->datatrienionova = $nova_data_trienio->format('d/m/Y');

                $dados->totaltempo = $nova_data_trienio->diff($dataAtual)->format("%y");
                $quantidadeTrienio = intval($dados->totaltempo/3);
                $novoTempo = $quantidadeTrienio * 3;
                $dataPrevisao = new DateTime($nova_data_trienio_formatado);

                if ($dados->totaltempo % 3) {
                    $quantidadeTrienio += 1;
                    $novoTempo = $quantidadeTrienio * 3;
                } else {
                    // aqui validamos a data atual com a data do trienio para somar ou nao a quantidade
                    $dataBase = clone $dataPrevisao;
                    $dataBase->modify("{$novoTempo} years");
                    if ($dataBase <= $dataAtual) {
                        $quantidadeTrienio += 1;
                        $novoTempo = $quantidadeTrienio * 3;

                        if ($dataBase->format('Y') == $dataAtual->format('Y') && $dataBase->format('m') == $dataAtual->format('m')) {
                            $dados->alterado = true;
                        }

                    } else {
                    }
                }
                $novoTempo = $quantidadeTrienio * 3;
                
                $dataPrevisao->modify("{$novoTempo} years");

                if ($dataPrevisao->format('Y') == $dataAtual->format('Y') && $dataPrevisao->format('m') == $dataAtual->format('m')) {
                    $dados->alterado = true;
                }

                $dados->dataprevisaotrienio = $dataPrevisao->format('d/m/Y');

                $relatorio[$matricula] = $dados;

                $atualiza_data_trienio = "UPDATE rhpessoal SET rh01_trienio = '$nova_data_trienio_formatado' WHERE rh01_instit = $iInstituicao and rh01_regist = $matricula";
                
                db_query($atualiza_data_trienio);
            }
        } else {
            throw new Exception("Erro ao Calcular as Quantidades de Triênios!");
        }
    } else {
        throw new Exception("Erro na consulta SQL: " . $rsTrienio->errorInfo()[2]);
    }
    return $relatorio;
}

function novaDataProgressao($iAnoCompetencia, $iMesCompetencia, $iInstituicao, $relatorio = [])
{
    $queryProgressao = <<<SQL
        SELECT 
            h19_op01 as operador,
            SUM(h16_quant) as quantidade,
            rh01_admiss as data_admissao,
            rh02_regist as matricula
        FROM rhpessoal
            LEFT JOIN assenta ON h16_regist = rh01_regist
            LEFT JOIN tipoasse ON h12_codigo = h16_assent
            left join protelac on CAST(h12_codigo AS VARCHAR) = h19_assent and h19_tipo = 'P'
            INNER JOIN rhpessoalmov ON rh01_regist = rh02_regist and rh01_instit = rh02_instit
            INNER JOIN rhregime     ON rh02_codreg = rh30_codreg and rh02_instit = rh30_instit
            LEFT JOIN rhpesrescisao ON rh02_seqpes = rh05_seqpes
        WHERE rh02_anousu = $iAnoCompetencia
            AND rh02_mesusu = $iMesCompetencia
            AND rh01_instit = $iInstituicao
            AND rh30_regime in (1,2)
            AND rh30_vinculo = 'A'
            AND rh05_recis is null
        GROUP BY 1,3,4
SQL;

    $rsProgressao = db_query($queryProgressao);
    $existeCalculo = (pg_num_rows($rsProgressao)) > 0;
    if ($existeCalculo) {
        $calculoProgressao = db_utils::getCollectionByRecord($rsProgressao);

        if (!empty($calculoProgressao)) {

            foreach ($calculoProgressao as $progressao) {
                $operador      = $progressao->operador;
                $quantidade    = $progressao->quantidade;
                $data_admissao = $progressao->data_admissao;
                $matricula     = $progressao->matricula;


                /**
                 * TODO - nao implementado pois cliente pediu apenas a parte do trienio
                 * aqui ja está meio pronto, só ajustar e validar conforme for solicitado futuramente
                 */                
                // $servidor = ServidorRepository::getInstanciaByCodigo($matricula);
                // $dados = novoRegistro($servidor);

                if (!isset($dias_direito_total_por_matricula[$matricula])) {
                    $dias_direito_total_por_matricula[$matricula] = 0;
                }

                if (!empty($operador)) {
                    if ($operador === '+') {
                        $dias_direito_total_por_matricula[$matricula] += $quantidade;
                    } else {
                        $dias_direito_total_por_matricula[$matricula] -= $quantidade;
                    }    
                } else {
                    $dias_direito_total_por_matricula[$matricula] = 0;
                }

                $nova_data_progressao = new DateTime($data_admissao);
                $nova_data_progressao->modify("{$dias_direito_total_por_matricula[$matricula]} days");
                $nova_data_progressao_formatado = $nova_data_progressao->format('Y-m-d');

                /**
                 * TODO - nao implementado pois cliente pediu apenas a parte do trienio
                 * aqui ja está meio pronto, só ajustar e validar conforme for solicitado futuramente
                 */
                // if (!empty($relatorio[$dados->matricula])) {
                // } else {
                // }
                $atualiza_data_progressao = "UPDATE rhpessoal SET rh01_progres = '$nova_data_progressao_formatado' WHERE rh01_instit = $iInstituicao and rh01_regist = $matricula";

                db_query($atualiza_data_progressao);
            }
        } else {
            throw new Exception("Erro ao Calcular as Quantidade de Progressões!");
        }
    }
    return $relatorio;
}

function novoRegistro(\Servidor $servidor)
{
    $dados = new stdClass();
    $dados->matricula = $servidor->getMatricula();
    $dados->nome = mb_convert_encoding($servidor->getCgm()->getNome(), "UTF8");
    $dados->cargo = mb_convert_encoding($servidor->getDadosCargo()->rh37_descr, "UTF8");
    $lotacao = LotacaoRepository::getInstanceByCodigo($servidor->getCodigoLotacao());
    $dados->lotacao = mb_convert_encoding($lotacao->getDescricaoLotacao(), "UTF8");
    $dados->dataadmissao = $servidor->getDataAdmissao()->getDate(DBDate::DATA_PTBR);
    $dados->datatrienioatual = "";
    $dados->datatrienionova = "";
    $dados->dataprevisaotrienio = "";
    $dados->dataprogressaoatual = "";
    $dados->dataprogressaonova = "";
    $dados->dataprogressaotrienio = "";
    $dados->percentualtrienioatual = "0%";
    $dados->alterado = false;
    $dados->afastado = "N";

    return $dados;
}
