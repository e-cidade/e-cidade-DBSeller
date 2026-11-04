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

namespace ECidade\RecursosHumanos\ESocial\Repository;

use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use BusinessException;
use DBException;
use ParameterException;
use db_utils;
use cl_cgm;
use DBCompetencia;
use InstituicaoRepository;

/**
 * Class CadastroBeneficiario
 * @package ECidade\RecursosHumanos\ESocial\Repository
 */
class CadastroBeneficiario extends \BaseClassRepository
{
    protected static $oInstance;

    /**
     * @param DBCompetencia $dbCompetencia
     * @return \stdClass[]
     * @throws DBException
     */
    public static function buscarBeneficiarios(DBCompetencia $dbCompetencia, $servidores = null, $selecao = null)
    {
        $retorno = array();
        
        $servidorMovimentacaoRepository = new ServidorMovimentacaoRepository();
        $codigoInstituicao = InstituicaoRepository::getInstituicaoSessao()->getCodigo();
        
        if (empty($servidores)) {
            $clRhPessoalmov = new \cl_rhpessoalmov();
          
          /*
           * Buscamos todas as matriculas que possuem a situacao do vinculo do regime aposentado/inativo ou pensionista
           * e que nao possuem rescisao
           */
            //validacao regime
            $where  = " rhregime.rh30_vinculo in ('I','P') ";
            
            //validacao rescisao
            $where .= " AND not exists (select 1 from rhpesrescisao where rh05_seqpes = rh02_seqpes) ";
            
            if (!empty($selecao)) {
                $clselecao = new \cl_selecao();
                $condicaoSelecao = $clselecao->getCondicaoSelecao($selecao, $codigoInstituicao);
                $where .= " and {$condicaoSelecao} ";
            }
            $sSqlServidores = $clRhPessoalmov->sql_query_baseServidores(
                $dbCompetencia->getMes(),
                $dbCompetencia->getAno(),
                $codigoInstituicao,
                "rh01_regist",
                $where,
                "rh01_regist",
                "rh01_regist"
            );
            $rsServidores = $clRhPessoalmov->sql_record($sSqlServidores);
            $qtdServidores = $clRhPessoalmov->numrows;
            if ($qtdServidores == 0) {
                $mensagem = "Nenhum cadastro de beneficário encontrado na competência informada na instituição logada.";
                throw new DBException($mensagem);
            }
          
            for ($contador = 0; $contador < $qtdServidores; $contador++) {
                $matriculaServidor = db_utils::fieldsMemory($rsServidores, $contador)->rh01_regist;
                
                $servidor = \ServidorRepository::getInstanciaByCodigo($matriculaServidor);
                $servidor->movimentacao = $servidorMovimentacaoRepository->scopeSeqPes(
                    $servidor->getCodigoMovimentacao()
                )->first();
                
                $retorno[] = $servidor;
            }
          
            if (count($retorno) == 0) {
                $mensagem = "Nenhum cadastro de beneficário encontrado na competência informada na instituição logada.";
                throw new DBException($mensagem);
            }
        } else {
            foreach ($servidores as $servidor) {
              /*
               * Verificamos se a matricula informada eh aposentado ou pensionista e não possui rescisao
               */
                $clRegime = new \cl_rhregime();

                $where  = " rhregime.rh30_codreg = {$servidor->getCodigoRegime()} ";
                $where .= " AND rhregime.rh30_vinculo in ('I','P')";
                           
                $rsRegime = $clRegime->sql_record($clRegime->sql_query_file(null, "rh30_codreg", null, $where));
                if ($clRegime->numrows == 0) {
                    continue;
                }
                
                $servidor->movimentacao = $servidorMovimentacaoRepository->scopeSeqPes(
                    $servidor->getCodigoMovimentacao()
                )->first();
                
                $retorno[] = $servidor;
            }
            
            if (count($retorno) == 0) {
                $mensagem  = "Nenhum cadastro de beneficário encontrado.\n";
                $mensagem .= "Verifique o vinculo do regime e se as matriculas informadas ";
                $mensagem .= "não estão rescindidas.";
                throw new DBException($mensagem);
            }
        }
        
        return $retorno;
    }
}
