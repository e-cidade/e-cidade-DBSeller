<?php

namespace App\Domain\Patrimonial\Ouvidoria\Services;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Model\Mensageria;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Services\MensageriaService;
use ECidade\Patrimonial\Protocolo\Servicos\AndamentoProcessoService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessoEletronicoService
{
    /**
     * @throws \Exception
     */
    public static function validaAnexoBase64eAdicionaCaminhoTemporario(&$anexo, $usarNome = false)
    {
        if (empty($anexo["nome"])) {
            throw new \Exception("Nome do arquivo não informado!");
        }

        if (empty($anexo["conteudo"])) {
            throw new \Exception("Conteudo do arquivo {$anexo["nome"]} não informado!");
        }

        if (empty($anexo["type"])) {
            throw new \Exception("Type do arquivo {$anexo["nome"]} não informado!");
        }

        $conteudo = explode("base64,", $anexo["conteudo"]);
        $file = base64_decode($conteudo[count($conteudo) - 1]);

        if ($usarNome) {
            $caminhoarquivo = 'tmp/'.\DBString::removerCaracteresEspeciaisAcentos($anexo["nome"]);
        } else {
            $typesPartes = explode("/", $anexo["type"]);
            $PATH_TEMP_FILE = 'tmp/ouvidoriaatendimento' . date('dmY') . time() . ".";
            $caminhoarquivo = $PATH_TEMP_FILE . $typesPartes[count($typesPartes) - 1];
        }

        file_put_contents($caminhoarquivo, $file);
        if (!is_readable($caminhoarquivo)) {
            throw new \Exception("Arquivo {$anexo["nome"]} mal formado!");
        }
        $anexo["caminho"] = $caminhoarquivo;
    }
}
