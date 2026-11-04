<?php

namespace App\Domain\Educacao\Secretaria\Relatorios;

use Exception;
use FpdfMultiCellBorder;

class FuncionariosAtivosPDF extends FpdfMultiCellBorder
{
    private $dataInicial;
    private $dataFinal;
    private $funcoes;
    private $cargos;
    private $escolas;

    public function __construct($dadosCabecalho, $filtros)
    {
        parent::__construct('L');
        global $head1;
        global $head3;
        global $head4;
        $head1 = "RELATÓRIO FUNCIONÁRIOS ATIVOS";
        if (empty($dadosCabecalho->dataFinal)) {
            $dadosCabecalho->dataFinal = date('Y-m-d');
        }
        if (!empty($dadosCabecalho->dataInicial) && !empty($dadosCabecalho->dataFinal)) {
            $head4 = sprintf(
                "De %s Até %s",
                db_formatar($dadosCabecalho->dataInicial, 'd'),
                db_formatar($dadosCabecalho->dataFinal, 'd')
            );
        } elseif (!empty($dadosCabecalho->dataInicial)) {
            $head4 = sprintf("Apartir de %s", db_formatar($dadosCabecalho->dataInicial, 'd'));
        } elseif (!empty($dadosCabecalho->dataFinal)) {
            $head4 = sprintf("Até de %s", db_formatar($dadosCabecalho->dataFinal, 'd'));
        }

        $this->dataInicial = $dadosCabecalho->dataInicial;
        $this->dataFinal = $dadosCabecalho->dataFinal;
        $this->funcoes = $filtros->funcoes;
        $this->cargos = $filtros->cargos;
        $this->escolas = $filtros->escolas;
    }

    protected function initPdf()
    {
        $this->mostrarRodape(true);
        $this->mostrarTotalDePaginas(true);
        $this->SetMargins(10, 8, 8);
        $this->Open();
        $this->SetAutoPageBreak(true, 10);
        $this->AliasNbPages();
        $this->SetFillColor(225);
        $this->SetFont('Arial', 'B', 9);
        $this->exibeHeader(true);
    }

    protected function imprimir()
    {
        $fileName = 'tmp/relatorio_funcionarios_ativos_' . time() . '.pdf';
        $this->Output($fileName, false, true);
        return [
            "name" => "Relatório Funcionários Ativos PDF",
            "path" => $fileName,
            "file" => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function emitirPdf()
    {
        $escolas = $this->buscarDados();
        $this->initPdf();
        $this->AddPage();

        $totalizadorGeral = (object)[
            "ativos" => 0,
            "inativos" => 0
        ];

        foreach ($escolas as $escola) {
            $totalizador = (object)[
                "ativos" => 0,
                "inativos" => 0
            ];

            $this->imprimirHeader($escola->codigo_escola, $escola->nome_escola);
            $fill = false;
            foreach ($escola->funcionarios as $funcionario) {
                if ($this->getAvailHeight() < 5) {
                    $this->AddPage('L');
                    $this->imprimirHeader();
                    $fill = false;
                }
                $this->SetFont('Arial', '', 7);
                $this->Cell(16, 5, $funcionario->mat_cgm, 0, 0, 'C', $fill);
                $this->Cell(65, 5, $funcionario->nome_rh, 0, 0, 'L', $fill);
                $this->Cell(22, 5, $funcionario->cpf, 0, 0, 'C', $fill);
                $this->Cell(44, 5, $funcionario->funcao, 0, 0, 'C', $fill);
                $this->Cell(52, 5, $funcionario->cargo, 0, 0, 'C', $fill);
                $this->Cell(36, 5, $funcionario->vinculo, 0, 0, 'C', $fill);
                $this->Cell(22, 5, db_formatar($funcionario->ed75_d_ingresso, 'd'), 0, 0, 'C', $fill);
                $this->Cell(22, 5, db_formatar($funcionario->ed75_i_saidaescola, 'd'), 0, 1, 'C', $fill);

                $fill = !$fill;
                if (empty($funcionario->ed75_i_saidaescola)) {
                    $totalizador->ativos++;
                    $totalizadorGeral->ativos++;
                } else {
                    $totalizador->inativos++;
                    $totalizadorGeral->inativos++;
                }
            }

            if ($this->getAvailHeight() < 5) {
                $this->AddPage('L');
            }
            $this->imprimirTotalizadorGeral(
                "Total de Funcionários na Escola",
                $totalizador
            );
        }
        $this->imprimirTotalizadorGeral(
            "Total Geral das Escolas",
            $totalizadorGeral,
            5
        );

        return $this->imprimir();
    }

    private function imprimirHeader($codigo = 0, $escola = '')
    {
        $this->SetFillColor(225);
        $this->SetY($this->GetY() + 5);
        $this->SetFont('Arial', 'B', 8);
        if (!empty($codigo) && !empty($escola)) {
            $this->Cell(289, 8, "{$codigo} - {$escola}", 0, 1, "L", 0);
        }
        $this->Cell(16, 6, "Matrícula", 0, 0, "C", 1);
        $this->Cell(65, 6, "Nome", 0, 0, "L", 1);
        $this->Cell(22, 6, "CPF", 0, 0, "C", 1);
        $this->Cell(44, 6, "Função", 0, 0, "C", 1);
        $this->Cell(52, 6, "Cargo", 0, 0, "C", 1);
        $this->Cell(36, 6, "Vínculo", 0, 0, "C", 1);
        $this->Cell(22, 6, "Dt. Ingresso", 0, 0, "C", 1);
        $this->Cell(22, 6, "Dt. Saída", 0, 1, "C", 1);
        $this->SetFillColor(240);
    }

    private function imprimirTotalizador($totalizador)
    {
        $this->SetFillColor(225);
        $this->SetY($this->GetY() + 5);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(220, 6, "", 0, 0, "L", 0);
        $this->Cell(49, 6, "Total de Funcionários Ativos:", 0, 0, "L", 1);
        $this->Cell(10, 6, "{$totalizador->ativos}", 0, 1, "L", 1);
        $this->Cell(220, 6, "", 0, 0, "L", 0);
        $this->Cell(49, 6, "Total de Funcionários Inativos:", 0, 0, "L", 1);
        $this->Cell(10, 6, "{$totalizador->inativos}", 0, 1, "L", 1);
        $this->SetFillColor(240);
    }

    private function imprimirTotalizadorGeral($label, $totalizador, $marginTop = 0)
    {
        $this->SetFillColor(225);
        $this->SetY($this->GetY() + $marginTop);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(149, 6, $label, 0, 0, "L", 1);
        $this->Cell(35, 6, "Funcionários Ativos:", 0, 0, "L", 1);
        $this->Cell(30, 6, "{$totalizador->ativos}", 0, 0, "L", 1);
        $this->Cell(35, 6, "Funcionários Inativos:", 0, 0, "L", 1);
        $this->Cell(30, 6, "{$totalizador->inativos}", 0, 1, "L", 1);
        $this->SetFillColor(240);
    }

    /**
     * @return \stdClass[]
     * @throws Exception
     */
    private function buscarDados()
    {
        $where = [];
        if (!empty($this->funcoes)) {
            $where[] = "ed01_i_codigo in ($this->funcoes)";
        }
        if (!empty($this->cargos)) {
            $instit = db_getsession('DB_instit');
            $where[] = "rh37_funcao in ($this->cargos) and rh37_instit = {$instit}";
        }
        if (!empty($this->escolas)) {
            $where[] = "ed75_i_escola in ($this->escolas)";
        }

        $whereFuncionarios = implode(' and ', $where);
        if (!empty($whereFuncionarios)) {
            $whereFuncionarios = "where {$whereFuncionarios}";
        }
        $sql = "
SELECT distinct on (NomeEscola, mat_cgm, Nome_RH) ed18_c_nome        AS NomeEscola,
                                                  ed75_i_escola      AS COD_escola,

                                                  CASE
                                                      WHEN ed20_i_tiposervidor = 1 THEN cgmrh.z01_nome
                                                      ELSE cgmcgm.z01_nome
                                                      END            AS Nome_RH,
                                                  trim(ed01_c_descr) AS Funcao,
                                                  TRIM(rh37_descr)   AS cargo,
                                                  CASE
                                                      WHEN ed20_i_tiposervidor = 1 THEN 'SIM'
                                                      WHEN ed20_i_tiposervidor = 2 THEN 'NAO'
                                                      ELSE 'NAO INFORMADO'
                                                      END            AS Servidor,
                                                  Trim(rh30_descr)   AS Vinculo,
                                                  CASE
                                                      WHEN ed20_i_tiposervidor = 1 THEN rh01_regist
                                                      ELSE ed285_i_cgm
                                                      END            AS MAT_CGM,
                                                  cgmrh.z01_cgccpf   AS CPF,
                                                  ed75_d_ingresso,
                                                  ed75_i_saidaescola
FROM atividaderh
         INNER JOIN rechumanoativ ON ed22_i_atividade = ed01_i_codigo
         INNER JOIN rechumanoescola ON ed75_i_codigo = ed22_i_rechumanoescola
         INNER JOIN escola ON ed18_i_codigo = ed75_i_escola
         INNER JOIN rechumano ON ed20_i_codigo = ed75_i_rechumano
         LEFT JOIN rechumanocgm ON ed285_i_rechumano = ed20_i_codigo
         LEFT JOIN rechumanopessoal ON ed284_i_rechumano = ed20_i_codigo
         LEFT JOIN rhpessoal ON ed284_i_rhpessoal = rh01_regist
         LEFT JOIN rhfuncao ON rh01_funcao = rh37_funcao AND rh01_instit = rh37_instit
         LEFT JOIN cgm AS cgmcgm ON cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm
         LEFT JOIN cgm AS cgmrh ON cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
         left join rhpessoalmov on rh02_regist = rh01_regist
         LEFT JOIN rhregime ON rh30_codreg = rh02_codreg
{$whereFuncionarios}
order by ed18_c_nome, Nome_RH asc, mat_cgm, Vinculo desc
";
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar Funcionários Ativos");
        }

        $funcionarios = [];
        while ($funcionario = pg_fetch_object($rs)) {
            if (is_null($funcionario->ed75_i_saidaescola) && $funcionario->ed75_d_ingresso > $this->dataFinal) {
                continue;
            }
            if (!is_null($funcionario->ed75_i_saidaescola) && $funcionario->ed75_d_ingresso > $this->dataFinal) {
                continue;
            }
            if (!empty($this->dataInicial) && !is_null($funcionario->ed75_i_saidaescola) &&
                $funcionario->ed75_i_saidaescola < $this->dataInicial) {
                continue;
            }
            $funcionarios[] = $funcionario;
        }

        $escolas = [];
        foreach ($funcionarios as $funcionario) {
            if (!array_key_exists($funcionario->cod_escola, $escolas)) {
                $escolas[$funcionario->cod_escola] = (object)[
                    "codigo_escola" => $funcionario->cod_escola,
                    "nome_escola" => $funcionario->nomeescola,
                    "funcionarios" => []
                ];
            }
            $escolas[$funcionario->cod_escola]->funcionarios[] = $funcionario;
        }

        return $escolas;
    }
}
