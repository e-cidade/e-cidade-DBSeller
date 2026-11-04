<?php

namespace App\Domain\Educacao\Escola\Controllers\Relatorios;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\Profissional;
use App\Domain\Educacao\Escola\Requests\RelatorioVacinacaoRequest;
use App\Http\Controllers\Controller;
use DBDate;
use ECidade\Educacao\Escola\Relatorios\VacinasRelatorioCSV;
use ECidade\Educacao\Escola\Relatorios\VacinasRelatorioPDF;
use Illuminate\Support\Facades\DB;

class Vacinacao extends Controller
{
    public function emitir(RelatorioVacinacaoRequest $request)
    {
        $where = ["ed75_i_saidaescola is null"];

        $codigoEscola = $request->get('escola');

        if (!empty($codigoEscola)) {
            $where[] = "ed18_i_codigo = {$codigoEscola}";
        }

        switch ($request->get("tipo")) {
            case 2: // Caso o tipo de relatorio for apenas profissionais vacinados
                $where[] = "ed178_codigo is not null";
                break;
            case 3: // Caso o tipo de relatorio for apenas profissionais não vacinados
                $where[] = "ed178_codigo is null";
                break;
        }

        if (!is_null($request->get('vacinas'))) {
            $sVacinas = implode(', ', $request->get('vacinas'));
            $where[] = "(ed178_codigo in ({$sVacinas}) or ed178_codigo is null)";
        }

        $sWhere = implode(' and ', $where);
        $sql = "
            select ed18_i_codigo,
            ed18_codigoreferencia,
            ed18_c_nome as escola,
            rhpessoal.rh01_regist as matricula,
            case
                when cgmcgm.z01_numcgm is null
                    then cgmrh.z01_numcgm
                else cgmcgm.z01_numcgm end as numcgm,
            case
                when cgmcgm.z01_nome is null
                    then cgmrh.z01_nome
                else cgmcgm.z01_nome end as nome,
            case
                when cgmfisico.z04_nomesocial is null
                    then cgmfisicorh.z04_nomesocial
                else cgmfisico.z04_nomesocial end as nomesocial,
            ed178_descricao as vacina,
            rechumano_vacinacao.ed181_data,
            doses.ed180_descricao as dose
            from rechumano
              join rechumanoescola ON rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo
              join escola ON escola.ed18_i_codigo = rechumanoescola.ed75_i_escola
              left join rechumano_vacinacao on ed181_rechumano = ed20_i_codigo
              left join vacinas_escola ON vacinas_escola.ed178_codigo = rechumano_vacinacao.ed181_vacina
              left join doses ON doses.ed180_codigo = rechumano_vacinacao.ed181_dose
              left join rechumanocgm ON rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
              left join protocolo.cgm as cgmcgm ON cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm
              left join cgmfisico ON cgmfisico.z04_numcgm = cgmcgm.z01_numcgm
              left join rechumanopessoal ON rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
              left join pessoal.rhpessoal ON rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
              left join protocolo.cgm as cgmrh ON cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
              left join cgmfisico cgmfisicorh ON cgmfisicorh.z04_numcgm = cgmrh.z01_numcgm
            where {$sWhere}
            order by ed18_i_codigo, nome, ed178_descricao, ed180_descricao;
        ";

        if ($request->get('exportacao') == 'csv') {
            $dados = DB::select($sql);
            $relatorio = new VacinasRelatorioCSV($dados);
            return new DBJsonResponse($relatorio->emitirCsv(), "Emitindo relatório em CSV");
        }

        $dados = [];
        collect(DB::select($sql))->each(function ($dado) use (&$dados) {
            if (!array_key_exists($dado->ed18_i_codigo, $dados)) {
                $dados[$dado->ed18_i_codigo] = (object)[
                    "ed18_i_codigo" => $dado->ed18_i_codigo,
                    "ed18_codigoreferencia" => $dado->ed18_codigoreferencia,
                    "nome" => $dado->escola,
                    "profissionais" => []
                ];
            }

            $uKey = "{$dado->numcgm}#{$dado->matricula}";
            if (!array_key_exists($uKey, $dados[$dado->ed18_i_codigo]->profissionais)) {
                $dados[$dado->ed18_i_codigo]->profissionais[$uKey] = [];
            }

            $dado->data_vacinacao = '';
            if (!empty($dado->ed181_data)) {
                $dado->data_vacinacao = DBDate::create($dado->ed181_data)->getDate(DBDate::DATA_PTBR);
            }

            $dados[$dado->ed18_i_codigo]->profissionais[$uKey][] = $dado;
        });

        $relatorio = new VacinasRelatorioPdf($dados, $request->get('tipo'));
        return new DBJsonResponse($relatorio->emitirPdf(), "Emitindo relatório em PDF");
    }
}
