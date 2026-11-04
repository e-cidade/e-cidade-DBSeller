<?php

namespace App\Domain\Financeiro\Contabilidade\Services\ContaCorrente;

use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use Carbon\Carbon;
use ECidade\File\Csv\Dumper\Dumper;
use Illuminate\Support\Facades\DB;

class TemplateDdr
{
    private $template = [
        ['Estrutural', 'Reduzido', 'Instituição', 'Fonte Gestão', 'Subrecurso', 'Complemento', 'Saldo', 'Natureza']
    ];
    /**
     * @var Carbon
     */
    private $data;

    public function __construct(Carbon $data)
    {
        $this->data = $data;
    }

    public function getContas()
    {
        $sql = "
            select distinct c60_estrut, c61_reduz, c61_instit
              from conplano
              join conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
              join conplanoatributos on (c120_conplano, c120_anousu) = (c60_codcon, c60_anousu)
             where c60_anousu = {$this->data->year} and c60_estrut like '82111%'
        ";
        return DB::select($sql);
    }

    public function getRecursos()
    {
        return FonteRecurso::with('recurso')
            ->where('exercicio', $this->data->year)
            ->where('orctiporec_id', '!=', 0)
            ->recursoAtivo($this->data->format('Y-m-d'))
            ->orderBy('gestao')
            ->get()
            ->sortBy(function ($fonte, $key) {
                return sprintf(
                    '%s#%s#%s',
                    $fonte->gestao,
                    $fonte->recurso->o15_recurso,
                    $fonte->recurso->o15_complemento
                );
            });
    }

    public function download()
    {
        $this->processar();
        $filepath = sprintf('tmp/template_%s.csv', time());
        $cvs = new Dumper();
        $cvs->setCsvControl(';');
        $cvs->dumpToFile($this->template, $filepath);

        return [
            'csv' => $filepath,
            'csvLinkExterno' => ECIDADE_REQUEST_PATH . $filepath
        ];
    }

    /**
     * @return void
     */
    public function processar()
    {
        $contas = $this->getContas();
        $recursos = $this->getRecursos();
        foreach ($contas as $conta) {
            foreach ($recursos as $recurso) {
                $data = [
                    $conta->c60_estrut,
                    $conta->c61_reduz,
                    $conta->c61_instit,
                    $recurso->gestao,
                    $recurso->recurso->o15_recurso,
                    $recurso->recurso->o15_complemento,
                    0,
                    'C'
                ];

                $this->template[] = $data;
            }
        }
    }
}
