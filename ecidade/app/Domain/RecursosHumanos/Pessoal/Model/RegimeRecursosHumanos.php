<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use App\Domain\RecursosHumanos\Pessoal\Model\InssIrf\InssIrf;
use App\Domain\RecursosHumanos\Pessoal\Model\Instituicao\Instituicao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class RegimeRecursosHumanos
 * @package App\Domain\RecursosHumanos\Pessoal\Model
 * @property integer $rh30_codreg
 * @property integer $rh30_descr
 */
class RegimeRecursosHumanos extends Model
{
    protected $table = 'pessoal.rhregime';
    protected $primaryKey = 'rh30_codreg';
    public $maternidade = [];
    public $rubricaMaternidade = 0;
    public $tiposValores = [];
    public $dadosGroups = [];
    public $cfpess = [];

    public function relatorioPrevidencia($mes, $ano, $lotacoes, $tipo, $previdencia, $regime)
    {
        $this->tiposValores = $tipo;
        //VALIDAR SALARIO MATERNIDADE
        $this->getPrevidencia($ano, $mes, $previdencia);

        $this->getCfpess($ano, $mes);

        $lotaForms = [];
        foreach ($this->getLotacoes($lotacoes) as $lota) {
            $lotaForms[] = $lota['r70_codigo'];
        }

        $regForms = [];
        foreach ($this->getRegime($regime) as $reg) {
            $regForms[] = $reg['rh30_codreg'];
        }

        $parametrosOpcoes = [
            'instituicao' => $this->instituicao()[0]->codigo,
            'ano' => $ano,
            'mes' => $mes
        ];

        if (sizeof($lotaForms) > 0) {
            $parametrosOpcoes['lotacao'] = implode(',', $lotaForms);
        }

        if (sizeof($regime) > 0) {
            $parametrosOpcoes['regime'] = implode(',', $regime);
        }

        if (sizeof($previdencia) > 0) {
            $parametrosOpcoes['previdencia'] = implode(',', $previdencia);
        }

        $implodeRubricas = [];

        foreach ($this->maternidade as $rubrica) {
            if ($rubrica !== "") {
                $implodeRubricas[] = "'" . $rubrica . "'";
                $this->rubricaMaternidade = $rubrica;
            }
        }

        $rubricasR = " 'R901','R902','R903','R904',
        'R905','R906','R907','R908','R909','R910','R911', 
        'R912', 'R913', 'R918', 'R919', 'R920',  
        'R938', 'R985', 'R986', 'R987', 'R992'";
        if (sizeof($implodeRubricas) > 0) {
            $parametrosOpcoes['rubricas'] = $rubricasR . ',' . implode(',', $implodeRubricas);
        } else {
            $parametrosOpcoes['rubricas'] = $rubricasR;
        }

        $sqlFim = "
         ) AS x
        GROUP BY 
            r70_codigo,
            r70_descr,
            r70_instit,
            r70_ativo,
            r70_estrut,
            rh30_descr,
            rh30_codreg,
            rh02_tbprev,
            r33_ppatro,
            rh30_codigocategoria
        ORDER BY r70_codigo DESC
    ";

        $feriasGroup = [];
        $salarioGroup = [];
        $rescisaoGroup = [];
        $decimoGroup = [];
        $complementarGroup = [];
        if (in_array('sa', $this->tiposValores)) {
            $sql = $this->resultadoTipoFolhas('sa') . $this->sqlBase('r14', $parametrosOpcoes) . $sqlFim;
            $sql = DB::select($sql);
            for ($i = 0; $i < sizeof($sql); $i++) {
                $salarioGroup[] = $sql[$i];
            }
        }
        if (in_array('fe', $this->tiposValores)) {
            $sql = $this->resultadoTipoFolhas('fe') . $this->sqlBase('r31', $parametrosOpcoes) . $sqlFim;
            $sql = DB::select($sql);
            for ($i = 0; $i < sizeof($sql); $i++) {
                $feriasGroup[] = $sql[$i];
            }
        }

        if (in_array('re', $this->tiposValores)) {
            $sql = $this->resultadoTipoFolhas('re') . $this->sqlBase('r20', $parametrosOpcoes) . $sqlFim;
            $sql = DB::select($sql);
            for ($i = 0; $i < sizeof($sql); $i++) {
                $rescisaoGroup[] = $sql[$i];
            }
        }

        if (in_array('d13', $this->tiposValores)) {
            $sql = $this->resultadoTipoFolhas('d13') . $this->sqlBase('r35', $parametrosOpcoes) . $sqlFim;
            $sql = DB::select($sql);
            for ($i = 0; $i < sizeof($sql); $i++) {
                $decimoGroup[] = $sql[$i];
            }
        }

        if (in_array('co', $this->tiposValores)) {
            $sql = $this->resultadoTipoFolhas('co') . $this->sqlBase('r48', $parametrosOpcoes) . $sqlFim;
            $sql = DB::select($sql);
            for ($i = 0; $i < sizeof($sql); $i++) {
                $complementarGroup[] = $sql[$i];
            }
        }

        $salario = array_map(function ($item) {
            return [
                'salario_lotacao' => $item->r70_descr,
                'salario_regime' => $item->rh30_descr,
                'salario_total_servidores' => $item->total_servidores,
                'salario_valor' => $item->total_salario,
                'salario_deducao' => $item->total_deducao,
                'salario_fgts' => $item->fgts,
                'salario_familia' => $item->salario_familia,
                'salario_maternidade' => $item->salario_maternidade,
                'salario_agente_nocivo' => $item->agente_nocivo,
                'salario_patronal' => $item->r33_ppatro,
                'codigo' => $item->r70_codigo,
                'categorias' => $item->rh30_codigocategoria,
                'rpps' => $item->rpps,
                'rgps' => $item->rgps,
            ];
        }, $salarioGroup);

        $ferias = array_map(function ($item) {
            return [
                'ferias_lotacao' => $item->r70_descr,
                'ferias_regime' => $item->rh30_descr,
                'ferias_total_servidores' => $item->total_servidores,
                'ferias_valor' => $item->ferias,
                'total_deducao_fes' => $item->total_deducao_fes,
                'ferias_patronal' => $item->r33_ppatro,
                'codigo' => $item->r70_codigo,
                'categorias' => $item->rh30_codigocategoria,
                'rpps' => $item->rpps,
                'rgps' => $item->rgps,
            ];
        }, $feriasGroup);

        $rescisao = array_map(function ($item) {
            return [
                'rescisao_lotacao' => $item->r70_descr,
                'rescisao_regime' => $item->rh30_descr,
                'rescisao_total_servidores' => $item->total_servidores,
                'rescisao_valor' => $item->rescisao,
                'total_deducao_res' => $item->total_deducao_res,
                'rescisao_patronal' => $item->r33_ppatro,
                'codigo' => $item->r70_codigo,
                'categorias' => $item->rh30_codigocategoria,
                'rpps' => $item->rpps,
                'rgps' => $item->rgps,
            ];
        }, $rescisaoGroup);

        $decimo = array_map(function ($item) {
            return [
                'decimo_lotacao' => $item->r70_descr,
                'decimo_regime' => $item->rh30_descr,
                'decimo_total_servidores' => $item->total_servidores,
                'decimo_valor' => $item->decimo,
                'total_deducao_dec' => $item->total_deducao_dec,
                'decimo_patronal' => $item->r33_ppatro,
                'codigo' => $item->r70_codigo,
                'categorias' => $item->rh30_codigocategoria,
                'rpps' => $item->rpps,
                'rgps' => $item->rgps,
            ];
        }, $decimoGroup);

        $complentar = array_map(function ($item) {
            return [
                'complementar_lotacao' => $item->r70_descr,
                'complementar_regime' => $item->rh30_descr,
                'complementar_total_servidores' => $item->total_servidores,
                'complementar_valor' => $item->complementar,
                'total_deducao_co' => $item->total_deducao_co,
                'complementar_patronal' => $item->r33_ppatro,
                'codigo' => $item->r70_codigo,
                'categorias' => $item->rh30_codigocategoria,
                'rpps' => $item->rpps,
                'rgps' => $item->rgps,
            ];
        }, $complementarGroup);

        $agrupado = [];

        foreach ([$salario, $ferias, $rescisao, $decimo, $complentar] as $dados) {
            foreach ($dados as $item) {
                $codigo = $item['codigo'];
                if (isset($agrupado[$codigo])) {
                    $agrupado[$codigo][] = $item;
                } else {
                    $agrupado[$codigo] = [$item];
                }
            }
        }

        $resultado = [];
        $categorias = [
            '701',
            '711',
            '712',
            '721',
            '722',
            '723',
            '731',
            '734',
            '738',
            '741',
            '751',
            '761',
            '771',
            '781'
        ];
        if (sizeof($agrupado) == 0) {
            return 0;
        }
        foreach ($agrupado as $codigo => $valores) {
            foreach ($valores as $item) {
                //VERIFICA SE AS CHAVES ESTÃO CORRETAS EXEMPLO COM IF ACIMA
                $lotacao = $this->getFirstSetValue($item, [
                    'salario_lotacao',
                    'ferias_lotacao',
                    'rescisao_lotacao',
                    'decimo_lotacao',
                    'complementar_lotacao'
                ], 0);

                $patronal = $this->getFirstSetValue($item, [
                    'salario_patronal',
                    'ferias_patronal',
                    'rescisao_patronal',
                    'decimo_patronal',
                    'complementar_patronal'
                ], 0);

                $regime = $this->getFirstSetValue($item, [
                    'salario_regime',
                    'ferias_regime',
                    'rescisao_regime',
                    'decimo_regime',
                    'complementar_regime'
                ], 0);


                // Inicializa o array para o codigo e regime, se ainda nao estiver definido
                if (empty($resultado[$codigo][$regime])) {
                    $resultado[$codigo][$regime] = [
                        'codigo' => $codigo,
                        'lotacao' => $lotacao,
                        'regime' => $regime,
                        'patronal' => $patronal,
                        'total_servidores' => 0,
                        'total_liquido' => 0,
                        'salario_fgts' => 0,
                        'salario_deducao' => 0,
                        'salario_familia' => 0,
                        'salario_maternidade' => 0,
                        'salario_agente_nocivo' => 0,
                        'categorias' => 0,
                        'rpps' => 0,
                        'rgps' => 0,
                        'terceiros' => 0,
                        'fat' => 0,
                    ];
                }

                //SOMA OS VALORES PARA A TABELA DO PDF
                $resultado[$codigo][$regime]['total_servidores'] += $this->sumItemValues($item, [
                    'salario_total_servidores',
                    'ferias_total_servidores',
                    'rescisao_total_servidores',
                    'decimo_total_servidores',
                    'complementar_total_servidores'
                ]);

                $resultado[$codigo][$regime]['total_liquido'] += $this->sumItemValues($item, [
                    'salario_valor',
                    'ferias_valor',
                    'rescisao_valor',
                    'decimo_valor',
                    'complementar_valor'
                ]);

                $resultado[$codigo][$regime]['salario_fgts'] +=
                    $this->sumItemValues($item, ['salario_fgts']);
                $resultado[$codigo][$regime]['salario_familia'] +=
                    $this->sumItemValues($item, ['salario_familia']);
                $resultado[$codigo][$regime]['salario_maternidade'] +=
                    $this->sumItemValues($item, ['salario_maternidade']);
                $resultado[$codigo][$regime]['salario_agente_nocivo'] +=
                    $this->sumItemValues($item, ['salario_agente_nocivo']);
                $resultado[$codigo][$regime]['salario_deducao'] +=
                    $this->sumItemValues($item, [
                        'salario_deducao',
                        'total_deducao_res',
                        'total_deducao_fes',
                        'total_deducao_dec',
                        'total_deducao_co'
                    ]);

                $resultado[$codigo][$regime]['categorias'] = in_array($item['categorias'], $categorias) ? 1 : 0;

                $resultado[$codigo][$regime]['rpps'] += $this->sumItemValues($item, ['rpps']);
                $resultado[$codigo][$regime]['rgps'] += $this->sumItemValues($item, ['rgps']);

                $resultado[$codigo][$regime]['terceiros'] = isset($this->cfpess['terceiros']) ?
                    $this->cfpess['terceiros'] : 0;
                $resultado[$codigo][$regime]['fat'] = isset($this->cfpess['fat']) ?
                    $this->cfpess['fat'] : 0;
            }
        }
        return $resultado;
    }

    private function getFirstSetValue($item, $keys, $default = 0)
    {
        foreach ($keys as $key) {
            if (isset($item[$key])) {
                return $item[$key];
            }
        }
        return $default;
    }

    private function sumItemValues($item, $keys)
    {
        $total = 0;
        foreach ($keys as $key) {
            if (isset($item[$key])) {
                $total += $item[$key];
            }
        }
        return $total;
    }

    public function getLotacoes($lotacoes)
    {
        $lotacoes = RhLota::whereIn('r70_estrut', $lotacoes)
            ->where('r70_instit', $this->instituicao()[0]->codigo)
            ->get();

        $lotacoesArray = [];
        foreach ($lotacoes as $lota) {
            $lotacoesArray[] = $lota;
        }
        return $lotacoesArray;
    }

    public function getRegime($regime)
    {
        $regimes = RegimeRecursosHumanos::whereIn('rh30_codreg', $regime)
            ->where('rh30_instit', $this->instituicao()[0]->codigo)
            ->get();

        $regimesArray = [];

        foreach ($regimes as $reg) {
            $regimesArray[] = $reg;
        }
        return $regimesArray;
    }
    //COREÇÂO SAL. MATERNIDADE
    public function getPrevidencia($ano, $mes, $r33_codtab)
    {
        $sql = InssIrf::where('r33_anousu', $ano)
            ->where('r33_mesusu', $mes)->whereIn('r33_codtab', $r33_codtab)
            ->where('r33_instit', $this->instituicao()[0]->codigo)
            ->get()->unique('r33_nome');

        foreach ($sql as $prev) {
            $this->maternidade[] = trim($prev->r33_rubmat);
        }
    }

    public function getCfpess($ano, $mes)
    {
        $sql = DB::table('pessoal.cfpess')
            ->select('r11_pcterc', 'r11_peactr')
            ->where('r11_mesusu', $mes)
            ->where('r11_anousu', $ano)
            ->where('r11_instit', $this->instituicao()[0]->codigo)
            ->get();

        foreach ($sql as $cfpess) {
            $this->cfpess['terceiros'] = trim($cfpess->r11_pcterc);
            $this->cfpess['fat'] = trim($cfpess->r11_peactr);
        }
    }

    private function instituicao()
    {
        return Instituicao::where('codigo', db_getsession('DB_instit'))->get();
    }

    private function resultadoTipoFolhas($tipoFolha)
    {
        $sql = "
            SELECT 
            COUNT(DISTINCT rh02_regist) AS total_servidores,
            COUNT(DISTINCT rpps) AS rpps,
	        COUNT(DISTINCT rgps) AS rgps,
        ";

        switch ($tipoFolha) {
            case 'sa':
                $sql .=  "
                ROUND(SUM(
                    total_deducao 
                ), 2) AS total_deducao,
                  ROUND(SUM(
                    total_salario
                  ), 2) AS total_salario,
                ROUND(SUM(
                    fgts 
                ), 2) AS fgts,
                ROUND(SUM(	
                    salario_familia
                ), 2) AS salario_familia,
                ROUND(SUM(	 
                    salario_maternidade     
                ), 2) AS salario_maternidade,
                ROUND(SUM(	
                    agente_nocivo
                ), 2) AS agente_nocivo,";
                break;
            case 'fe':
                $sql .= "
                ROUND(SUM(	 
                    ferias
                ), 2) AS ferias,
                 ROUND(SUM(	 
                    total_deducao_fes
                ), 2) AS total_deducao_fes,
            ";
                break;
            case 're':
                $sql .= "
                ROUND(SUM(	
                    rescisao
                ), 2) AS rescisao,
                  ROUND(SUM(	
                    total_deducao_res
                ), 2) AS total_deducao_res,
                ";
                break;
            case 'd13':
                $sql .= "
                ROUND(SUM(	
                    decimo
                ), 2) AS decimo,
                 ROUND(SUM(	
                    total_deducao_dec
                ), 2) AS total_deducao_dec,
                ";
                break;
            case 'co':
                $sql .= "
                ROUND(SUM(	
                    complementar
                ), 2) AS complementar,
                ROUND(SUM(	
                    total_deducao_co
                ), 2) AS total_deducao_co,
                ";
                break;
        }

        $sql .= "
                r70_codigo,
                r70_descr,
                r70_instit,
                r70_ativo,
                r70_estrut,
                rh30_descr,
                rh30_codreg,
                rh02_tbprev,
                r33_ppatro,
                rh30_codigocategoria
            FROM (
        
        ";

        return trim($sql);
    }

    private function sqlBase($sigla, $opcoes)
    {

        $tabelas = [
            'r14' => 'gerfsal',
            'r31' => 'gerffer',
            'r20' => 'gerfres',
            'r35' => 'gerfs13',
            'r48' => 'gerfcom'
        ];

        $campos = [
            'r14' => " 
                case when r14_rubric IN ('R901','R902','R903','R904',
                'R905','R906','R907','R908','R909','R910','R911', 'R912') then r14_valor else 0 end as total_deducao, 
                case when r14_rubric IN ('R985','R986','R987') then r14_valor else 0 end as total_salario, 
                case when r14_rubric IN ('R938') then r14_valor else 0 end as fgts, 
                case when r14_rubric IN ('R918', 'R919') then r14_valor else 0 end as salario_familia, 
                case when r14_rubric = '{$this->rubricaMaternidade}' then r14_valor else 0 end as salario_maternidade, 
                case 
                    when (r14_rubric = 'R985' and rh02_ocorre = '02') then r14_valor * 0.12 
                    when (r14_rubric = 'R985' and rh02_ocorre = '03') then r14_valor * 0.09 
                    when (r14_rubric = 'R985' and rh02_ocorre = '04') then r14_valor * 0.06 
                    else 0 end as agente_nocivo ",
            'r31' => " 
                case when r31_rubric = 'R987' then r31_valor else 0 end as ferias,
                case when r31_rubric IN ('R903', 'R906', 
                'R909', 'R912') then r31_valor else 0 end as total_deducao_fes ",
            'r20' => " 
                case when r20_rubric IN ('R985', 'R987', 'R986') then r20_valor else 0 end as rescisao,
                case when r20_rubric IN ('R901', 'R902', 'R903', 'R904', 
                'R905', 'R906', 'R907', 'R908', 
                'R909', 'R910', 'R911', 'R912') then r20_valor else 0 end as total_deducao_res ",
            'r35' => " 
                case when r35_rubric = 'R986' then r35_valor else 0 end as decimo,
                case when r35_rubric IN ('R902', 'R905', 
                'R908', 'R911') then r35_valor else 0 end as total_deducao_dec ",
            'r48' => " 
                case when r48_rubric = 'R985' then r48_valor else 0 end as complementar,
                case when r48_rubric IN ('R901', 'R902', 'R903', 
                'R904', 'R905', 'R906', 'R907', 'R908', 
                'R909', 'R910', 'R911', 'R912') then r48_valor else 0 end as total_deducao_co "
        ];

        $where = [
            'rh02_instit = ' . $opcoes['instituicao'],
            'rh02_anousu = ' . $opcoes['ano'],
            'rh02_mesusu = ' . $opcoes['mes'],
        ];


        if (!empty($opcoes['lotacao'])) {
            $where[] = "rh02_lota IN({$opcoes['lotacao']})";
        }

        if (!empty($opcoes['regime'])) {
            $where[] = "rh30_codreg IN({$opcoes['regime']})";
        }

        if (!empty($opcoes['previdencia'])) {
            $where[] = "r33_codtab IN({$opcoes['previdencia']})";
        }

        $sql = "
            select
            r70_codigo,
            r70_descr,
            r70_instit,
            r70_ativo,
            r70_estrut,
            rh30_descr,
            rh30_codreg,
            {$sigla}_valor,
            {$sigla}_rubric,
            rh02_regist,
            rh02_tbprev,
            rh02_ocorre,
            r33_ppatro,
            rh30_codigocategoria,
            {$campos[$sigla]},
            CASE WHEN rh129_regimeprevidencia = 1 THEN rh02_regist end as rpps,
            CASE WHEN rh129_regimeprevidencia = 2 THEN rh02_regist end as rgps
        from
            pessoal.rhpessoalmov
        inner join pessoal.rhpessoal on
            rh01_regist = rh02_regist
            and rh01_instit = rh02_instit
        inner join pessoal.inssirf on
            rh02_instit = r33_instit
            and rh02_anousu = r33_anousu
            and rh02_mesusu = r33_mesusu
            and rh02_tbprev = cast(r33_codtab as INTEGER) - 2
        inner join pessoal.regimeprevidenciainssirf on 
        rh129_codigo = r33_codigo and rh129_instit = r33_instit
        inner join pessoal.rhregime on
            rh30_codreg = rh02_codreg 
        inner join recursoshumanos.rhcodigocategoria on
	        rh30_codigocategoria = rh255_codigo 
        inner join rhlota on
            r70_codigo = rh02_lota and r70_ativo = true
        inner join cgm on
            z01_numcgm = rh01_numcgm
        inner join {$tabelas[$sigla]} on
            {$sigla}_regist = rh02_regist
            and {$sigla}_anousu = rh02_anousu
            and {$sigla}_mesusu = rh02_mesusu
            and {$sigla}_rubric in({$opcoes['rubricas']})
        where
            " . implode(" AND ", $where) . "
            AND rh30_codigocategoria IN ('701', '711', '712', 
                                         '721', '722', '723', 
                                         '731', '734', '738', 
                                         '741', '751', '761', 
                                         '771', '781')
        group by
            r70_codigo,
            r70_descr,
            r70_instit,
            r70_ativo,
            r70_estrut,
            rh30_descr,
            rh30_codreg,
            {$sigla}_valor,
            {$sigla}_rubric,
            rh02_regist,
            rh02_tbprev,
            rh02_ocorre,
            r33_ppatro,
            rh129_regimeprevidencia,
            rh30_codigocategoria

            UNION ALL

                      select
            r70_codigo,
            r70_descr,
            r70_instit,
            r70_ativo,
            r70_estrut,
            rh30_descr,
            rh30_codreg,
            {$sigla}_valor,
            {$sigla}_rubric,
            rh02_regist,
            rh02_tbprev,
            rh02_ocorre,
            r33_ppatro,
            rh30_codigocategoria,
            {$campos[$sigla]},
            CASE WHEN rh129_regimeprevidencia = 1 THEN rh02_regist end as rpps,
            CASE WHEN rh129_regimeprevidencia = 2 THEN rh02_regist end as rgps
        from
            pessoal.rhpessoalmov
        inner join pessoal.rhpessoal on
            rh01_regist = rh02_regist
            and rh01_instit = rh02_instit
        inner join pessoal.inssirf on
            rh02_instit = r33_instit
            and rh02_anousu = r33_anousu
            and rh02_mesusu = r33_mesusu
            and rh02_tbprev = cast(r33_codtab as INTEGER) - 2
        inner join pessoal.regimeprevidenciainssirf on 
        rh129_codigo = r33_codigo and rh129_instit = r33_instit
        inner join pessoal.rhregime on
            rh30_codreg = rh02_codreg
        inner join recursoshumanos.rhcodigocategoria on
	        rh30_codigocategoria = rh255_codigo 
        inner join rhlota on
            r70_codigo = rh02_lota and r70_ativo = true
        inner join cgm on
            z01_numcgm = rh01_numcgm
        inner join {$tabelas[$sigla]} on
            {$sigla}_regist = rh02_regist
            and {$sigla}_anousu = rh02_anousu
            and {$sigla}_mesusu = rh02_mesusu
            and {$sigla}_rubric in({$opcoes['rubricas']})
        where
            " . implode(" AND ", $where) . "
            AND rh30_codigocategoria NOT IN ('701', '711', '712', 
                                             '721', '722', '723', 
                                             '731', '734', '738', 
                                             '741', '751', '761', 
                                             '771', '781')
        group by
            r70_codigo,
            r70_descr,
            r70_instit,
            r70_ativo,
            r70_estrut,
            rh30_descr,
            rh30_codreg,
            {$sigla}_valor,
            {$sigla}_rubric,
            rh02_regist,
            rh02_tbprev,
            rh02_ocorre,
            r33_ppatro,
            rh129_regimeprevidencia,
            rh30_codigocategoria
        ";

        return $sql;
    }
}
