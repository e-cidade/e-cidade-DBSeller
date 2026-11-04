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

use App\Domain\Patrimonial\Contratos\Services\AcordoLicitacaoCompartilhadaService;
use ECidade\Patrimonial\Licitacao\Licitacon\Regra\Emissao\DotacaoCon as Regra;

class DotacaoConLicitaCon extends ArquivoLicitaCon
{

    const NOME_ARQUIVO = "DOTACAO_CON";

    public function __construct(CabecalhoLicitaCon $oCabecalho)
    {

        parent::__construct($oCabecalho, new Regra($oCabecalho->getDataGeracao()));
        $this->sNomeArquivo  = self::NOME_ARQUIVO;
        $this->iCodigoLayout = $this->oRegra->getCodigoLayout();
    }

  /**
   * @return array
   */
    public function getDados()
    {

        $aTiposInstrumento = LicitaConTipoInstrumentoAcordo::getSiglas();

        $aDados = array();

        $sCampos = implode(', ', array(
        'ac16_sequencial            as sequencial',
        'ac16_numero                as nr_contrato',
        'ac16_anousu                as ano_contrato',
        'ac16_tipoinstrumento       as tp_instrumento',
        'ac16_origem                as origem',
        '(case when empdot.o58_projativ is not null then empdot.o58_projativ else acodot.o58_projativ end) as cd_projeto_atividade',

        '
      (CASE
         WHEN empdot.o58_codigo IS NOT NULL
           THEN (
                  select o15_recurso
                    from orctiporec
                    join fonterecurso on o15_codigo = orctiporec_id
                      and exercicio = empdot.o58_anousu
                    where o15_codigo = empdot.o58_codigo
                )
         ELSE (
                select o15_recurso
                  from orctiporec
                  join fonterecurso on o15_codigo = orctiporec_id
                                   and exercicio = acodot.o58_anousu
                  where o15_codigo = acodot.o58_codigo)
       END) AS cd_recurso_orcamentario ',

        'substr((case when empele.o56_elemento is not null then empele.o56_elemento else acoele.o56_elemento end), 2, 6) as cd_natureza_despesa',
        ));

        $sDataAtual = $this->oCabecalho->getDataGeracao()->getDate();

        $sWhere = implode(' and ', array(
        "ac16_instit = {$this->oCabecalho->getInstituicao()->getCodigo()}",
        "(ac58_sequencial is null or ac58_data >= '{$sDataAtual}')",
        "(empdot.o58_projativ is not null or acodot.o58_projativ is not null)",
        "acordoitem.ac20_sequencial is not null"
        ));

        $sSql = implode(" \n", array(
        "select distinct {$sCampos} from acordo",
        'inner join acordoposicao on ac26_acordo = ac16_sequencial',
        'left join acordoencerramentolicitacon on ac16_sequencial = ac58_acordo',

        // Busca dotações pela estrutura do acordo
        'inner join acordoitem         on ac20_acordoposicao         = ac26_sequencial',
        'left join acordoitemdotacao  on ac22_acordoitem            = ac20_sequencial',
        'left join orcdotacao acodot  on (ac22_anousu, ac22_coddot) = (acodot.o58_anousu, acodot.o58_coddot)',
        'left join orcelemento acoele on (acodot.o58_codele, acodot.o58_anousu) = (acoele.o56_codele, acoele.o56_anousu)',

        // Busca dotações pelos empenhos vinculados
        'left join acordoempempenho   on ac54_acordo              = ac16_sequencial',
        'left join empempenho         on ac54_empempenho          = e60_numemp',
        'left join orcdotacao empdot  on (e60_anousu, e60_coddot) = (empdot.o58_anousu, empdot.o58_coddot)',
        'left join orcelemento empele on (empdot.o58_codele, empdot.o58_anousu) = (empele.o56_codele, empele.o56_anousu)',

        "where {$sWhere}


      ",

        'order by ac16_sequencial',
        ));


      //echo $sSql; die();
        $rsContratos = db_query($sSql);
        if (!$rsContratos) {
            $sMensagem = "Não foi possível buscar os contratos para geração do arquivo {$this->sNomeArquivo}.";
            throw new DBException($sMensagem);
        }

        $iQuantidadeContratos = pg_num_rows($rsContratos);
        for ($iContrato = 0; $iContrato < $iQuantidadeContratos; $iContrato++) {
            $oStdContrato = db_utils::fieldsMemory($rsContratos, $iContrato);
            $oStdLicitacao = $this->oRegra->getDadosDaLicitacao(
                $oStdContrato->sequencial,
                $this->oCabecalho->getDataGeracao()
            );

            $service = new AcordoLicitacaoCompartilhadaService();
            $dados = $service->buscarDados($oStdContrato->sequencial);

            if (!$this->oRegra->enviar($oStdLicitacao->codigo)) {
                continue;
            }

            $modalidade = $service->getModalidade($dados->ac63_modalidade)->getSiglaTipoCompraTribunal();
            $oStdLinha = new stdClass;
            $oStdLinha->NR_LICITACAO = $dados->ac63_sequencial == "" ? $oStdLicitacao->numero : $dados->ac63_numero;
            $oStdLinha->ANO_LICITACAO =  $dados->ac63_sequencial == "" ? $oStdLicitacao->ano: $dados->ac63_ano;
            $oStdLinha->CD_TIPO_MODALIDADE = $dados->ac63_sequencial == "" ? $oStdLicitacao->tipo : $modalidade;

            $oStdLinha->NR_CONTRATO = $oStdContrato->nr_contrato;
            $oStdLinha->ANO_CONTRATO = $oStdContrato->ano_contrato;
            $oStdLinha->TP_INSTRUMENTO = $aTiposInstrumento[$oStdContrato->tp_instrumento];

            $oStdLinha->CD_PROJETO_ATIVIDADE = $oStdContrato->cd_projeto_atividade;
            $oStdLinha->CD_RECURSO_ORCAMENTARIO = $oStdContrato->cd_recurso_orcamentario;
            $oStdLinha->CD_NATUREZA_DESPESA = $oStdContrato->cd_natureza_despesa;

            $aDados[] = $oStdLinha;
        }

        return $aDados;
    }
}
