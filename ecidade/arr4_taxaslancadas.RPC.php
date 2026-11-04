<?php

/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2018  DBSeller Servicos de Informatica
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

use App\Domain\Tributario\Arrecadacao\Repositories\GrupoTaxasRepository;
use App\Domain\Tributario\Arrecadacao\Repositories\TaxaGrupoTaxasRepository;
use ECidade\Tributario\Arrecadacao\Model\Subtaxas;
use ECidade\Tributario\Arrecadacao\Model\TaxasLancadas;
use ECidade\Tributario\Arrecadacao\Model\TaxasLancadasDepart;
use ECidade\Tributario\Arrecadacao\Model\TaxasLancadasDinamicos;
use ECidade\Tributario\Arrecadacao\Repository\SubtaxasRepository;
use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasRepository;
use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasDepartRepository;
use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasDinamicosRepository;

$post = db_utils::postMemory($_REQUEST);
$post->json = str_replace("\\", "", $post->json);
$parametro = JSON::create()->parse($post->json);
$retorno = (object)array('erro' => false, 'mensagem' => '');

try {
    db_inicio_transacao();

    switch ($parametro->executa) {
        case "salvar":
            db_inicio_transacao();

            $taxasLancadas = new TaxasLancadas();
            $taxasLancadasRepository = TaxasLancadasRepository::getInstance();

            $taxasLancadas->setSequencial($parametro->ar44_sequencial);
            $taxasLancadas->setDescricao($parametro->ar44_descricao);
            $taxasLancadas->setValorinflator($parametro->ar44_valorinflator);
            $taxasLancadas->setInflator($parametro->ar44_inflator);
            $taxasLancadas->setDiasvencimento($parametro->ar44_diasvencimento);
            $taxasLancadas->setTipo($parametro->ar44_tipo);
            $taxasLancadas->setReceitaxaexpediente($parametro->ar44_receitaxaexpediente);
            $taxasLancadas->setValortaxaexpediente($parametro->ar44_valortaxaexpediente);
            $taxasLancadas->setDatavigencia((!empty($parametro->ar44_datavigencia)
                ? date('Y-m-d', strtotime(str_replace("/", "-", $parametro->ar44_datavigencia))) : ""));
            $taxasLancadas->setProcedencia((!empty($parametro->ar44_procedencia)
                ? $parametro->ar44_procedencia : null));
            $taxasLancadas->setReceita((!empty($parametro->ar44_receita) ? $parametro->ar44_receita : null));
            $taxasLancadas->setEmissaoweb($parametro->ar44_emissaoweb);
            $taxasLancadas->setRecursoadm($parametro->ar44_recursoadm);
            $taxasLancadas->setOrigem($parametro->ar44_origem);
            $taxasLancadas->setPermiteSubtaxas($parametro->ar44_permitesubtaxas);

            $ar44_sequencial = $taxasLancadasRepository->persist($taxasLancadas);

            $taxasLancadasDepart = new TaxasLancadasDepart();
            $taxasLancadasDepartRepository = TaxasLancadasDepartRepository::getInstance();

            $taxasLancadasDepart->setSequencial(null);
            $taxasLancadasDepart->setTaxaslancadas($ar44_sequencial);

            $taxasLancadasDepartRepository->delete($taxasLancadasDepart);

            if (!empty($parametro->departamentos)) {
                foreach (explode(",", $parametro->departamentos) as $departamento) {
                    $taxasLancadasDepart->setDepartamento($departamento);

                    $taxasLancadasDepartRepository->persist($taxasLancadasDepart);
                }
            }

            $aCamposDinamicos = JSON::create()->parse($parametro->camposDonamicos);

            $taxasLancadasDinamicos = new TaxasLancadasDinamicos();
            $taxasLancadasDinamicosRepository = TaxasLancadasDinamicosRepository::getInstance();

            $taxasLancadasDinamicos->setTaxaslancadas($ar44_sequencial);

            $taxasLancadasDinamicosRepository->delete($taxasLancadasDinamicos);

            if (!empty($aCamposDinamicos)) {
                foreach ($aCamposDinamicos as $key => $oCamposDinamicos) {
                    $taxasLancadasDinamicos->setCodcam($oCamposDinamicos->ar47_codcam);
                    $taxasLancadasDinamicos->setObrigatorio($oCamposDinamicos->ar47_obrigatorio);
                    $taxasLancadasDinamicos->setTipocampo($oCamposDinamicos->ar47_tipocampo);
                    $taxasLancadasDinamicos->setValordefault(($oCamposDinamicos->ar47_valordefault != ""
                        ? $oCamposDinamicos->ar47_valordefault
                        : null));

                    $taxasLancadasDinamicosRepository->persist($taxasLancadasDinamicos);
                }
            }
            if (isset($parametro->subTaxasSelecionadas) && count($parametro->subTaxasSelecionadas) > 0) {
                $subtaxasRepository = SubtaxasRepository::getInstance();
                $subtaxasRepository->excluirSubtaxas(null, "ar54_taxa = $ar44_sequencial");

                foreach ($parametro->subTaxasSelecionadas as $sequencialSubtaxa) {
                    $subTaxa = new Subtaxas();
                    $subTaxa->setSequencial(null);
                    $subTaxa->setSequencialTaxa($ar44_sequencial);
                    $subTaxa->setSequencialSubtaxa($sequencialSubtaxa);

                    $subtaxasRepository->persist($subTaxa);
                }
            }

            $taxaGrupoTaxasRepository = new TaxaGrupoTaxasRepository();
            db_query($taxaGrupoTaxasRepository->sqlDeletar(null, $ar44_sequencial));
            
            if (isset($parametro->gruposTaxasSelecionados) && count($parametro->gruposTaxasSelecionados) > 0) {
                foreach ($parametro->gruposTaxasSelecionados as $sequencialGrupo) {
                    db_query($taxaGrupoTaxasRepository->sqlAdicionar($ar44_sequencial, $sequencialGrupo));
                }
            }

            db_fim_transacao(false);
            $retorno->mensagem = "Taxa salva com sucesso.";

            break;

        case "buscar":
            $taxasLancadasRepository = TaxasLancadasRepository::getInstance();

            $oTaxa = $taxasLancadasRepository->getTaxa($parametro->ar44_sequencial);

            $taxasLancadasDepart = new TaxasLancadasDepart();
            $taxasLancadasDepartRepository = TaxasLancadasDepartRepository::getInstance();

            $taxasLancadasDepart->setTaxaslancadas($parametro->ar44_sequencial);
            $oDepartamentos = $taxasLancadasDepartRepository->getDepartamentos($taxasLancadasDepart);

            $oTaxa->departamentos = implode(",", array_map(function ($oDepartamento) {
                return $oDepartamento->ar45_departamento;
            }, $oDepartamentos));

            $taxasLancadasDinamicos = new TaxasLancadasDinamicos();
            $taxasLancadasDinamicosRepository = TaxasLancadasDinamicosRepository::getInstance();

            $taxasLancadasDinamicos->setTaxaslancadas($parametro->ar44_sequencial);
            $oTaxa->camposDinamicos = $taxasLancadasDinamicosRepository->getCampos($taxasLancadasDinamicos);

            $oTaxa->ar44_datavigencia = (!empty($oTaxa->ar44_datavigencia)
                ? date('d/m/Y', strtotime($oTaxa->ar44_datavigencia))
                : "");

            $subtaxasRepository = SubtaxasRepository::getInstance();
            $subtaxas = $subtaxasRepository->getSubtaxas('*', "ar54_taxa = $parametro->ar44_sequencial");

            // busca das subtaxas
            $oTaxa->subTaxas = array_map(function ($subtaxa) {
                return getInformacoesTaxa($subtaxa);
            }, $subtaxas);

            $retorno->oTaxa = $oTaxa;
            break;

        case "buscargrupo":
            $grupoTaxasRepository = new GrupoTaxasRepository();
            $aGrupos = $grupoTaxasRepository->buscarSemPaginacao($parametro->ar55_sequencial);
            $oGrupo = $aGrupos[0];

            $oGrupo['taxas'] = array_map(function ($taxa) {
                return getInformacoesTaxa((object)$taxa);
            }, $oGrupo['taxas']);

            $retorno->oGrupo = $oGrupo;

            break;

        case "listar":
            $sWhere = "";

            if (isset($parametro->isDepartamento)) {
                $taxasLancadasDepart = new TaxasLancadasDepart();
                $taxasLancadasDepartRepository = TaxasLancadasDepartRepository::getInstance();

                $departamento = db_getsession("DB_coddepto");
                $taxasLancadasDepart->sWhere = "(ar45_sequencial IS NULL OR ar45_departamento = {$departamento})";

                $oDepartamentos = $taxasLancadasDepartRepository->getDepartamentos($taxasLancadasDepart);

                $sTaxas = implode(",", array_map(function ($oDepartamento) {
                    return $oDepartamento->ar45_taxaslancadas;
                }, $oDepartamentos));

                if (!empty($sTaxas)) {
                    $sWhere = " ar44_sequencial IN ({$sTaxas}) AND ";
                }

                $sWhere .= " (TO_CHAR(ar44_datavigencia, 'YYYY-MM-DD')::date >= '"
                    . date("Y-m-d")
                    . "'::date OR ar44_datavigencia IS NULL)";
            }

            $taxasLancadas = new TaxasLancadas();
            $taxasLancadasRepository = TaxasLancadasRepository::getInstance();

            $oTaxas = $taxasLancadasRepository->getTaxas($sWhere);

            $retorno->oTaxas = $oTaxas;
            break;

        case "listargrupos":
            $grupoTaxasRepository = new GrupoTaxasRepository();

            //filtro de tipo de origem do grupo de taxas
            // 1 - todos
            // 2 - CGM
            // 3 - Matricula
            // 4 - Inscricao
            $filtroTipoOrigem = !isset($parametro->filtroOrigem) || (int)$parametro->filtroOrigem == 1
                ? null : $parametro->filtroOrigem;
            $aGruposTaxas = $grupoTaxasRepository->buscarSemPaginacao(
                null,
                null,
                null,
                $filtroTipoOrigem
            );

            $retorno->aGruposTaxas = $aGruposTaxas;
            break;

        case "listarInflatores":
            $Datausu = $_SESSION['DB_anousu'];
            $Datausu .= "-01-01";

            $sql = "SELECT
                        *
                    FROM
                        infla
                        INNER JOIN inflan ON i01_codigo = i02_codigo
                    WHERE
                        i02_codigo = '$oParam->inflator'
                        AND i02_data = '$Datausu';
            ";

            $result = db_query($sql);
            if (pg_num_rows($result) > 0) {
                $retorno->resultado = true;
            } else {
                $retorno->resultado = false;
                $retorno->mensagem = "Inflator sem valor lançado";
            }

            break;

        default:
            throw new Exception('Nenhuma ação encontrada.');
            break;
    }

    db_fim_transacao(false);
} catch (Exception $erro) {
    db_fim_transacao(true);

    $retorno->erro = true;
    $retorno->mensagem = $erro->getMessage();
}

function getInformacoesTaxa($taxa)
{
    $taxasLancadasRepository = TaxasLancadasRepository::getInstance();
    $oTaxa = $taxasLancadasRepository->getTaxa($taxa->ar44_sequencial);

    $taxasLancadasDepart = new TaxasLancadasDepart();
    $taxasLancadasDepartRepository = TaxasLancadasDepartRepository::getInstance();

    $taxasLancadasDepart->setTaxaslancadas($taxa->ar44_sequencial);
    $oDepartamentos = $taxasLancadasDepartRepository->getDepartamentos($taxasLancadasDepart);

    $oTaxa->departamentos = implode(",", array_map(function ($oDepartamento) {
        return $oDepartamento->ar45_departamento;
    }, $oDepartamentos));

    $taxasLancadasDinamicos = new TaxasLancadasDinamicos();
    $taxasLancadasDinamicosRepository = TaxasLancadasDinamicosRepository::getInstance();

    $taxasLancadasDinamicos->setTaxaslancadas($taxa->ar44_sequencial);
    $oTaxa->camposDinamicos = $taxasLancadasDinamicosRepository->getCampos($taxasLancadasDinamicos);

    $oTaxa->ar44_datavigencia = (!empty($oTaxa->ar44_datavigencia)
        ? date('d/m/Y', strtotime($oTaxa->ar44_datavigencia)) : "");

    return $oTaxa;
}

echo JSON::create()->stringify($retorno);
