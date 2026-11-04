<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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

require_once(modification("interfaces/iPadArquivoTxtBase.interface.php"));
require_once(modification("model/contabilidade/arquivos/sigfis/SigfisArquivoBase.model.php"));

/**
 *
 * Classe Responsável pela geração dos dados necessários para o arquivo Diversos
 * @author
 * @package contabilidade
 * @subpackage sigfis
 *
 */
class SigfisArquivoFolha extends SigfisArquivoBase implements iPadArquivoTXTBase
{
    protected $iCodigoLayout     = 200;
    protected $sNomeArquivo      = 'FolhaPgt';
    protected $aMovimentoContabil = array();

    /**
     * Busca os dados para gerar o Arquivo Diversos
     */
    public function gerarDados()
    {

        $iInstituicaoSessao = db_getsession('DB_instit');

        $clConLanCamEmp = new cl_conlancamemp;

        $qtdTrubunal = strlen($this->sCodigoTribunal) + 1;

        $sCampos  = "LPAD('{$this->sCodigoTribunal}',{$qtdTrubunal},' ') as codTribunal, ";
        $sCampos .= "LPAD(o58_unidade::text,4,' ') as codUnidade, ";
        $sCampos .= "LPAD(e60_codemp::text,10,' ') as codUnidadeOrcamento, ";
        $sCampos .= "LPAD(to_char(max(conlancam.c70_data), 'ddmmYYYY'),8,' ') as dataEmpenho, ";
        $sCampos .= "LPAD(e60_anousu::text,4,' ') as anoEmpenho, ";
        $sCampos .= "extract(month from max(conlancam.c70_data)::date) as mesreferencia, ";
        $sCampos .= "LPAD(to_char(max(conlancam.c70_data), 'YYYY'),4,' ') as anoReferencia, ";
        $sCampos .= "LPAD(substring(regexp_replace(e60_resumo,'[\n\r]+', ' - ', 'g' ),0,120),120) as objetoFolha, ";
        $sCampos .= "CAST(ROUND(sum( case c53_tipo when 30 then conlancam.c70_valor when 31 then (conlancam.c70_valor * -1) end), 2) as text) as valorFolhaEmpenho, ";
        $sCampos .= "coalesce(e164_data, to_char(c70_data,'MM/YYYY')) as anoMesCompetencia, ";
        $sCampos .= "orcdotacao.o58_orgao as codOrgao, ";
        $sCampos .= "'0' as numeroSubEmpenho ";

        $sWhere  = " conlancam.c70_anousu = {$this->iAnoUso} ";
        $sWhere .= "and empempenho.e60_instit = {$iInstituicaoSessao} ";
        $sWhere .= "and empempenho.e60_anousu = {$this->iAnoUso} ";
        $sWhere .= "and conhistdoc.c53_tipo in (30, 31) ";
        $sWhere .= "and conlancam.c70_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' ";
        $sWhere .= "and exists ( ";
        $sWhere .= "    select ";
        $sWhere .= "        1 ";
        $sWhere .= "    from ";
        $sWhere .= "        empelemento ";
        $sWhere .= "        join orcelemento on o56_codele = e64_codele ";
        $sWhere .= "        and o56_anousu = {$this->iAnoUso} ";
        $sWhere .= "    where ";
        $sWhere .= "        e64_numemp = e60_numemp ";
        $sWhere .= "        and o56_elemento ilike '%331%') ";
        $sWhere .= "group by empempenho.e60_codemp, z01_numcgm, z01_cgccpf, z01_nome, empempenho.e60_anousu, orcdotacao.o58_orgao, orcdotacao.o58_unidade, e60_resumo, c70_data, empcompetencialiquidacao.e164_data ";

        $sOrdem = " e60_codemp :: int, c70_data";

        $sSqlConLanCamEmp = "
        select
            {$sCampos}
        from
            conlancamemp
        inner join conlancam on
            conlancam.c70_codlan = conlancamemp.c75_codlan
        inner join conlancamord on
            conlancamord.c80_codlan = conlancam.c70_codlan
        inner join pagordem on
            pagordem.e50_codord = conlancamord.c80_codord
        inner join empempenho on
            empempenho.e60_numemp = conlancamemp.c75_numemp
        inner join cgm on
            cgm.z01_numcgm = empempenho.e60_numcgm
        inner join conlancamdoc on
            conlancamdoc.c71_codlan = conlancam.c70_codlan
        inner join orcdotacao on
            orcdotacao.o58_anousu = empempenho.e60_anousu
            and orcdotacao.o58_coddot = empempenho.e60_coddot
        inner join conhistdoc on
            conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
        inner join conlancampag on
            conlancampag.c82_codlan = conlancam.c70_codlan
        inner join conplanoreduz on
            conplanoreduz.c61_reduz = conlancampag.c82_reduz
            and conplanoreduz.c61_anousu = conlancampag.c82_anousu
        inner join conplano on
            conplano.c60_codcon = conplanoreduz.c61_codcon
            and conplano.c60_anousu = conplanoreduz.c61_anousu
        left join empenho.empcompetencialiquidacao on
            conlancamord.c80_codord = empenho.empcompetencialiquidacao.e164_codord
        where
            {$sWhere}
        order by
            {$sOrdem}
        ";

        $rsConLanCamEmp    = $clConLanCamEmp->sql_record($sSqlConLanCamEmp);
        $this->addLog("=====Arquivo" . $this->getNomeArquivo() . " Erros:\n");

        if ($clConLanCamEmp->numrows > 0) {

            if (empty($this->sCodigoTribunal)) {
                throw new Exception("O código do tribunal deve ser informado para geração do arquivo");
            }

            for ($i = 0; $i < $clConLanCamEmp->numrows; $i++) {

                $oDadosQuery = new stdClass();
                $oDadosQuery = db_utils::fieldsMemory($rsConLanCamEmp, $i);

                $oDados = new stdClass();

                $oDados->cd_Unidade             =  str_pad($oDadosQuery->codtribunal,    4, ' ', STR_PAD_LEFT);
                $oDados->cd_UnidadeOrcamentaria =  str_pad($oDadosQuery->codunidade,     4, ' ', STR_PAD_LEFT);
                $oDados->nu_Empenho             =  str_pad($oDadosQuery->codunidadeorcamento,     10, ' ', STR_PAD_RIGHT);
                $oDados->dt_PagamentoEmpenho    =  str_pad($oDadosQuery->dataempenho, 0, ' ');
                $oDados->dt_AnoReferencia       =  str_pad($oDadosQuery->anoempenho, 4, ' ');
                $oDados->dt_MesReferencia       =  str_pad($oDadosQuery->mesreferencia, 2, '0', STR_PAD_LEFT);
                $oDados->dt_Ano                 =  str_pad($oDadosQuery->anoreferencia, 4, ' ');
                $res = preg_replace('/[^a-z0-9" "]/i', '', $oDadosQuery->objetofolha);
                $oDados->de_Folha               =  str_pad(substr($res, 0, 120), 120, ' ', STR_PAD_RIGHT);
                $oDados->vl_Folha               =  str_pad(str_replace('.', '', $oDadosQuery->valorfolhaempenho), 16, ' ', STR_PAD_LEFT);
                $oDados->dt_AnoMes              =  str_pad(preg_replace('/(\d{2}).(\d{4})/', '$2$1', $oDadosQuery->anomescompetencia), 6, ' ', STR_PAD_RIGHT);
                $oDados->cd_Orgao               =  str_pad(trim($oDadosQuery->codorgao), 4, ' ', STR_PAD_LEFT);
                $oDados->nu_EmpenhoSup          =  str_pad('0',  10, ' ', STR_PAD_RIGHT);

                $oDados->codigolinha            = 646;
                $this->aDados[] = $oDados;
            }
        }

        $this->addLog("===== Fim do Arquivo: " . $this->getNomeArquivo() . "\n");
    }
}
