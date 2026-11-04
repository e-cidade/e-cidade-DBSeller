<?php
/**
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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("classes/db_arretipo_classe.php"));

use App\Domain\Tributario\Arrecadacao\Repositories\ConfiguracoesteftipodebitoRepository;
use ECidade\Tributario\Arrecadacao\Custas\Service;
use ECidade\Tributario\Arrecadacao\Custas\Relatorio;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo as ProcessoForoEntity;
use ECidade\Tributario\Juridico\Inicial\Inicial as InicialEntity;

$oJson       = new services_json(0, true);
$oParametros = $oJson->decode(str_replace("\\", "", $_POST["json"]));

$oRetorno           = new stdClass();
$oRetorno->erro     = false;
$oRetorno->sMessage = null;

try {
    switch ($oParametros->sExecucao) {
        case "geralFinanceiraDebitosRequest":
          // Remove indice sExecucao do array de debitos
            unset($oParametros->sExecucao);

            $oGetalFinanceiraDebitos = new GeralFinanceiraDebitosRequest();

          // Armazena a requisicao dos debitos
            $oGetalFinanceiraDebitos->setDebitos($oParametros);

          // DEBUG PARA VISUALIZAR A MANUTENÇÃO DAS INFORMAÇÕES ENCAPSULADAS NA SESSION
          // file_put_contents('/var/www/dbportal_prj/tmp/debug.log', print_r($_SESSION, true));
          // $oGetalFinanceiraDebitos->clearDebitos();
          // file_put_contents('/var/www/dbportal_prj/tmp/debug2.log', print_r($_SESSION, true));

            break;

        case "getDadosBoleto":
            $oReciboPago = new ReciboPago;

            $aReciboPago = $oReciboPago->getDadosBoleto(
                $oParametros->iNumpre,
                $oParametros->iNumpar,
                $oParametros->iReceit
            );

            $oDados = new stdClass();
            $oDados->aLinhas = $aReciboPago;

            $oRetorno->oDados = DBString::utf8_encode_all($oDados);
            break;

        case "mostrarCustas":
            db_inicio_transacao();

            $arrayDebitos = array();
            $arrayIniciais = array();

            foreach ($oParametros->debitos as $debito) {
                if (isset($debito->inicial)) {
                    $arrayDebitos[] = $debito->inicial;
                    $arrayIniciais[] = $debito->inicial;
                    continue;
                }

                $arrayDebitos[] = $debito;
            }

            $service = Service\Relatorio\Factory::create($oParametros->tipoDebito, $oParametros->cadTipo, $arrayDebitos);
            $debitos = $service->processar();

            $processos = array();
            $iniciais = array();
            $recibos = array();

            foreach ($debitos as $debito) {
                if ($debito instanceof \recibo) {
                    $recibos = $debitos;
                    break;
                }

                if ($debito instanceof ProcessoForoEntity) {
                    $processos[] = $debito;
                }

                if ($debito instanceof InicialEntity) {
                    $iniciais[] = $debito;
                }
            }

            $daoCgm = new cl_cgm();
            $result = \db_query($daoCgm->sql_query($oParametros->cgm, 'z01_cgccpf, z01_nome'));

            $cgm = pg_fetch_object($result, 0);

            $processoForo = ProcessoForo::getInstance();

            if (empty($arrayIniciais)) {
                $numpres = implode(',', array_map(function ($numpres) {
                    return $numpres->numpre;
                }, $arrayDebitos));

                $sql = "SELECT termoini.inicial
                          FROM termoini
                          JOIN termo
                            ON v07_parcel = parcel
                         WHERE v07_numpre IN ({$numpres});";

                $result = db_query($sql);

                $arrIniciais = \db_utils::getCollectionByRecord($result);

                foreach ($arrIniciais as $key => $inicial) {
                    $arrayIniciais[] = $inicial->inicial;
                }
            }

            if ($processoForo->verificaProcessoMigracaoPago($arrayIniciais)) {
                foreach ($recibos as $key => $recibo) {
                    $recibo->setPartilhaPagaMigracao(true);
                }

                foreach ($processos as $processo) {
                    foreach ($processo->getProcessoForoPartilhas() as $partilha) {
                        $partilha->setPartilhaPagaMigracao(true);
                    }
                }

                foreach ($iniciais as $inicial) {
                    foreach ($inicial->getProcessoForoPartilhas() as $partilha) {
                        $partilha->setPartilhaPagaMigracao(true);
                    }
                }
            }

            $relatorio = new Relatorio\Custas;
            $relatorio
              ->setProcessos($processos)
              ->setIniciais($iniciais)
              ->setRecibos($recibos)
              ->setCpfCnpj($cgm->z01_cgccpf)
              ->setContribuinte($cgm->z01_nome);

            if (empty($recibos)) {
                $relatorio->setDatasVencimentos($service->getDatasVencimentos());
            }

            $oRetorno->arquivo = $relatorio->imprimir(false);

          // rollback necessário para não criar registros de recibo no banco de dados.
            db_fim_transacao(true);

            break;
        case "verificaLiberaTef":
            if (!isset($oParametros->tipoDebito)) {
                throw new \Exception("Tipo de débito não informado!");
            }

            $configuracoesteftipodebitoRepository = new ConfiguracoesteftipodebitoRepository();
            $oConfiguracoesteftipodebito = $configuracoesteftipodebitoRepository->getByTipo($oParametros->tipoDebito);

            $clcfautent = new cl_cfautent;

            $sSql = $clcfautent->sql_query_file(null, "*", null, "k11_ipterm = '".db_getsession("DB_ip")."'");
            $rResult = db_query($sSql);

            if (!$rResult) {
                throw new \Exception("Erro ao buscar os dados da autenticadora. ".pg_last_error());
            }

            $oResult = \db_utils::fieldsMemory($rResult, 0);

            $oRetorno->liberaTef = false;
            $oRetorno->liberadoTefTipoDebito = ($oConfiguracoesteftipodebito->k196_aceitatef == "t");
            $oRetorno->isAutenticadora = ($oResult->k11_tef == "t");
            $oRetorno->idAutenticadora = $oResult->k11_id;

            if ($oRetorno->liberadoTefTipoDebito == "t" && $oRetorno->isAutenticadora) {
                $clcfautentconta = new cl_cfautentconta;
                $rResult = $clcfautentconta->sql_record($clcfautentconta->sql_query($oResult->k11_id));
                $oResult = \db_utils::fieldsMemory($rResult, 0);

                $oRetorno->contaAutenticadora = $oResult->k16_conta;
                $oRetorno->liberaTef = true;
            }
            break;
        case "verificaLiberaDebito":
            if (!isset($oParametros->tipoDebito)) {
                throw new \Exception("Tipo de débito não informado!");
            }
            
            $clarretipo = new cl_arretipo;

            $result   = $clarretipo->sql_record($clarretipo->sql_query($oParametros->tipoDebito,'k00_emrec'));

            if (!$result) {
                throw new \Exception("Erro ao buscar os dados do Tipo de débito . ".pg_last_error());
            }

            $oResult = \db_utils::fieldsMemory($result, 0);

            $oRetorno->k00_emrec = $oResult->k00_emrec;
            break;
    }
} catch (Exception $oErro) {
    $oRetorno->erro      = true;
    $oRetorno->sMensagem = urlencode($oErro->getMessage());
}

echo $oJson->encode($oRetorno);
