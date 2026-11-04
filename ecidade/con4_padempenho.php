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

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Campos\BaseLegalContratacao\BaseLegalContratacaoCampo;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Campos\IdentificadorDespesaFuncionario\IdentificadorDespesaFuncionarioCampo;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories\EmpenhoFactory;
use ECidade\Financeiro\Orcamento\Recurso\Origem;

class padEmpenho
{
    public $arq = null;
    public $header;
    protected $emissaoMGS = false;

    /**
     * Empenho constructor.
     * @param $header
     */
    public function __construct($header)
    {
        $this->header = $header;
    }

    public function isMGS($bool)
    {
        $this->emissaoMGS = $bool;
    }
    /**
     * @param $data_ini
     * @param $data_fim
     * @param $sele
     * @param $exercicio
     * @return string
     */
    private function getSqlEmpenho($data_ini, $data_fim, $sele, $exercicio)
    {
        $origemEmpenho = Origem::EMISSAO_EMPENHO;
        $origemRP = Origem::EMPENHO_RP;

        return "
        select e60_numemp,
	           e60_anousu,
		       trim(e60_codemp)::integer as e60_codemp,
		       o58_coddot,
               o58_orgao,
               o58_unidade,
    	       o58_funcao,
 	           o58_subfuncao,
               o58_programa,
 	           o58_projativ,
		       case
		           when o58_anousu >= 2005
		              then substr(trim(substr(o56_elemento,2,14))||'00000000000',1,15)::varchar(15)
                   else  substr(trim(o56_elemento)||'000000000',1,15)::varchar(15)
		       end as rubrica,
	           o15_recurso as recurso,
               case
                  when o200_tribunal is true then o206_complementorecurso
                  else 0
               end as complemento,
		       (case when c71_coddoc in(32,31) then c70_data else e60_emiss end) as e60_emiss,
	           c70_valor as valor_empenho,
	           (case when c53_tipo = 10 then '+' else '-' end)::char(1) as sinal,
	           e60_numcgm,
               ('DOT:['||e60_coddot||'] '|| 'NUMEMP:['||e60_numemp||']'||e60_resumo) as e60_resumo,
		       e60_instit,
               e60_concarpeculiar,
               e60_numerol,
               cgc,
               (select e171_dados from  empempenhooutrosdados where e171_numemp = e60_numemp) as outros_dados,
               codigo_siconfi
          from empempenho
	           inner join conlancamemp on c75_numemp = e60_numemp
	           inner join conlancamdoc on c71_codlan = c75_codlan
               inner join conhistdoc on c53_coddoc = c71_coddoc
	           inner join conlancam on c70_codlan = c75_codlan
               inner join orcdotacao on o58_coddot =e60_coddot and o58_anousu=e60_anousu and o58_instit = e60_instit

               inner join origemcomplementorecurso on o206_numero = e60_numemp
                                                  and o206_origem = {$origemEmpenho}
               inner join orctiporec on o15_codigo = o206_recurso
               inner join fonterecurso on orctiporec_id = o15_codigo and exercicio = e60_anousu
               inner join complementofonterecurso on o200_sequencial = o15_complemento

		       inner join orcelemento on o56_codele = o58_codele and o56_anousu = o58_anousu
		       inner join db_config  ON db_config.codigo = empempenho.e60_instit
         where c75_data >='$data_ini' and c75_data <='$data_fim'
	       and e60_emiss <='$data_fim'
		   and c53_tipo in (10, 11)
           and e60_instit in $sele

        union all

        select distinct (e91_numemp) ,
		       e60_anousu,
		       trim(e60_codemp)::integer as e60_codemp,
		       o58_coddot,
               o58_orgao,
               o58_unidade,
    	       o58_funcao,
 	           o58_subfuncao,
               o58_programa,
 	           o58_projativ,
		       case
		           when o58_anousu >= 2005 then substr(trim(substr(o56_elemento,2,14))||'00000000000',1,15)::varchar(15)
  		           else substr(trim(o56_elemento)||'000000000',1,15)::varchar(15)
		       end as rubrica,
	           o15_recurso as recurso,
	           case
                  when o200_tribunal is true then o206_complementorecurso
                  else 0
               end as complemento,
	           e60_emiss,
	           round((e91_vlremp-e91_vlranu-e91_vlrpag),2)::float8 as valor_empenho,
	           '+'::char(1) as sinal,
	           e60_numcgm,
               ('DOT:['||e60_coddot||'] '|| 'NUMEMP:['||e60_numemp||']'||e60_resumo) as e60_resumo,
		       e60_instit,
               e60_concarpeculiar,
               e60_numerol,
               cgc,
               (select e171_dados from  empempenhooutrosdados where e171_numemp = e60_numemp) as outros_dados,
               codigo_siconfi
          from empresto
               inner join empempenho on e60_numemp = e91_numemp
	           inner join orcdotacao on o58_coddot=e60_coddot and  o58_anousu=e60_anousu and o58_instit = e60_instit

               inner join origemcomplementorecurso on o206_numero = e60_numemp
                                                  and o206_origem = {$origemRP}
               inner join orctiporec on o15_codigo = o206_recurso
              inner join fonterecurso on orctiporec_id = o15_codigo and exercicio = e60_anousu
               inner join complementofonterecurso on o200_sequencial = o15_complemento
               inner join orcelemento on o56_codele = o58_codele and o56_anousu = o58_anousu
               inner join db_config  ON db_config.codigo = empempenho.e60_instit
	          where e91_anousu = {$exercicio}
                and e60_instit in $sele
                and e91_rpcorreto is false

	    order by o58_orgao,
                 o58_unidade,
    	         o58_funcao,
 	             o58_subfuncao,
                 o58_programa,
 	             o58_projativ,
		         rubrica,
		         e60_emiss
       ";
    }

    /**
     * @param $e60_numemp
     * @return bool|null|string
     */
    private function getdataAutLicita($e60_numemp)
    {
        $sql = " SELECT *  FROM  empempenho WHERE e60_numemp = {$e60_numemp}; ";
        $rsSqlEmpAut = db_query($sql);
        $iNumRowsEmpAut = pg_num_rows($rsSqlEmpAut);

        if ($iNumRowsEmpAut) {
            $oEmpAutItem = db_utils::fieldsMemory($rsSqlEmpAut, 0);
            $ano = substr($oEmpAutItem->e60_numerol, -4);
            if (strlen($ano) != 4) {
                return null;
            }

            return $ano;
        }

        return null;
    }


    public function processa($instit = 1, $data_ini = "", $data_fim = "", $tribinst = null, $subelemento = "")
    {
        $exercicio = db_getsession('DB_anousu');

        $sSqlTesta = "
            SELECT
                e60_anousu,
                e60_numemp,
                e60_instit,
                e64_codele,
                o56_elemento,
                o56_descr
            FROM empelemento
                INNER JOIN empempenho ON empempenho.e60_numemp = empelemento.e64_numemp
                INNER JOIN conlancamemp ON c75_numemp = e60_numemp
                LEFT JOIN orcelemento ON orcelemento.o56_codele = empelemento.e64_codele AND orcelemento.o56_anousu = empempenho.e60_anousu
            WHERE o56_elemento IS NULL
              AND c75_data >= '{$data_ini}'
              AND c75_data <= '{$data_fim}'
              AND e60_emiss <= '{$data_fim}'
              AND e60_instit IN ($instit)
        ";
        $rsTesta = db_query($sSqlTesta) or die($sSqlTesta);

        if (pg_num_rows($rsTesta) > 0) {
            echo "<br><b>PROVAVEIS ERROS NOS REGISTROS - SEM DESDOBRAMENTO VINCULADO:</b><br>";
            for ($x = 0; $x < pg_num_rows($rsTesta); $x++) {
                $anousu_erro = pg_fetch_result($rsTesta, $x, "e60_anousu");
                $numemp_erro = pg_fetch_result($rsTesta, $x, "e60_numemp");
                $instit_erro = pg_fetch_result($rsTesta, $x, "e60_instit");
                $codele_erro = pg_fetch_result($rsTesta, $x, "e64_codele");

                echo "ano: $anousu_erro - numemp: $numemp_erro - instit: $instit_erro - codele: $codele_erro <br>";
            }
            echo "<br>";
            flush();
        }


        $sql_testa_rp = "
            SELECT * FROM empresto
             WHERE e91_anousu = {$exercicio} AND round(e91_vlrpag, 2) > round(e91_vlrliq, 2)";
        $result_testa_rp = db_query($sql_testa_rp) or die($sql_testa_rp);

        if (pg_num_rows($result_testa_rp) > 0) {
            echo "<br><b>RESTOS A PAGAR COM REGISTROS DE PAGAMENTOS MAIORES QUE LIQUIDAÇÕES:</b><br>";
            for ($x = 0; $x < pg_num_rows($result_testa_rp); $x++) {
                db_fieldsmemory($result_testa_rp, $x);
                echo "NUMEMP: $e91_numemp - VALOR LIQUIDADO: $e91_vlrliq - VALOR PAGO: $e91_vlrpag<br>";
            }
        }

        if ($exercicio >= 2023) {
            $instituicoes = InstituicaoRepository::getInstituicaoConsolida(db_getsession('DB_instit'));

            $service = EmpenhoFactory::getService($exercicio, $instituicoes, $data_ini, $data_fim);
            $service->setHeader($this->header);
            $service->processa();

            return true;
        }

        umask(74);
        $this->arq = fopen("tmp/EMPENHO.TXT", 'w+');
        fputs($this->arq, $this->header);
        fputs($this->arq, "\r\n");

        global $contador, $nomeinst, $o58_unidade, $o58_funcao, $o58_subfuncao, $o58_programa, $o58_subprograma, $o58_coddot, $e60_numemp, $e60_anousu, $e60_concarpeculia, $complemento;

        $contador = 0;
        $sele = " ($instit) ";



        $sql = $this->getSqlEmpenho($data_ini, $data_fim, $sele, $exercicio);

        $res = db_query($sql) or die($sql);
        $rows = pg_num_rows($res);

        for ($x = 0; $x < $rows; $x++) {
            $dados = db_utils::fieldsMemory($res, $x);

            db_fieldsmemory($res, $x);
            $ano = pg_fetch_result($res, $x, "e60_anousu");
            $orgao = formatar(pg_fetch_result($res, $x, "o58_orgao"), 2, 'n');
            $instituicao = pg_fetch_result($res, $x, "e60_instit");
            $empenho = pg_fetch_result($res, $x, 'e60_numemp');
            $cnpj = str_repeat('0', 14);
            $licitacaoCompartilhada = 'X';

            if (!empty($dados->outros_dados)) {
                $outrosDados = json_decode($dados->outros_dados);
                if (isset($outrosDados->licitacao_compartilhada)) {
                    $licitacaoCompartilhada = $outrosDados->licitacao_compartilhada;
                }

                if (!empty($outrosDados->cnpj_gerenciador) && $licitacaoCompartilhada === 'S') {
                    $cnpj = $outrosDados->cnpj_gerenciador;
                }
            }

            // se ano menor que 2005, pega funcao, subfuncao e subprograma da tabel orcdotacaorp
            if ($ano < 2005) {
                $sql = "
                    select o73_funcao as o58_funcao,
	                       o73_subfuncao as o58_subfuncao,
			               o73_subprograma as o58_subprograma
                      from orcdotacaorp
		            where o73_anousu = $ano
		              and o73_coddot = $o58_coddot
                ";
                $rr = db_query($sql);
                if (pg_num_rows($rr) > 0) {
                    db_fieldsmemory($rr, 0);
                }

                $sql = "
                    select o32_subprog as o58_subprograma
                      from orcsubprogramarp
                     where o32_anousu = $ano
                ";
                $rr = db_query($sql);
                if (pg_num_rows($rr) > 0) {
                    db_fieldsmemory($rr, 0);
                }
            }


            $o58_subprograma = 000;
            $unidade = formatar($o58_unidade, 2, 'n');
            $funcao = formatar($o58_funcao, 2, 'n');
            $subfuncao = formatar($o58_subfuncao, 3, 'n');
            $programa = formatar($o58_programa, 4, 'n');
            $subprograma = formatar($o58_subprograma, 3, 'n');
            $proj_ativ = formatar(pg_fetch_result($res, $x, "o58_projativ"), 5, 'n');


            $sRegistroPreco = 'N';
            $iModalidadeLicitacao = '';
            $sDescricaoModalidadeLicitacao = '';
            $sOutrasModalidades = '';
            $sNumeroLicitacao = '';
            $iAnoLicitacao = 0;
            $sigla = '';

            $sSqlEmpAutItem = "SELECT DISTINCT ";
            $sSqlEmpAutItem .= "    l20_numero AS l20_codigo, ";
            $sSqlEmpAutItem .= "    l20_anousu, ";
            $sSqlEmpAutItem .= "    e54_numerl, ";
            $sSqlEmpAutItem .= "    (SELECT l44_codigotribunal ";
            $sSqlEmpAutItem .= "       FROM pctipocompratribunal ";
            $sSqlEmpAutItem .= "      WHERE l44_sequencial = l03_pctipocompratribunal) AS l44_codigotribunal, ";
            $sSqlEmpAutItem .= "    (SELECT l44_sigla ";
            $sSqlEmpAutItem .= "       FROM pctipocompratribunal ";
            $sSqlEmpAutItem .= "      WHERE l44_sequencial = l03_pctipocompratribunal) AS sigla, ";
            $sSqlEmpAutItem .= "    pc50_descr ";
            $sSqlEmpAutItem .= "FROM empautitem ";
            $sSqlEmpAutItem .= "    INNER JOIN empautitempcprocitem ON empautitempcprocitem.e73_sequen = empautitem.e55_sequen ";
            $sSqlEmpAutItem .= "                                   AND empautitempcprocitem.e73_autori = empautitem.e55_autori ";
            $sSqlEmpAutItem .= "    INNER JOIN liclicitem ON liclicitem.l21_codpcprocitem = empautitempcprocitem.e73_pcprocitem ";
            $sSqlEmpAutItem .= "    INNER JOIN liclicita ON liclicitem.l21_codliclicita = liclicita.l20_codigo ";
            $sSqlEmpAutItem .= "    INNER JOIN cflicita ON liclicita.l20_codtipocom = cflicita.l03_codigo ";
            $sSqlEmpAutItem .= "    INNER JOIN pctipocompra ON cflicita.l03_codcom = pc50_codcom ";
            $sSqlEmpAutItem .= "    INNER JOIN empautoriza ON empautoriza.e54_autori = empautitem.e55_autori ";
            $sSqlEmpAutItem .= "    INNER JOIN empempaut ON e61_autori = e54_autori ";
            $sSqlEmpAutItem .= "WHERE e61_numemp = {$e60_numemp} ";

            $rsSqlEmpAutItem = db_query($sSqlEmpAutItem);
            $iNumRowsEmpAutItem = pg_num_rows($rsSqlEmpAutItem);

            if ($iNumRowsEmpAutItem) {
                $oEmpAutItem = db_utils::fieldsMemory($rsSqlEmpAutItem, 0);
                $iModalidadeLicitacao = $oEmpAutItem->l44_codigotribunal;
                $sDescricaoModalidadeLicitacao = $oEmpAutItem->pc50_descr;
                $sNumeroLicitacao = $oEmpAutItem->l20_codigo;
                $iAnoLicitacao = $oEmpAutItem->l20_anousu;
                $sigla = $oEmpAutItem->sigla;
            } else {
                $sSqlEmpEmpenho = "
                    SELECT DISTINCT l44_codigotribunal, l44_sigla AS sigla, pc50_descr
                    FROM empempenho
                    JOIN pctipocompra ON e60_codcom = pc50_codcom
                    JOIN pctipocompratribunal ON pc50_pctipocompratribunal = l44_sequencial
                   WHERE e60_numemp = {$e60_numemp}
                ";

                $rsSqlEmpEmpenho = db_query($sSqlEmpEmpenho);
                $iNumRowsEmpEmpenho = pg_num_rows($rsSqlEmpEmpenho);

                if ($iNumRowsEmpEmpenho) {
                    $oEmpEmpenho = db_utils::fieldsMemory($rsSqlEmpEmpenho, 0);
                    $iModalidadeLicitacao = $oEmpEmpenho->l44_codigotribunal;
                    $sDescricaoModalidadeLicitacao = $oEmpEmpenho->pc50_descr;
                    $sigla = $oEmpEmpenho->sigla;
                }
            }

            if ($iModalidadeLicitacao == '99') {
                $sOutrasModalidades = "{$iModalidadeLicitacao} - {$sDescricaoModalidadeLicitacao}";
            }

            $sSqlRegistroPreco = " select pc11_numero,                                                            ";
            $sSqlRegistroPreco .= "        pc10_solicitacaotipo                                                    ";
            $sSqlRegistroPreco .= "   from empempenho                                                              ";
            $sSqlRegistroPreco .= "        inner join empempitem           on e60_numemp        = e62_numemp       ";
            $sSqlRegistroPreco .= "        inner join empempaut            on e61_numemp        = e60_numemp       ";
            $sSqlRegistroPreco .= "        inner join empautoriza          on e61_autori        = e54_autori       ";
            $sSqlRegistroPreco .= "        inner join empautitem           on e54_autori        = e55_autori       ";
            $sSqlRegistroPreco .= "                                       and e62_sequen        = e55_sequen       ";
            $sSqlRegistroPreco .= "        inner join empautitempcprocitem on e73_sequen        = e55_sequen       ";
            $sSqlRegistroPreco .= "                                       and e73_autori        = e55_autori       ";
            $sSqlRegistroPreco .= "        inner join pcprocitem           on  pc81_codprocitem = e73_pcprocitem   ";
            $sSqlRegistroPreco .= "        inner join solicitem            on pc11_codigo       = pc81_solicitem   ";
            $sSqlRegistroPreco .= "        inner join solicita             on pc11_numero       = pc10_numero      ";
            $sSqlRegistroPreco .= "  where e62_numemp           = {$e60_numemp}                                    ";
            $sSqlRegistroPreco .= "    and pc10_solicitacaotipo = 5                                                ";
            $rsSqlRegistroPreco = db_query($sSqlRegistroPreco);
            $iNumRowsRegistroPreco = pg_num_rows($rsSqlRegistroPreco);
            if ($iNumRowsRegistroPreco > 0) {
                $sRegistroPreco = 'S';
            }

            $rubrica_despesa = formatar(pg_fetch_result($res, $x, "rubrica"), 15, 'c'); // pendente

            if ($e60_anousu >= 2005) {
                $sqlele = "select o56_elemento
                  from empempenho
                       inner join empelemento on e60_numemp = e64_numemp
                       inner join orcelemento on o56_codele = e64_codele and o56_anousu = e60_anousu
                  where e60_numemp = $e60_numemp limit 1";

                $resrub = db_query($sqlele);
                if ($resrub == false || pg_num_rows($resrub) == 0) {
                    echo "Verifique empenho (numemp: $e60_numemp) sem elemento cadastrado";
                    exit;
                }

                $rubrica_despesa = formatar(substr(pg_fetch_result($resrub, 0, "o56_elemento"), 1, 12) . "000", 15, 'c'); // pendente
            }

            $recurso = formatar(pg_fetch_result($res, $x, "recurso"), 4, 'n');
            $contrapartida_recurso = espaco(4, '0'); // pendente
            $numero_empenho = $ano . str_pad($instituicao, 2, "0", STR_PAD_LEFT) . "0" . formatar(pg_fetch_result($res, $x, "e60_codemp"), 6, 'n');
            $data_empenho = formatar(pg_fetch_result($res, $x, "e60_emiss"), 8, 'd');
            $valor_empenho = formatar(pg_fetch_result($res, $x, "valor_empenho"), 13, 'v');
            $sinal_valor = pg_fetch_result($res, $x, "sinal");

            $codigo_credor = formatar(pg_fetch_result($res, $x, "e60_numcgm"), 10, 'n');

            $hist = pg_fetch_result($res, $x, "e60_resumo");

            $e60_numerol = pg_fetch_result($res, $x, "e60_numerol");
            $concarpeculiar = formatar((int)pg_fetch_result($res, $x, "e60_concarpeculiar"), 3, 'n');

            if (trim($e60_numerol) != '' && trim($e60_numerol) != '0') {
                if ($sNumeroLicitacao == '') {
                    $dadosLicitacao = explode('/', trim($e60_numerol));

                    $sNumeroLicitacao = $dadosLicitacao[0];
                    $iAnoLicitacao = !empty($dadosLicitacao[1]) ? $dadosLicitacao[1] : $ano;
                }
            }

            if ($hist == "") {
                $hist = "sem resumo";
            }

            $hist = str_replace("\n", " ", $hist);
            $hist = str_replace("\r", "", $hist);
            $historico_empenho = formatar(" ", 165, 'c');

            $sNovoHistorico = formatar($hist, 400, 'c');


            // A partir de 2008, vigora o uso de CARACTERISTICA PECULIAR
            if ($exercicio > 2007) {
                $line = $orgao
                    . $unidade
                    . $funcao
                    . $subfuncao
                    . $programa
                    . $subprograma
                    . $proj_ativ
                    . $rubrica_despesa
                    . $recurso
                    . $contrapartida_recurso
                    . $numero_empenho
                    . $data_empenho
                    . $valor_empenho
                    . $sinal_valor
                    . $codigo_credor
                    . $historico_empenho
                    . $concarpeculiar;

                if ($exercicio >= 2011) {

                    $sNumeroLicitacaoPAD = ' ';
                    if ($exercicio >= 2014) {
                        $sNumeroLicitacao = str_replace('/', '', $sNumeroLicitacao);
                        $sNumeroLicitacaoPAD = '0';
                    }

                    $iModalidadeLicitacao = str_pad($iModalidadeLicitacao, 2, ' ', STR_PAD_LEFT);
                    $sRegistroPreco = str_pad($sRegistroPreco, 1, ' ', STR_PAD_LEFT);
                    $sOutrasModalidades = substr(str_pad($sOutrasModalidades, 20, ' ', STR_PAD_LEFT), 0, 20);
                    $sNumeroLicitacao = str_pad($sNumeroLicitacao, 20, $sNumeroLicitacaoPAD, STR_PAD_LEFT);
                    $iAnoLicitacao = str_pad($iAnoLicitacao, 4, '0', STR_PAD_LEFT);

                    if ($exercicio >= 2014) {
                        $sigla = $sigla ?: 'NSA';

                        /**
                         * Campos obsoletos
                         */
                        $iModalidadeLicitacao = str_repeat(' ', 2);
                        $sOutrasModalidades = str_repeat(' ', 20);
                    }

                    $line .= $iModalidadeLicitacao . $sRegistroPreco . $sOutrasModalidades . $sNumeroLicitacao . $iAnoLicitacao . $sNovoHistorico;

                    $campoBaseLegalContratacao = new BaseLegalContratacaoCampo($exercicio, $empenho);
                    $campoIdentificadorDespesaFuncionario = new IdentificadorDespesaFuncionarioCampo($exercicio, $empenho);

                    if ($exercicio >= 2014) {

                        if ($campoIdentificadorDespesaFuncionario->getValor() == 'F') {
                            $sigla = 'NSA';
                        }
                        $line .= $sigla;
                    }

                    $line .= $campoBaseLegalContratacao->getValor();
                    $line .= $campoIdentificadorDespesaFuncionario->getValor();
                    $line .= $licitacaoCompartilhada;
                    $line .= $cnpj;
                }
            } else {
                $line = $orgao
                    . $unidade
                    . $funcao
                    . $subfuncao
                    . $programa
                    . $subprograma
                    . $proj_ativ
                    . $rubrica_despesa
                    . $recurso
                    . $contrapartida_recurso
                    . $numero_empenho
                    . $data_empenho
                    . $valor_empenho
                    . $sinal_valor
                    . $codigo_credor
                    . $historico_empenho;
            }

            /**
             * campo CF
             */
            if ($exercicio >= 2020) {
                $complementoFonteRecurso = str_pad($complemento, 4, '0', STR_PAD_LEFT);
                $line .= $complementoFonteRecurso;
            }

            if ($exercicio >= 2022) {
                $line .= "00000000";
            }
            fputs($this->arq, $line);
            fputs($this->arq, "\r\n");

            $contador = $contador + 1; // incrementa contador global
        }

        //  trailer
        $contador = espaco(10 - (strlen($contador)), '0') . $contador;
        $line = "FINALIZADOR" . $contador;
        fputs($this->arq, $line);
        fputs($this->arq, "\r\n");
        fclose($this->arq);

        $teste = "true";
        return $teste;
    }
}
