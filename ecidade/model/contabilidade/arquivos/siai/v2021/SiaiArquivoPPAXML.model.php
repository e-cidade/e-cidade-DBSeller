<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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
require_once(modification("model/contabilidade/arquivos/siai/SiaiArquivoBase.model.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("libs/db_stdlib.php"));

class SiaiArquivoPPAXML extends SiaiArquivoBase
{

    protected $oDocumento;
  
    public $aTipoPrograma = array(1 => "FINALÍSTICO",
                                2 => "Programas de Apoio as Políticas e Áreas Especiais",
                                3 => "Programas Temáticos",
                                4 => "Programas de Gestão, Manutenção e Serviços ao Estado");
  
    function __construct()
    {
    
        $this->oDocumento = new DOMDocument('1.0', 'ISO-8859-1');
        $this->oDocumento->formatOutput = true;
        $this->oDocumento->encoding = 'ISO-8859-1';
    }
  
    function addElement($oAtributos, $oElementoPai = null)
    {
    
        foreach ($oAtributos as $sAtributo => $sValor) {
            if (!empty($oElementoPai)) {
                $oElementoPai->appendChild($this->oDocumento->createElement($sAtributo, $sValor));
            } else {
                $this->oDocumento->createElement($sAtributo, $sValor);
            }
        }
    
        return true;
    }
  
  /**
   * Gera o arquivo XML
   */
    public function processar()
    {
      
        $sWhereOrgaoUnidade = "";
        if ($this->getCodigoOrgao() != 0) {
            $sWhereOrgaoUnidade = " and exists ( select 1 
                                     from orcamento.orcindicaprograma
                                          inner join plugins.orcindicaunidade on orcindicaunidade.orcindica = orcindicaprograma.o18_orcindica
                                    where orcindicaunidade.orgao = {$this->getCodigoOrgao()}
                                      and orcindicaunidade.unidade = {$this->getCodigoUnidade()} 
                                      and orcindicaunidade.instituicao = ".db_getsession("DB_instit").")";
        }
      
        $oNodeRemessa = $this->oDocumento->createElement("remessa");
        /*
         * linha da remessa
         */
        $oDadosRemessa = new stdClass();
        $oDadosRemessa->codigoOrgao    = 422;
        $oDadosRemessa->cpfGestor      = "00061532401";
        $oDadosRemessa->tipoRemessa    = 1;
        $oDadosRemessa->ano            = $this->getAno();
        $oDadosRemessa->dataCriacao    = str_replace("/", "-", db_formatar($this->getDataGeracao(), "d"));
        $oDadosRemessa->sistemaGerador = "e-cidade";
        $this->addElement($oDadosRemessa, $oNodeRemessa);
      
        $oNodeProgramas = $this->oDocumento->createElement("programas");
      
        $sSqlPPALei = "select * 
                       from orcamento.ppalei 
                      where {$this->getAno()} between o01_anoinicio and o01_anofinal";
        $rsPPALei = db_query($sSqlPPALei);
        if (pg_num_rows($rsPPALei) == 0) {
            throw new Exception("Não encontrada versão do ppa para o ano {$this->getAno()}");
        }
        $oPPALei = db_utils::fieldsMemory($rsPPALei, 0);
      
        $sSqlProgramas = "select distinct 
                               orcprograma.o54_programa, 
                               orcprograma.o54_anousu,
                               orcprograma.o54_descr, 
                               orcprograma.o54_publicoalvo,
                               orcprograma.o54_tipoprograma,
                               orcprograma.o54_finali,
                               orcprograma.o54_justificativa,
                               orcprogramahorizontetemp.o17_dataini, 
                               orcprogramahorizontetemp.o17_datafin
                          from orcamento.orcprograma
                               left join orcamento.orcprogramahorizontetemp  on orcprogramahorizontetemp.o17_programa = orcprograma.o54_programa
                                                                            and orcprogramahorizontetemp.o17_anousu   = orcprograma.o54_anousu
                         where o54_anousu = ".$this->getAno()."
			               and orcprograma.o54_programa not in (1,999)
                           {$sWhereOrgaoUnidade}";
        $rsProgramas = db_query($sSqlProgramas);
        $iQtdProgramas = pg_num_rows($rsProgramas);
        if ($iQtdProgramas == 0) {
            throw new Exception("Não encontrados dados dos programas no exerício {$this->getAno()}");
        }
        for ($iInd = 0; $iInd < $iQtdProgramas; $iInd++) {
            $oDadosProgramas = db_utils::fieldsMemory($rsProgramas, $iInd);
          
            $oNodePrograma = $this->oDocumento->createElement("programa");
            /*
             * Comentada logica que busca os dados do orgao e unidade baseado nos indicadores.
             * Foi definido que ficaria fixo o nome Perfeitura Municipal do Natal e Camara Municipal do Natal para o programa 0159
            $sSqlOrgaoUnidade = "select distinct
                                      o40_orgao,
                                      o40_descr,
                                      o41_unidade,
                                      o41_descr
                                 from orcamento.orcindicaprograma
                                      inner join plugins.orcindicaunidade       on orcindicaunidade.orcindica        = orcindicaprograma.o18_orcindica
                                      inner join orcamento.orcunidade           on orcunidade.o41_unidade            = orcindicaunidade.unidade
                                                                               and orcunidade.o41_orgao              = orcindicaunidade.orgao
                                                                               and orcunidade.o41_instit             = orcindicaunidade.instituicao
                                                                               and orcunidade.o41_anousu             = orcindicaprograma.o18_anousu
                                      inner join orcamento.orcorgao             on orcorgao.o40_orgao                = orcindicaunidade.orgao
                                                                               and orcorgao.o40_instit               = orcindicaunidade.instituicao
                                                                               and orcorgao.o40_anousu               = orcindicaprograma.o18_anousu
                                       left join plugins.orcunidadecomplemento  on orcunidadecomplemento.anousu      = orcindicaprograma.o18_anousu
                                                                               and orcunidadecomplemento.instituicao = orcindicaunidade.instituicao
                                                                               and orcunidadecomplemento.orgao       = orcindicaunidade.orgao
                                                                               and orcunidadecomplemento.unidade     = orcindicaunidade.unidade
                                where orcindicaprograma.o18_orcprograma     = {$oDadosProgramas->o54_programa}
                                  and orcindicaprograma.o18_anousu          = {$oDadosProgramas->o54_anousu}";
            $rsOrgaoUnidade   = db_query($sSqlOrgaoUnidade);
            if (pg_num_rows($rsOrgaoUnidade) > 1) {
            $sLog = "Programa {$oDadosProgramas->o54_programa}: Encontrados mais de um Orgão/Unidade para os indicadores ligados ao programa\n";
            $sLog .= " - Deve ser verificado o cadastros de orgão/unidade dos indicadores vinculados ao programa \n";
            $sLog .= " - O arquivo esta sendo gerado com o primeiro orgão/unidade encontrado\n\n";
            $this->addLog($sLog);
            } else if (pg_num_rows($rsOrgaoUnidade) == 0) {
            $sLog  = "Programa {$oDadosProgramas->o54_programa}: Não foi possível identificar o Orgão/Unidade. \n";
            $sLog .= " - Dentre as possibilidades da causa do erro estão: \n";
            $sLog .= "   - Não existem indicadores vinculados ao programa\n";
            $sLog .= "   - Cadastro do indicador não possui vinculo com o orgão/unidade\n";
            $sLog .= "   - O vinculo com o orgão/unidade está inválido devido instituição, unidade/orgão ou exercício\n\n";
            $this->addLog($sLog);
            }
            $oDadosOrgaoUnidade = db_utils::fieldsMemory($rsOrgaoUnidade,0);
            */
          
            $sMetas = "";
            $sSqlMetas = "select array_to_string(array_accum(meta),'\n') as metas
                          from (select meta 
                                  from plugins.orcprojativprograma
                                       inner join plugins.orcprojativppametas on orcprojativppametas.orcprojativ = orcprojativprograma.orcprojativ 
                                                                             and orcprojativppametas.anousu      = orcprojativprograma.anousu
                                       inner join plugins.ppametas            on ppametas.sequencial             = orcprojativppametas.ppametas
                                       inner join orcamento.ppalei            on ppalei.o01_sequencial           = ppametas.ppalei
                                                                             and {$oDadosProgramas->o54_anousu} between o01_anoinicio and o01_anofinal
                                 where orcprojativprograma.anousu      = {$oDadosProgramas->o54_anousu}
                                   and orcprojativprograma.orcprograma = {$oDadosProgramas->o54_programa}
                                 order by orcprojativppametas.orcprojativ, orcprojativppametas.ppametas ) as dados";
            $rsMetas = db_query($sSqlMetas);
            if (pg_num_rows($rsMetas) > 0) {
                $sMetas = db_utils::fieldsMemory($rsMetas, 0)->metas;
            }
          
            if (empty($sMetas)) {
                $sLog = "Programa {$oDadosProgramas->o54_programa} - Não encontradas metas para o programa\n";
                $sLog .= " - Devem ser verificados os projeto/atividades vinculados ao programa e o cadastro das metas\n\n";
                $this->addLog($sLog);
            }
            /*
            if (strlen($sMetas) > 8000) {
             $sLog = "Programa {$oDadosProgramas->o54_programa} - No validador do arquivo XML existe um limite de caracteres para a descrição das metas\n";
             $sLog .= " - a quantidade de caracteres das metas do programa é ".strlen($sMetas)." e o limite é 8000\n\n";
             $this->addLog($sLog);
            }
            */
            $oPrograma = new stdClass();
            $oPrograma->codigoPrograma     = str_pad($oDadosProgramas->o54_programa, 4, "0", STR_PAD_LEFT);
            $oPrograma->nomePrograma       = utf8_encode($oDadosProgramas->o54_descr);
            $codigoOrgao        = "Prefeitura Municipal do Natal";
            $nomeOrgao          = "Prefeitura Municipal do Natal";
            if ($oDadosProgramas->o54_programa == 0159) {
                $codigoOrgao        = "Prefeitura Municipal do Natal";
                $nomeOrgao          = "Prefeitura Municipal do Natal";
            }
            $oPrograma->codigoOrgao        = $codigoOrgao;
            $oPrograma->nomeOrgao          = $nomeOrgao;
            $oPrograma->objetivo           = utf8_encode($oDadosProgramas->o54_finali);
            $oPrograma->metas              = utf8_encode($sMetas);
            $oPrograma->publicoAlvo        = utf8_encode($oDadosProgramas->o54_publicoalvo);
            $oPrograma->tipoPrograma       = utf8_encode($this->aTipoPrograma[1]);
            $oPrograma->dataInicio         = "{$oPPALei->o01_anoinicio}-01-01";
            $oPrograma->dataTermino        = "{$oPPALei->o01_anofinal}-12-31";
            $oPrograma->justificativa      = utf8_encode($oDadosProgramas->o54_justificativa);
            $oPrograma->esferaOrcamentaria = "FISCAL";
            $this->addElement($oPrograma, $oNodePrograma);
          
            $oNodeFonteFinanciamento = $this->oDocumento->createElement("fonteFinanciamento");
          
            $oFonteFinanciamento = new stdClass();
            $oFonteFinanciamento->valorFonteMunicipal       = 0.00;
            $oFonteFinanciamento->valorFonteEstadual        = 0.00;
            $oFonteFinanciamento->valorFonteFederal         = 0.00;
            $oFonteFinanciamento->valorFonteOperacaoCredito = 0.00;
            $oFonteFinanciamento->valorFonteOutros          = 0.00;
            $sSqlFonteFinanciamento = "select sum(case when ppafontesrecurso.sequencial = 1 then valor else 0 end) as municipal,
                                            sum(case when ppafontesrecurso.sequencial = 2 then valor else 0 end) as estadual,
                                            sum(case when ppafontesrecurso.sequencial = 3 then valor else 0 end) as operacaocredito,
                                            sum(case when ppafontesrecurso.sequencial = 4 then valor else 0 end) as federal,
                                            sum(case when ppafontesrecurso.sequencial = 5 then valor else 0 end) as parcerias,
                                            sum(case when ppafontesrecurso.sequencial = 6 then valor else 0 end) as diretamentearrecadados,
                                            sum(valor) as total
                                       from plugins.orcprojativprograma
                                            inner join plugins.orcprojativdetalhamentorecursos on orcprojativdetalhamentorecursos.orcprojativ = orcprojativprograma.orcprojativ 
                                                                                              and orcprojativdetalhamentorecursos.anousu      = orcprojativprograma.anousu 
                                            inner join plugins.ppafontesrecurso                on ppafontesrecurso.sequencial                 = orcprojativdetalhamentorecursos.ppafonterecurso
                                      where orcprojativprograma.anousu = {$oDadosProgramas->o54_anousu}
                                        and orcprojativprograma.orcprograma = {$oDadosProgramas->o54_programa}";
            $rsFonteFinanciamento = db_query($sSqlFonteFinanciamento);
            if (pg_num_rows($rsFonteFinanciamento) > 0) {
                $oDadosFonteFinanciamento = db_utils::fieldsMemory($rsFonteFinanciamento, 0);
              
                $oFonteFinanciamento->valorFonteMunicipal       = number_format($oDadosFonteFinanciamento->municipal, 2, '.', '');
                $oFonteFinanciamento->valorFonteEstadual        = number_format($oDadosFonteFinanciamento->estadual, 2, '.', '');
                $oFonteFinanciamento->valorFonteFederal         = number_format($oDadosFonteFinanciamento->federal, 2, '.', '');
                $oFonteFinanciamento->valorFonteOperacaoCredito = number_format($oDadosFonteFinanciamento->operacaocredito, 2, '.', '');
                $oFonteFinanciamento->valorFonteOutros          = number_format($oDadosFonteFinanciamento->diretamentearrecadados+$oDadosFonteFinanciamento->parcerias, 2, '.', '');
                $this->addElement($oFonteFinanciamento, $oNodeFonteFinanciamento);
            }
            $oNodePrograma->appendChild($oNodeFonteFinanciamento);
          
          
            $oNodeIndicadoresPrograma = $this->oDocumento->createElement("indicadoresPrograma");
          
            $sSqlIndicadores = "select orcindica.o10_indica,
                                     orcindica.o10_descr, 
                                     orcindica.o10_descrunidade,
                                     orcindicadados.dataapuracao,
                                     coalesce(orcindicaindiceesperado.o25_valor,0) as o25_valor 
                                from orcamento.orcindica
                                     inner join orcamento.orcindicaprograma        on orcindicaprograma.o18_orcindica       = orcindica.o10_indica
                                      left join plugins.orcindicadados             on orcindicadados.orcindica              = orcindica.o10_indica 
                                                                                  and orcindicadados.anousu                 = orcindicaprograma.o18_anousu
                                      left join orcamento.orcindicaindiceesperado  on orcindicaindiceesperado.o25_orcindica = orcindica.o10_indica
                                                                                  and orcindicaindiceesperado.o25_anousu    = orcindicaprograma.o18_anousu
                               where orcindicaprograma.o18_orcprograma = {$oDadosProgramas->o54_programa}  
                                 and orcindicaprograma.o18_anousu = {$oDadosProgramas->o54_anousu}";
            $rsIndicadores = db_query($sSqlIndicadores);
            if (pg_num_rows($rsIndicadores) > 0) {
                $oNodeIndicadores = $this->oDocumento->createElement("indicadores");
                for ($iIndicador = 0; $iIndicador < pg_num_rows($rsIndicadores); $iIndicador++) {
                    $oDadosIndicador = db_utils::fieldsMemory($rsIndicadores, $iIndicador);
                
                    $oNodeIndicador = $this->oDocumento->createElement("indicador");
                
                    $oIndicador = new stdClass();
                    $oIndicador->nomeIndicador          = utf8_encode($oDadosIndicador->o10_descr);
                    $oIndicador->unidadeMedida          = utf8_encode($oDadosIndicador->o10_descrunidade);
                    $oIndicador->dataMedida             = $oDadosIndicador->dataapuracao;
                    $oIndicador->valorAuferidoIndicador = "0.00"; //valor auferido deixar 0 ou - porque não foi avaliado ainda, só no final do ano
                    $oIndicador->valorEstimadoIndicador = number_format($oDadosIndicador->o25_valor, 2, '.', '');
                    $this->addElement($oIndicador, $oNodeIndicador);
                
                    $oNodeIndicadores->appendChild($oNodeIndicador);
                }
                $oNodeIndicadoresPrograma->appendChild($oNodeIndicadores);
            } else {
                $sJustificativa = "...";
                $oNodeJustificativa = $this->oDocumento->createElement("justificativaAusenciaIndicador", $sJustificativa);
                $oNodeIndicadoresPrograma->appendChild($oNodeJustificativa);
            }
            $oNodePrograma->appendChild($oNodeIndicadoresPrograma);
                    
          
            $oNodeAcoes = $this->oDocumento->createElement("acoes");
          
            $sSqlAcoes = "select distinct on (o55_projativ) o55_projativ, 
                               o55_descr,
                               o55_finali,
                               o41_orgao, 
                               o41_unidade,
                               o41_descr
                          from plugins.orcprojativprograma
                               inner join plugins.orcprojativorcunidade on orcprojativprograma.orcprojativ = orcprojativorcunidade.orcprojativ 
                                                                       and orcprojativprograma.anousu      = orcprojativorcunidade.anousu
                               inner join orcamento.orcprojativ         on orcprojativ.o55_projativ        = orcprojativorcunidade.orcprojativ 
                                                                       and orcprojativ.o55_anousu          = orcprojativorcunidade.anousu
                               inner join orcamento.orcunidade          on orcunidade.o41_unidade          = orcprojativorcunidade.orcunidade 
                                                                       and orcunidade.o41_orgao            = orcprojativorcunidade.orcorgao
                                                                       and orcunidade.o41_instit           = orcprojativ.o55_instit  
                               inner join orcamento.orcorgao            on orcorgao.o40_orgao              = orcunidade.o41_orgao
                                                                       and orcorgao.o40_instit             = orcunidade.o41_instit
                         where orcprojativprograma.orcprograma = {$oDadosProgramas->o54_programa}
                           and orcprojativprograma.anousu = {$oDadosProgramas->o54_anousu}";
            $rsAcoes = db_query($sSqlAcoes);
            for ($iAcao = 0; $iAcao < pg_num_rows($rsAcoes); $iAcao++) {
                $oDadosAcao = db_utils::fieldsMemory($rsAcoes, $iAcao);

                $oDadosMetas = new stdClass();
                $oDadosMetas->meta          = null;
                $oDadosMetas->unidademedida = null;
                $oDadosMetas->valormeta     = null;
                $sSqlMetas = "select ppametas.meta, 
                                 ppametaunidade.descricao as unidademedida, 
                                 orcprojativppametas.valormeta 
                            from plugins.orcprojativppametas 
                                 inner join plugins.ppametas on ppametas.sequencial = orcprojativppametas.ppametas 
                                 inner join plugins.ppametaunidade on ppametaunidade.sequencial = ppametas.ppametaunidade 
                           where anousu = {$oDadosProgramas->o54_anousu} 
                             and orcprojativ = {$oDadosAcao->o55_projativ}
                           order by orcprojativppametas.orcprojativ, orcprojativppametas.ppametas";
                $rsMetas = db_query($sSqlMetas);
                if (pg_num_rows($rsMetas) > 0) {
                    $oDadosMetas = db_utils::getCollectionByRecord($rsMetas);
                }
            
            
                $sWhereDetalhamentoDespesa  = " orcdotacao.o58_anousu = {$oDadosProgramas->o54_anousu} ";
                $sWhereDetalhamentoDespesa .= " and orcdotacao.o58_projativ = {$oDadosAcao->o55_projativ} ";
            
              /*
               *  Quando for listado do PPA o Programa 146 e os projetos/atividade 1051, 2412, 2414, 2415, 2416, 2417, 2419, 2426, 2983 o valor assumido será do orçamento no Programa 001.
               */
                $sLogComplementar  = "\n";
                if ($oDadosProgramas->o54_programa == 146 && in_array($oDadosAcao->o55_projativ, array(1051, 2412, 2414, 2415, 2416, 2417, 2419, 2426, 2983))) {
                    $sWhereDetalhamentoDespesa .= " and orcdotacao.o58_programa = 1 ";
                    $sLogComplementar  = " - Utilizando a regra: \n";
                    $sLogComplementar .= " Quando for listado do PPA o Programa 146 e os projetos/atividade 1051, 2412, 2414, 2415, 2416, 2417, 2419, 2426, 2983 o valor assumido será do orçamento no Programa 001.";
                } else {
                    $sWhereDetalhamentoDespesa .= " and orcdotacao.o58_programa = {$oDadosProgramas->o54_programa} ";
                }
            
                $sSqlDetalhamentoDespesa = "select o58_funcao as funcao, 
                                               o58_subfuncao as subfuncao, 
                                               coalesce(sum(case when substr(o56_elemento,1,2) = '33' and o15_recurso = '15000000' then o58_valor else 0 end),0)  as vlrdespesacorrentetesouro,  
                                               coalesce(sum(case when substr(o56_elemento,1,2) = '33' and o15_recurso <> '15000000' then o58_valor else 0 end),0) as vlrdespesacorrenteoutras,
                                               coalesce(sum(case when substr(o56_elemento,1,2) = '34' and o15_recurso = '15000000' then o58_valor else 0 end),0)  as vlrdespesacapitaltesouro,
                                               coalesce(sum(case when substr(o56_elemento,1,2) = '34' and o15_recurso <> '15000000' then o58_valor else 0 end),0) as vlrdespesacapitaloutras
                                          from orcamento.orcdotacao 
                                               inner join orcamento.orcelemento on orcelemento.o56_codele = orcdotacao.o58_codele 
                                                                               and orcelemento.o56_anousu = orcdotacao.o58_anousu 
                                               inner join orcamento.orctiporec  on orctiporec.o15_codigo  = orcdotacao.o58_codigo 
                                         where {$sWhereDetalhamentoDespesa}
                                         group by o58_funcao, o58_subfuncao";
                $rsDetalhamentoDespesa = db_query($sSqlDetalhamentoDespesa);
                if (pg_num_rows($rsDetalhamentoDespesa) > 1) {
                    $sLog = "Programa {$oDadosProgramas->o54_programa} - Projeto/atividade {$oDadosAcao->o55_projativ} - Encontrados mais de uma Função/SubFunção para as despesas vinculadas\n";
                    $sLog .= " - se consultarmos todas as metas da despesa (dotação) ligadas ao programa e projeto/atividade existiram registros com Função e/ou SubFunções diferentes\n";
                    $sLog .= " $sLogComplementar";
                    $this->addLog($sLog);
                } elseif (pg_num_rows($rsDetalhamentoDespesa) == 0) {
                    $sLog = "Programa {$oDadosProgramas->o54_programa} - Projeto/atividade {$oDadosAcao->o55_projativ} - Não foram encontradas metas da despesa (dotações)\n";
                    $sLog .= " - sem encontrar metas da despesa (dotação) não é possível definir as informações:";
                    $sLog .= "codigoFuncao, codigoSubfuncao, valorDespesaCorrenteTesouro, valorDespesaCorrenteOutras, valorDespesaCapitalTesouro, valorDespesaCapitalOutras\n";
                    $sLog .= " $sLogComplementar";
                    $this->addLog($sLog);
                }
            
                $oDetalhamentoDespesa = db_utils::fieldsMemory($rsDetalhamentoDespesa, 0);
                         
                $oNodeAcao = $this->oDocumento->createElement("acao");
                $oNodeAcao->appendChild($this->oDocumento->createElement("codigoAcao", $oDadosAcao->o55_projativ));
                $oNodeAcao->appendChild($this->oDocumento->createElement("nomeAcao", utf8_encode($oDadosAcao->o55_descr)));
                $oNodeAcao->appendChild($this->oDocumento->createElement("codigoUnidadeGestora", $this->getCodigoOrgaoTCEXml()));
                $oNodeAcao->appendChild($this->oDocumento->createElement("nomeUnidadeGestora", utf8_encode($this->getNomeUnidade())));
                $oNodeAcao->appendChild($this->oDocumento->createElement("codigoUnidadeOrcamentaria", $oDadosAcao->o41_orgao.str_pad($oDadosAcao->o41_unidade, 3, "0", STR_PAD_LEFT)));
                $oNodeAcao->appendChild($this->oDocumento->createElement("nomeUnidadeOrcamentaria", utf8_encode($oDadosAcao->o41_descr)));
                $oNodeAcao->appendChild($this->oDocumento->createElement("objetivo", utf8_encode($oDadosAcao->o55_finali)));
            
                foreach ($oDadosMetas as $oMeta) {
                    $oNodeAcao->appendChild($this->oDocumento->createElement("meta", utf8_encode($oMeta->meta)));
                    $oNodeAcao->appendChild($this->oDocumento->createElement("unidadeMedidaMeta", utf8_encode($oMeta->unidademedida)));
                    $oNodeAcao->appendChild($this->oDocumento->createElement("valorMeta", number_format($oMeta->valormeta, 2, '.', '')));
                }
            
                $oNodeAcao->appendChild($this->oDocumento->createElement("tipoAcao", (substr($oDadosAcao->o55_projativ, 0, 1)=="1"?"PROJETO":"ATIVIDADE")));
                $oNodeAcao->appendChild($this->oDocumento->createElement("dataInicio", "$oPPALei->o01_anoinicio}-01-01"));
                $oNodeAcao->appendChild($this->oDocumento->createElement("dataTermino", "$oPPALei->o01_anofinal}-12-31"));
                $oNodeAcao->appendChild($this->oDocumento->createElement("codigoFuncao", str_pad($oDetalhamentoDespesa->funcao, 2, "0", STR_PAD_LEFT)));
                $oNodeAcao->appendChild($this->oDocumento->createElement("codigoSubfuncao", str_pad($oDetalhamentoDespesa->subfuncao, 3, "0", STR_PAD_LEFT)));
                $oNodeAcao->appendChild($this->oDocumento->createElement("valorDespesaCorrenteTesouro", "0.00"));
                $oNodeAcao->appendChild($this->oDocumento->createElement("valorDespesaCorrenteOutras", "0.00"));
                $oNodeAcao->appendChild($this->oDocumento->createElement("valorDespesaCapitalTesouro", number_format($oDetalhamentoDespesa->vlrdespesacapitaltesouro, 2, '.', '')));
                $oNodeAcao->appendChild($this->oDocumento->createElement("valorDespesaCapitalOutras", number_format($oDetalhamentoDespesa->vlrdespesacapitaloutras, 2, '.', '')));
            
                $oNodeAcoes->appendChild($oNodeAcao);
            }
            $oNodePrograma->appendChild($oNodeAcoes);
            $oNodeProgramas->appendChild($oNodePrograma);
        }
        $oNodeRemessa->appendChild($oNodeProgramas);
      
        $this->oDocumento->appendChild($oNodeRemessa);
      
        $oRetornoValidacao = $this->validarXML($this->oDocumento, 'model/contabilidade/arquivos/siai/v2022/xsd/XSD_PPA.txt');
        $this->addLog("\n".$oRetornoValidacao->sMsg);
      
        $this->sArquivo = $this->oDocumento->saveXML();
    }
  
  
    public function getArquivo()
    {
        return $this->sArquivo;
    }
}
