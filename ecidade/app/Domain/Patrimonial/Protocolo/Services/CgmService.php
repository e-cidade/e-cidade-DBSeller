<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Repository\CgmRepository;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Helper\ProcessoEletronicoHelper;
use Illuminate\Support\Facades\DB;

class CgmService
{
    public function getByNumcgm($numcgm)
    {
        $cgmRepository = new CgmRepository();
        $oCgm = $cgmRepository->getByNumcgm($numcgm);

        if (empty($oCgm->z01_numcgm)) {
            throw new \Exception("Nenhum contribuinte cadastrado para este CGM");
        }

        if (substr(trim($oCgm->z01_cgccpf), 0, 7) == "0000000" || empty(trim($oCgm->z01_cgccpf))) {
            throw new \Exception("Contribuinte com o cadastro desatualizado, dirija-se a prefeitura.");
        }

        return [
            "numcgm" => $oCgm->z01_numcgm,
            "cpfCnpj" => $oCgm->z01_cgccpf,
            "nome" => $oCgm->z01_nome,
            "sexo" => (!empty($oCgm->z01_sexo) ? strtoupper($oCgm->z01_sexo) : ""),
            "endereco" => $oCgm->z01_ender,
            "numero" => $oCgm->z01_numero,
            "complemento" => $oCgm->z01_compl,
            "caixaPostal" => $oCgm->z01_cxpostal,
            "bairro" => $oCgm->z01_bairro,
            "municipio" => $oCgm->z01_munic,
            "uf" => $oCgm->z01_uf,
            "cep" => $oCgm->z01_cep,
            "email" => $oCgm->z01_email,
            "tipo" => $this->getTipoDeCadastro($oCgm->z01_cgccpf),
        ];
    }

    public function getCgmByCpfCnpj($cpfCnpj)
    {
        $cgmRepository = new CgmRepository();
        $aCgm = $cgmRepository->getByCpfCnpj($cpfCnpj)->toArray();

        if (count($aCgm) == 0) {
            $sForma = (strlen($cpfCnpj) == 11 ? "CPF" : "CNPJ");

            throw new \Exception("Nenhum contribuinte cadastrado para este {$sForma}");
        }

        return  $this->formataResposta($aCgm);
    }

    /**
     * Metodo para buscar registros a partir de um ou mais parametros:
     * cgm, nome, cgcpf ou email
     *
     * @param CgmParamsRequest $request
     */
    public function getByParams($request)
    {
        $porPagina = $request->porPagina ? $request->porPagina : 10;
        $cgm = $request->cgm ? $request->cgm : "";
        $nome = $request->nome ? $request->nome : "";
        $cgcpf = $request->cgcpf ? $request->cgcpf : "";
        $email = $request->email ? $request->email : "";

        $cgmRepository = new CgmRepository();
        $resultados = $cgmRepository->getByParams($cgm, $nome, $cgcpf, $email, $porPagina)->toArray();
        $resultados['data'] = $this->formataResposta($resultados['data']);

        return $resultados;
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     *
     * @return object
     */
    public function getRotulosPesquisaCgm()
    {
        $cgmRepository = new CgmRepository();
        $data = [];

        $nomeCampos = [
            'z01_numcgm',
            'z01_cgccpf',
            'z01_nome',
            'z01_sexo',
            'z01_ender',
            'z01_numero',
            'z01_compl',
            'z01_cxpostal',
            'z01_bairro',
            'z01_munic',
            'z01_uf',
            'z01_cep',
            'z01_email',
        ];

        $nomeLabels = $cgmRepository->getRotulosPesquisaCgm($nomeCampos);

        if (count($nomeCampos) == count($nomeLabels)) {
            for ($index = 0; $index < count($nomeCampos); $index++) {
                $data[$nomeCampos[$index]] = ucfirst($nomeLabels[$index]["rotulo"]);
            }
        }

        return $data;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function findOrCreateLegacy($cpfCnpj, $data)
    {
        $cgm = \CgmFactory::getInstanceByCnpjCpf($cpfCnpj);

        if ($cgm) {
            return $cgm;
        }

        $cgm = \CgmFactory::getInstanceByNumber($cpfCnpj);

        if (\DBString::isCPF($cpfCnpj)) {
            $cgm->setCpf($cpfCnpj);
        } else {
            $cgm->setCnpj($cpfCnpj);
        }

        $cgm->setNome(\DBString::upperCaseRemoveCaracteresEspeciais(substr($data->nome, 0, 40)));
        $cgm->setNomeCompleto(\DBString::upperCaseRemoveCaracteresEspeciais(
            substr($data->nomeCompleto, 0, 100)
        ));

        $oInstituicao = (object) ProcessoEletronicoHelper::getDadosMunicipio("munic, uf");

        $cgm->setUf(mb_strtoupper($oInstituicao->uf));
        $cgm->setMunicipio(mb_strtoupper($oInstituicao->munic));

        if (isset($data->cep)) {
            $cgm->setCep(onlyNumbers($data->cep));
        }

        if (isset($data->bairro)) {
            $cgm->setBairro(mb_strtoupper($data->bairro));
        }

        if (isset($data->numero)) {
            $cgm->setNumero(mb_strtoupper($data->numero));
        }

        if (isset($data->logradouro)) {
            $cgm->setLogradouro(mb_strtoupper($data->logradouro));
        }

        if (isset($data->complemento)) {
            $cgm->setComplemento(mb_strtoupper($data->complemento));
        }

        $cgm->save();

        return $cgm;
    }

    /**
     * Helper para formatar resposta da api
     *
     * @param array $arrayCgm
     */
    private function formataResposta($arrayCgm)
    {
        return array_map(function ($aCgm) {
            return [
                "numcgm" => $aCgm["z01_numcgm"],
                "cpfCnpj" => $aCgm["z01_cgccpf"],
                "nome" => $aCgm["z01_nome"],
                "sexo" => (!empty($aCgm->z01_sexo) ? strtoupper($aCgm->z01_sexo) : ""),
                "endereco" => $aCgm["z01_ender"],
                "numero" => $aCgm["z01_numero"],
                "complemento" => $aCgm["z01_compl"],
                "caixaPostal" => $aCgm["z01_cxpostal"],
                "bairro" => $aCgm["z01_bairro"],
                "municipio" => $aCgm["z01_munic"],
                "uf" => $aCgm["z01_uf"],
                "cep" => $aCgm["z01_cep"],
                "email" => $aCgm["z01_email"],
                "tipo" => $this->getTipoDeCadastro($aCgm["z01_cgccpf"]),
            ];
        }, $arrayCgm);
    }

    /**
     * Helper para mostrar o tipo de cadastro cgm:
     * FISICA ou JURIDICA
     *
     * @param string $z01_cgccpf
     */
    private function getTipoDeCadastro($z01_cgccpf)
    {
        if ($z01_cgccpf && trim($z01_cgccpf) != "") {
            if (strlen($z01_cgccpf) <= 11) {
                return "FISICA";
            } else {
                return "JURIDICA";
            }
        }
        return "";
    }

    public function getEnderecoLocalidadeCep($cep = '')
    {
        return DB::table('ceplocalidades')
            ->select(
                'cp06_cep as cep',
                'cp06_logradouro as descricao_rua',
                'cp06_codlocalidade as codigo_rua',
                'cp05_localidades as descricao_cidade',
                'cp05_codlocalidades as codigo_cidade',
                'cp03_sigla as sigla_estado',
                'cp03_estado as descricao_estado',
                'cp01_codbairro as codigo_bairro',
                'cp01_bairro as descricao_bairro',
                DB::raw("'1' as codigo_pais"),
                DB::raw("'BRASIL' as descricao_pais")
            )
            ->leftJoin('cepestados', 'cp05_sigla', '=', 'cp03_sigla')
            ->leftJoin('ceplogradouros', 'cp06_codlocalidade', '=', 'cp05_codlocalidades')
            ->leftJoin('cepbairros', function ($join) {
                $join->on('cp01_sigla', '=', 'cp03_sigla')
                    ->on('cp01_codbairro', '=', 'cp06_codbairroinicial');
            })
            ->where(function ($query) use ($cep) {
                $query->whereRaw("cp05_situacao = 'C' AND cp06_cep = ?", [$cep])
                    ->orWhereRaw("cp05_cepinicial = ?", [$cep]);
            })
            ->orderBy('cp05_sigla')
            ->orderBy('cp05_localidades')
            ->orderBy('cp06_logradouro')
            ->first();
    }

    public function saveCgm($parametros)
    {
        if (array_key_exists('cpfcnpj', $parametros)) {
            $parametros["cpfcnpj"] = str_replace(['.', '-', '/'], '', $parametros["cpfcnpj"]);
        }

        if (array_key_exists('cep', $parametros)) {
            $parametros["cep"] = str_replace('-', '', $parametros["cep"]);
        }

        if (array_key_exists('telefone', $parametros)) {
            $numero_telefone_formatado = str_replace(['(', ')', '-'], '', $parametros["telefone"]);
            $parametros["telefone"] =
                substr($numero_telefone_formatado, 0, 2) . ' ' . substr($numero_telefone_formatado, 2);
        }

        if (array_key_exists('celular', $parametros)) {
            $numero_celular_formatado = str_replace(['(', ')', '-'], '', $parametros["celular"]);
            $parametros["celular"] =
                substr($numero_celular_formatado, 0, 2) . ' ' . substr($numero_celular_formatado, 2);
        }

        if (array_key_exists('dataNascimento', $parametros)) {
            if (date_create($parametros["dataNascimento"])) {
                $data_objeto = date_create($parametros["dataNascimento"]);
                $parametros["dataNascimento"] = date_format($data_objeto, "Y-m-d");
            }
        }

        $result = Cgm::where('z01_cgccpf', '=', $parametros["cpfcnpj"])->get();

        if ($result->count() == 1) {
            $cgm = Cgm::find($result[0]->z01_numcgm);
            $cgm->setNome(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["nomeRazaoSocial"])
            ));
            $cgm->setNomeCompleto(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["nomeRazaoSocial"])
            ));
            $cgm->setLogradouro(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["logradouro"])
            ));
            $cgm->setNumero(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["numeroEndereco"])
            ));
            $cgm->setLogin($parametros["DB_id_usuario"]);
            $cgm->setComplemento(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["complemento"])
            ));
            $cgm->setBairro(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["bairro"])
            ));
            $cgm->setMunicipio(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["municipio"])
            ));
            $cgm->setUf(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["uf"])
            ));
            $cgm->setCep($parametros["cep"]);
            $cgm->setTelefone($parametros["telefone"]);
            $cgm->setCelular($parametros["celular"]);
            $cgm->setEmail($parametros["email"]);
            $cgm->setNascimento($parametros["dataNascimento"]);
            $cgm->setPai(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["nomePai"])
            ));
            $cgm->setMae(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["nomeMae"])
            ));
            if (isset($parametros["sexo"]['code'])) {
                $cgm->setSexo($parametros["sexo"]['code']);
            }

            if ($cgm->update()) {
                return new DBJsonResponse(['mensagem' => 'CGM atualizado com sucesso', 'erro' => false]);
            }

            return new DBJsonResponse(['mensagem' => 'Erro ao tentar editar CGM', 'erro' => true]);
        }

        if ($result->isEmpty()) {
            $ultimoId = Cgm::orderBy('z01_numcgm', 'desc')->first()->z01_numcgm;

            $cgm = new Cgm();
            $cgm->setId($ultimoId + 1);
            $cgm->setNome(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["nomeRazaoSocial"])
            ));
            $cgm->setNomeCompleto(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["nomeRazaoSocial"])
            ));
            $cgm->setLogradouro(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["logradouro"])
            ));
            $cgm->setNumero(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["numeroEndereco"])
            ));
            $cgm->setLogin($parametros["DB_id_usuario"]);
            $cgm->setComplemento(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["complemento"])
            ));
            $cgm->setBairro(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["bairro"])
            ));
            $cgm->setMunicipio(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["municipio"])
            ));
            $cgm->setUf(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["uf"])
            ));
            $cgm->setCep($parametros["cep"]);
            $cgm->setTelefone($parametros["telefone"]);
            $cgm->setCelular($parametros["celular"]);
            $cgm->setEmail($parametros["email"]);
            $cgm->setCgcCpf($parametros["cpfcnpj"]);
            $cgm->setNascimento($parametros["dataNascimento"]);
            $cgm->setPai(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["nomePai"])
            ));
            $cgm->setMae(preg_replace(
                '/\s+/',
                ' ',
                preg_replace('/[^A-Za-z0-9\s\-]/', '', $parametros["nomeMae"])
            ));
            if (isset($parametros["sexo"]['code'])) {
                $cgm->setSexo($parametros["sexo"]['code']);
            }
            $cgm->setHora(db_hora());
            $cgm->save();

            if ($cgm->save()) {
                return new DBJsonResponse(['mensagem' => 'CGM salvo com sucesso', 'erro' => false]);
            }

            return new DBJsonResponse(['mensagem' => 'Erro ao salvar CGM', 'erro' => true]);
        }

        return new DBJsonResponse(['mensagem' => 'Erro ao tentar inserir/editar CGM', 'erro' => true]);
    }

    public function verificaPermissaoCgm($parametros)
    {
        $resultados = DB::table('configuracoes.db_permissao')
            ->where(function ($query) {
                $query->whereIn('id_item', [1387, 289616, 8451]);
            })
            ->where('id_usuario', $parametros['DB_id_usuario'])
            ->where('anousu', $parametros['DB_anousu'])
            ->get();

        if ($resultados->count() > 1) {
            return new DBJsonResponse(['mensagem' => 'Possui permissão para adicionar CGM', 'erro' => false]);
        } else {
            $resultados = DB::table('configuracoes.db_usuarios')
                ->where('id_usuario', $parametros['DB_id_usuario'])
                ->get();

            if ($resultados[0]->administrador === 1) {
                return new DBJsonResponse(['mensagem' => 'Possui permissão para adicionar CGM', 'erro' => false]);
            } else {
                return new DBJsonResponse(['mensagem' => 'Não possui permissão para adicionar CGM', 'erro' => true]);
            }
        }
    }

    public function verificaCgm($cpfCnpj)
    {
        if (isset($cpfCnpj)) {
            $cpfCnpj = str_replace(['.', '-', '/'], '', $cpfCnpj);
        } else {
            return new DBJsonResponse(['acao' => 400, 'mensagem' => 'Cpf/Cnpj não definido', 'erro' => true]);
        }

        $result = Cgm::where('z01_cgccpf', '=', $cpfCnpj)->get();

        if ($result->count() > 1) {
            return new DBJsonResponse([
                'acao' => 100,
                'mensagem' => 'CPF/CNPJ vinculada há mais de um CGM,
                 entre em contato com a prefeitura para unificar o mesmo.',
                'erro' => true]);
        } elseif ($result->count() == 1) {
            return new DBJsonResponse(['acao' => 200, $result, 'erro' => false]);
        } else {
            return new DBJsonResponse(['acao' => 300, 'mensagem' => 'Não há esse cpf/cnpj na base', 'erro' => false]);
        }
    }
}
