<?php

namespace App\Domain\Tributario\Cadastro\Services;

use App\Domain\Tributario\Cadastro\Models\Bairro;
use App\Domain\Tributario\Cadastro\Repositories\IptubaseRepository;
use App\Domain\Tributario\Cadastro\Repositories\LocalidaderuralRepository;
use App\Domain\Tributario\Cadastro\Repositories\SetorregimovelRepository;
use App\Domain\Tributario\Cadastro\Repositories\IptuconstrRepository;
use App\Domain\Tributario\Cadastro\Traits\Logradouro;
use DBString;
use Illuminate\Support\Facades\DB;

final class CadastroService
{
    use Logradouro;

    public function getDadosRegImovByMatric($matricula, $matriculaAtiva = true)
    {
        $iptubaseRepository = new IptubaseRepository();

        $oDadosMatric = $iptubaseRepository->getDadosRegImovByMatric($matricula);

        if ($matriculaAtiva && !empty($oDadosMatric->j01_baixa)) {
            throw new \Exception("A matrícula {$matricula} está baixada.");
        }

        $nLados = ($oDadosMatric->j36_testad) ? ($oDadosMatric->j34_area / $oDadosMatric->j36_testad) : 0;

        return [
            "setorBairro" => $oDadosMatric->j34_setor,
            "logradouro" => $oDadosMatric->j14_nome,
            "numero" => $oDadosMatric->j39_numero,
            "quadra" => $oDadosMatric->j34_quadra,
            "complemento" => $oDadosMatric->j39_compl,
            "lote" => $oDadosMatric->j34_lote,
            "areaTotal" => $oDadosMatric->j34_area,
            "frente" => $oDadosMatric->j36_testad,
            "fundos" => $oDadosMatric->j36_testad,
            "ladoDireito" => round($nLados, 4),
            "ladoEsquerdo" => round($nLados, 4),
            "setorRi" => intval($oDadosMatric->j04_setorregimovel),
            "quadraRi" => $oDadosMatric->j04_quadraregimo,
            "loteRi" => $oDadosMatric->j04_loteregimo,
            "matriculaRi" => $oDadosMatric->j04_matricregimo,
            "proprietario" => $oDadosMatric->z01_nomecomple,
        ];
    }

    public function getSetorRegImoveis()
    {
        $setorregimovelRepository = new SetorregimovelRepository();
        $aSetor = $setorregimovelRepository->get()->toArray();

        return array_map(function ($aSetor) {
            return (object) [
                "codigo" => $aSetor["j69_sequencial"],
                "descricao" => $aSetor["j69_descr"]
            ];
        }, $aSetor);
    }

    public function getLocalidadeRural()
    {
        $localidaderuralRepository = new LocalidaderuralRepository();
        $aLocalidade = $localidaderuralRepository->getAll()->toArray();

        return array_map(function ($aLocalidade) {
            return [
                "codigo" => $aLocalidade["j137_sequencial"],
                "descricao" => $aLocalidade["j137_descricao"]
            ];
        }, $aLocalidade);
    }

    public function getBairros()
    {
        $aCampos = [
            "j13_codi as codigo",
            "j13_descr as descricao",
            "j13_codant as codigoAnterior",
            "j13_rural as rural"
        ];

        return Bairro::all($aCampos)->toArray();
    }

    public function getLogradouros($iCep = null, $iBairro = null)
    {
        $aCampos = [
            "j14_codigo as codigo_logradouro",
            \DB::raw("j88_descricao||' '||j14_nome as logradouro"),
            "j13_codi as codigo_bairro",
            "j13_descr as bairro",
            "j29_cep as cep"
        ];

        $oQuery = $this->getLogradouro();

        if (!empty($iCep)) {
            return $oQuery->where("j29_cep", $iCep)->first($aCampos)->toArray();
        }

        if (!empty($iBairro)) {
            $oQuery->where("j13_codi", "=", $iBairro);
        }

        return $oQuery->get($aCampos)->toArray();
    }

    /**
     * Metodo para buscar uma lista de imoveis de acordo com
     * os parametros fornecidos
     *
     * @param ImoveisRequest $request
     */
    public function getListaImoveis($request)
    {
        $iptubaseRepository = new IptubaseRepository();
        $oDadosMatric = $iptubaseRepository->getListaImoveis(
            strval($request->porPagina) ? strval($request->porPagina) : "",
            strval($request->matricula) ? strval($request->matricula) : "",
            strval($request->codCondominio) ? strval($request->codCondominio) : "",
            strval($request->codLoteamento) ? strval($request->codLoteamento) : "",
            strval($request->codLogradouro) ? strval($request->codLogradouro) : "",
            strval($request->nome) ? strval($request->nome) : "",
            strval($request->setor) ? strval($request->setor) : "",
            strval($request->quadra) ? strval($request->quadra) : "",
            strval($request->lote) ? strval($request->lote) : "",
            strval($request->setorLocalizacao) ? strval($request->setorLocalizacao) : "",
            strval($request->quadraLocalizacao) ? strval($request->quadraLocalizacao) : "",
            strval($request->loteLocalizacao) ? strval($request->loteLocalizacao) : "",
            strval($request->refAnterior) ? strval($request->refAnterior) : "",
            strval($request->registroCartografico) ? strval($request->registroCartografico) : "",
            $request->matriculasBaixadas
        );
        return $oDadosMatric;
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     */
    public function getLabelsListaImoveis()
    {
        $iptubaseRepository = new IptubaseRepository();
        $data = [];

        $nomeCampos = [
            "j01_matric",
            "j108_condominio",
            "j34_loteam",
            "j14_codigo",
            "z01_nome",
            "j34_setor",
            "j34_quadra",
            "j34_lote",
            "j05_codigoproprio",
            "j06_quadraloc",
            "j06_lote",
            "j40_refant",
            "j40_registrocartografico",
            "j14_nome",
            "j39_numero",
            "j39_compl",
            "j01_baixa",
        ];

        $nomeLabels = $iptubaseRepository->getLabelsListaImoveis($nomeCampos);

        if (count($nomeCampos) == count($nomeLabels)) {
            for ($index = 0; $index < count($nomeCampos); $index++) {
                $data[$nomeCampos[$index]] = ucfirst($nomeLabels[$index]["rotulo"]);
            }
        }

        return $data;
    }

    public function getEnderecoBaseCliente($cep, $logradouro = "")
    {
        require_once(ECIDADE_PATH . "std/DBString.php");
        $cep = DBString::somenteNumeros($cep);

        $query = DB::table('cadastro.ruascep')
            ->select('cadastro.ruas.j14_codigo as id', 'cadastro.ruas.j14_nome as descricao')
            ->distinct()
            ->join('cadastro.ruasbairro', 'cadastro.ruasbairro.j16_lograd', '=', 'cadastro.ruascep.j29_codigo')
            ->join('cadastro.ruas', 'cadastro.ruas.j14_codigo', '=', 'cadastro.ruasbairro.j16_lograd')
            ->join('cadastro.bairro', 'cadastro.bairro.j13_codi', '=', 'cadastro.ruasbairro.j16_bairro')
            ->where('cadastro.ruascep.j29_cep', $cep)
            ->where('cadastro.ruas.j14_nome', 'ilike', $logradouro . '%');

        return $query->paginate(15);
    }

    public function getBairroBaseCliente($id_rua)
    {
        $sql = "select distinct
        cadastro.bairro.j13_codi as id,
        cadastro.bairro.j13_descr as descricao
        from cadastro.ruascep
        inner join cadastro.ruasbairro on cadastro.ruasbairro.j16_lograd  = cadastro.ruascep.j29_codigo
        inner join cadastro.ruas on cadastro.ruas.j14_codigo = cadastro.ruasbairro.j16_lograd
        inner join cadastro.bairro on cadastro.bairro.j13_codi = cadastro.ruasbairro.j16_bairro
        where cadastro.ruas.j14_codigo = {$id_rua} limit 1;";

        return DB::select($sql);
    }

    public function getEscritoriosContabeis()
    {
        $sql = "
            SELECT
              z01_numcgm AS id,
              z01_nome AS descricao,
              z01_numcgm AS cgm,
              z01_nome AS nome,
              z01_cgccpf AS cnpj,
              z01_telef AS telefone,
              z01_email AS email
            FROM
              cadescrito
            INNER JOIN
              cgm ON q86_numcgm = z01_numcgm
            ORDER BY
              z01_nome
        ";
        return DB::select($sql);
    }

    public function getAtividadesPorTipo($tipo)
    {
        switch (strtoupper($tipo)) {
            case 'MEI':
                $atividades = $this->getAtividadesMei();
                break;

            case 'EMPRESA':
                $atividades = $this->getAtividadesEmpresa();
                break;

            case 'AUTONOMO':
                $atividades = $this->getAtividadesAutonomo();
                break;

            default:
                $atividades = [];
                break;
        }

        return $atividades;
    }

    public function getAtividadesMei()
    {
        $sql = "
            SELECT DISTINCT
                q03_ativ AS codigo,
                q71_estrutural || '-' || q03_ativ || '-' || q03_descr AS descricao,
                q71_estrutural AS estrutural_cnae,
                q71_descr AS descricao_cnae
            FROM ativid
            INNER JOIN atividcnae ON q03_ativ = q74_ativid
            INNER JOIN cnaeanalitica ON q72_sequencial = q74_cnaeanalitica
            INNER JOIN cnae ON q71_sequencial = q72_cnae
            WHERE
                (q71_permitemei IS true OR true IS NULL)
                AND (q03_limite IS NULL OR q03_limite >= CURRENT_DATE)
            ORDER BY q03_ativ
        ";
        return DB::select($sql);
    }

    public function getAtividadesAutonomo()
    {
        $sql = "
            SELECT DISTINCT
                q03_ativ AS codigo,
                rh70_estrutural || '-' || q03_ativ || '-' || q03_descr AS descricao,
                rh70_sequencial AS codigo_rhcbo,
                rh70_estrutural AS estrutural_cbo,
                q03_descr AS descricao_atividade
            FROM atividcbo
            INNER JOIN ativid ON q03_ativ = q75_ativid
            INNER JOIN rhcbo ON rh70_sequencial = q75_rhcbo
            WHERE
                (q03_limite IS NULL OR q03_limite >= CURRENT_DATE)
            ORDER BY 1
        ";
        return DB::select($sql);
    }

    public function getAtividadesEmpresa()
    {
        $sql = "
            SELECT DISTINCT
                q03_ativ AS codigo,
                q71_estrutural || '-' || q03_ativ || '-' || q03_descr AS descricao,
                q71_estrutural AS estrutural_cnae,
                q71_descr AS descricao_cnae
            FROM ativid
            INNER JOIN atividcnae ON q03_ativ = q74_ativid
            INNER JOIN cnaeanalitica ON q72_sequencial = q74_cnaeanalitica
            INNER JOIN cnae ON q71_sequencial = q72_cnae
            WHERE
            (q03_limite IS NULL OR q03_limite >= CURRENT_DATE)
            ORDER BY q03_ativ
        ";
        return DB::select($sql);
    }

    public function getIptuMatricula($matricula)
    {
        $sql = "
                    SELECT
            CASE
                WHEN iptuconstr.j39_matric IS NOT NULL THEN ruas_cep_construcao.j29_cep
                ELSE ruas_cep_lote.j29_cep
            END AS cep,
            proprietario AS proprietario,
            bairro_lote.j13_codi AS codigo_bairro,
            bairro_lote.j13_descr AS nome_bairro,
            CASE
                WHEN iptuconstr.j39_matric IS NOT NULL THEN rua_construcao.j14_codigo
                ELSE ruas_lote.j14_codigo
            END AS codigo_rua,
            CASE
                WHEN iptuconstr.j39_matric IS NOT NULL THEN rua_construcao.j14_nome
                ELSE ruas_lote.j14_nome
            END AS nome_rua,
            CASE
                WHEN iptuconstr.j39_matric IS NOT NULL THEN iptuconstr.j39_compl
                ELSE ''
            END AS complemento,
            CASE
                WHEN iptuconstr.j39_matric IS NOT NULL THEN iptuconstr.j39_numero
                ELSE j12_numero
            END AS numero,
            CASE
                WHEN iptuconstr.j39_matric IS NOT NULL THEN zona_construcao.j50_zona
                ELSE zona_Lote.j50_zona
            END AS codigo_zona,
            CASE
                WHEN iptuconstr.j39_matric IS NOT NULL THEN zona_construcao.j50_descr
                ELSE zona_lote.j50_descr
            END AS descricao_zona
        FROM
            iptubase
        LEFT JOIN iptuconstr ON iptubase.j01_matric = iptuconstr.j39_matric AND iptuconstr.j39_idprinc IS TRUE
        LEFT JOIN ruas AS rua_construcao ON rua_construcao.j14_codigo = iptuconstr.j39_codigo
        LEFT JOIN proprietario AS proprietario_construcao ON proprietario_construcao.j01_matric = iptubase.j01_matric
        LEFT JOIN zonas AS zona_construcao ON proprietario_construcao.j34_zona = zona_construcao.j50_zona
        LEFT JOIN ruascep AS ruas_cep_construcao ON ruas_cep_construcao.j29_codigo = rua_construcao.j14_codigo
        LEFT JOIN ruasbairro AS ruas_bairro_construcao ON ruas_bairro_construcao.j16_lograd = rua_construcao.j14_codigo
        LEFT JOIN bairro AS bairro_construcao ON bairro_construcao.j13_codi = ruas_bairro_construcao.j16_bairro
        INNER JOIN cadastro.lote ON iptubase.j01_idbql = j34_idbql
        INNER JOIN cadastro.testpri ON iptubase.j01_idbql = j49_idbql
        INNER JOIN cadastro.ruas AS ruas_lote ON j49_codigo = ruas_lote.j14_codigo
        LEFT JOIN cadastro.lotenumero ON j34_idbql = j12_idbql
        LEFT JOIN cadastro.ruascep AS ruas_cep_lote ON ruas_cep_lote.j29_codigo = ruas_lote.j14_codigo
        LEFT JOIN cadastro.zonas AS zona_Lote ON ruas_cep_lote.j29_codigo = ruas_lote.j14_codigo
        INNER JOIN cadastro.bairro AS bairro_lote ON lote.j34_bairro = bairro_lote.j13_codi
        WHERE
            iptubase.j01_matric = {$matricula}
        ORDER BY
            codigo_rua
        ";

        return DB::select($sql);
    }

    public function getZonas($termo = null)
    {
        if (empty($termo)) {
            $termo = '';
        }

        $termo = '%'.$termo.'%';

        $sql = "
            SELECT
                DISTINCT j50_zona AS id,
                j50_descr AS descricao
            FROM
                zonas
            WHERE j50_descr ILIKE ?
            ORDER BY
                descricao
        ";
        return DB::select($sql, [$termo]);
    }

    public function getByMatricIdCons($matricula, $idCons, $campos = ["*"])
    {
        $IptuconstrRepository = new IptuconstrRepository();

        return $IptuconstrRepository->getByMatricIdCons($matricula, $idCons, $campos);
    }

    public function getAllDtDemoNull($matricula, $campos = ["*"])
    {
        $IptuconstrRepository = new IptuconstrRepository();

        return $IptuconstrRepository->getAllDtDemoNull($matricula, $campos);
    }

    public function getMatriculaCondominios($dados)
    {
        $condominios = DB::table('condominio')
        ->select(
            'condominio.j107_sequencial as sequencial',
            'condominio.j107_nome as nome',
            'condominio.j107_tipo as tipo'
        )
        ->when($dados->sequencial, function ($query) use ($dados) {
            $query->whereRaw("CAST(condominio.j107_sequencial AS TEXT) like ?", ["%$dados->sequencial%"]);
        })
        ->when($dados->nome, function ($query) use ($dados) {
            $query->where('condominio.j107_nome', 'ilike', "%$dados->nome%");
        })
        ->orderBy('condominio.j107_sequencial')
        ->get();

        return $condominios;
    }

    public function getMatriculaLogradouros($dados)
    {
        $logradouros = DB::table('ruas')
        ->select(
            'ruas.j14_codigo as sequencial',
            'ruas.j14_nome as nome',
            'ruastipo.j88_sigla as sigla',
            'ruas.j14_tipo as tipo',
            'ruascep.j29_cep as cep'
        )
        ->leftJoin('ruastipo', 'j88_codigo', '=', 'j14_tipo')
        ->leftJoin('ruascep', 'j29_codigo', '=', 'j14_codigo')
        ->when($dados->sequencial, function ($query) use ($dados) {
            $query->whereRaw("CAST(ruas.j14_codigo AS TEXT) like ?", ["%$dados->sequencial%"]);
        })
        ->when($dados->nome, function ($query) use ($dados) {
            $query->where('ruas.j14_nome', 'ilike', "%$dados->nome%");
        })
        ->when($dados->cep, function ($query) use ($dados) {
            $query->whereRaw("CAST(ruascep.j29_cep AS TEXT) like ?", ["%$dados->cep%"]);
        })
        ->orderBy('ruas.j14_codigo')
        ->get();

        return $logradouros;
    }

    public function getMatriculaLoteamento($dados)
    {
        $loteamentos = DB::table('loteam')
        ->select(
            'loteam.j34_loteam as sequencial',
            'loteam.j34_descr as descricao',
            'lote.j34_setor as setor',
            'lote.j34_quadra as quadra',
            'lote.j34_lote as lote'
        )
        ->join('loteloteam', 'loteloteam.j34_loteam', '=', 'loteam.j34_loteam')
        ->join('lote', 'loteloteam.j34_idbql', '=', 'lote.j34_idbql')
        ->when($dados->sequencial, function ($query) use ($dados) {
            $query->whereRaw("CAST(loteam.j34_loteam AS TEXT) like ?", ["%$dados->sequencial%"]);
        })
        ->when($dados->descricao, function ($query) use ($dados) {
            $query->where('loteam.j34_descr', 'ilike', "%$dados->descricao%");
        })
        ->orderBy('loteam.j34_loteam')
        ->get();

        return $loteamentos;
    }
}
