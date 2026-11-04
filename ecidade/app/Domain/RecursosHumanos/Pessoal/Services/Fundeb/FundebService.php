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

namespace App\Domain\RecursosHumanos\Pessoal\Services\Fundeb;

use App\Domain\RecursosHumanos\Pessoal\Model\CalculoFolha\Gerfsal;
use App\Domain\RecursosHumanos\Pessoal\Model\Fundeb\RhCargosFundeb;
use App\Domain\RecursosHumanos\Pessoal\Model\Fundeb\RhParametrosFundeb;
use App\Domain\RecursosHumanos\Pessoal\Model\Fundeb\RhCalculoFundeb;
use App\Domain\RecursosHumanos\Pessoal\Model\Fundeb\RhProcessamentoFundeb;
use App\Domain\RecursosHumanos\Pessoal\Model\RhFuncao;
use App\Domain\RecursosHumanos\Pessoal\Model\Ponto\PontoSalario;

use App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb\CargosFundebRequest;
use App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb\ParametrosFundebRequest;
use App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb\CalculoFundebRequest;
use App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb\ProcessamentoFundebRequest;
use Exception;

/**
 * Class CalculoFundebService
 * @package App\Domain\RecursosHumanos\Pessoal\Services\Fundeb
 */
class FundebService
{
    /**
     * @param $id
     * @return RhCargosFundeb
     */
    public function findCargos($id)
    {
        return RhCargosFundeb::find($id);
    }

    /**
     * @param CargosFundebRequest $request
     * @return RhCargosFundeb|mixed
     * @throws Exception
     */
    public function salvarCargosFundeb(CargosFundebRequest $request)
    {
        $id = $request->input('rh283_codigo');
        $cargo = new RhCargosFundeb();
        if (!empty($id)) {
            $cargo = RhCargosFundeb::find($id);
        }
        $cargo->rh283_descricao = $request->input('rh283_descricao');
        $cargo->save();
        return $cargo;
    }

    /**
     * @param
     * @return RhParametrosFundeb
     */
    public function findParametrosCargos()
    {
        return RhParametrosFundeb::where(
            'rh284_instituicao',
            db_getsession(('DB_instit'))
        )->with('descricaoCargo')->get();
    }

    /**
     * @param
     * @return RhParametrosFundeb
     */
    public function findParametrosFuncoes()
    {
        return RhParametrosFundeb::where(
            'rh284_instituicao',
            db_getsession(('DB_instit'))
        )->with('descricaoFuncao')->get();
    }

    /**
     * @param
     * @return RhParametrosFundeb
     */
    public function findParametrosLocais()
    {
        return RhParametrosFundeb::where(
            'rh284_instituicao',
            db_getsession(('DB_instit'))
        )->with('descricaoLocal')->get();
    }

    /**
     * @param
     * @return RhParametrosFundeb
     */
    public function findParametrosAssentamentos()
    {
        return RhParametrosFundeb::where(
            'rh284_instituicao',
            db_getsession(('DB_instit'))
        )->with('descricaoAssentamento')->get();
    }

    /**
     * @param
     * @return RhParametrosFundeb
     */
    public function findParametroRubricaAbatimento()
    {
        return RhParametrosFundeb::where(
            'rh284_instituicao',
            db_getsession(('DB_instit'))
        )->with('descricaoRubricaAbatimento')->get();
    }

    /**
     * @param ParametrosFundebRequest $request
     * @return RhParametrosFundeb|mixed
     * @throws Exception
     */
    public function salvarParametrosFundeb(ParametrosFundebRequest $request)
    {
        $id = $request->input('rh284_sequencial');
        $parametros = new RhParametrosFundeb();
        if (!empty($id)) {
            $parametros = RhParametrosFundeb::find($id);
        }

        $parametros->rh284_tipo               = $request->input('tipo');
        $parametros->rh284_cargo              = $request->input('cargo');
        $parametros->rh284_funcao             = $request->input('funcao');
        $parametros->rh284_local_trabalho     = $request->input('local');
        $parametros->rh284_assentamento       = $request->input('assentamento');
        $parametros->rh284_rubrica_abatimento = $request->input('rubrica');
        $parametros->rh284_instituicao        = db_getsession('DB_instit');
        $parametros->save();
        return $parametros;
    }

    /**
     * @param $cargo
     * @throws Exception
     */
    public function removerParametroCargo($cargo)
    {
        RhParametrosFundeb::where('rh284_cargo', $cargo)
            ->where('rh284_instituicao', db_getsession(('DB_instit')))->delete();
    }

    /**
     * @param $funcao
     * @param $instituicao
     * @throws Exception
     */

    public function removerParametroFuncao($funcao)
    {
        RhParametrosFundeb::where('rh284_funcao', $funcao)
            ->where('rh284_instituicao', db_getsession(('DB_instit')))->delete();
    }

    /**
     * @param $local
     * @param $instituicao
     * @throws Exception
     */
    public function removerParametroLocal($local)
    {
        RhParametrosFundeb::where('rh284_local_trabalho', $local)
            ->where('rh284_instituicao', db_getsession(('DB_instit')))->delete();
    }

    /**
     * @param $assentamento
     * @param $instituicao
     * @throws Exception
     */
    public function removerParametroAssentamento($assentamento)
    {
        RhParametrosFundeb::where('rh284_assentamento', $assentamento)
            ->where('rh284_instituicao', db_getsession(('DB_instit')))->delete();
    }

    /**
     * @param $rubrica
     * @param $instituicao
     * @throws Exception
     */
    public function removerParametroRubricaAbatimento($rubrica)
    {
        RhParametrosFundeb::where('rh284_rubrica_abatimento', $rubrica)
            ->where('rh284_instituicao', db_getsession(('DB_instit')))->delete();
    }

    /**
     * @param $id
     * @return RhCalculoFundeb
     */
    public function salvarValoresFundeb(CalculoFundebRequest $request)
    {
        $exists = RhCalculoFundeb::where('rh285_ano', $request->input('id_ano'))
                                ->where('rh285_mes', $request->input('id_mes'))
                                ->exists();

        if ($exists) {
            RhCalculoFundeb::where('rh285_ano', $request->input('id_ano'))
                            ->where('rh285_mes', $request->input('id_mes'))
                            ->delete();
        }

        $calculo = new RhCalculoFundeb();
        $calculo->rh285_ano   = strval($request->input('id_ano'));
        $calculo->rh285_mes   = strval($request->input('id_mes'));
        $calculo->rh285_valor = number_format($request->input('valor'), 2, '.', '');

        $calculo->save();

        return redirect()->back()->with('success', 'O Valor Fundeb foi atualizado com sucesso.');
    }

    /**
     * @param  ProcessamentoFundebRequest $request
     * @param  CalculoFundebRequest $calculo
     * @return float
     * @throws Exception
     */
    public function calcularCota(ProcessamentoFundebRequest $request, RhCalculoFundeb $calculo)
    {
        $quantidadeCotaDiretor    = RhCargosFundeb::retornaQuantidadeCargo(RhCargosFundeb::TIPO_DIRETOR);
        $quantidadeCotaSupervisor = RhCargosFundeb::retornaQuantidadeCargo(RhCargosFundeb::TIPO_SUPERVISOR);
        $quantidadeCotaProfessor  = RhCargosFundeb::retornaQuantidadeCargo(RhCargosFundeb::TIPO_PROFESSOR);
        $quantidadeCotaApoio      = RhCargosFundeb::retornaQuantidadeCargo(RhCargosFundeb::TIPO_APOIO);

        $quantidadeServidores = RhFuncao::quantidadeServidores(
            $request->ano,
            $request->mes
        );

        $quantidadeTotalDiretor    = 0;
        $quantidadeTotalSupervisor = 0;
        $quantidadeTotalProfessor  = 0;
        $quantidadeTotalApoio      = 0;

        foreach ($quantidadeServidores as $quantidadeServidorPorTipo) {
            switch ($quantidadeServidorPorTipo->tipo) {
                case RhCargosFundeb::TIPO_DIRETOR:
                    $quantidadeTotalDiretor +=
                        $quantidadeServidorPorTipo->total * $quantidadeCotaDiretor->rh283_quantidade;
                    break;
                case RhCargosFundeb::TIPO_SUPERVISOR:
                    $quantidadeTotalSupervisor +=
                        $quantidadeServidorPorTipo->total * $quantidadeCotaSupervisor->rh283_quantidade;
                    break;
                case RhCargosFundeb::TIPO_PROFESSOR:
                    $quantidadeTotalProfessor +=
                        $quantidadeServidorPorTipo->total * $quantidadeCotaProfessor->rh283_quantidade;
                    break;
                case RhCargosFundeb::TIPO_APOIO:
                    $quantidadeTotalApoio +=
                        $quantidadeServidorPorTipo->total * $quantidadeCotaApoio->rh283_quantidade;
                    break;
                default:
                    return response()->json(['error' => 'Quantidade tipo não configurado'], 404);
                    break;
            }
        }

        $quantidadeTotal = $quantidadeTotalDiretor
            + $quantidadeTotalSupervisor
            + $quantidadeTotalProfessor
            + $quantidadeTotalApoio;

        $valorPreenchidoSemAbatimento = $calculo->rh285_valor;

        $valorTotalAbatimento = RhProcessamentoFundeb::valorAbatimentoFundeb(
            $request->ano,
            $request->mes,
            $request->rubrica
        );
        
        $valorPreenchidoComAbatimento = $valorPreenchidoSemAbatimento - $valorTotalAbatimento;

        $valorPorCota = $valorPreenchidoComAbatimento / $quantidadeTotal;

        return $valorPorCota;
    }

    /**
     * @param ProcessamentoFundebRequest $request
     * @throws Exception
     */
    public function salvarProcessamentoFundeb(ProcessamentoFundebRequest $request)
    {
        $deletePontoSalario = PontoSalario::where('r10_anousu', $request->input('ano'))
                                            ->where('r10_mesusu', $request->input('mes'))
                                            ->where('r10_rubric', $request->input('rubrica'))
                                            ->get();

        foreach ($deletePontoSalario as $registro) {
                $registro->delete();
        }

        $sequenciais = RhProcessamentoFundeb::pluck('rh286_sequencial')->toArray();

        RhProcessamentoFundeb::whereIn('rh286_sequencial', $sequenciais)
                                ->where('rh286_ano', $request->input('ano'))
                                ->where('rh286_mes', $request->input('mes'))
                                ->delete();

        $calculo = RhCalculoFundeb::where('rh285_ano', $request->input('ano'))
                                ->where('rh285_mes', $request->input('mes'))
                                ->first();

        if (!$calculo) {
            return response()->json(['error' => 'Valores não configurados no parâmetro'], 404);
        }

        $cota = $this->calcularCota($request, $calculo);

        $servidores = RhProcessamentoFundeb::quantidadeServidorProcessar(
            $request->ano,
            $request->mes
        );

        foreach ($servidores as $servidor) {
            $processamento = new RhProcessamentoFundeb();
            $processamento->rh286_matricula      = $servidor->rh01_regist;
            $processamento->rh286_ano            = $request->input('ano');
            $processamento->rh286_mes            = $request->input('mes');
            $processamento->rh286_cargo          = $servidor->rh284_cargo;
            $processamento->rh286_funcao         = $servidor->rh284_funcao;
            $processamento->rh286_local_trabalho = $servidor->rh284_local_trabalho;
            $processamento->rh286_rubrica        = $request->input('rubrica');
            $processamento->rh286_valor          = $cota * $servidor->maior_quantidade;
            $processamento->rh286_instituicao    = $servidor->rh284_instituicao;
            $processamento->save();

            $pontofs = new PontoSalario();
            $pontofs->r10_anousu = $processamento->rh286_ano;
            $pontofs->r10_mesusu = $processamento->rh286_mes;
            $pontofs->r10_regist = $processamento->rh286_matricula;
            $pontofs->r10_rubric = $processamento->rh286_rubrica;
            $pontofs->r10_valor  = $processamento->rh286_valor;
            $pontofs->r10_quant  = '1';
            $pontofs->r10_lotac  = $servidor->rh02_lota;
            $pontofs->r10_datlim = '0';
            $pontofs->r10_instit = $processamento->rh286_instituicao;
            $pontofs->save(['forceInsert' => true]);
        }
    }
}
