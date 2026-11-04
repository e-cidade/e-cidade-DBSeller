<?php

namespace App\Domain\RecursosHumanos\RH\Relatorios\Services;

use App\Domain\RecursosHumanos\RH\Relatorios\Repository\CertidaoTempoContribuicaoRepository;
use ECidade\Pdf\Pdf;
use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\RhPessoal;
use DBPessoal;
use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;
use funcao;
use Illuminate\Support\Facades\DB;

class CertidaoTempoContribuicaoService extends Pdf
{

    private $matricula;

    private $periodoInicial;

    private $periodoFinal;

    private $codigoInstituicao;

    private $servidor;

    private $anoInicio;

    private $anoFim;

    private $posicoes = [];

    private $fonteValor = 9;
    private $fonteCampo = 10;

    private $ano;

    private $numero;

    private $meses = [
        1 => "JANEIRO",
        2 => "FEVEREIRO",
        3 => "MARÇO",
        4 => "ABRIL",
        5 => "MAIO",
        6 => "JUNHO",
        7 => "JULHO",
        8 => "AGOSTO",
        9 => "SETEMBRO",
        10 => "OUTUBRO",
        11 => "NOVEMBRO",
        12 => "DEZEMBRO"
    ];

    private $headers = [];
    private $dados = [];

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    }

    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    public function setPeriodoInicial($periodoInicial)
    {
        $this->periodoInicial = $periodoInicial;
    }

    public function setPeriodoFinal($periodoFinal)
    {
        $this->periodoFinal = $periodoFinal;
    }

    public function gerar()
    {
        $repositorio = new CertidaoTempoContribuicaoRepository();
        $this->headers[] = "Certidão de Tempo de Contribuição";
        $periodo = "Período: ";
        
        $this->getServidor();

        if (empty($this->periodoInicial)) {
            $periodoStatus = false;
        }

        if (empty($this->periodoInicial)) {
            $this->periodoInicial = $this->servidor->rh01_admiss;
            $periodo .= "admissão (" . $this->formataData($this->servidor->rh01_admiss) . ")";
        } else {
            $periodo .= $this->formataData($this->periodoInicial);
        }

        $repositorio->setPeriodoInicial($this->periodoInicial);
        if (empty($this->periodoFinal)) {
            if (!empty($this->servidor->rh05_recis)) {
                $this->periodoFinal = $this->servidor->rh05_recis;
                $periodo .= " e rescisão (" . $this->formataData($this->servidor->rh05_recis) . ")";
            } else {
                $competencia = CompetenciaHelper::get();
                $this->periodoFinal = "{$competencia->getAno()}-{$competencia->getMes()}-01";
                $periodo .= " até a competência atual  ({$competencia->getMes()}/{$competencia->getAno()})";
            }
        } else {
            $periodo .= " até " . $this->formataData($this->periodoFinal);
        }

        if (!empty($this->periodoFinal)) {
            $repositorio->setPeriodoFinal($this->periodoFinal);
        }

        $this->anoInicio = (int) substr($this->periodoInicial, 0, 4);
        $this->anoFim = (int) substr($this->periodoFinal, 0, 4);

        $this->headers[] = "Servidor: {$this->matricula} - {$this->servidor->z01_nome}";
        $this->headers[] = $periodo;
        
        $repositorio->setMatricula($this->matricula);
        
        $repositorio->setCodigoInstituicao($this->codigoInstituicao);
        
        $dados = $repositorio->getDados();

        $this->rubricaR985($dados);
        // $this->preparaDados($dados);
        return $this->emitir();
    }

    public function emitir()
    {
        foreach ($this->headers as $header) {
            $this->addTitulo($header);
        }

        $this->init();
        $this->imprimir();

        $filename = sprintf('tmp/certidao-tempo-contribuicao-%s.pdf', time());
        $this->Output('F', $filename, false);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    private function imprimir()
    {

        $this->setFont('arial', 'b', $this->fonteCampo);
        $this->ln();
        $this->cell(192, 5, 'RELAÇÃO DAS REMUNERAÇÕES QUE INCIDEM CONTRIBUIÇÕES PREVIDENCIÁRIAS', 0, 1, 'C');
        $this->cell(192, 5, 'REFERENTE À DECLARAÇÃO DE TEMPO DE CONTRIBUIÇÃO AO RGPS - DTC', 0, 1, 'C');
        $this->cell(192, 5, "(Nº/ANO) $this->numero/$this->ano", 0, 1, 'C');

        $instituicao = DBConfig::find($this->codigoInstituicao);
        $this->setFont('arial', '', $this->fonteCampo);
        $this->ln();
        $this->cell(100, 5, 'ÓRGÃO EMITENTE:', 'RLT', 0, 'L');
        $this->cell(92, 5, 'CNPJ:', 'RLT', 1, 'L');
        $this->setFont('arial', '', $this->fonteValor);
        $this->cell(100, 5, $instituicao->nomeinst, 'RLB', 0, 'L');
        $this->cell(92, 5, $this->formataCnpjCpf($instituicao->cgc), 'RLB', 1, 'L');

        $this->setFont('arial', 'b', $this->fonteCampo);
        $this->ln();
        $this->cell(192, 5, 'DADOS PESSOAIS', 0, 1, 'L');
        $this->setFont('arial', '', $this->fonteCampo);
        $this->cell(100, 5, 'NOME DO SERVIDOR/AGENTE PÚBLICO:', 'RLT', 0, 'L');
        $this->cell(92, 5, 'MATRÍCULA:', 'RLT', 1, 'L');
        $this->setFont('arial', '', $this->fonteValor);
        $this->cell(100, 5, $this->servidor->z01_nome, 'RLB', 0, 'L');
        $this->cell(92, 5, $this->matricula, 'RLB', 1, 'L');

        $this->setFont('arial', '', $this->fonteCampo);
        $this->cell(100, 5, 'DOCUMENTO DE IDENTIFICAÇÃO/ ÓRGÃO EXPEDIDOR:', 'RLT', 0, 'L');
        $this->cell(40, 5, 'CPF:', 'RLT', 0, 'L');
        $this->cell(52, 5, 'PIS/PASEP:', 'RLT', 1, 'L');
        $this->setFont('arial', '', $this->fonteValor);
        if (!empty($this->servidor->z01_ident)) {
            $this->cell(100, 5, $this->servidor->z01_ident . "/" . $this->servidor->z01_identorgao, 'RLB', 0, 'L');
        } else {
            $this->cell(100, 5, "", 'RLB', 0, 'L');
        }
        $this->cell(40, 5, $this->formataCnpjCpf($this->servidor->z01_cgccpf), 'RLB', 0, 'L');
        $this->cell(52, 5, $this->servidor->rh16_pis, 'RLB', 1, 'L');

        $this->setFont('arial', '', $this->fonteCampo);
        $this->cell(100, 5, 'NOME DO PAI:', 'RLT', 0, 'L');
        $this->cell(92, 5, 'DATA DE NASCIMENTO:', 'RLT', 1, 'L');
        $this->setFont('arial', '', $this->fonteValor);
        $this->cell(100, 5, $this->servidor->z01_pai, 'RL', 0, 'L');
        $this->cell(92, 5, '', 'RL', 1, 'L');
        $this->setFont('arial', '', $this->fonteCampo);
        $this->cell(100, 5, 'NOME DA MÃE:', 'RL', 0, 'L');
        if (!empty($this->servidor->rh01_nasc)) {
            $this->cell(92, 5, $this->formataData($this->servidor->rh01_nasc), 'RL', 1, 'L');
        } else {
            $this->cell(92, 5, $this->formataData($this->servidor->z01_nasc), 'RL', 1, 'L');
        }
        $this->setFont('arial', '', $this->fonteValor);
        $this->cell(100, 5, $this->servidor->z01_mae, 'RLB', 0, 'L');
        $this->cell(92, 5, '', 'RLB', 1, 'L');

        $this->ln();
        $this->setFont('arial', '', $this->fonteCampo);
        $this->cell(50, 5, 'DATA DE ADMISSÃO:', 'RLT', 0, 'L');
        $this->cell(50, 5, 'DATA DA EXONERAÇÃO:', 'RLT', 0, 'L');
        $this->cell(50, 5, 'PIS/PASEP:', 'RLT', 0, 'L');
        $this->cell(42, 5, 'CPF:', 'RLT', 1, 'L');
        $this->setFont('arial', '', $this->fonteValor);
        $this->cell(50, 5, $this->formataData($this->servidor->rh01_admiss), 'RLB', 0, 'L');
        $this->cell(50, 5, $this->servidor->rh05_recis, 'RLB', 0, 'L');
        $this->cell(50, 5, $this->servidor->rh16_pis, 'RLB', 0, 'L');
        $this->cell(42, 5, $this->formataCnpjCpf($this->servidor->z01_cgccpf), 'RLB', 1, 'L');

        $this->ln();
        $this->setFont('arial', 'b', $this->fonteCampo);
        $this->cell(192, 5, 'DADOS DE REMUNERAÇÕES', 0, 1, 'L');
        $this->setFont('arial', '', $this->fonteValor);

        $this->geraTabelaRemuneracao();
        $this->geraAssinatura();
    }


    private function getServidor()
    {
        $campos = [
            'rh01_regist',
            'z01_numcgm',
            'z01_nome',
            'rh01_admiss',
            'z01_ident',
            'z01_identorgao',
            'z01_cgccpf',
            'rh16_pis',
            'z01_pai',
            'z01_mae',
            'z01_nasc',
            'rh01_nasc',
            'rh01_admiss',
            'rh05_recis',
            'rh16_pis',
            'z01_cgccpf'
        ];

        $where = [
            ['rh02_anousu', DBPessoal::getAnoFolha()],
            ['rh02_mesusu', DBPessoal::getMesFolha()],
            ['rh02_instit', $this->codigoInstituicao],
            ['rh01_regist', $this->matricula]
        ];
        
        $rhpessoal = RhPessoal::select($campos)
            ->join('pessoal.rhpessoalmov', 'rh02_regist', '=', 'rh01_regist')
            ->join('protocolo.cgm', 'cgm.z01_numcgm', '=', 'rhpessoal.rh01_numcgm')
            ->leftJoin('pessoal.rhpesrescisao', 'rh02_seqpes', '=', 'rh05_seqpes')
            ->leftJoin('pessoal.rhpesdoc', 'rh16_regist', '=', 'rh01_regist')
            ->where($where)
            ->orderBy('z01_nome', 'asc');
        $this->servidor =  $rhpessoal->first();
    }

    private function rubricaR985($dados)
    {
        // for ($i = $this->anoInicio; $i <= $this->anoFim; $i++) {
        //     if (empty($this->dados[$i])) {
        //         $this->dados[$i] = [];
        //     }
        //     foreach ($this->meses as $key => $value) {
        //         $this->dados[$i][$key] = '--';
        //     }
        // }
        $rubricasR985 = [];
        $rubricas = [];
        $resultados = DB::table('gerfsal')
        ->select('r14_valor', 'r14_mesusu', 'r14_anousu', 'r14_rubric')
            // ->where('r14_mesusu', $dado->mes)
            // ->where('r14_anousu', $dado->ano)
            ->where('r14_regist', $dados[0]->matricula)
            ->whereBetween('r14_anousu', [$this->anoInicio, $this->anoFim])
            // ->where('r14_rubric', $dado->rubrica)
        ->get();

        // $this->anoInicio; $i <= $this->anoFim;

        foreach ($resultados as $dado) {
            if ($dado->r14_rubric == 'R985') {
                $rubricasR985[] = [
                    'mes' => $dado->r14_mesusu,
                    'ano' => $dado->r14_anousu,
                    'total' => $dado->r14_valor,
                ];
            }
        }

        foreach ($dados as $rubricaArray) {
            $rubricas[] = [
                'mes' => $rubricaArray->mes,
                'ano' => $rubricaArray->ano,
                'total' => $rubricaArray->total,
            ];
        }
        
        $resultados = array_merge($rubricas, $rubricasR985);
        return $this->preparaDados($resultados);
    }

    private function geraTabelaRemuneracao()
    {
        $limite = 8;
        $atual = 0;
        $this->ln(2);
        foreach ($this->dados as $indice => $dado) {
            if ($atual == 0) {
                $this->geraCabecalhoRemuneracao();
            }
            $this->geraColunaAnoRemuneracao($indice);
            $atual += 1;
            if ($atual >= $limite) {
                $atual = 0;
            }
        }
    }

    private function preparaDados($dados)
    {
        // dd($this->anoInicio.'---'.$this->anoFim);
        for ($i = $this->anoInicio; $i <= $this->anoFim; $i++) {
            if (empty($this->dados[$i])) {
                $this->dados[$i] = [];
            }
            foreach ($this->meses as $key => $value) {
                $this->dados[$i][$key] = '--';
            }
        }
        foreach ($dados as $dado) {
            $this->dados[$dado['ano']][$dado['mes']] = number_format($dado['total'], 2, ',', '.');
        }
        // dump($this->dados[$dado['ano']][$dado['mes']]);
        $validacao = $this->anoInicio;
        $temValor = false;
        for ($i = $this->anoInicio; $i <= $this->anoFim; $i++) {
            if ($validacao != $i) {
                if (!$temValor) {
                    unset($this->dados[$validacao]);
                    $validacao = $i;
                }
                $temValor = false;
            }
            foreach ($this->meses as $key => $value) {
                if ($this->dados[$validacao][$key] != '--') {
                    $temValor = true;
                }
            }
        }
    }

    private function geraCabecalhoRemuneracao()
    {
        $coluna = 32;
        $this->posicoes['X'] = $this->getX() + $coluna;
        $this->posicoes['Y'] = $this->getY();
        if ($this->posicoes['Y'] > 212) {
            $this->addPage();
            $this->posicoes['X'] = $this->getX() + $coluna;
            $this->posicoes['Y'] = $this->getY();
        }
        $this->cell($coluna, 10, 'Mês:', 1, 1, 'C');
        foreach ($this->meses as $mes) {
            $this->cell($coluna, 5, $mes, 1, 1, 'C');
        }
    }

    private function geraColunaAnoRemuneracao($i)
    {
        // dd($this->dados);
        $coluna = 20;
        $this->setXY($this->posicoes['X'], $this->posicoes['Y']);
        $this->cell($coluna, 5, "Ano: {$i}", 1, 1, 'C');
        $this->setX($this->posicoes['X']);
        $this->cell($coluna, 5, "Valor ($)", 1, 1, 'C');
       
        foreach ($this->meses as $key => $mes) {
            // dump($this->dados);
            $this->setX($this->posicoes['X']);
            // dd($this->dados[$i][$key]);
            $this->cell($coluna, 5, $this->dados[$i][$key], 1, 1, 'C');
            // dd();
        }
        // dd();
        $this->posicoes['X'] = $this->posicoes['X'] + $coluna;
    }

    private function formataData($data)
    {
        $novaData = $data;
        $data = explode("-", $data);
        $novaData = "{$data[2]}/{$data[1]}/{$data[0]}";
        return $novaData;
    }

    private function formataCnpjCpf($dado)
    {
        $cpf_length = 11;
        $cnpj_cpf = preg_replace("/\D/", '', $dado);
        
        if (strlen($cnpj_cpf) === $cpf_length) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }
        
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }

    private function geraAssinatura()
    {
        if ($this->getY() > 230) {
            $this->addPage();
        }
        $this->ln();
        $this->setFont('arial', 'b', $this->fonteCampo);
        $this->cell(192, 5, 'ASSINATURA E RESPONSABILIDADE PELAS INFORMAÇÕES', 0, 1, 'L');
        $this->setFont('arial', '', $this->fonteValor);
        $this->cell(192, 5, 'Declaro que os documentos que serviram de base para a emissão desta', "RLT", 1, 'L');
        $this->cell(192, 5, 'Declaração encontram-se à disposição do INSS para eventual consulta.', "RLB", 1, 'L');
        $this->cell(96, 5, 'Lavrei a presente Declaração, que não contém emendas', "RLT", 0, 'L');
        $this->cell(96, 5, 'Visto do Dirigente do Órgão competente.', "RLT", 1, 'L');
        $this->cell(96, 5, 'nem rasuras.', "RL", 0, 'L');
        $this->cell(96, 5, '', "RL", 1, 'L');
        $this->cell(96, 5, 'Local e data: _________________________________', "RL", 0, 'L');
        $this->cell(96, 5, '', "RL", 1, 'L');
        $this->cell(96, 5, '                                          ____/____/____', "RL", 0, 'L');
        $this->cell(96, 5, '', "RL", 1, 'C');
        $this->cell(96, 5, '', "RL", 0, 'L');
        $this->cell(96, 5, '', "RL", 1, 'L');
        $this->cell(96, 5, '______________________________________________', "RL", 0, 'C');
        $this->cell(96, 5, '______________________________________________', "RL", 1, 'C');
        $this->cell(96, 5, 'Assinatura do servidor que lavrou a Declaração.', "RL", 0, 'C');
        $this->cell(96, 5, 'Assinatura do Dirigente do Órgão competente.', "RL", 1, 'C');
        $this->cell(96, 5, 'Nome/Cargo/Matrícula', "RLB", 0, 'C');
        $this->cell(96, 5, 'Nome/Cargo/Matrícula', "RLB", 1, 'C');

        $this->ln();
        if ($this->getY() > 250) {
            $this->addPage();
        }
        $this->setFont('arial', 'b', $this->fonteCampo);
        $this->cell(192, 5, 'ORIENTAÇÕES DE PREENCHIMENTO:', 0, 1, 'L');
        $this->setFont('arial', '', $this->fonteValor);
        $this->ln();
        $this->cell(192, 5, '1. Orientações Gerais:', 0, 1, 'L');
        $this->ln();
        $msg = '1.1  Este anexo "RELAÇÃO DAS REMUNERAÇÕES QUE INCIDEM CONTRIBUIÇÕES PREVIDENCIÁRIAS"';
        $msg .= ' quando for utilizado';
        $this->cell(192, 5, $msg, 0, 1, 'L');
        $msg = '       deverá acompanhar o respectivo anexo ';
        $msg .= '"DECLARAÇÃO DE TEMPO DE CONTRIBUIÇÃO AO RGPS - DTC (Nº/ANO ) ___ / ___";';
        $this->cell(192, 5, $msg, 0, 1, 'L');
        $msg = '1.2  Deverão ser informadas as remunerações para as quais incidem obrigatoriamente ';
        $msg .= 'contribuições previdenciárias;';
        $this->cell(192, 5, $msg, 0, 1, 'L');
        $msg = '1.3  O campo "Valor ($)" deverá ser preenchido com a remuneração em moeda da época.';
        $this->cell(192, 5, $msg, 0, 1, 'L');
    }

    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }
}
