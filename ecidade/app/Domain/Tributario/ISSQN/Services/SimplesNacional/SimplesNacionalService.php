<?php

namespace App\Domain\Tributario\ISSQN\Services\SimplesNacional;

use App\Domain\Tributario\ISSQN\Repository\SimplesNacional\SimplesNacionalRepository;

/**
 * Classe para lidar com a busca de innformacoes e armazenamento do arquivo de novos
 * optantes pelo simples nacional
 */
class SimplesNacionalService
{
    protected $repository;

    /**
     * Construtor da classe
     *
     * @param  SimplesNacionalRepository $simplesNacionalRepository
     * @return void
     */
    public function __construct(
        SimplesNacionalRepository $simplesNacionalRepository
    ) {
        $this->repository = $simplesNacionalRepository;
    }

    /**
     * Metodo para importacao de dados de optantes
     *
     * @param array $registros
     */
    public function importaOptantes(
        $registros
    ) {
        $resposta = [];
        $jaImportado = true;
        $dataImportacao = "";
        $nomeArquivo = "";

        foreach ($registros as $registro) {
            $cnaesResposta = [];
            $cnaes = [
                $registro['cnae0'],
                $registro['cnae1'],
                $registro['cnae2'],
                $registro['cnae3'],
                $registro['cnae4'],
                $registro['cnae5'],
                $registro['cnae6'],
                $registro['cnae7'],
                $registro['cnae8'],
                $registro['cnae9'],
                $registro['cnae10'],
            ];

            foreach ($cnaes as $cnae) {
                if ($cnae != "0000000") {
                    $cnaesResposta[] = $cnae;
                }
            }

            $cnpj = $registro['cnpj'];
            $data = $this->repository->importaOptantes($cnpj, $cnaes);

            $resposta[] = array_merge($data, ['cnaes' => $cnaesResposta]);
        }

        if (count($registros) > 0) {
            $registrosEncontrados = [];

            for ($index = 0; $index < count($registros); $index++) {
                $registrosEncontrados[] = $this
                    ->repository
                    ->buscarArquivoPorCnpj(
                        $registros[$index]['cnpj'],
                        $registros[$index]['dataLimite']
                    );
            }

            foreach ($registrosEncontrados as $registroUnico) {
                if (count($registroUnico) == 0) {
                    $jaImportado = false;
                } else {
                    $dataImportacao = $registroUnico[0]->q183_dt_import;
                    $nomeArquivo =  $registroUnico[0]->q183_nomearq;
                }
            }
        }

        return [
            'dados' => $resposta,
            'jaImportado' => $jaImportado,
            'dataImportacao' => $dataImportacao,
            'nomeArquivo' => $nomeArquivo
        ];
    }

    /**
     * Metodo para exportacao e armazenamento do arquivo de optantes
     * pelo simples nacional
     *
     * @param string $nomeArquivo,
     * @param string $dataImportacao,
     * @param string $periodoApuracaoDe,
     * @param string $periodoApuracaoAte,
     * @param string $dataLimite,
     * @param array  $registros
     */
    public function exportaArquivo(
        $nomeArquivo,
        $dataImportacao,
        $periodoApuracaoDe,
        $periodoApuracaoAte,
        $dataLimite,
        $registros
    ) {
        $sequencialArqsimples = $this->repository->insereInformacoesArquivoArqSimples(
            $nomeArquivo,
            $dataImportacao,
            $periodoApuracaoDe,
            $periodoApuracaoAte,
            $dataLimite
        );

        foreach ($registros as $registro) {
            $cnpj = $registro['cnpj'];
            $dataSolicitacao = $registro['dataSolicitacao'];
            $situacaoPendencia = $registro['situacao'];


            $sequencialArqSimplesReg = $this->repository->insereInformacoesRegistrosArqSimplesReg(
                $sequencialArqsimples,
                $dataSolicitacao,
                $cnpj
            );

            foreach ($registro['cnaes'] as $registroCnae) {
                $cnae = $registroCnae['cnae'];
                $tipo = $registroCnae['tipo'];

                $this->repository->insereInformacoesCnaeArqSimplesRegCnae(
                    $sequencialArqSimplesReg,
                    $cnae,
                    $tipo
                );
            }
            $sequencialArqSimplesEnvio = $this->repository->insereInformacoesEnvioArqSimplesEnvio(
                $sequencialArqsimples,
                $sequencialArqSimplesReg,
                $situacaoPendencia
            );
        }
    }

    /**
     * Lista os arquivos armazenados no sistema referente ao simples nacional
     *
     * @param string $nomeArquivo,
     * @param string $dataImportacao,
     * @param string $dataLimite
     */
    public function buscaTodosArquivos(
        $nomeArquivo,
        $dataImportacao,
        $dataLimite
    ) {
        return $this->repository->buscaTodosArquivos(
            $nomeArquivo,
            $dataImportacao,
            $dataLimite
        );
    }

    /**
     * @param string $idArquivo
     */
    public function buscarArquivoPorId(
        $idArquivo
    ) {
        $dadosArquivo = $this->repository->buscarArquivoPorId($idArquivo);

        return $dadosArquivo;
    }
}
