<?php

namespace App\Domain\Tributario\ISSQN\Services\SimplesNacional;

class ConsultaApiSimplesNacionalService
{
    /**
     * Metodo para buscar registros de uma lista de cnpjs
     * em uma api externa
     *
     * @param array $arrayCnpjs
     */
    public function getDadosCadastros($arrayCnpjs)
    {
        $dadosConsultaApi = [];
        $registros = [];

        foreach ($arrayCnpjs as $cnpj) {
            $dadosConsultaApi[] = $this->getDadosCnpj($cnpj);
        }

        for ($index = 0; $index < count($dadosConsultaApi); $index++) {
            $dados = $dadosConsultaApi[$index];

            $dadosDefinidos = $dados && $dados != null;

            if ($dadosDefinidos) {
                $razaoSocialDefinida = $dados
                    && property_exists($dados, 'razao_social')
                    && $dados->razao_social != null
                    && strval(trim($dados->razao_social)) != '';

                $porteDefinido = property_exists($dados, 'porte')
                    && $dados->porte != null;

                $estabelecimentoDefinido = property_exists($dados, 'estabelecimento')
                    && $dados->estabelecimento != null;

                $cnpjDefinido = $estabelecimentoDefinido ?
                    (property_exists($dados->estabelecimento, 'cnpj')
                        && $dados->estabelecimento->cnpj != null
                        && strval(trim($dados->estabelecimento->cnpj)) != '')
                    : false;

                $objSimplesDefinido = property_exists($dados, 'simples')
                    && $dados->simples != null;

                $simplesDefinido =  $objSimplesDefinido ?
                    (property_exists($dados->simples, 'simples')
                        && $dados->simples->simples != null
                        && strval(trim($dados->simples->simples)) != '')
                    : false;

                $meiDefinido = $objSimplesDefinido ?
                    (property_exists($dados->simples, 'mei')
                        && $dados->simples->mei != null
                        && strval(trim($dados->simples->mei)) != '')
                    : false;

                $nome = $razaoSocialDefinida ? ($dados->razao_social) : null;
                $cnpj = $cnpjDefinido ? ($dados->estabelecimento->cnpj) : null;
                $descricaoPorte = $porteDefinido ? $dados->porte->descricao : null;

                $simples = $simplesDefinido ? ($dados->simples->simples == 'Sim') : false;
                $dataOpcaoSimples = $simplesDefinido ? ($dados->simples->data_opcao_simples) : null;
                $dataExclusaoSimples = $simplesDefinido ? ($dados->simples->data_exclusao_simples) : null;

                $mei = $meiDefinido ? ($dados->simples->mei == 'Sim') : false;
                $dataOpcaoMei = $meiDefinido ? ($dados->simples->data_opcao_mei) : null;
                $dataExclusaoMei = $meiDefinido ? ($dados->simples->data_exclusao_mei) : null;

                $numeroPorte = $this->getNumeroPorte($descricaoPorte, $mei);

                $dadosValidos = $razaoSocialDefinida;

                if ($dadosValidos) {
                    $registros[] = [
                        'nome' => $nome,
                        'cnpj' => $cnpj,
                        'porte' => $numeroPorte,
                        'simples' => [
                            'simples' => $simples,
                            'dataOpcaoSimples' => $dataOpcaoSimples,
                            'dataExclusaoSimples' => $dataExclusaoSimples,
                        ],
                        'mei' => [
                            'mei' => $mei,
                            'dataOpcaoMei' => $dataOpcaoMei,
                            'dataExclusaoMei' => $dataExclusaoMei,
                        ],
                    ];
                }
            }
        }

        return $registros;
    }

    /**
     * Metodo para buscar registro de um cnpj em uma api externa
     *
     * @param string $cnpj
     */
    private function getDadosCnpj($cnpj)
    {
        $url = "https://comercial.cnpj.ws/cnpj/{$cnpj}";
        $token = "?token=UGTNcSIeFN32mjZofsSqdvSRAnnGhIDS144pw06A5Vam";
        $ch = curl_init(trim($url . $token));

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return json_decode(curl_exec($ch));
    }

    /**
     * Metodo para definir no numero do porte de uma empresa
     *
     * Retornos:
     * 1 - ME
     * 2 - EPP / Outros
     * 3 - MEI
     *
     * @param String $descricaoPorte
     * @return Int
     */
    private function getNumeroPorte($descricaoPorte, $enquadramentoMei)
    {
        $porte = strtolower($descricaoPorte);
        $me = str_contains($porte, 'micro') && str_contains($porte, 'empresa');

        if ($enquadramentoMei) {
            return 3;
        }

        if ($me) {
            return 1;
        }


        return 2;
    }
}
