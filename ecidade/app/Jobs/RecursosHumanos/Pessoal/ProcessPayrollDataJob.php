<?php

namespace App\Jobs\RecursosHumanos\Pessoal;

use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ProcessPayrollDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $chunkIn = 200;
    public $timeout = 7200;

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

    private $token = null;
    private $url = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($year, $month, $payrollTypes = [])
    {
        $this->year = $year;
        $this->month = $month;
        $this->payrollTypes = $payrollTypes;

        $token = env('API_DATA_AVAILABLE_TOKEN');
        $url = env('API_DATA_AVAILABLE_URL');
        if (empty($token) || empty($url)) {
            die("Não foram configurados TOKEN e URL no .env para acesso a API de Débitos.");
        }

        $this->token = $token;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $period = (object)['year' => $this->year, 'month' => $this->month];

        $unities = DB::table('configuracoes.db_config')->orderBy('codigo')->get();

        foreach ($unities as $unity) {
            $instit = $unity->codigo;

            foreach ($this->payrollType() as $index => $type) {
                if (!empty($this->payrollTypes) && !in_array($index, $this->payrollTypes)) {
                    continue;
                }

                Log::info("Processando folha {$type->name} / {$unity->nomeinst} -> FOLHA: {$index}");

                DB::table("pessoal.rhpessoal")
                    ->select([
                        "rhpessoal.rh01_regist as matricula",
                        "cgm.z01_cgccpf as documento"
                    ])->distinct()
                    ->join('protocolo.cgm', 'rhpessoal.rh01_numcgm', '=', "cgm.z01_numcgm")
                    ->join('pessoal.rhpessoalmov', 'rhpessoalmov.rh02_regist', '=', 'rhpessoal.rh01_regist')
                    ->join("pessoal.{$type->table}", function ($joinTable) use ($type, $period, $instit) {
                        $joinTable->on("{$type->table}.{$type->prefix}_regist", '=', 'rhpessoalmov.rh02_regist')
                            ->where("{$type->prefix}_anousu", $period->year)
                            ->where("{$type->prefix}_mesusu", $period->month)
                            ->where("{$type->prefix}_instit", $instit);
                    })
                    ->where("rhpessoalmov.rh02_anousu", $period->year)
                    ->where("rhpessoalmov.rh02_mesusu", $period->month)
                    ->where("rhpessoalmov.rh02_instit", $instit)
//                    ->where("rhpessoalmov.rh02_regist", 10024)
                    ->orderBy('rhpessoal.rh01_regist')
                    ->chunk($this->chunkIn, function ($allEmployees) use ($period, $instit, $type) {
                        $returnEmployeeData = [];
                        $registryControl = [];
                        foreach ($allEmployees as $employee) {
                            if (empty($registryControl[$employee->matricula])) {
                                $registryControl[$employee->matricula] = count($registryControl);
                            }
                            $indexRegistry = $registryControl[$employee->matricula];

                            $employeeData = (object)[
                                'uuid' => Uuid::uuid4()->toString(),
                                'document' => $employee->documento,
                                'registry' => $employee->matricula,
                                'year' => $period->year,
                                'month' => $period->month,
                                'items' => []
                            ];

                            $table = $type->table;
                            $prefix = $type->prefix;
                            $payroll = (object)[
                                'uuid' => Uuid::uuid4()->toString(),
                                'paycheck_code' => null, /* codigo do eStorage */
                                'payroll_type' => $type->name,
                                'values' => (object)[
                                    "discount" => 0,
                                    "gross_value" => 0,
                                    "net_value" => 0,
                                ],
                                'items' => []
                            ];

                            $selectcValueEmployees = DB::table("pessoal.{$table}")
                                ->select([
                                    "{$table}.{$prefix}_rubric as rubrica",
                                    "rhrubricas.rh27_descr as descricao",
                                    DB::raw("round({$table}.{$prefix}_valor, 2) as valor"),
                                    DB::raw("round({$table}.{$prefix}_quant, 2) as quantidade"),
                                    DB::raw("(CASE WHEN {$table}.{$prefix}_pd = 1 THEN 'rendimento'
                                         WHEN {$table}.{$prefix}_pd = 2 THEN 'desconto'
                                         ELSE 'base'
                                     END) as tipo_evento")
                                ])
                                ->join("pessoal.rhrubricas", function ($joinRhRubricas) use ($instit, $prefix) {
                                    $joinRhRubricas->on('rhrubricas.rh27_rubric', '=', "{$prefix}_rubric")
                                        ->where("rhrubricas.rh27_instit", $instit);
                                })
                                ->where("{$table}.{$prefix}_regist", $employee->matricula)
                                ->where("{$table}.{$prefix}_anousu", $period->year)
                                ->where("{$table}.{$prefix}_mesusu", $period->month)
                                ->where("{$table}.{$prefix}_instit", $instit);

                            $valuesEmployees = $selectcValueEmployees->get();

                            if (count($valuesEmployees) === 0) {
                                continue;
                            }

                            foreach ($valuesEmployees as $information) {
                                $payroll->values->discount += $information->tipo_evento === 'desconto'
                                    ? $information->valor
                                    : 0;
                                $payroll->values->gross_value += $information->tipo_evento === 'rendimento'
                                    ? $information->valor : 0;

                                $payroll->items[] = (object)[
                                    "text" => utf8_encode($information->descricao),
                                    "value" => $information->valor,
                                    "quantity" => $information->quantidade,
                                    "rubric" => $information->rubrica,
                                    "type" => $information->tipo_evento,
                                ];
                            }

                            $payroll->values->gross_value = round($payroll->values->gross_value, 2);
                            $payroll->values->discount = round($payroll->values->discount, 2);
                            $payroll->values->net_value = round(
                                ($payroll->values->gross_value - $payroll->values->discount),
                                2
                            );

                            if (!empty($payroll->items)) {
                                $selectCodeStorage = DB::table("pessoal.rhemitecontracheque")
                                    ->select('rh85_estorage as estorage_code')
                                    ->where('rh85_instit', $instit)
                                    ->where('rh85_regist', $employee->matricula)
                                    ->where('rh85_anousu', $period->year)
                                    ->where('rh85_mesusu', $period->month)
                                    ->where('rh85_sigla', $prefix)
                                    ->orderByDesc('rh85_sequencial')
                                    ->first();
                                if ($selectCodeStorage) {
                                    $payroll->paycheck_code = $selectCodeStorage->estorage_code;
                                }

                                $employeeData->items[] = $payroll;
                            }

                            if (!empty($employeeData->items)) {
                                $returnEmployeeData[$indexRegistry] = $employeeData;
                            }
                        }

                        $counter = count($returnEmployeeData) . "/" . $this->chunkIn;
                        Log::info("Enviando para API de Dados Disponibilizados: " . $counter);

                        $this->sendToDebtsApi($returnEmployeeData);
                    });
            }
        }
    }

    private function payrollType()
    {
        return [

            'salario' => (object)[
                'prefix' => 'r14',
                'table' => 'gerfsal',
                'name' => 'Salário',
                'type_payroll' => 1,
            ],
            'ferias' => (object)[
                'prefix' => 'r31',
                'table' => 'gerffer',
                'name' => 'Férias',
                'type_payroll' => null,
            ],
            'rescisao' => (object)[
                'prefix' => 'r20',
                'table' => 'gerfres',
                'name' => 'Rescisão',
                'type_payroll' => null,
            ],
            'adiantamento' => (object)[
                'prefix' => 'r22',
                'table' => 'gerfadi',
                'name' => 'Adiantamento',
                'type_payroll' => null,
            ],
            '13salario' => (object)[
                'prefix' => 'r35',
                'table' => 'gerfs13',
                'name' => '13o Salário',
                'type_payroll' => null,
            ],
            'complementar' => (object)[
                'prefix' => 'r48',
                'table' => 'gerfcom',
                'name' => 'Complementar',
                'type_payroll' => 3,
            ],
            'fixo' => (object)[
                'prefix' => 'r53',
                'table' => 'gerffx',
                'name' => 'Fixo',
                'type_payroll' => null,
            ],
            /*
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

    private function sendToDebtsApi($data)
    {
        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-type' => "application/json"
            ]
        ]);
        $response = $client->post(
            "{$this->url}/api/payrolls",
            [
                RequestOptions::JSON => $data
            ]
        );

        if ($response->getStatusCode() !== 200) {
            die("A API não retornou status 200.");
        }
    }
}
