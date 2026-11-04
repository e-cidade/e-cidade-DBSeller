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

namespace ECidade\RecursosHumanos\ESocial\Mapeadores\Relatorios;

class RubricaMapeador implements FormularioMapeador
{
    private $dadosEcidade = [];

    private $dadosFormulario = [];

    public function __construct()
    {
    }

    public function getPerguntas()
    {
        //os intentificadores devem estar em minusculo pois o sql coloca em minusculo os nomes das colunas
        return [
            0 => "codrubr",
            1 => "idetabrubr",
            2 => "inivalid",
            3 => "fimvalid",
            4 => "dscrubr",
            5 => "natrubr",
            6 => "tprubr",
            7 => "codinccp",
            8 => "codincirrf",
            9 => "codincfgts",
            10 => "codincsind",
            11 => "observacao"
        ];
    }

    public function getConfiguracoesColunas()
    {
        //comprimento maximo = 192
        $comprimentoMaximo = 192;
        $comprimentoBase = 50;
        $comprimento = ($comprimentoMaximo - $comprimentoBase)/2;
        return [
            "codrubr" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "idetabrubr" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "inivalid" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "fimvalid" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "dscrubr" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "natrubr" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "tprubr" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "codinccp" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "codincirrf" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "codincfgts" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "codincsind" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "observacao" => ['tamanho' => $comprimento, 'tamanhoTitulo' => $comprimentoBase, 'colunas' => 2],
            "base" => [
                'titulo' => 'Bases:',
                'tamanho' => $comprimento,
                'tamanhoTitulo' => $comprimentoBase,
                'colunas' => 1,
                'fontsize' => 6
            ],
            "tiporubrica" => [
                'titulo' => 'Tipo Rúbrica:',
                'tamanho' => $comprimento,
                'tamanhoTitulo' => $comprimentoBase,
                'colunas' => 1,
                'fontsize' => 6
            ],
        ];
    }

    public function getCamposSistema()
    {
        return [
            'rh27_rubric as codRubr',
            'rh27_descr as dscRubr',
            "'' as ideTabRubr",
            "'' as iniValid",
            "'' as fimValid",
            "'' as natRubr",
            "'' as tpRubr",
            "'' as codIncCP",
            "'' as codIncIRRF",
            "'' as codIncFGTS",
            "'' as codIncSIND",
            "'' as observacao"
        ];
    }

    private function getCamposAuxiliares()
    {
        return [
            "array_to_string(array_accum(r09_base), ',') as bases",
            "
                case
                     when rh27_pd = 1 then 'PROVENTO'
                     when rh27_pd = 2 then 'DESCONTO'
                         else 'BASE'
                end as tiporubrica
            "
        ];
    }

    public function getIdentificador($original = false)
    {
        if ($original) {
            return "codRubr";
        }
        return "codrubr";
    }

    public function getDePara()
    {
    }

    public function getSqlDePara($ano, $mes)
    {
        $instituicao = \InstituicaoRepository::getInstituicaoSessao();
        $campos = array_merge($this->getCamposSistema(), $this->getCamposAuxiliares());
        $campos = implode(',', $campos);
        $oCompetenciaAtual = \DBPessoal::getCompetenciaFolha();
        $sql = "
            select
              codRubr,
              dscRubr,
              ideTabRubr,
              iniValid,
              fimValid,
              natRubr,
              tpRubr,
              coalesce(array_to_string(array_accum(codIncCP), ','), '') as codIncCP,
              coalesce(array_to_string(array_accum(codIncIRRF), ','), '') as codIncIRRF,
              coalesce(array_to_string(array_accum(codIncFGTS), ','), '') as codIncFGTS,
              codIncSIND,
              observacao,
              array_to_string(array_accum(bases), ',') as bases,
              tiporubrica
            from (
                select
                       {$campos}
                from
                     rhrubricas
                    left join basesr on
                        rh27_rubric = r09_rubric
                        and r09_instit = rh27_instit
                        and r09_anousu = {$ano}
                        and r09_mesusu = {$mes}
                    left join bases on
                        r08_instit = r09_instit
                        and r08_anousu = {$ano}
                        and r08_mesusu = {$mes}
                        and r08_codigo = r09_base
                where
                      rh27_instit = {$instituicao->getCodigo()}
                      and rh27_rubric in (select distinct rubrica from (
                            select distinct r14_rubric as rubrica
                            from gerfsal
                            where r14_instit = {$instituicao->getCodigo()}
                                and  r14_mesusu = {$mes} and r14_anousu = {$ano}
                            union
                            select distinct r20_rubric as rubrica
                            from gerfres
                            where r20_instit = {$instituicao->getCodigo()}
                                and  r20_mesusu = {$mes} and r20_anousu = {$ano}
                            union
                            select distinct r48_rubric as rubrica
                            from gerfcom
                            where r48_instit = {$instituicao->getCodigo()}
                                and  r48_mesusu = {$mes} and r48_anousu = {$ano}
                            union
                            select distinct r35_rubric as rubrica
                            from gerfs13
                            where r35_instit = {$instituicao->getCodigo()}
                                and  r35_mesusu = {$mes} and r35_anousu = {$ano}
                            ) as x)
                group by
                    rh27_rubric,
                    rh27_pd,
                    rh27_descr,
                    r09_base ) as x
            group by
                codRubr,
                dscRubr,
                ideTabRubr,
                iniValid,
                fimValid,
                natRubr,
                tpRubr,
                codIncSIND,
                observacao,
                tiporubrica
            order by codRubr asc
            ";
        return $sql;
    }

    public function setDadosEcidade($dadosEcidade)
    {
        $this->dadosEcidade = $dadosEcidade;
    }

    public function getDadosEcidade()
    {
        return $this->dadosEcidade;
    }

    public function setDadosFormulario($dadosFormulario)
    {
        $this->dadosFormulario = $dadosFormulario;
    }

    public function getDadosFormulario()
    {
        return $this->dadosFormulario;
    }

    public function processarDadosEcidade($mes, $ano)
    {
        $sql = $this->getSqlDePara($mes, $ano);
        $rs = db_query($sql);

        if (!$rs) {
            throw new DBException("Erro ao buscar as informações do sistema.");
        }

        $this->dadosEcidade =\db_utils::makeCollectionFromRecord(
            $rs,
            function ($dadoEcidade) {
                return (get_object_vars($dadoEcidade));
            }
        );
    }

    public function getColunas()
    {
        $colunas = [];
        $configuracoes = $this->getConfiguracoesColunas();
        foreach ($this->getPerguntas() as $campo) {
            foreach ($this->getDadosFormulario()->fields as $perguntaFormulario) {
                if ($campo == $perguntaFormulario->identificador) {
                    foreach ($configuracoes as $configuracao => $dados) {
                        if ($configuracao == $perguntaFormulario->identificador) {
                            $dados['titulo'] = $perguntaFormulario->descricao;
                            $colunas[$campo] = $dados;
                            continue;
                        }
                    }
                }
            }
        }
        // Adicionando campo auxiliar
        $colunas["bases"] = $configuracoes['base'];
        $colunas["tiporubrica"] = $configuracoes['tiporubrica'];

        return $colunas;
    }
}
