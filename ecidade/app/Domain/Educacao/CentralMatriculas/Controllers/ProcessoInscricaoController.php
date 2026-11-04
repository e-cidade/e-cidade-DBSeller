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

namespace App\Domain\Educacao\CentralMatriculas\Controllers;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Helpers\CentralMatriculasHelper;
use App\Domain\Educacao\CentralMatriculas\Services\ProcessoInscricaoService;
use App\Domain\Educacao\Escola\Models\Aluno;
use App\Domain\Educacao\Escola\Models\CensoEstado;
use App\Domain\Educacao\Escola\Models\CensoMunicipio;
use App\Domain\Educacao\Escola\Models\CensoOrgaoEmissorRG;
use \App\Domain\Educacao\CentralMatriculas\Models\Escola;
use App\Domain\Educacao\Escola\Models\NecessidadeEspecial;
use App\Domain\Educacao\Escola\Models\Pais;
use App\Domain\Educacao\Escola\Resources\NecessidadeEspecialResource;
use App\Domain\Educacao\MatriculaOnline\Models\Candidato;
use App\Domain\Educacao\MatriculaOnline\Models\Fase;
use App\Domain\Educacao\MatriculaOnline\Models\RedeOrigem;
use App\Domain\Educacao\MatriculaOnline\Models\RendaFamiliar;
use App\Domain\Educacao\MatriculaOnline\Resources\FaseResource;
use App\Domain\Educacao\MatriculaOnline\Resources\RedeOrigemResource;
use App\Domain\Educacao\Secretaria\Models\ZonaResidencia;
use App\Domain\Tributario\Cadastro\Models\Bairro;
use App\Domain\Tributario\Cadastro\Models\TipoRua;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessoInscricaoController extends Controller
{
    protected $service;

    public function __construct(ProcessoInscricaoService $service)
    {
        $this->service = $service;
    }

    public function getEtapasFasePorData($fase, $data)
    {
        return new DBJsonResponse($this->service->getEtapasFasePorData($fase, $data));
    }

    public function temFaseAberta()
    {
        $fases = Fase::where('mo04_encerrada', false)->get()->filter(function ($fase) {
            $dataI = explode("-", $fase->mo04_dtini->format('Y-m-d'));
            $horaI = explode(":", $fase->mo04_hora_inicial);
            $dataF = explode("-", $fase->mo04_dtfim->format('Y-m-d'));
            $horaF = explode(":", $fase->mo04_hora_final);
            $dataI = Carbon::create(
                $dataI[0],
                $dataI[1],
                $dataI[2],
                $horaI[0],
                $horaI[1],
                $horaI[2],
                'America/Sao_Paulo'
            );
            $dataF = Carbon::create(
                $dataF[0],
                $dataF[1],
                $dataF[2],
                $horaF[0],
                $horaF[1],
                $horaF[2],
                'America/Sao_Paulo'
            );
            if (Carbon::now()->between($dataI, $dataF)) {
                return $fase;
            }
        });
        return new DBJsonResponse($fases->count() > 0);
    }

    public function getRedesOrigem()
    {
        $redes = RedeOrigem::all()->map(function ($rede) {
            return RedeOrigemResource::toResponse($rede);
        });
        return new DBJsonResponse($redes);
    }

    public function getZonasResidencia()
    {
        $zonas = ZonaResidencia::all()->map(function ($zona) {
            return (object) [
                'codigo' => $zona->ed194_id,
                'nome' => $zona->ed194_descriicao
            ];
        });
        return new DBJsonResponse($zonas);
    }

    public function getFasePorData($data)
    {
        $etapas = $this->service->getFasePorData($data);
        return new DBJsonResponse($etapas);
    }

    public function getFasePorDataInterno($data)
    {
        $etapas = $this->service->getFasePorDataInterno($data);
        return new DBJsonResponse($etapas);
    }

    public function getEscolasOrigem()
    {
        $escolas = Escola::all()->map(function ($escola) {
            return (object) ['codigo' => $escola->mo53_codigo, 'nome'=> $escola->mo53_nome];
        });
        return new DBJsonResponse($escolas);
    }

    public function getNecessidadesEspeciais()
    {
        $necessidades = NecessidadeEspecial::all()->map(function ($necessidade) {
            return NecessidadeEspecialResource::toResponse($necessidade);
        });

        return new DBJsonResponse($necessidades);
    }

    public function getBairros()
    {
        $dep = DBConfig::find(1);

        $bairros = Bairro::orderBy('j13_descr')->get()->map(function ($bairro) use ($dep) {
            return (object) [
                'codigo' => $bairro->j13_codi,
                'nome' => $bairro->j13_descr,
                'uf' => $dep->uf,
                'municipio' => $dep->munic
            ];
        });

        return new DBJsonResponse($bairros);
    }


    public function getProfissoes()
    {
        return new DBJsonResponse($this->service->getProfissoes());
    }

    public function getTiposRuas()
    {
        $tipos = TipoRua::all()->map(function ($tipo) {
            return (object) [
                'codigo' => $tipo->j88_codigo,
                'nome' => $tipo->j88_descricao
            ];
        });

        return new DBJsonResponse($tipos);
    }

    public function getEstados()
    {
        $estados = CensoEstado::orderBy('ed260_c_nome')->get()->map(function ($estado) {
            return (object) [
                'codigo' => $estado->ed260_i_codigo,
                'nome' => trim($estado->ed260_c_nome)
            ];
        });
        return new DBJsonResponse($estados);
    }

    public function getPaises()
    {
        $paises = Pais::orderBy('ed228_c_descr')->get()->map(function ($pais) {
            return (object) [
                'codigo' => $pais->ed228_i_codigo,
                'nome' => trim($pais->ed228_c_descr)
            ];
        });
        return new DBJsonResponse($paises);
    }

    public function getMunicipios($estado)
    {
        $municipios = CensoMunicipio::where('ed261_i_censouf', $estado)->get()->map(function ($municipio) {
            return (object) [
                'codigo' => $municipio->ed261_i_codigo,
                'nome' => trim($municipio->ed261_c_nome)
            ];
        });
        return new DBJsonResponse($municipios);
    }

    public function getFaixasRenda()
    {
        $faixas = RendaFamiliar::all()->map(function ($faixa) {
            return (object) [
                'codigo' => $faixa->mo25_id,
                'faixa' => trim($faixa->mo25_faixa)
            ];
        });
        return new DBJsonResponse($faixas);
    }

    public function getAlunoByCpf($cpf)
    {
        $aluno = Aluno::whereHas('matriculas', function ($matricula) {
            $matricula->where('ed60_c_situacao', 'MATRICULADO')->where('ed60_c_concluida', 'N');
        })->where('ed47_v_cpf', $cpf)->get()->map(function ($aluno) {
            return (object) [
                'nome' => trim($aluno->ed47_v_nome)
            ];
        });
        return  $aluno->count() === 0 ? new DBJsonResponse(null) : new DBJsonResponse($aluno->first());
    }

    public function getAlunoEscolaByCpf($escola, $cpf)
    {
        $dados = DB::select("
            select
                ed47_v_nome as nome
            from
                aluno
                inner join matricula on ed60_i_aluno = ed47_i_codigo
                inner join turma on ed60_i_turma = ed57_i_codigo
                left join escola.escola on ed18_i_codigo = ed57_i_escola
                left join plugins.escolas on mo53_escola = ed18_i_codigo
            where
                ed60_c_concluida = 'N'
                and (ed57_i_escola = {$escola} or plugins.escolas.mo53_codigo = {$escola} )
                and ed47_v_cpf = '{$cpf}'
            limit 1;
        ");

        foreach ($dados as $dado) {
            return new DBJsonResponse($dado);
        }
        return new DBJsonResponse(null);
    }

    public function getAlunoByVisto($visto)
    {
        $aluno = Aluno::whereHas('matriculas', function ($matricula) {
            $matricula->where('ed60_c_situacao', 'MATRICULADO')->where('ed60_c_concluida', 'N');
        })->where('ed47_visto', $visto)->get()->map(function ($aluno) {
            return (object) [
                'nome' => trim($aluno->ed47_v_nome)
            ];
        });
        return  $aluno->count() === 0 ? new DBJsonResponse(null) : new DBJsonResponse($aluno->first());
    }

    public function getAlunoByRne($rne)
    {
        $aluno = Aluno::whereHas('matriculas', function ($matricula) {
            $matricula->where('ed60_c_situacao', 'MATRICULADO')->where('ed60_c_concluida', 'N');
        })->where('ed47_rnm', $rne)->get()->map(function ($aluno) {
            return (object) [
                'nome' => trim($aluno->ed47_v_nome)
            ];
        });
        return  $aluno->count() === 0 ? new DBJsonResponse(null) : new DBJsonResponse($aluno->first());
    }
    public function getCandidatoByCpf($fase, $cpf)
    {
        $cand = Candidato::where('mo01_cpf', $cpf)->get()->filter(function ($candidato) use ($fase) {
            $naFase = $candidato->candidaturas()->where('mo12_fase', $fase)->where('mo12_status', true)->get();
            return $naFase->count() > 0;
        })->map(function ($candidato) {
            return (object) [
                'nome' => trim($candidato->mo01_nome)
            ];
        });

        return  $cand->count() === 0 ? new DBJsonResponse(null) : new DBJsonResponse($cand->first());
    }

    public function getCandidatoByVisto($fase, $visto)
    {
        $cand = Candidato::where('mo01_visto', $visto)->get()->filter(function ($candidato) use ($fase) {
            $naFase = $candidato->candidaturas()->where('mo12_fase', $fase)->where('mo12_status', true)->get();
            return $naFase->count() > 0;
        })->map(function ($candidato) {
            return (object) [
                'nome' => trim($candidato->mo01_nome)
            ];
        });

        return  $cand->count() === 0 ? new DBJsonResponse(null) : new DBJsonResponse($cand->first());
    }

    public function getCandidatoByRne($fase, $rne)
    {
        $cand = Candidato::where('mo01_rnm', $rne)->get()->filter(function ($candidato) use ($fase) {
            $naFase = $candidato->candidaturas()->where('mo12_fase', $fase)->where('mo12_status', true)->get();
            return $naFase->count() > 0;
        })->map(function ($candidato) {
            return (object) [
                'nome' => trim($candidato->mo01_nome)
            ];
        });

        return  $cand->count() === 0 ? new DBJsonResponse(null) : new DBJsonResponse($cand->first());
    }

    public function getDadosPrefeitura()
    {
        $pref =  DBConfig::where('codigo', 1)->get()->map(function ($prefeitura) {
            $rua = trim($prefeitura->ender);
            $cidade = trim($prefeitura->munic);
            return (object) [
                'nome' => $prefeitura->nomeinst,
                'endereco' => "{$rua}, {$prefeitura->numero} - {$cidade} - {$prefeitura->uf}",
                'telefone' => $prefeitura->telef
            ];
        })->first();
        return new DBJsonResponse($pref);
    }

    public function getOrgaosEmissores()
    {
        $orgaos = CensoOrgaoEmissorRG::all()->map(function ($orgao) {
            return (object) [
                "label" => trim($orgao->ed132_c_descr),
                "value" => trim($orgao->ed132_i_codigo)
            ];
        });

        return new DBJsonResponse($orgaos);
    }

    public function getCorRaca()
    {
        $centralMatriculas = new CentralMatriculasHelper();
        $response = $centralMatriculas->get($centralMatriculas->getApiCentral().'/processamento/cor-raca');
        $response = (object) json_decode($response->getBody(), true);
        return new DBJsonResponse($response->data);
    }
}
