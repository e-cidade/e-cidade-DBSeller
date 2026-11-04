<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Secretaria\Models\ModeloRelatorio;
use App\Http\Controllers\Controller;

class ModelosRelatoriosController extends Controller
{
    public function getModelosHistorico()
    {
        $modelos = ModeloRelatorio::select(["ed217_orientacao as orientacao", "ed217_i_codigo", "ed217_c_nome"])
            ->where('ed217_i_relatorio', 1)->get();
        return new DBJsonResponse($modelos);
    }

    public function getModelosRelatoriosPorTipo($tipo)
    {
        $modelos = ModeloRelatorio::select(["ed217_i_codigo", "ed217_c_nome", "ed217_brasao", "ed217_exibir_brasao",
            "ed217_disposicao_brasao", "ed217_exibir_assinatura_secretario", "ed217_exibir_assinatura_adicional",
            "ed217_exibir_grade_alunos", "ed217_exibir_coluna_disciplinas", "ed217_exibir_coluna_resultado_final",
            "ed217_disposicao_cabecalho", "ed217_orientacao as orientacao"
            ])
            ->where('ed217_i_relatorio', $tipo)->get();
        return new DBJsonResponse($modelos);
    }
}
