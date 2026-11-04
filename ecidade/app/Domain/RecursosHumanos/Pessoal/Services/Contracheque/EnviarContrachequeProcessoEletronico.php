<?php

namespace App\Domain\RecursosHumanos\Pessoal\Services\Contracheque;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\RecursosHumanos\Pessoal\Model\RhPessoal;
use ECidade\Lib\Request\ProcessoEletronico\ProcessoEletronico;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class EnviarContrachequeProcessoEletronico
{

//    public $timeout = 7200;

    /**
     * @var int
     */
    private $year;
    /**
     * @var int
     */
    private $month;

    /**
     * @var array|null
     */
    private $payrollTypes;

    /**
     * matricula
     *
     * @var int
     */
    private $registry;

    /**
     * @var int
     */
    private $instit;

    /**
     * @param $registry
     * @param $instit
     * @param $month
     * @param $year
     * @param $payrollTypes
     */
    public function __construct(
        $registry,
        $instit,
        $month,
        $year,
        $payrollTypes = []
    ) {
        $this->registry     = $registry;
        $this->instit       = $instit;
        $this->month        = $month;
        $this->year         = $year;
        $this->payrollTypes = $payrollTypes;
    }

    /**
     * @return \stdClass
     * @throws \Exception
     */
    public function execute()
    {
        $rhPessoal = RhPessoal::find($this->registry);

        $employeeData           = new \stdClass();
        $employeeData->registry = $this->registry;
        $employeeData->year     = $this->year;
        $employeeData->month    = $this->month;
        $employeeData->instit   = $this->instit;
        $employeeData->items    = [];
        $employeeData->document = $rhPessoal->cgm->z01_cgccpf;

        foreach ($this->payrollType() as $index => $type) {
            Log::info("Processando folha {$type->name} / INSTITUIÇÃO{$employeeData->instit} -> FOLHA: {$index} 
                Matricula {$employeeData->registry}
                Mes {$employeeData->month}
                Ano {$employeeData->year}
            ");

            $table  = $type->table;
            $prefix = $type->prefix;

            $emitecontracheque = DB::table("pessoal.rhemitecontracheque")
                                   ->where('rh85_instit', $this->instit)
                                   ->where('rh85_regist', $this->registry)
                                   ->where('rh85_anousu', $this->year)
                                   ->where('rh85_mesusu', $this->month)
                                   ->where('rh85_sigla', $prefix)
                                   ->orderByDesc('rh85_sequencial')->first();


            if (!$emitecontracheque) {
                continue;
            }

            $payroll = (object)[
                'paycheck_code'  => null,
                'payroll_type'   => $type->name,
                'payroll_prefix' => $type->prefix,
                'values'         => (object)[
                    "discount"    => 0,
                    "gross_value" => 0,
                    "net_value"   => 0,
                ],
                'items'          => [],
            ];

            $payroll->paycheck_code = $emitecontracheque->rh85_estorage;
            $payroll->values->gross_value = round($emitecontracheque->rh85_provento, 2);
            $payroll->values->discount = round($emitecontracheque->rh85_desconto, 2);
            $payroll->values->net_value = round($emitecontracheque->rh85_liquido, 2);

            $valuesEmployees = DB::table("pessoal.{$table}")->select([
                "{$table}.{$prefix}_rubric as rubrica",
                "rhrubricas.rh27_descr as descricao",
                DB::raw("round({$table}.{$prefix}_valor, 2) as valor"),
                DB::raw("round({$table}.{$prefix}_quant, 2) as quantidade"),
                DB::raw("(CASE WHEN {$table}.{$prefix}_pd = 1 THEN 'rendimento'
                                         WHEN {$table}.{$prefix}_pd = 2 THEN 'desconto'
                                         ELSE 'base'
                                     END) as tipo_evento"),
            ])->join(
                "pessoal.rhrubricas",
                function ($joinRhRubricas) use ($prefix) {
                    $joinRhRubricas->on(
                        'rhrubricas.rh27_rubric',
                        '=',
                        "{$prefix}_rubric"
                    )->where(
                        "rhrubricas.rh27_instit",
                        $this->instit
                    );
                }
            )->where("{$table}.{$prefix}_regist", $this->registry)
                                 ->where(
                                     "{$table}.{$prefix}_anousu",
                                     $this->year
                                 )
                                 ->where(
                                     "{$table}.{$prefix}_mesusu",
                                     $this->month
                                 )
                                 ->where(
                                     "{$table}.{$prefix}_instit",
                                     $this->instit
                                 )->get();


            foreach ($valuesEmployees as $information) {
                $payroll->items[] = (object)[
                    "text"     => $information->descricao,
                    "value"    => $information->valor,
                    "quantity" => $information->quantidade,
                    "rubric"   => $information->rubrica,
                    "type"     => $information->tipo_evento,
                ];
            }

            $employeeData->items[] = $payroll;
        }

        $employeeData = (array)$employeeData;

        return $this->enviarFolhaPagamento($employeeData);
    }

    private function payrollType()
    {
        return [

            'salario'      => (object)[
                'prefix'       => 'r14',
                'table'        => 'gerfsal',
                'name'         => 'Salário',
                'type_payroll' => 1,
            ],
            'ferias'       => (object)[
                'prefix'       => 'r31',
                'table'        => 'gerffer',
                'name'         => 'Férias',
                'type_payroll' => null,
            ],
            'rescisao'     => (object)[
                'prefix'       => 'r20',
                'table'        => 'gerfres',
                'name'         => 'Rescisão',
                'type_payroll' => null,
            ],
            'adiantamento' => (object)[
                'prefix'       => 'r22',
                'table'        => 'gerfadi',
                'name'         => 'Adiantamento',
                'type_payroll' => null,
            ],
            '13salario'    => (object)[
                'prefix'       => 'r35',
                'table'        => 'gerfs13',
                'name'         => '13o Salário',
                'type_payroll' => null,
            ],
            'complementar' => (object)[
                'prefix'       => 'r48',
                'table'        => 'gerfcom',
                'name'         => 'Complementar',
                'type_payroll' => 3,
            ],
            /*
            'fixo' => (object)[
                'prefix' => 'r53',
                'table' => 'gerffx',
                'name' => 'Fixo',
                'type_payroll' => null,
            ],
            'previden' => (object)[
                'prefix'       => 'r60',
                'table'        => 'previden',
                'name'         => 'Ajuste da Previdência',
                'type_payroll' => null,
            ],
            'irf' => (object)[
                'prefix'       => 'r61',
                'table'        => 'ajusteir',
                'name'         => 'Ajuste do IRRF',
                'type_payroll' => null,
            ],
            'suplementar' => (object)[
                'prefix'       => 'r14',
                'table'        => 'gerfsal',
                'name'         => 'Suplementar',
                'type_payroll' => 6,
            ],
            */
        ];
    }

    /**
     * @param  array  $data
     *
     * @return object
     * @throws \Exception
     */
    private function enviarFolhaPagamento(array $data)
    {
        $processo = new ProcessoEletronico();

        return $processo->enviarFolhaPagamento($data);
    }
}
