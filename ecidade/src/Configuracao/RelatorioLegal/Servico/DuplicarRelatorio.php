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

namespace ECidade\Configuracao\RelatorioLegal\Servico;

use cl_orcparamseqorcparamseqcoluna;
use db_utils;
use DBException;
use ECidade\Configuracao\RelatorioLegal\Modelo\Relatorio;
use ECidade\Configuracao\RelatorioLegal\Repositorio\RelatorioRepositorio;
use Exception;

/**
 * Class DuplicarRelatorio
 * @package ECidade\Configuracao\RelatorioLegal\Servico
 */
class DuplicarRelatorio
{
    /**
     * @var Relatorio
     */
    public $relatorioBase;
    /**
     * @var int
     */
    private $idNovoRelatorio;
    /**
     * @var string
     */
    private $nomeNovo;

    public function __construct($relatorio, $nomeNovo)
    {
        $this->relatorioBase = $relatorio;
        $this->nomeNovo = $nomeNovo;
    }

    public function getCodigoNovoRelatorio()
    {
        return $this->idNovoRelatorio;
    }

    /**
     * @throws Exception
     */
    public function duplicar()
    {
        $this->idNovoRelatorio = RelatorioRepositorio::nextval();
        $this->duplicaRelatorio();
        $this->duplicaPeriodos();
        $this->duplicarLinhas();
        $this->duplicarColunas();
        $this->duplicarConfiguracaoPadrao();
        $this->duplicarConfiguracaoMSC();
    }

    /**
     * @throws Exception
     */
    private function duplicaRelatorio()
    {
        $inserir = array(
            "o42_codparrel" => $this->idNovoRelatorio,
            "o42_descrrel" => "'{$this->nomeNovo}'",
            "o42_orcparamrelgrupo" => $this->relatorioBase->getGrupo(),
            "o42_notapadrao" => "'{$this->relatorioBase->getNotaPadrao()}'",
        );

        $columns = implode(', ', array_keys($inserir));
        $values = implode(', ', $inserir);

        $sql = "
            INSERT INTO orcparamrel ({$columns})
            VALUES ({$values});
        ";
        $this->executar($sql);
    }

    /**
     * @param string $sql
     * @throws Exception
     */
    private function executar($sql)
    {
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception(pg_last_error());
        }
    }

    /**
     * @throws Exception
     */
    private function duplicaPeriodos()
    {
        $sql = "
            INSERT INTO orcparamrelperiodos (
                o113_sequencial,
                o113_periodo,
                o113_orcparamrel
            ) (
                SELECT nextval('orcparamrelperiodos_o113_sequencial_seq'),
                       o113_periodo,
                       {$this->idNovoRelatorio}
                FROM orcparamrelperiodos
                WHERE o113_orcparamrel = {$this->relatorioBase->getSequencial()}
            )
        ";

        $this->executar($sql);
    }

    /**
     * @throws Exception
     */
    private function duplicarLinhas()
    {
        $sql = "
            INSERT INTO orcparamseq (
                SELECT {$this->idNovoRelatorio},
                       o69_codseq,
                       o69_descr,
                       o69_grupo,
                       o69_grupoexclusao,
                       o69_nivel,
                       o69_libnivel,
                       o69_librec,
                       o69_libsubfunc,
                       o69_libfunc,
                       o69_verificaano,
                       o69_labelrel,
                       o69_manual,
                       o69_totalizador,
                       o69_ordem,
                       o69_nivellinha,
                       o69_observacao,
                       o69_desdobrarlinha,
                       o69_origem
                FROM orcparamseq
                WHERE o69_codparamrel = {$this->relatorioBase->getSequencial()}
            )
        ";
        $this->executar($sql);
    }

    /**
     * @throws Exception
     */
    private function duplicarColunas()
    {
        $colunasDePara = array();

        $daoColuna = new \cl_orcparamseqcoluna();

        $consultaColunasRelatorio = "
            SELECT DISTINCT o115_sequencial,
                            o115_anousu,
                            o115_descricao,
                            o115_tipo,
                            o115_valoresdefault,
                            o115_nomecoluna,
                            o115_formula,
                            o115_origem,
                            o115_relatorio
            FROM orcparamseqcoluna
            INNER JOIN orcparamseqorcparamseqcoluna ON o116_orcparamseqcoluna = o115_sequencial
            WHERE o116_codparamrel = {$this->relatorioBase->getSequencial()}
        ";
        $resultadoConsultaColunas = db_query($consultaColunasRelatorio);
        $quantidadeLinhasConsulta = pg_num_rows($resultadoConsultaColunas);

        if (!$resultadoConsultaColunas) {
            throw new DBException("Ocorreu um erro ao buscar as colunas do relatório base.");
        }

        for ($linha = 0; $linha < $quantidadeLinhasConsulta; $linha++) {
            $coluna = db_utils::fieldsMemory($resultadoConsultaColunas, $linha);
            $daoColuna->o115_anousu = $coluna->o115_anousu;
            $daoColuna->o115_descricao = $coluna->o115_descricao;
            $daoColuna->o115_tipo = $coluna->o115_tipo;
            $daoColuna->o115_valoresdefault = $coluna->o115_valoresdefault;
            $daoColuna->o115_nomecoluna = $coluna->o115_nomecoluna;
            $daoColuna->o115_formula = $coluna->o115_formula;
            $daoColuna->o115_origem = $coluna->o115_origem;
            $daoColuna->o115_relatorio = $this->idNovoRelatorio;
            $daoColuna->incluir(null);

            if ($daoColuna->erro_status == 0) {
                throw new DBException("Ocorreu um erro ao duplicar as colunas do relatório.");
            }

            $colunasDePara[$coluna->o115_sequencial] = $daoColuna->o115_sequencial;
        }

        $daoLinhaColuna = new cl_orcparamseqorcparamseqcoluna();
        $where = "o116_codparamrel = {$this->relatorioBase->getSequencial()}";
        $consultaLinhaColuna = $daoLinhaColuna->sql_query_file(null, '*', null, $where);
        $resultadoLinhaColuna = db_query($consultaLinhaColuna);

        if (!$resultadoLinhaColuna) {
            throw new Exception("Ocorreu um erro ao buscar o vínculo entre linhas e colunas do relatório base.");
        }

        $quantidadeLinhasConsulta = pg_num_rows($resultadoLinhaColuna);
        for ($linha = 0; $linha < $quantidadeLinhasConsulta; $linha++) {
            $linhaColuna = db_utils::fieldsMemory($resultadoLinhaColuna, $linha);
            $daoLinhaColuna->o116_sequencial = null;
            $daoLinhaColuna->o116_codseq = $linhaColuna->o116_codseq;
            $daoLinhaColuna->o116_codparamrel = $this->idNovoRelatorio;
            $daoLinhaColuna->o116_orcparamseqcoluna = $colunasDePara[$linhaColuna->o116_orcparamseqcoluna];
            $daoLinhaColuna->o116_ordem = $linhaColuna->o116_ordem;
            $daoLinhaColuna->o116_periodo = $linhaColuna->o116_periodo;
            $daoLinhaColuna->o116_formula = pg_escape_string($linhaColuna->o116_formula);
            $daoLinhaColuna->incluir(null);

            if ($daoLinhaColuna->erro_status == 0) {
                throw new Exception("Ocorreu um erro ao incluir o vínculo entre colunas e linhas do relatório.");
            }
        }
    }

    /**
     * @throws Exception
     */
    private function duplicarConfiguracaoPadrao()
    {
        $sql = "
            INSERT INTO orcparamseqfiltropadrao (
                o132_sequencial,
                o132_orcparamrel,
                o132_orcparamseq,
                o132_anousu,
                o132_filtro
            ) (
                SELECT nextval('orcparamelementospadrao_o132_sequencial_seq'),
                       {$this->idNovoRelatorio},
                       o132_orcparamseq,
                       o132_anousu,
                       o132_filtro
                FROM orcparamseqfiltropadrao
                WHERE o132_orcparamrel = {$this->relatorioBase->getSequencial()}
            )
        ";
        $this->executar($sql);
    }

    /**
     * @throws Exception
     */
    private function duplicarConfiguracaoMSC()
    {
        $sql = "
            SELECT nextval('orcparamseqinfocomplementar_o157_sequencial_seq') AS o157_sequencial,
                   o157_valor,
                   o157_conplanoinfocomplementar,
                   {$this->idNovoRelatorio}                                   AS o157_relatorio,
                   o157_linha,
                   o157_padrao,
                   o157_infocomplementarlancamento
            FROM orcparamseqinfocomplementar
            WHERE o157_relatorio = {$this->relatorioBase->getSequencial()} AND o157_padrao IS TRUE;
        ";

        $rsNovosRegistros = db_query($sql);

        if (!$rsNovosRegistros) {
            throw new Exception('Não foi possível buscar as informações complementares.');
        }

        $sql = "
            SELECT nextval('orcparamseqinfocomplementarlancamento_o102_sequencial_seq') AS o102_sequencial_novo,
                   o102_sequencial,
                   o102_exclusao
            FROM orcparamseqinfocomplementar
                     INNER JOIN orcparamseqinfocomplementarlancamento
                         ON o157_infocomplementarlancamento = o102_sequencial
            WHERE o157_relatorio = {$this->relatorioBase->getSequencial()} AND o157_padrao IS TRUE
            GROUP BY o102_sequencial, o102_exclusao;
        ";

        $rsNovosLancamentos = db_query($sql);

        if (!$rsNovosLancamentos) {
            throw new Exception('Não foi possível buscar os lançamentos das informações complementares.');
        }

        $lancamentos = array();

        while ($novoLancamento = pg_fetch_object($rsNovosLancamentos)) {
            $sql = "
                INSERT INTO orcparamseqinfocomplementarlancamento
                VALUES (
                    {$novoLancamento->o102_sequencial_novo},
                    '{$novoLancamento->o102_exclusao}'
                )
            ";

            $this->executar($sql);

            $lancamentos[$novoLancamento->o102_sequencial] = $novoLancamento;
        }

        while ($novoRegistro = pg_fetch_object($rsNovosRegistros)) {
            $lancamento = $lancamentos[$novoRegistro->o157_infocomplementarlancamento];

            $sql = "
                INSERT INTO orcparamseqinfocomplementar
                VALUES (
                    {$novoRegistro->o157_sequencial},
                    '{$novoRegistro->o157_valor}',
                    {$novoRegistro->o157_conplanoinfocomplementar},
                    {$novoRegistro->o157_relatorio},
                    {$novoRegistro->o157_linha},
                    '{$novoRegistro->o157_padrao}',
                    {$lancamento->o102_sequencial_novo}
                )
            ";

            $this->executar($sql);
        }
    }
}
