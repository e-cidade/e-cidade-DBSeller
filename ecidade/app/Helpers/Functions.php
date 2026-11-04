<?php

use App\Domain\Configuracao\Departamento\Factories\DepartamentoFactory;
use App\Domain\Patrimonial\Protocolo\Factories\BuscaCgmLogadoFactory;
use ECidade\V3\Datasource\Database;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

if (!function_exists('valorCodigoBarras')) {
    /**
     * Função que formata o valor para ser adicionado ao codigo de barras
     * @param $fValor
     * @return float
     */
    function valorCodigoBarras($fValor)
    {
        $fValor = number_format($fValor, 2, "", ".");
        $fValor = str_pad($fValor, 11, "0", STR_PAD_LEFT);
        $fValor = str_replace('.', '', $fValor);
        return db_formatar($fValor, 's', '0', 11, 'e');
    }
}

if (!function_exists('convertToPdf')) {
    /**
     * Função que converte um arquivo (SXW, DOC, DOCX) para PDF
     * @param $sLocalArquivo
     * @param string $sCaminhoSaida
     * @return float
     */
    function convertToPdf($sLocalArquivo, $sCaminhoSaida = ".")
    {
        $command = "export HOME=/tmp && soffice --convert-to pdf ";
        $command .= $sLocalArquivo;
        $command .= " --headless --outdir ";
        $command .= $sCaminhoSaida;
        $command .= " -env:UserInstallation=file:///tmp";

        ob_start();
        system($command);
        ob_end_clean();
    }
}

if (!function_exists('formataValorMonetario')) {
    /**
     * Função que adiciona as casas decimais para valor monetário
     * @param $fValor
     * @return float
     */
    function formataValorMonetario($fValor, $decimais = 2)
    {
        if (!is_numeric($fValor)) {
            return 0;
        }

        return number_format(floatval($fValor), $decimais, ",", ".");
    }
}

if (!function_exists('parseStringJson')) {
    /**
     * Realiza o parse de uma string json para um array ou objeto stdClass
     * @param $stringJson
     * @return mixed|stdClass|array
     */
    function parseStringJson($stringJson)
    {
        $filtro = str_replace('\"', '"', $stringJson);
        return \JSON::create()->parse($filtro);
    }
}

if (!function_exists('validaDepartamentoLogado')) {
    /**
     * Valida o departamento logado de acordo com o tipo do departamento passado por parametro
     * @param $tipo
     * @return boolean
     */
    function validaDepartamentoLogado($tipo)
    {
        try {
            $validador = DepartamentoFactory::getValidador($tipo);
        } catch (\Exception $e) {
            db_redireciona("db_erros.php?fechar=true&db_erro={$e->getMessage()}");
            return false;
        }
        return $validador->validar();
    }
}

if (!function_exists('buscaCgmLogado')) {
    /**
     * Busca o cgm logado de acordo com o tipo passado por parametro
     * @param $tipo
     * @return int|string|void
     */
    function buscaCgmLogado($tipo)
    {
        try {
            $service = BuscaCgmLogadoFactory::getService($tipo);
        } catch (\Exception $e) {
            db_redireciona("db_erros.php?fechar=true&db_erro={$e->getMessage()}");
            return;
        }

        return $service->getCgm();
    }
}

if (!function_exists('validaRequest')) {
    /**
     * valida se o array de campos está conforme o array com as regras passados por parametro,
     * caso falhe toca a excessão com a devida mensagem
     * @param array $campos
     * @param array $regras
     * @param array $mensagens
     * @throws Exception
     */
    function validaRequest(array $campos, array $regras, array $mensagens = [])
    {
        $validator = Validator::make($campos, $regras, $mensagens);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $mensagem = $errors->all()[0];

            throw new Exception($mensagem, 406);
        }
    }
}

if (!function_exists('getPusherConfig')) {
    /**
     * Retorna um objeto com as váriaveis de configuração do pusher
     * @return object
     */
    function getPusherConfig()
    {
        return (object)[
            'enabled' => env('BROADCAST_DRIVER', 'log') === 'pusher',
            'appKey' => env('PUSHER_APP_KEY', 'app-id'),
            'host' => env('PUSHER_HOST', 'localhost'),
            'port' => env('PUSHER_PORT', 6001)
        ];
    }
}

if (!function_exists('array_remove')) {
    /**
     * Remove um item do array e retorna seu valor.
     *
     * @param array $arr The input array
     * @param $key The key pointing to the desired value
     * @return The value mapped to $key or null if none
     */
    function array_remove(array &$arr, $key)
    {
        if (array_key_exists($key, $arr)) {
            $val = $arr[$key];
            unset($arr[$key]);

            return $val;
        }

        return null;
    }
}

if (!function_exists('withNewTransactionAndRollbackOnError')) {
    /**
     * Abre uma nova transação para a closure
     * @return boolean
     * @throws Exception
     */
    function withNewTransactionAndRollbackOnError(Closure $closure, $options = null)
    {
        $hasError = false;

        $database = Database::getInstance(true);

        global $conn;
        $conn = $database->getConnection();

        $isLegacy = !isset($_SESSION[\ECidade\Lib\Session\DefaultSession::DB_REQUEST_FROM_API]);

        $db = Illuminate\Support\Facades\DB::class;

        if ($isLegacy) {
            $db = Illuminate\Database\Capsule\Manager::class;
        }

        $db::beginTransaction();
        $database->begin();

        try {
            $closure();

            $db::commit();
            $database->commit();
        } catch (\Exception $exception) {
            $db::rollback();

            if ($database->hasFailed() || $database->inTransation()) {
                $database->execute("ROLLBACK;");
            }

            if (isset($options["errorMessageLog"])) {
                $exceptionMessage = "{$exception->getMessage()} >> {$exception->getFile()} >> {$exception->getLine()}";

                if (isset($options["printStackTrace"])) {
                    $exceptionMessage .= "\n {$exception->getTraceAsString()}";
                }

                if ($isLegacy) {
                    dump($exceptionMessage);
                } else {
                    \Illuminate\Support\Facades\Log::error($options["errorMessageLog"] . " {$exceptionMessage}");
                }
            }

            if (isset($options["onError"])) {
                $options["onError"]($exception);
            }

            $hasError = true;
        }

        return $hasError;
    }
}

if (!function_exists('getBuilderRawQuery')) {
    /**
     * @param Builder $builder
     * @return string
     */
    function getBuilderRawQuery($builder)
    {
        $addSlashes = str_replace('?', "'?'", $builder->toSql());
        return vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
    }
}

if (!function_exists('toBoolean')) {
    /**
     * @param $value
     * @return boolean
     */
    function toBoolean($value)
    {
        if ($value === true) {
            return true;
        }

        if ($value === "true") {
            return true;
        }

        if ($value === 1) {
            return true;
        }

        if ($value === "1") {
            return true;
        }

        return false;
    }
}

if (!function_exists('DBQuery')) {
    /**
     * @param $value
     * @return Builder $builder
     */
    function DBQuery()
    {
        if (isset($_SESSION[\ECidade\Lib\Session\DefaultSession::DB_REQUEST_FROM_API])) {
            return Illuminate\Support\Facades\DB::query();
        }

        return Illuminate\Database\Capsule\Manager::query();
    }
}

if (!function_exists('formatCpfCnpj')) {
    function formatCpfCnpj($value)
    {
        $cpfCnpj = preg_replace("/\D/", "", $value);

        if (\DBString::isCPF($cpfCnpj)) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpfCnpj);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cpfCnpj);
    }
}

if (!function_exists('onlyNumbers')) {
    function onlyNumbers($value)
    {
        $value = trim($value);

        return preg_replace("/\D/", "", $value);
    }
}
if (!function_exists('debugQueryBuilder')) {
    /**
     * retorna o sql de um QueryBuilder COM os parÃ¢metros
     * @param $query (QueryBuilder)
     */
    function debugQueryBuilder($query)
    {
        $sql = str_replace(['?'], ['\'%s\''], $query->toSql());
        $sql = vsprintf($sql, $query->getBindings());

        die($sql);
    }
}

if (!function_exists('stripslashes_array')) {
    /**
     * aplica stripslahses nos elementos de um array
     * @param $query (QueryBuilder)
     */
    function stripslashes_array($value)
    {
        $value = is_array($value) ?
            array_map('stripslashes_array', $value) :
            stripslashes($value);

        return $value;
    }
}

if (!function_exists('validarCPFCNPJ')) {
    /**
     * Validar CPF
     *
     * @param string $value
     * @throws Exception
     */
    function validarCPFCNPJ($value)
    {
        $value = preg_replace('/[^0-9]/is', '', $value);

        if (strlen($value) === 11) {
            if (preg_match('/(\d)\1{10}/', $value)) {
                return false;
            }

            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $value[$c] * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($value[$c] != $d) {
                    return false;
                }
            }

            return true;
        } elseif (strlen($value) === 14) {
            if (preg_match('/(\d)\1{13}/', $value)) {
                return false;
            }

            for ($t = 12; $t < 14; $t++) {
                for ($d = 0, $m = ($t - 7), $i = 0; $i < $t; $i++) {
                    $d += $value[$i] * $m;
                    $m = ($m == 2 ? 9 : --$m);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($value[$i] != $d) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}


if (!function_exists('formatMatricula')) {
    /**
     * Formatar Matricula
     *
     * @param int|string $matricula
     *
     * @return string
     */
    function formatMatricula($matricula)
    {
        $matricula = str_split($matricula, strlen($matricula) - 1);

        return $matricula[0] . '-' . $matricula[1];
    }
}


if (!function_exists('formatarMetro')) {

    function formatarMetro($valor, $quadrado = true, $decimais = 2)
    {
        return utf8_encode(formataValorMonetario($valor, $decimais) . ($quadrado ? " m²" : " m"));
    }
}

if (!function_exists('w1250_to_utf8')) {
    /**
     * This function converts a given text from Windows 1250 encoding to UTF-8 encoding.
     * The text is passed as a parameter to the function.
     * The function uses an array of characters and their corresponding UTF-8 characters to map the given text.
     * The text is decoded using html_entity_decode() with UTF-8 as the character set.
     * The text is then converted to UTF-8 encoding using mb_convert_encoding() and strtr() to map the characters.
     *
     * @param string $text
     *
     * @return string $text
     */
    function w1250_to_utf8($text)
    {
        $map = array(
            "\x8A" => "\xA9",
            "\x8C" => "\xA6",
            "\x8D" => "\xAB",
            "\x8E" => "\xAE",
            "\x8F" => "\xAC",
            "\x9C" => "\xB6",
            "\x9D" => "\xBB",
            "\xA1" => "\xB7",
            "\xA5" => "\xA1",
            "\xBC" => "\xA5",
            "\x9F" => "\xBC",
            "\xB9" => "\xB1",
            "\x9A" => "\xB9",
            "\xBE" => "\xB5",
            "\x9E" => "\xBE",
            "\x80" => '&euro;',
            "\x82" => '&sbquo;',
            "\x84" => '&bdquo;',
            "\x85" => '&hellip;',
            "\x86" => '&dagger;',
            "\x87" => '&Dagger;',
            "\x89" => '&permil;',
            "\x8B" => '&lsaquo;',
            "\x91" => '&lsquo;',
            "\x92" => '&rsquo;',
            "\x93" => '&ldquo;',
            "\x94" => '&rdquo;',
            "\x95" => '&bull;',
            "\x96" => '&ndash;',
            "\x97" => '&mdash;',
            "\x99" => '&trade;',
            "\x9B" => '&rsquo;',
            "\xA6" => '&brvbar;',
            "\xA9" => '&copy;',
            "\xAB" => '&laquo;',
            "\xAE" => '&reg;',
            "\xB1" => '&plusmn;',
            "\xB5" => '&micro;',
            "\xB6" => '&para;',
            "\xB7" => '&middot;',
            "\xBB" => '&raquo;',
        );

        return html_entity_decode(
            mb_convert_encoding(strtr($text, $map), 'UTF-8', 'ISO-8859-1'),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

/**
 * Realiza o login
 */
if (!function_exists('logMemoryUsage')) {
    function logMemoryUsage($msg = null)
    {
        \Illuminate\Support\Facades\Log::info(sprintf(
            '%s. Memória %s MB  Pico %s MB',
            $msg,
            round((memory_get_usage() / 1024) / 1024, 2),
            round((memory_get_peak_usage() / 1024) / 1024, 2)
        ));
    }
}
