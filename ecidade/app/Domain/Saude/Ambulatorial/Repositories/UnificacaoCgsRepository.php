<?php

namespace App\Domain\Saude\Ambulatorial\Repositories;

use App\Domain\Saude\Ambulatorial\Models\CgsUnidade;
use App\Domain\Saude\Ambulatorial\Models\CgsCorreto;
use App\Domain\Saude\Ambulatorial\Models\CgsErrado;
use App\Domain\Configuracao\Menu\Models\Permissao;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class UnificacaoCgsRepository
{
    protected $modelCgsCorreto;
    protected $modelCgsErrado;

    public function __construct()
    {
        $this->modelCgsCorreto = new CgsCorreto();
        $this->modelCgsErrado = new CgsErrado();
    }

    public function listarUnificacoes($dados)
    {
        $query = CgsCorreto::query();
        
        $queryFields = [
            DB::raw("distinct s127_i_codigo as codigo "),
            DB::raw("s127_i_numcgs as cgs "),
            DB::raw("cgscorreto.z01_v_nome as nome "),
            DB::raw("s127_data_proc as data_processamento"),
            DB::raw("s127_hora_proc as hora_processamento"),
            DB::raw("s127_login_proc as login_processamento"),
            DB::raw("
                case when s127_b_proc is true then 'Sim' 
                else 'Não' end as processado"),
            DB::raw("s127_i_instit as instituicao")
        ];

        if (array_key_exists('unificado', $dados)) {
            $query->Unificado($dados['unificado']);
        }

        if (array_key_exists('cgs_correto', $dados)) {
            $query->CgsCorreto($dados['cgs_correto']);
        }

        
        $totalQuery = clone $query;
        $totalRecords = $totalQuery->count();
        
     
        $pagedQuery = clone $query;
        $results = $pagedQuery->select($queryFields)
            ->join(DB::raw('cgs_und cgscorreto'), 'cgscorreto.z01_i_cgsund', 's127_i_numcgs')
            ->orderByDesc("s127_i_codigo")
            ->paginate($dados['rows']);
        
        return [
            'total' => $totalRecords,
            'data' => $results
        ];
    }

    public function detalharUnificacao($dados)
    {
        $query = CgsCorreto::query();

        $queryFields = [
            DB::raw("distinct s127_i_numcgs as cgs_correto "),
            DB::raw("cgscorreto.z01_v_nome as nome_correto"),
            DB::raw("cgscorreto.z01_v_cgccpf as cpf_correto"),
            DB::raw("'' as cns_correto"),
            DB::raw("cgscorreto.z01_d_nasc as data_nascimento_correto"),
            DB::raw("cgscorreto.z01_v_mae as nome_mae_correto"),
            DB::raw("'true' as situacao_correto"),
            DB::raw("cgscorreto.z01_v_ender as endereco_correto"),
            DB::raw("cgscorreto.z01_v_munic as municipio_correto"),
            DB::raw("cgserrado.z01_i_cgsund as cgs_errado"),
            DB::raw("cgserrado.z01_v_nome as nome_errado"),
            DB::raw("cgserrado.z01_v_cgccpf as cpf_errado"),
            DB::raw("'' as cns_errado"),
            DB::raw("cgserrado.z01_d_nasc as data_nascimento_errado"),
            DB::raw("cgserrado.z01_v_mae as nome_mae_errado"),
            DB::raw("'true' as situacao_errado"),
            DB::raw("cgserrado.z01_v_ender as endereco_errado"),
            DB::raw("cgserrado.z01_v_munic as municipio_errado")
        ];

        if (array_key_exists('unificado', $dados)) {
            $queryFields = [
                DB::raw("sau_cgscorreto.s127_i_codigo as codigo_unificacao"),
                DB::raw("sau_cgscorreto.s127_i_numcgs as cgs_correto"),
                DB::raw("cgscorreto.z01_v_nome as nome_correto"),
                DB::raw("sau_cgscorreto.s127_d_data as data_unificacao"),
                DB::raw("sau_cgscorreto.s127_c_hora as hora_unificacao"),
                DB::raw("sau_cgscorreto.s127_i_login as login_unificacao"),
                DB::raw("sau_cgscorreto.s127_b_proc as processamento_unificacao"),
                DB::raw("sau_cgscorreto.s127_data_proc as data_processamento"),
                DB::raw("sau_cgscorreto.s127_hora_proc as hora_processamento"),
                DB::raw("sau_cgscorreto.s127_login_proc as login_processamento"),
                DB::raw("sau_cgscorreto.s127_i_instit as instituicao_unificacao"),
                DB::raw("sau_cgserrado.s128_i_numcgs as cgs_errado"),
                DB::raw("sau_cgserrado.s128_v_nome as nome_errado")
            ];

            $query->select($queryFields)
            ->join(DB::raw('cgs_und cgscorreto'), 'cgscorreto.z01_i_cgsund', 's127_i_numcgs')
            ->join('sau_cgserrado', 's127_i_codigo', 's128_i_codigo');
        } else {
            $query->select($queryFields)
            ->join(DB::raw('cgs_und cgscorreto'), 'cgscorreto.z01_i_cgsund', 's127_i_numcgs')
            ->join('sau_cgserrado', 's127_i_codigo', 's128_i_codigo')
            ->join(DB::raw('cgs_und cgserrado'), 'cgserrado.z01_i_cgsund', 's128_i_numcgs');
        }

        $query->where('s127_i_codigo', $dados['codigo_unificacao']);
        $results = $query->get();
        return $results;
    }

    public function detalharCartoesUnificacao($dados)
    {
        

        $queryFields = [
           's115_i_cgs as cgs',
           's115_c_cartaosus as numero',
           DB::raw("
               case when s115_c_tipo = 'D' then 'Definitivo'
                  when  s115_c_tipo = 'P' then 'Provisório' end as tipo
           ")
        ];

        $query = CgsCorreto::select($queryFields)
        ->join(DB::raw('cgs_und cgscorreto'), 'cgscorreto.z01_i_cgsund', 's127_i_numcgs')
        ->join('cgs_cartaosus', 's115_i_cgs', 's127_i_numcgs')
        ->where('s127_i_codigo', $dados['codigo_unificacao']);

        $results = $query->get();
        return $results;
    }

    public function listarUnificacaoPorCgs($dados)
    {
        $campos = [
            's127_i_numcgs',
            's127_d_data',
            's127_c_hora',
            's127_i_login',
            's127_b_proc',
            's127_i_instit'
        ];

        $query = CgsCorreto::query();
        
        if (array_key_exists('unificado', $dados)) {
            $query->Unificado($dados['unificado']);
        }

        $query->select($campos);
        $query->where('s127_i_numcgs', $dados['cgs_correto']);

        $results = $query->get();
        
        return $results;
    }

    public function listarCgs($dados)
    {
        $query = CgsUnidade::query();

        if (array_key_exists('nome', $dados)) {
            $query->nome($dados['nome']);
        }
        if (array_key_exists('dataNascimento', $dados)) {
            $query->dataNascimento($dados['dataNascimento']);
        }
        if (array_key_exists('nomeMae', $dados)) {
            $query->nomeMae($dados['nomeMae']);
        }
        if (array_key_exists('cgs', $dados)) {
            $query->Cgs($dados['cgs']);
        }

        $queryFields = [
            'z01_i_cgsund as cgs',
            'z01_v_nome as nome',
            'z01_d_nasc as data_nascimento',
            'z01_v_cgccpf as cpf',
            'z01_v_ender as endereco',
            'z01_v_munic as municipio',
            'z01_v_mae as nome_mae',
            DB::raw("false as errado")
        ];
        $query->select($queryFields);
        $query->leftJoin('sau_cgscorreto', 's127_i_numcgs', 'z01_i_cgsund');
        $query->leftJoin('sau_cgserrado', 's128_i_numcgs', 'z01_i_cgsund');
        $query->whereRaw("
            case when s127_i_numcgs is not null or s128_i_numcgs is not null
                then z01_i_cgsund <> s127_i_numcgs and z01_i_cgsund <> s128_i_numcgs
            else 1 = 1 end
        ");
        $query->orderBy('z01_v_nome');
        $query->orderBy('z01_v_mae');
        $query->limit(150);
        $results = $query->get();
        return $results;
    }

    public function salvarCgsCorreto($dados)
    {
        
        if (count($this->listarUnificacaoPorCgs($dados)) > 0) {
            throw new Exception("Ja existe unificacao vinculada ao Cgs informado!");
        }

        $nextId = DB::select("SELECT nextval('sau_cgscorreto_codigo_seq')")[0]->nextval;

        $this->modelCgsCorreto->s127_i_codigo = $nextId;
        $this->modelCgsCorreto->s127_i_numcgs = $dados["cgs_correto"];
        $this->modelCgsCorreto->s127_d_data = Carbon::now()->toDateString();
        $this->modelCgsCorreto->s127_c_hora = Carbon::now()->format('H:i');
        $this->modelCgsCorreto->s127_i_login = db_getsession("DB_id_usuario");
        $this->modelCgsCorreto->s127_b_proc = false;
        $this->modelCgsCorreto->s127_i_instit = db_getsession("DB_instit");

        if ($this->modelCgsCorreto->save()) {
            $id = $this->modelCgsCorreto->s127_i_codigo;
            return $id;
        }
        return "";
    }

    public function salvarCgsErrado($dados, $codigoCgsCorreto)
    {
        $this->modelCgsErrado = new CgsErrado();
        $this->modelCgsErrado->s128_i_codigo = $codigoCgsCorreto;
        $this->modelCgsErrado->s128_i_numcgs = $dados["cgs_errado"];
        $this->modelCgsErrado->s128_v_nome = $dados["nome"];

        return $this->modelCgsErrado->save();
    }

    public function excluirCgsCorretoUnificacao($dados)
    {
        $cgsCorreto = CgsCorreto::find($dados["codigo_unificacao"]);
        if ($cgsCorreto) {
            $cgsCorreto->delete();
        }
        return $cgsCorreto;
    }
    
    public function excluirCgsErradoUnificacao($dados)
    {
        $cgsErrado = CgsErrado::where('s128_i_codigo', $dados["codigo_unificacao"]);
        if ($cgsErrado) {
            $cgsErrado->delete();
        }
        return $cgsErrado;
    }

    public function verificaPermissaoUsuario($dados)
    {
        $queryFields = ['id_item'];
        $query = Permissao::select($queryFields)
        ->where('id_item', $dados['id_item'])
        ->where('id_usuario', db_getsession('DB_id_usuario'))
        ->where('id_instit', db_getsession('DB_instit'))
        ->where('anousu', db_getsession('DB_anousu'));
        
        $results = $query->get();
        return $results;
    }
}
