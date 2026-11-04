<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\Dose;
use App\Domain\Educacao\Escola\Models\Profissional;
use App\Domain\Educacao\Escola\Models\ProfissionalFormacaoSuperior;
use App\Domain\Educacao\Escola\Models\ProfissionalVacinacao;
use App\Domain\Educacao\Escola\Models\Vacina;
use App\Domain\Educacao\Escola\Repositories\ProfissionalEscolaRepository;
use App\Domain\Educacao\Escola\Resources\ProfissionaisEscolaResource;
use App\Http\Controllers\Controller;
use db_utils;
use DBDate;
use Exception;
use Illuminate\Http\Request;

class ProfissionalController extends Controller
{
    private $repository;
    public function __construct()
    {
        $this->repository = new ProfissionalEscolaRepository();
    }

    public function vacinas(Profissional $profissional)
    {
        $vacinasProfissional = $profissional->vacinacao()->get()->each(function ($vacinacao) {
            $vacinacao->vacina;
            $vacinacao->dose;
        });

        $aVacinasProfissional = $vacinasProfissional->toArray();

        usort($aVacinasProfissional, function ($a, $b) {
            return strcmp($a['vacina']['ed178_descricao'], $b['vacina']['ed178_descricao']);
        });

        return new DBJsonResponse($aVacinasProfissional);
    }

    /**
     * @param Profissional $profissional
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function vacinar(Profissional $profissional, Request $request)
    {
        $data = new DBDate($request->get('data'));

        if ($data->getDate() > DBDate::now()->getDate()) {
            throw new Exception("A Data da aplicação não pode ser posterior a data atual!", 400);
        }

        $vacina = Vacina::find($request->get('vacina'));
        $dose = Dose::find($request->get('dose'));

        $vacinasProfissional = $profissional->vacinacao()->get()->filter(
            function ($vacinacaoProfissional) use ($vacina, $dose) {
                return (
                    $vacinacaoProfissional->ed181_vacina == $vacina->ed178_codigo &&
                    $vacinacaoProfissional->ed181_dose == $dose->ed180_codigo
                );
            }
        );

        if ($vacinasProfissional->count() > 0) {
            $erro = sprintf(
                "Profissional já tem o registro da Vacina (%s) com a Dose (%s)!",
                $vacina->ed178_descricao,
                $dose->ed180_descricao
            );
            throw new Exception($erro, 400);
        }

        $vacinacao = new ProfissionalVacinacao();
        $vacinacao->ed181_rechumano = $profissional->ed20_i_codigo;
        $vacinacao->ed181_data = $data->getDate();
        $vacinacao->ed181_vacina = $vacina->ed178_codigo;
        $vacinacao->ed181_dose = $dose->ed180_codigo;
        if (!$vacinacao->save()) {
            throw new Exception("Erro ao salvar vacina do Profissional");
        }

        $vacinacao->vacina;
        $vacinacao->dose;
        return new DBJsonResponse($vacinacao, "Vacinação salva com sucesso!");
    }

    public function deleteVacinacao(ProfissionalVacinacao $profissionalVacinacao)
    {
        if (!$profissionalVacinacao->delete()) {
            throw new Exception("Erro ao excluir vacinação!");
        }

        return new DBJsonResponse([], "Vacinação excluida com sucesso!");
    }

    public function getProfissional(Request $request)
    {
        $filtros = (object) [];
        $filtros->cgm = $request->user()->usuarioCgm->cgm->z01_numcgm;
        $filtros->escola = $request->get('DB_coddepto');
        $profissionalEscola = $this->repository->getProfissionaisEscola($filtros);

        return new DBJsonResponse($profissionalEscola);
    }

    public function getProfissionaisComSuperior($escola, $ativos)
    {
        $sql   = "with profissionais_com_superior as (
                    select * from rechumano
                        join rechumanoescola on rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo
                    where ed75_i_escola = {$escola} and ed20_i_escolaridade = 6 ";
        $sql  .= $ativos == 1 ? "and rechumanoescola.ed75_i_saidaescola is null " : " ";
        $sql  .= "), origem_profissinal as (
                    select case when ed285_i_cgm is null then rh01_numcgm else ed285_i_cgm end as cgm,
                        rh01_regist as matricula,
                        profissionais_com_superior.*
                    from profissionais_com_superior
                    left join rechumanocgm on rechumanocgm.ed285_i_rechumano = profissionais_com_superior.ed20_i_codigo
                    left join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = ed20_i_codigo
                    left join rhpessoal on rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
                ) select origem_profissinal.cgm, z01_nome, z01_nasc as data_nascimento, z01_cgccpf as cpf,
                case when
                    (select count(*) from rhformacaosuperior where ed183_cgm = cgm.z01_numcgm) > 0
                then true else false end as possuiPos
                    from origem_profissinal
                    join cgm on cgm.z01_numcgm = origem_profissinal.cgm order by z01_nome";

        $profissionais = collect(\DB::select($sql));
        return new DBJsonResponse($profissionais->all());
    }

    public function salvarFormacaoSuperiorPofissional(Request $request)
    {
        $parametros = $request->all();
        unset($parametros['_path']);
        try {
            if (empty($parametros['ed183_id']) || is_null($parametros['ed183_id'])) {
                unset($parametros['ed183_id']);
                $profissional = ProfissionalFormacaoSuperior::create($parametros);
                return new DBJsonResponse($profissional->ed183_id, 'Formação salva com sucesso!');
            } else {
                $profissional =  ProfissionalFormacaoSuperior::
                                                updateOrCreate(['ed183_id' => $parametros['ed183_id']], $parametros);
                return new DBJsonResponse($profissional->ed183_id, 'Formação alterada com sucesso!');
            }
        } catch (Exception $exception) {
            throw new Exception("Falha ao salvar formação do profissional!");
        }
    }

    public function getFormacoesSuperiorDoProfissional(Request $request)
    {
        $formacoes = ProfissionalFormacaoSuperior::where('ed183_cgm', $request->get('cgm'))->get();
        return new DBJsonResponse($formacoes->all());
    }

    public function excluirFormacaoSuperiorDoProfissional(Request $request)
    {
        try {
            ProfissionalFormacaoSuperior::where('ed183_id', $request->get('ed183_id'))->delete();
            return new DBJsonResponse([], 'Formação excluída com sucesso!');
        } catch (Exception $exception) {
            throw new Exception("Falha aso excluir formação do profisiional!");
        }
    }

    public function buscarDocumentoPosGraduacao(Request $request)
    {
        $arquivo = "";
        $arquivoPosgraduacao = "";

        $sqlPosgraduacao = "select ed183_docpos_estorage from escola.rhformacaosuperior where ed183_id=
                            {$request->get('ed183_id')}";
        $rsPosgraduacao = db_query($sqlPosgraduacao);

        if (!$rsPosgraduacao) {
            db_msgbox('Não foi possível buscar a formação.');
            return;
        }

        if (pg_num_rows($rsPosgraduacao) > 0) {
            $idPosgraduacao = db_utils::fieldsMemory($rsPosgraduacao, 0)->ed183_docpos_estorage;
            $arquivo = !empty($idPosgraduacao) ? StorageHelper::downloadArquivo($idPosgraduacao): "" ;
            $arquivoPosgraduacao = basename($arquivo);
        }

        return new DBJsonResponse((object)['arquivo' => $arquivo, 'arquivoPosgraduacao' => $arquivoPosgraduacao]);
    }

    public function salvarDocumentoPosGraduacao(Request $request)
    {
        $arquivo = "";
        $arquivoPosgraduacao = "";

        try {
            db_query("begin");
            $sqlPosgraduacao = "select ed183_docpos_estorage from escola.rhformacaosuperior where ed183_id=
                                {$request->get('ed183_id')}";
            $rsPosgraduacao = db_query($sqlPosgraduacao);

            if (!$rsPosgraduacao) {
                db_msgbox('Não foi possível buscar a formação.');
                return;
            }

            $idPosgraduacao = db_utils::fieldsMemory($rsPosgraduacao, 0)->ed183_docpos_estorage;
            $arquivoPosgraduacao = !empty($idPosgraduacao) ? StorageHelper::downloadArquivo($idPosgraduacao) : "";
            $namePosgraduacao = $request->get('arquivo_id');

            if ($namePosgraduacao != "" && $arquivoPosgraduacao != $namePosgraduacao) {
                $idFilePosgraduacao = StorageHelper::uploadArquivo($namePosgraduacao, null, true);
                $sqlIncluirPosgraduacao   = "UPDATE escola.rhformacaosuperior set ed183_docpos_estorage =
                                            {$idFilePosgraduacao} ";
                $sqlIncluirPosgraduacao  .= "where ed183_id = {$request->get('ed183_id')}";
                $rsPosgraduacao = db_query($sqlIncluirPosgraduacao);
                if (!$rsPosgraduacao) {
                    db_msgbox('Não foi possível salvar a formação.');
                    return;
                }
            }

            db_query("end");
        } catch (Exception $exception) {
            db_query("rollback");
            throw new Exception("Falha ao salvar o documento de Pós Graduação do profisional!");
        }
        return new DBJsonResponse([], 'Documento de Pós Graduação do profisional salva com sucesso!');
    }

    public function excluirDocumentoPosGraduacao(Request $request)
    {
        try {
            db_query("begin");
            $sqlPosgraduacao   = "UPDATE escola.rhformacaosuperior set ed183_docpos_estorage = null ";
            $sqlPosgraduacao  .= "where ed183_id = {$request->get('ed183_id')}";
            $rsPosgraduacao = pg_query($sqlPosgraduacao);

            db_query("end");
            return new DBJsonResponse([], 'Pós Graduação do profisiional excluida com sucesso!');
        } catch (Exception $exception) {
            db_query("rollback");
            throw new Exception("Falha ao exluir Pós Graduação do profisiional!");
        }
    }

    public function get(Request $request, $escola)
    {
        $filtros = $request->all();
        $filtros['escola'] = $escola;

        $retorno = $this->repository->getProfissionaisEscola($filtros)->map(function ($registro) {
            return ProfissionaisEscolaResource::toResponse($registro);
        });
        return new DBJsonResponse($retorno);
    }
}
