<?php

namespace App\Domain\Configuracao\Banco\Controller;

use App\Domain\Configuracao\Banco\Models\DBBancos;
use Exception;

use App\Http\Controllers\Controller;
use App\Domain\Configuracao\Banco\Models\DBBancosPix;
use App\Domain\Configuracao\Banco\Requests\BancoPixRequest;
use App\Domain\Tributario\Arrecadacao\Models\Arretipopixasso;
use App\Domain\Tributario\Arrecadacao\Models\Modcarnepadraopixasso;

use ECidade\Tributario\Caixa\Entity\RegraEmissao;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BancoPixController extends Controller
{
    private $rules;

    public function __construct()
    {
        $this->rules = new BancoPixRequest();
    }

    public function deletar($db90_codban)
    {
        try {
            $DBBancosPix = DBBancosPix::where([
                "db90_codban" => $db90_codban
            ])->first();

            if (!$DBBancosPix) {
                throw new Exception("Nenhum dado bancário pix foi encontrado");
            }

            $regras = Modcarnepadraopixasso::where([
                "db90_codban" => $db90_codban
            ])->count();

            if ($regras > 0) {
                throw new Exception(
                    "Para excluir esses dados bancários será necessário " .
                        "remover todas as regra de emissão relacionadas com ele"
                );
            }

            $tipoDebito = Arretipopixasso::where([
                "db90_codban" => $db90_codban
            ])->count();

            if ($tipoDebito > 0) {
                throw new Exception(
                    "Para excluir esses dados bancários será necessário remover " .
                        "todas os tipo de dÃ©bitos relacionadas com ele"
                );
            }

            if ($DBBancosPix->arretipopixasso->count() > 0) {
                throw new Exception(
                    "Para excluir os dados bancários Pix Ã© necessario remover esse banco " .
                        "dos tipos de dÃ©tidos e configuração de emissão com pix"
                );
            }

            if ($DBBancosPix->delete()) {
                return response()->json([
                    "status"  => "Success",
                    "message" => "Dados bancários Pix excluidos com sucesso"
                ]);
            }

            throw new Exception("Ocorreu um erro na tentativa de deletar os dados bancários");
        } catch (Exception $e) {
            return response()->json([
                "status"  => "Error",
                "message" => $e->getMessage()
            ], 400);
        }
    }

    public function pegarBancoPix(Request $request, $db90_codban)
    {
        try {
            $DBBancosPix = DBBancosPix::where([
                "db90_codban" => $db90_codban
            ])->first();

            if (is_null($DBBancosPix)) {
                $find_banco = (bool) DBBancos::where([
                    "db90_codban" => $db90_codban
                ])->count();

                if ($find_banco <= 0) {
                    throw new Exception(
                        "Nenhum banco com esse código foi encontrado"
                    );
                }

                $DBBancosPix = DBBancosPix::create([
                    "db90_codban" => $db90_codban
                ]);

                $DBBancosPix = DBBancosPix::latest()->first();
            }

            return response()->json([
                "status"  => "Success",
                "message" => $DBBancosPix
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status"  => "Error",
                "message" => $e->getMessage()
            ], 400);
        }
    }

    public function salvar(Request $request)
    {
        try {
            $validationData = $this->validationData($request);

            if ($validationData->getStatusCode() !== 200) {
                return $validationData;
            }

            $data = $this->mountData($request);

            if (DBBancosPix::create($data)) {
                return response()->json([
                    "status"  => "Success",
                    "message" => "Dados bancários pix salvos com sucesso"
                ]);
            }

            throw new Exception("Ocorreu um falha na tentativa de salvar os dados bancários pix");
        } catch (Exception $e) {
            return response()->json([
                "status"  => "Error",
                "message" => $e->getMessage()
            ], 400);
        }
    }

    public function atualizar(BancoPixRequest $request, $d90_codban = null)
    {
        try {
            $bancoPix = DBBancosPix::where("db90_codban", $d90_codban)->first();

            if (is_null($bancoPix)) {
                throw new Exception(
                    "Nenhuma configuração do banco pix " .
                        "referente ao d90_codban foi encontrado"
                );
            }

            $data = $this->mountData($request);

            $data['db90_tipo_ambiente'] = trim(strval($data['db90_tipo_ambiente'])) != '' ?
                $data['db90_tipo_ambiente'] :
                null;

            if ($bancoPix->update($data)) {
                return response()->json([
                    "status"  => mb_convert_encoding(
                        'Success',
                        'UTF-8',
                        'ISO-8859-1'
                    ),
                    "message" => mb_convert_encoding(
                        "Dados bancários pix atualizados com sucesso",
                        'UTF-8',
                        'ISO-8859-1'
                    )
                ]);
            }

            throw new Exception("Ocorreu um falha na tentativa de atualizar os dados bancários pix");
        } catch (Exception $e) {
            return response()->json([
                "status"  => "Error",
                "message" => $e->getMessage()
            ], 400);
        }
    }

    public function listar(Request $request)
    {
        try {
            $banco_pix = new DBBancosPix();
            $listaBancos = DBBancosPix::all();
            $fillables   = $banco_pix->getFillable();

            foreach ($listaBancos as $index => $banco) {
                foreach ($fillables as $campo) {
                    if (!is_null($banco->{$campo}) and
                        strlen(trim($banco->{$campo})) > 0
                        || in_array($campo, ['db90_cnpj_municipio', 'db90_cnpj', 'db90_tipo_ambiente'])) {
                        continue;
                    }
                    unset($listaBancos[$index]);
                    break;
                }
            }

            foreach ($listaBancos as $banco) {
                $banco->dadosBanco();
            }

            return response()->json([
                "status"  => "Success",
                "message" => $listaBancos
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status"  => "Error",
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * Validação dos dados para cadastrar ou atualizar dados bancários pix
     *
     * @param Request $request
     * @param int     $codtipopix Defailt null
     *
     * @return Response|ResponseFactory
     */
    public function validationData(Request $request, $d90_codban = null)
    {
        $validation = Validator::make(
            $request->all(),
            $this->rules->rules($d90_codban)
        );

        if ($validation->fails()) {
            return response()->json([
                "status"  => "Error",
                "message" => $validation->errors()
            ], 422);
        }

        return response()->json([
            "status"   => "Success",
            "messagem" => "Os dados são validos para " .
                ((!is_null($d90_codban)) ? "inclusão" : "atualização") .
                " dos dados bancários API PIX."
        ]);
    }

    /**
     * Retorna uma lista com todos os dados utilizados na rules
     *
     * @param Request $request
     *
     * @return array;
     */
    private function mountData(Request &$request)
    {
        $data = request(array_keys($this->rules->rules()));

        return $data;
    }
}
