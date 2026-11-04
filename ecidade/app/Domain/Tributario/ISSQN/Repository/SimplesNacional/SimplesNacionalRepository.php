<?php

namespace App\Domain\Tributario\ISSQN\Repository\SimplesNacional;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Tributario\ISSQN\Model\ArquivoSimples\ArquivoSimples;
use App\Domain\Tributario\ISSQN\Model\ArquivoSimples\ArquivoSimplesEnvio;
use App\Domain\Tributario\ISSQN\Model\ArquivoSimples\ArquivoSimplesRegistro;
use App\Domain\Tributario\ISSQN\Model\ArquivoSimples\ArquivoSimplesRegistroCnae;

/**
 * Classe para lidar com a busca de innformacoes e armazenamento do arquivo de novos
 * optantes pelo simples nacional
 */
class SimplesNacionalRepository extends BaseRepository
{
    /**
     * Metodo para importacao de dados de optantes
     *
     * @param string $cnpj
     */
    public function importaOptantes($cnpj)
    {
        $observacoes = "";
        $inscricoes = "";
        $numeroInscricoesBaixadas = 0;
        $apto = true;
        $naoPossuiDebitosVencidos = true;

        $pendencias = \DB::table('arquivosimplesimportacaodetalhe')->where(
            "q142_cnpj",
            "=",
            $cnpj
        )->get()->toArray();

        foreach ($pendencias as $pendencia) {
            if (!str_contains($observacoes, rtrim($pendencia->q142_observacao, " . "))) {
                $observacoes .= " {$pendencia->q142_observacao}";
                $observacoes = trim($observacoes);

                if (strlen($observacoes) > 0  && $observacoes[strlen($observacoes) - 1] == ".") {
                    $observacoes = rtrim($observacoes, ". ");
                    $observacoes .= " (CNAE {$pendencia->q142_cnae}),";
                }
            }

            if ($apto) {
                $apto = $pendencia->q142_apto;
            }
        }
        $observacoes = rtrim($observacoes, " , ");

        $dados = \DB::table("cgm")->selectRaw("
                                    z01_nome as nome,
                                    q02_inscr as inscricao,
                                    (z01_cgccpf is not null) as possuiCgm,
                                    (q02_inscr > 0) as possuiInscricao,
                                    (q02_dtbaix is not null) as possuiInscricaoMunicipalAtiva,
                                    (q02_dtbaix is not null) as baixa
                            ")
            ->join('issbase', 'issbase.q02_numcgm', '=', 'cgm.z01_numcgm')
            ->where('cgm.z01_cgccpf', '=', $cnpj)->get()->toArray();

        foreach ($dados as $dado) {
            if (!str_contains($inscricoes, $dado->inscricao)) {
                $inscricoes .= " {$dado->inscricao} / ";
                $inscricoes = trim($inscricoes);
            }

            if ($dado->baixa) {
                $observacoes = $observacoes . ", Inscrição {$dado->inscricao} baixada";
                $numeroInscricoesBaixadas++;
            }

            $debitosVencidos = \DB::table('arreinscr')
                ->join('arrecad', 'arrecad.k00_numpre', '=', 'arreinscr.k00_numpre')
                ->whereRaw("arrecad.k00_dtoper::date < now()::date and arreinscr.k00_inscr = {$dado->inscricao}")
                ->get();

            if (count($debitosVencidos) > 0) {
                $observacoes = $observacoes . ", Inscrição {$dado->inscricao} com débito(s) vencido(s)";
                $naoPossuiDebitosVencidos = false;
            }
        }

        $possuiCgm = count($dados) > 0 ? $dados[0]->possuicgm : false;
        $possuiInscricao = count($dados) > 0 ? $dados[0]->possuiinscricao : false;

        if ($apto) {
            if ($numeroInscricoesBaixadas == count($dados)) {
                $apto = false;
            }

            if (!$naoPossuiDebitosVencidos) {
                $apto = false;
            }

            if (!$possuiCgm && !$possuiInscricao) {
                $apto = false;
                $observacoes = $observacoes . ", Cadastro não encontrado";
            }
        }

        $observacoes = trim($observacoes, ", ");
        $inscricoes = trim($inscricoes, ' / ');

        return [
            'cnpj' => $cnpj,
            'nome' => count($dados) > 0 ? $dados[0]->nome : "",
            'naoPossuiDebitosVencidos' => $naoPossuiDebitosVencidos,
            'apto' => $apto,
            'observacoes' => $observacoes,
            'inscricoes' => $inscricoes,
            'possuiCgm' => $possuiCgm,
            'possuiInscricao' => $possuiInscricao,
            'possuiInscricaoMunicipalAtiva' => count($dados) > 0 ?
                $dados[0]->possuiinscricaomunicipalativa
                : false,
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
     * @param array $registros
     *
     * @return integer $sequencialArqsimples
     */
    public function insereInformacoesArquivoArqSimples(
        $nomeArquivo,
        $dataImportacao,
        $periodoApuracaoDe,
        $periodoApuracaoAte,
        $dataLimite
    ) {
        $criado = ArquivoSimples::create([
            'q183_nomearq' => $nomeArquivo,
            'q183_dt_import' => $dataImportacao,
            'q183_periodo_ini' => $periodoApuracaoDe,
            'q183_periodo_fim' => $periodoApuracaoAte,
            'q183_data_limite' => $dataLimite,
        ]);

        $sequencialArqsimples = $criado->q183_sequencial;

        return $sequencialArqsimples;
    }

    /**
     * @param integer $sequencialArqsimples
     * @param string $dataSolicitacao
     * @param string $cnpj
     *
     * @return number $sequencialArqSimplesReg
     */
    public function insereInformacoesRegistrosArqSimplesReg(
        $sequencialArqsimples,
        $dataSolicitacao,
        $cnpj
    ) {
        $criado = ArquivoSimplesRegistro::create([
            'q184_arqsimples' => $sequencialArqsimples,
            'q184_dt_solicitacao' => $dataSolicitacao,
            'q184_cnpj' => $cnpj
        ]);

        $sequencialArqSimplesReg = $criado->q184_sequencial;

        return $sequencialArqSimplesReg;
    }

    /**
     * @param integer $sequencialArqsimplesReg
     * @param integer $cnae
     * @param string $tipoCnae
     *
     * @return number $sequencialArqSimplesRegCnae
     */
    public function insereInformacoesCnaeArqSimplesRegCnae(
        $sequencialArqsimplesReg,
        $cnae,
        $tipoCnae
    ) {
        $criado = ArquivoSimplesRegistroCnae::create([
            'q185_arqsimplesreg' => $sequencialArqsimplesReg,
            'q185_cnae' => $cnae,
            'q185_tipo' => $tipoCnae,
        ]);

        $sequencialArqSimplesRegCnae = $criado->q185_sequencial;

        return $sequencialArqSimplesRegCnae;
    }

    /**
     * @param integer $sequencialArqsimples
     * @param integer $sequencialArqsimplesReg
     * @param string $situacaoPendencia
     *
     * @return number $sequencialArqSimplesEnvioSequencial
     */
    public function insereInformacoesEnvioArqSimplesEnvio(
        $sequencialArqsimples,
        $sequencialArqsimplesReg,
        $situacaoPendencia
    ) {
        $criado = ArquivoSimplesEnvio::create([
            'q186_arqsimples' => $sequencialArqsimples,
            'q186_arqsimplesreg' => $sequencialArqsimplesReg,
            'q186_situacao' => $situacaoPendencia,
        ]);

        $sequencialArqSimplesEnvioSequencial = $criado->q186_sequencial;

        return $sequencialArqSimplesEnvioSequencial;
    }

    /**
     * @param string $cnpj
     * @param string $dataLimite
     */
    public function buscarArquivoPorCnpj(
        $cnpj,
        $dataLimite
    ) {

        $resultados = ArquivoSimples::join(
            "arqsimplesreg",
            "arqsimplesreg.q184_arqsimples",
            '=',
            'arqsimples.q183_sequencial'
        )
            ->join(
                "arqsimplesregcnae",
                "arqsimplesregcnae.q185_arqsimplesreg",
                '=',
                'arqsimplesreg.q184_sequencial'
            )
            ->join(
                "arqsimplesenvio",
                "arqsimplesenvio.q186_arqsimples",
                '=',
                'arqsimples.q183_sequencial'
            )
            ->where(
                "arqsimplesreg.q184_cnpj",
                "=",
                $cnpj
            )
            ->where(
                "arqsimples.q183_data_limite",
                "=",
                "$dataLimite"
            )
            ->get();

        return $resultados;
    }

    /**
     * @param string $idArquivo
     */
    public function buscarArquivoPorId(
        $idArquivo
    ) {
        $infoArquivo = ArquivoSimples::where(
            "q183_sequencial",
            "=",
            "$idArquivo"
        )
            ->get()[0];

        $infoRegistros = ArquivoSimplesRegistro::where(
            'q184_arqsimples',
            '=',
            $infoArquivo->q183_sequencial
        )
            ->get()->toArray();

        for ($index = 0; $index < count($infoRegistros); $index++) {
            $infoCnaes = ArquivoSimplesRegistroCnae::where(
                'q185_arqsimplesreg',
                '=',
                $infoRegistros[$index]['q184_sequencial']
            )
                ->get()->toArray();

            $infoRegistros[$index]['cnaes'] = $infoCnaes;

            $infoEnvio = ArquivoSimplesEnvio::where(
                'q186_arqsimples',
                "=",
                "$infoArquivo->q183_sequencial"
            )
                ->where(
                    'q186_arqsimplesreg',
                    '=',
                    $infoRegistros[$index]['q184_sequencial']
                )
                ->get()->toArray()[0];

            $infoRegistros[$index]['envio'] = $infoEnvio;
        }

        $infoArquivo->registros = $infoRegistros;

        return $infoArquivo;
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
        $arquivos = ArquivoSimples::query();

        if ($nomeArquivo) {
            $arquivos->where('q183_nomearq', 'ilike', $nomeArquivo);
        }

        if ($dataImportacao) {
            $arquivos->where('q183_dt_import', '=', $dataImportacao);
        }

        if ($dataLimite) {
            $arquivos->where('q183_data_limite', '=', $dataLimite);
        }

        return $arquivos->get();
    }
}
