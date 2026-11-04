<?php

namespace App\Domain\Financeiro\Contabilidade\Services\ContaCorrente;

use App\Domain\Financeiro\Contabilidade\Models\ConplanoExeContaCorrente;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoSistema;
use App\Domain\Financeiro\Contabilidade\Models\InformacaoComplementar;
use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use ECidade\File\Csv\LerCsv;

/**
 * Como essa classe foi feita para ser especifica para importação da DDR, estou deixando fixo os códigos do
 * sistema e atributo
 */
class ImportarDdr
{

    /**
     * @var integer
     */
    private $exercicio;
    /**
     * @var string
     */
    private $filePath;

    protected $mapa = [
        "estrutural" => 0,
        "reduzido" => 1,
        "instituicao" => 2,
        "fonteGestao" => 3,
        "subrecurso" => 4,
        "complemento" => 5,
        "saldo" => 6,
        "natureza" => 7,
    ];
    /**
     * @var ConplanoSistema
     */
    private $contaCorrente;
    /**
     * @var InformacaoComplementar
     */
    private $atributo;

    /**
     * @var array
     */
    protected $fontesRecurso = [];

    /**
     *
     * @param integer $exercicio
     * @param string $filePath
     */
    public function __construct($exercicio, $filePath)
    {
        $this->exercicio = $exercicio;
        $this->filePath = $filePath;

        $this->contaCorrente = ConplanoSistema::find(100);

        $this->atributo = $this->contaCorrente->atributos->first()->atributo;
    }

    public function importar()
    {
        $csv = new LerCsv($this->filePath);
        $csv->setCsvControl(';');
        $linha = $csv->read();

        foreach ($linha as $id => $dadosLinha) {
            $teste = mb_strtolower($dadosLinha[0]);
            if (($teste === 'estrutural') || !is_array($dadosLinha)) {
                continue;
            }

            $this->salvar($dadosLinha, $id);
        }
    }

    /**
     * @param array $dadosLinha
     * @param $linha
     * @return void
     * @throws \Exception
     */
    private function salvar(array $dadosLinha, $linha)
    {
        $this->apagar($dadosLinha, $linha);
        $saldo = $dadosLinha[$this->mapa['saldo']];
        $saldo = $this->sanitize($saldo);

        $recurso = $this->getFonteRecurso($dadosLinha, $linha);

        $saldoInicial = new ConplanoExeContaCorrente();
        $saldoInicial->c143_conplanoreduz = $dadosLinha[$this->mapa['reduzido']];
        $saldoInicial->c143_exercicio = $this->exercicio;
        $saldoInicial->contaCorrente()->associate($this->contaCorrente);
        $saldoInicial->c143_saldo = $saldo;
        $saldoInicial->c143_natureza = $dadosLinha[$this->mapa['natureza']];
        $saldoInicial->save();

        $saldoInicial->atributos()->create([
            'c144_conplanoinfocomplementar' => $this->atributo->c121_sequencial,
            'c144_valor' => $recurso->orctiporec_id
        ]);
    }

    private function getFonteRecurso($dadosLinha, $linha)
    {
        $fonteGestao = $dadosLinha[$this->mapa['fonteGestao']];
        $subrecurso = $dadosLinha[$this->mapa['subrecurso']];
        $complemento = $dadosLinha[$this->mapa['complemento']];

        $hash = sprintf('%s#%s#%s', $fonteGestao, $subrecurso, $complemento);
        if (!array_key_exists($hash, $this->fontesRecurso)) {
            $fonte = FonteRecurso::fonteRecurso($fonteGestao, $this->exercicio, $subrecurso, $complemento)
                ->first();

            if (is_null($fonte)) {
                throw new \Exception(sprintf(
                    "Não foi encontrado o recurso da linha %s.
                    Reduzido: %s Fonte Gestão: %s	Subrecurso: %s Complemento: %s",
                    $linha,
                    $dadosLinha[$this->mapa['reduzido']],
                    $fonteGestao,
                    $subrecurso,
                    $complemento
                ));
            }

            $this->fontesRecurso[$hash] = $fonte;
        }

        return $this->fontesRecurso[$hash];
    }

    private function sanitize($saldo)
    {
        if (strpos($saldo, ',')) {
            $saldo = str_replace('.', '', $saldo);
            $saldo = str_replace(',', '.', $saldo);
        }

        return round($saldo, 2);
    }

    private function apagar(array $dadosLinha, $linha)
    {
        $recurso = $this->getFonteRecurso($dadosLinha, $linha);

        ConplanoExeContaCorrente::query()
            ->join(
                'conplanoexecontacorrenteatributo',
                'conplanoexecontacorrenteatributo.c144_conplanoexecontacorrente',
                '=',
                'conplanoexecontacorrente.id'
            )
            ->where('c143_conplanoreduz', $dadosLinha[$this->mapa['reduzido']])
            ->where('c143_exercicio', $this->exercicio)
            ->where('c143_conplanosistema', $this->contaCorrente->c122_sequencial)
            ->where('c144_conplanoinfocomplementar', $this->atributo->c121_sequencial)
            ->where('c144_valor', $recurso->orctiporec_id)
            ->delete();
    }
}
