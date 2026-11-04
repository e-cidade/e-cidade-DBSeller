<?php

namespace App\Domain\Financeiro\Empenho\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use cl_db_config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Ramsey\Uuid\Uuid;
use function file_get_contents;

class RelatorioSubcontratacaoController extends Controller
{
    private $mpdf;
    private $css = ECIDADE_PATH . 'storage/assets/css/style.css';

    private $dataInicial;
    private $dataInicialHeader;
    private $dataFinal;
    private $dataFinalHeader;
    private $contratado;
    private $subcontratado;


    public function emitirRelatorio(Request $request)
    {
//        ini_set('max_execution_time', '300');
//        ini_set('memory_limit', '1500000M');
//        ini_set("pcre.backtrack_limit", "5000000");


        $this->dataInicial = preg_replace(
            "/(\d{2})?(\d{2})?(\d{4})/",
            "$3-$2-$1",
            preg_replace("/\D/", "", $request->dataInicial)
        );
        $this->dataInicialHeader = $request->dataInicial;
        $this->dataFinal = preg_replace(
            "/(\d{2})?(\d{2})?(\d{4})/",
            "$3-$2-$1",
            preg_replace("/\D/", "", $request->dataFinal)
        );
        $this->dataFinalHeader = $request->dataFinal;
        $this->contratado = $request->contratado;
        $this->subcontratado = $request->subcontratado;


        $this->mpdf = new Mpdf([
            'tempDir' => ECIDADE_PATH . 'tmp/',
            'orientation' => 'P',
            'format' => [210, 297],
            'autoMarginPadding' => 0
        ]);


        $this->mpdf->WriteHTML(
            file_get_contents($this->css),
            HTMLParserMode::HEADER_CSS
        );


//        $this->logo = base64_encode(\file_get_contents($this->logo));

        $s = $this->emitirPDF();
        return new DBJsonResponse($s);
    }

    public function emitirPDF()
    {

        $this->mountHTMLPDF();

        $uuid = Uuid::uuid4()->toString();
        $path = 'tmp/' . $uuid . '.pdf';

        $this->mpdf->Output(
            ECIDADE_PATH . $path,
            Destination::FILE
        );

        return $path;
    }

    private function mountHTMLPDF()
    {

        $html = "";
        $html .= implode('', [
            '<main>',
            $this->body(),
            $this->mountFooter(),
            '</main>'
        ]);
        $this->mpdf->SetHTMLHeader($this->header());
        $this->mpdf->setFooter('{PAGENO}');
        $this->mpdf->AddPage(
            '', // L - landscape, P - portrait
            '',
            '',
            '',
            '',
            5, // margin_left
            5, // margin right
            35, // margin top
            15, // margin bottom
            2, // margin header
            0
        );
        $this->mpdf->WriteHTML($html);
    }

    private function header()
    {
        $instituição = db_getsession("DB_instit");
        $daoUnidades = new cl_db_config();
        $result = \db_query($daoUnidades->sql_query(
            $instituição,
            'logo,nomeinst, ender, numero, munic, uf, telef, url,cgc'
        ));
        $unidade = pg_fetch_object($result, 0);
        $cnpj = $this->formatCnpjCpf($unidade->cgc);
        $ender = $this->formatEncode($unidade->ender);
        $nomeInstit = $this->formatEncode($unidade->nomeinst);

        $html = <<<HTML
<div class="container">
    <table>
      <tr>
        <td class="td1-header1">
            <img class="logo" src="imagens/files/{$unidade->logo}">
        </td>
        <td class="td2-header1">
            <h4>{$nomeInstit}</h4>
            <p>{$ender}, {$unidade->numero}<p>
            <p>{$unidade->munic} - {$unidade->uf}<p>
            <p>{$unidade->telef} - $cnpj<p>
            <br>
            <p>{$unidade->url}</p>
        </td>
        <td class="td3-header1">
            <table>
                <tr>
                    <td><p>Relatório de Subcontratações.</p></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td><p>Período:{$this->dataInicialHeader} a {$this->dataFinalHeader}</p></td>
                </tr>
            </table>
        </td>
      </tr>
    </table>
    <hr>
</div>
HTML;
        return $html;
    }

    private function body()
    {
        $html = <<<HTML
<div class="container">
        <table>
            {$this->mountBody()}
        </table>
    </div>
HTML;
        return $html;
    }

    private function mountBody()
    {
        $aWhere = [];
        $sWhere = '';

        if (!empty($this->contratado)) {
            $aWhere[] = " and contratado.z01_numcgm = {$this->contratado}";
        }

        if (!empty($this->subcontratado)) {
            $aWhere[] = " and subcontratado.z01_numcgm = {$this->subcontratado}";
        }

        if ($aWhere) {
            $sWhere = implode(" and ", $aWhere);
        }

        $iInstituicaoSessao = db_getsession('DB_instit');

        $dados = DB::select("
select concat(empempenho.e60_codemp, '/', empempenho.e60_anousu) as empenho_numero,
       e69_numero as nota_fiscal,
       contratado.z01_numcgm    as cgm_contratado,
       subcontratado.z01_numcgm    as cgm_subcontratado,
       e23_dtcalculo as data_apropriacao,
       contratado.z01_cgccpf as cpf_cnpj_contratado,
       e23_valorretencao as valorirrfretido_contratado,
       e50_numemp as empenho,
       e50_codord as op,
       contratado.z01_nome as nome_contratado,
       subcontratado.z01_nome as nome_subcontratado,
       subcontratado.z01_cgccpf as cpf_cnpj_subcontratado,
       e163_valor as valorirrfretido_subcontratado
from empenho.retencaoreceitassubcontratacao
         inner join retencaoreceitas on e163_retencaoreceitas = retencaoreceitas.e23_sequencial
         inner join retencaotiporec on e163_retencaotiporec = retencaotiporec.e21_sequencial
         inner join retencaopagordem on retencaoreceitas.e23_retencaopagordem = retencaopagordem.e20_sequencial
         inner join pagordem on retencaopagordem.e20_pagordem = pagordem.e50_codord
         inner join pagordemnota on pagordem.e50_codord = pagordemnota.e71_codord
         inner join empnotaele on e71_codnota = e70_codnota
         inner join empnota on empnotaele.e70_codnota = empnota.e69_codnota
         inner join empempenho on empempenho.e60_numemp = pagordem.e50_numemp
         inner join cgm as subcontratado on e163_numcgm = subcontratado.z01_numcgm
         inner join cgm as contratado on e60_numcgm = contratado.z01_numcgm
         inner join retencaoempagemov on retencaoempagemov.e27_retencaoreceitas = retencaoreceitas.e23_sequencial
         inner join empagemov on empagemov.e81_codmov = retencaoempagemov.e27_empagemov
where retencaoreceitas.e23_dtcalculo between '{$this->dataInicial}' and '{$this->dataFinal}'
and e60_instit = {$iInstituicaoSessao}
and e23_ativo is true and e81_cancelado is null
  {$sWhere}
  ");

        $dadosformatados = [];
        foreach ($dados as $dado) {
            if (array_key_exists($dado->empenho, $dadosformatados)) {
                $dadosformatados[$dado->empenho][] = $dado;
            } else {
                $dadosformatados[$dado->empenho] = [$dado];
            }
        }

        $tbody = '';
        foreach ($dadosformatados as $dado) {
            $dataApropriacao = date_format(date_create($dado[0]->data_apropriacao), 'd-m-Y');
            $cnpjCpfContratado = $this->formatCnpjCpf($dado[0]->cpf_cnpj_contratado);
            $valorContratado = number_format(
                $dado[0]->valorirrfretido_contratado,
                2,
                ",",
                "."
            );
            $nomecontratado = $this->formatEncode($dado[0]->nome_contratado);
            $tbody .= <<<HTML
                <tr class="tr-header">
                    <th class="relatorio">Data Apropriação</th>
                    <th class="relatorio">NF</th>
                    <th class="relatorio">Empenho</th>
                    <th class="relatorio">OP</th>
                    <th class="relatorio">Contratado</th>
                    <th class="relatorio">CNPJ/CPF</th>
                    <th class="relatorio">Valor IRRF Retido</th>

                </tr>
                <tr>
                    <td class="corporelatorio">{$dataApropriacao}</td>
                    <td class="corporelatorio">{$dado[0]->nota_fiscal}</td>
                    <td class="corporelatorio">{$dado[0]->empenho_numero}</td>
                    <td class="corporelatorio">{$dado[0]->op}</td>
                    <td class="corporelatorio">{$nomecontratado}</td>
                    <td class="corporelatorio">{$cnpjCpfContratado}</td>
                    <td class="corporelatorio">{$valorContratado}</td>
                </tr>
                <tr>
                <thead>
                <tr class="tr-header-subcontratado">
                    <th style="background-color: white"></th>
                    <th style="background-color: white"></th>
                    <th class="relatorio">NF</th>
                    <th class="relatorio">OP</th>
                    <th class="relatorio"><i>Subcontratado</i></th>
                    <th class="relatorio"><i>CNPJ/CPF</i></th>
                    <th class="relatorio"><i>Valor IRRF Retido</i></th>
                </thead>
                </tr>
            </tr>
HTML;
            $total = 0;
            foreach ($dado as $subcontratado) {
                $cnpjCpfSubcontratado = $this->formatCnpjCpf($subcontratado->cpf_cnpj_subcontratado);
                $valorSubcontratado = number_format(
                    $subcontratado->valorirrfretido_subcontratado,
                    2,
                    ",",
                    "."
                );

                $nomesubcontratado = $this->formatEncode($subcontratado->nome_subcontratado);
                $total += $subcontratado->valorirrfretido_subcontratado;
                $tbody .= <<<HTML
            <tr>
                <td></td>
                <td></td>
                <td class="corporelatorio"><i>{$dado[0]->nota_fiscal}</i></td>
                <td class="corporelatorio"><i>{$dado[0]->op}</i></td>
                <td class="corporelatorio"><i>{$nomesubcontratado}</i></td>
                <td class="corporelatorio"><i>{$cnpjCpfSubcontratado}</i></td>
                <td class="corporelatorio"><i>{$valorSubcontratado}</i></td>
            </tr>

HTML;
            }
            $total = number_format($total, 2, ",", ".");
            $tbody .= <<<HTML
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="corporelatorio"><b><i>Total:</i></b></td>
                <td class="corporelatorio"><b><i>{$total}</i></b></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
HTML;
        }
        return $tbody;
    }

    private function mountFooter()
    {
        $html = <<<HTML

HTML;
        return $html;
    }

    private function formatCnpjCpf($value)
    {
        $CPF_LENGTH = 11;
        $cnpj_cpf = preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === $CPF_LENGTH) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }

    private function formatEncode($value)
    {
        $text = mb_convert_encoding(
            $value,
            "UTF-8",
            mb_detect_encoding($value, ['ASCII', 'UTF-8', 'ISO-8859-1', 'ISO-8859-15'], true)
        );
        return $text;
    }
}
