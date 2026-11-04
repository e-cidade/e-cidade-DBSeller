<?php

namespace App\Domain\Tributario\Arrecadacao\Controller\RegraEmissao;

use App\Domain\Tributario\Arrecadacao\Requests\RegraEmissaoRequest;
use App\Domain\Tributario\Arrecadacao\Models\Modcarnepadraopixasso;
use App\Domain\Tributario\Arrecadacao\Models\Modcarnepadraopix;
use App\Domain\Tributario\Arrecadacao\Models\Modcarnepadrao;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class RegraEmissaoController extends Controller
{
    private $regraEmissaoRequest;

    public function __construct()
    {
        $this->regraEmissaoRequest = new RegraEmissaoRequest();
    }

    public function pegarDadosRegraEmissao(Request $request, $k48_sequencial)
    {
        try {
            $modcarne = Modcarnepadraopix::where([
                "k48_sequencial" => $k48_sequencial,
            ])->first();

            if (is_null($modcarne)) {
                $modcarne = Modcarnepadrao::where([
                    "k48_sequencial" => $k48_sequencial,
                ])->count();

                if ($modcarne <= 0) {
                    throw new Exception("Nenhuma regra de emissão referente ao k48_sequencial foi encontrada");
                }

                $modcarne = Modcarnepadraopix::create([
                    "k48_sequencial" => $k48_sequencial,
                    "k48_ammpix" => false,
                ]);

                $modcarne = Modcarnepadraopix::latest()->first();
            }

            $bancos = [];
            $bancosAsso = $modcarne->modcarnepadraopixasso()->get();

            foreach ($bancosAsso as $asso) {
                $banco = $asso->getBanco();
                $banco->dadosBanco();

                array_push($bancos, $banco);
            }

            $modcarne->bancos = $bancos;

            return response()->json([
                "status" => "Success",
                "message" => $modcarne,
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "Error",
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function salvar(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                $this->regraEmissaoRequest->rules()
            );

            if ($validation->fails()) {
                return response()->json([
                    "status" => "Error",
                    "message" => $validation->errors(),
                ], 422);
            }

            $data = $this->pegarDados($request);
            $modcarne = Modcarnepadraopix::create($data);

            if ($modcarne) {
                $this->mountAssocBan($request->all(), $modcarne);

                return response()->json([
                    "status" => "Success",
                    "message" => "Regra de emissão pix salva com sucesso",
                ]);
            }

            throw new Exception("Ocorreu um erro na tentativa de atualizar a regra de emissão");
        } catch (Exception $e) {
            return response()->json([
                "status" => "Error",
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function excluir(Request $request, $k48_sequencial)
    {
        try {
            $modcarne = Modcarnepadraopix::where([
                "k48_sequencial" => $k48_sequencial,
            ])->first();

            if (is_null($modcarne)) {
                throw new Exception(
                    "Nenhuma regra de emissão referente ao k48_sequencial foi encontrada"
                );
            }

            $modcarne->delete();

            return response()->json([
                "status" => "Success",
                "message" => "Regra de emissão excluida com sucesso",
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "Error",
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function atualizar(Request $request, $k48_sequencial)
    {
        try {
            $modcarne = Modcarnepadraopix::where([
                "k48_sequencial" => $k48_sequencial,
            ])->first();

            if (is_null($modcarne)) {
                throw new Exception("Nenhuma regra de emissão referente regra de emissão foi encontrada");
            }

            $validation = Validator::make(
                $request->all(),
                $this->regraEmissaoRequest->rules($k48_sequencial)
            );

            if ($validation->fails()) {
                return response()->json([
                    "status" => "Error",
                    "message" => $validation->errors(),
                ], 422);
            }

            $data = $request->all();

            $this->mountAssocBan($data, $modcarne);

            $data = $this->pegarDados($request);
            $data["k48_sequencial"] = $k48_sequencial;

            if ($modcarne->update($data)) {
                return response()->json([
                    "status" => "Success",
                    "message" => "Regra de emissão pix atualizada com sucesso",
                ]);
            }

            throw new Exception("Ocorreu um erro na tentativa de atualizar a regra de emissão");
        } catch (Exception $e) {
            return response()->json([
                "status" => "Error",
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Atualiza as associações de regra de emissão e dados bancários pix
     *
     * @param array $data
     * @param Arretipopix $arretipopix
     *
     * @return void
     */
    public function mountAssocBan(&$data, Modcarnepadraopix &$modcarnepadraopix)
    {
        if (isset($data["codbank"])) {
            if (is_array($data["codbank"]) and count($data["codbank"]) > 0) {
                $deletar = $modcarnepadraopix->modcarnepadraopixasso()->get();

                foreach ($deletar as $modcarnepadraopixasso) {
                    $index = array_search(
                        $modcarnepadraopixasso->db90_codban,
                        $data["codbank"]
                    );

                    if ($index !== false) {
                        unset($data["codbank"][$index]);
                    } else {
                        $modcarnepadraopixasso->delete();
                    }
                }

                foreach ($data["codbank"] as $db90_codban) {
                    Modcarnepadraopixasso::create([
                        "db90_codban" => $db90_codban,
                        "k48_sequencial" => $modcarnepadraopix->k48_sequencial,
                    ]);
                }
            }

            unset($data["codbank"]);

            return;
        }

        $deletar = $modcarnepadraopix->modcarnepadraopixasso()->get();

        foreach ($deletar as $modcarnepadraopixasso) {
            $modcarnepadraopixasso->delete();
        }
    }

    /**
     * Retorna uma lista de valores
     *
     * @return array
     */
    private function pegarDados(Request &$request)
    {
        $data = request(array_keys($this->regraEmissaoRequest->rules()));

        if (!isset($data["k48_ammpix"])) {
            $data["k48_ammpix"] = false;
        }

        if (isset($data["codbank"])) {
            unset($data["codbank"]);
        }

        if (isset($data["codbank.*"])) {
            unset($data["codbank.*"]);
        }

        return $data;
    }
}
