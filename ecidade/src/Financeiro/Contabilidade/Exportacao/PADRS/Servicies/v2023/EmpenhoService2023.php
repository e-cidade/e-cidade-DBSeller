<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023;

// phpcs:disable
use db_utils;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\Empenho2023Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Campos\BaseLegalContratacao\BaseLegalContratacaoCampo;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Campos\IdentificadorDespesaFuncionario\IdentificadorDespesaFuncionarioCampo;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\PadService;
use ECidade\Financeiro\Orcamento\Recurso\Origem;
use Exception;

// phpcs:enable

class EmpenhoService2023 extends PadService
{
    protected $fileName = 'EMPENHO.TXT';

    protected $dotacoesRps = [];

    public function __construct($exercicio, $instituicoes, $dataInicio, $dataFim)
    {
        $this->instituicoes = $instituicoes;
        $this->ano = $exercicio;
        $this->dataInicial = $dataInicio;
        $this->dataFinal = $dataFim;
    }

    protected function getDados()
    {
        $rs = db_query($this->getSqlEmpenho());
        while ($state = pg_fetch_assoc($rs)) {
            $builder = $this->getBuilder();

            $state['cnpj'] = str_repeat('0', 14);
            $state['licitacaoCompartilhada'] = 'X';
            $state['o58_subprograma'] = 000;

            // parceia o campo json com as outras informações do empenho
            if (!empty($state["outros_dados"])) {
                $outrosDados = json_decode($state["outros_dados"]);
                if (isset($outrosDados->licitacao_compartilhada)) {
                    $state['licitacaoCompartilhada'] = $outrosDados->licitacao_compartilhada;
                }

                if (!empty($outrosDados->cnpj_gerenciador) && $state['licitacaoCompartilhada'] === 'S') {
                    $state['cnpj'] = $outrosDados->cnpj_gerenciador;
                }
            }

            // se ano menor que 2005, pega funcao, subfuncao da tabel orcdotacaorp
            if ($state['e60_anousu'] < 2005) {
                $this->processaOrcDotacaoRPs($state);
            }

            // processa os dados da licitação e contrato do empenho
            $this->processaDadosLicitacao($state);
            $this->processaRegistroPreco($state);


            // Validação existente na emissão do PAD. Achei importante manter.
            if ($state['e60_anousu'] >= 2005 && empty($state['elemento'])) {
                throw new Exception("Verifique empenho (numemp: {$state['e60_numemp']}) sem elemento cadastrado");
            }

            yield $builder->addDados($state)->addModelo($this->emissaoMGS)->build();
        }
    }

    protected function getBuilder()
    {
        switch ($this->ano) {
            case 2023:
            default:
                return new Empenho2023Builder();
        }
    }

    /**
     * @param $data_ini
     * @param $data_fim
     * @param $sele
     * @param $exercicio
     * @return string
     */
    private function getSqlEmpenho()
    {
        $origemEmpenho = Origem::EMISSAO_EMPENHO;
        $origemRP = Origem::EMPENHO_RP;

        $codigosInstituicoes = $this->getListaInstituicoes();
        $whereExercicio = [
            "c75_data >='{$this->dataInicial}'",
            "c75_data <='{$this->dataFinal}'",
            "e60_emiss <='{$this->dataFinal}'",
            "c53_tipo in (10, 11)",
            "e60_instit in ({$codigosInstituicoes})",
        ];
        $whereExercicio = implode(' and ', $whereExercicio);

        $sql = "
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
		       end as elemento,
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
               inner join orcdotacao on o58_coddot = e60_coddot and o58_anousu=e60_anousu and o58_instit = e60_instit

               inner join origemcomplementorecurso on o206_numero = e60_numemp
                                                  and o206_origem = {$origemEmpenho}
               inner join orctiporec on o15_codigo = o206_recurso
               inner join fonterecurso on orctiporec_id = o15_codigo and exercicio = c70_anousu
               inner join complementofonterecurso on o200_sequencial = o15_complemento

               join empelemento on e64_numemp = e60_numemp
               JOIN orcelemento ON o56_codele = e64_codele
                               and o56_anousu = o58_anousu

		       inner join db_config  ON db_config.codigo = empempenho.e60_instit
         where $whereExercicio

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
		       end as elemento,
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
               inner join fonterecurso on orctiporec_id = o15_codigo and exercicio = e91_anousu
               inner join complementofonterecurso on o200_sequencial = o15_complemento
               join empelemento on e64_numemp = e60_numemp
               JOIN orcelemento ON o56_codele = e64_codele
                               and o56_anousu = o58_anousu
               inner join db_config  ON db_config.codigo = empempenho.e60_instit
	          where e91_anousu = {$this->ano}
                and e60_instit in ({$codigosInstituicoes})
                and e91_rpcorreto is false

	    order by o58_orgao,
                 o58_unidade,
    	         o58_funcao,
 	             o58_subfuncao,
                 o58_programa,
 	             o58_projativ,
		         elemento,
		         e60_emiss
       ";
        return $sql;
    }

    /**
     * Se ano menor que 2005, pega funcao, subfuncao  da tabel orcdotacaorp
     * @param array $state
     * @return void
     */
    private function processaOrcDotacaoRPs(array &$state)
    {
        $ano = $state['e60_anousu'];
        $hash = sprintf('%s#%s', $ano, $state['o58_coddot']);
        if (!array_key_exists($hash, $this->dotacoesRps)) {
            $sql = "
                    select o73_funcao as o58_funcao,
	                       o73_subfuncao as o58_subfuncao
                      from orcdotacaorp
		            where o73_anousu = {$ano}
		              and o73_coddot = {$state['o58_coddot']}
            ";
            $rr = db_query($sql);
            if (pg_num_rows($rr) > 0) {
                $this->dotacoesRps[$hash] = pg_fetch_array($rr, 0);
            }
        }

        if (array_key_exists($hash, $state)) {
            $state['o58_funcao'] = $this->dotacoesRps[$hash]['o58_funcao'];
            $state['o58_subfuncao'] = $this->dotacoesRps[$hash]['o58_subfuncao'];
        }
    }

    private function processaDadosLicitacao(array &$state)
    {
        $numeroEmpenho = $state['e60_numemp'];
        $descricaoModalidadeLicitacao = '';
        $outrasModalidades = '';
        $numeroLicitacao = '';
        $anoLicitacao = 0;
        $sigla = '';

        $sSqlEmpAutItem = "
        SELECT DISTINCT
               l20_numero AS l20_codigo,
               l20_anousu,
               e54_numerl,
               (SELECT l44_codigotribunal
                  FROM pctipocompratribunal
                 WHERE l44_sequencial = l03_pctipocompratribunal) AS l44_codigotribunal,
               (SELECT l44_sigla
                  FROM pctipocompratribunal
                 WHERE l44_sequencial = l03_pctipocompratribunal) AS sigla,
               pc50_descr
          FROM empautitem
          JOIN empautitempcprocitem ON empautitempcprocitem.e73_sequen = empautitem.e55_sequen
               AND empautitempcprocitem.e73_autori = empautitem.e55_autori
          JOIN liclicitem ON liclicitem.l21_codpcprocitem = empautitempcprocitem.e73_pcprocitem
          JOIN liclicita ON liclicitem.l21_codliclicita = liclicita.l20_codigo
          JOIN cflicita ON liclicita.l20_codtipocom = cflicita.l03_codigo
          JOIN pctipocompra ON cflicita.l03_codcom = pc50_codcom
          JOIN empautoriza ON empautoriza.e54_autori = empautitem.e55_autori
          JOIN empempaut ON e61_autori = e54_autori
        WHERE e61_numemp = {$numeroEmpenho} ";

        $rsSqlEmpAutItem = db_query($sSqlEmpAutItem);
        $iNumRowsEmpAutItem = pg_num_rows($rsSqlEmpAutItem);

        if ($iNumRowsEmpAutItem) {
            $oEmpAutItem = db_utils::fieldsMemory($rsSqlEmpAutItem, 0);
            $descricaoModalidadeLicitacao = $oEmpAutItem->pc50_descr;
            $numeroLicitacao = $oEmpAutItem->l20_codigo;
            $anoLicitacao = $oEmpAutItem->l20_anousu;
            $sigla = $oEmpAutItem->sigla;
        } else {
            $sSqlEmpEmpenho = "
                    SELECT DISTINCT l44_codigotribunal, l44_sigla AS sigla, pc50_descr
                    FROM empempenho
                    JOIN pctipocompra ON e60_codcom = pc50_codcom
                    JOIN pctipocompratribunal ON pc50_pctipocompratribunal = l44_sequencial
                   WHERE e60_numemp = {$numeroEmpenho}
                ";

            $rsSqlEmpEmpenho = db_query($sSqlEmpEmpenho);
            $iNumRowsEmpEmpenho = pg_num_rows($rsSqlEmpEmpenho);

            if ($iNumRowsEmpEmpenho) {
                $oEmpEmpenho = db_utils::fieldsMemory($rsSqlEmpEmpenho, 0);
                $descricaoModalidadeLicitacao = $oEmpEmpenho->pc50_descr;
                $sigla = $oEmpEmpenho->sigla;
            }
        }

        $sigla = $sigla ?: 'NSA';

        $anoEmpenho = $state['e60_anousu'];
        // se o empenho não tem vínculo no sistema com licitação
        // mas tem o número da licitação no campo "e60_numerol" seta esses valores como o número da licitação
        $e60_numerol = trim($state['e60_numerol']);
        if (empty($numeroLicitacao) && !empty($e60_numerol)) {
            $dadosLicitacao = explode('/', trim($e60_numerol));

            $numeroLicitacao = $dadosLicitacao[0];
            $anoLicitacao = !empty($dadosLicitacao[1]) ? $dadosLicitacao[1] : $anoEmpenho;
        }

        $numeroLicitacao = str_replace('/', '', $numeroLicitacao);

        $campoBaseLegalContratacao = new BaseLegalContratacaoCampo($this->ano, $numeroEmpenho);
        $campoIdentificadorDespesaFuncionario = new IdentificadorDespesaFuncionarioCampo($this->ano, $numeroEmpenho);
        if ($campoIdentificadorDespesaFuncionario->getValor() == 'F') {
            $sigla = 'NSA';
        }

        $baseLegal = $campoBaseLegalContratacao->getValor();
        if (in_array($sigla, ['CCP','CCE','PCE','PCP'])) {
            $baseLegal = '08';
        }

        $state['descricaoModalidadeLicitacao'] = $descricaoModalidadeLicitacao;
        $state['outrasModalidades'] = $outrasModalidades;
        $state['numeroLicitacao'] = $numeroLicitacao;
        $state['anoLicitacao'] = $anoLicitacao;
        $state['sigla'] = $sigla;
        $state['baseLegalContratacao'] = $baseLegal;
        $state['identificadorDespesaFuncionario'] = $campoIdentificadorDespesaFuncionario->getValor();
    }

    private function processaRegistroPreco(array &$state)
    {
        $state['registroPreco'] = 'N';
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
        $sSqlRegistroPreco .= "  where e62_numemp           = {$state['e60_numemp']}                           ";
        $sSqlRegistroPreco .= "    and pc10_solicitacaotipo = 5                                                ";
        $rsSqlRegistroPreco = db_query($sSqlRegistroPreco);
        $iNumRowsRegistroPreco = pg_num_rows($rsSqlRegistroPreco);
        if ($iNumRowsRegistroPreco > 0) {
            $state['registroPreco'] = 'S';
        }
    }
}
