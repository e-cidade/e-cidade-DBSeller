<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Configuracao\Departamento\Models\Departamento;
use App\Domain\Configuracao\Endereco\Model\Estado;
use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Model\Processo\TipoProc;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Transferencia;
use App\Helpers\Imagem;
use DBString;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CapaDoDocumento
{


    private $dompdf;
    private $nameFile;
    const PROCESSO = 1;
    const MEMORANDO = 2;
    const OFICIO = 3;
    const DECRETO = 4;
    const OUVIDORIA = 5;
    const EMPENHO = 6;
    const PORTARIA = 7;
    const TIPOS_TEMPLATES = [
        self::PROCESSO => "layouts.documentos-template.capa-do-processo",
        self::MEMORANDO => "layouts.documentos-template.capa-do-memorando",
        self::OFICIO => "layouts.documentos-template.capa-do-oficio",
        self::DECRETO => "layouts.documentos-template.capa-do-processo",
        self::OUVIDORIA => "layouts.documentos-template.capa-do-processo",
        self::EMPENHO => "layouts.documentos-template.capa-do-processo",
        self::PORTARIA => "layouts.documentos-template.capa-do-processo",
    ];

    public function render()
    {
        $this->dompdf->stream($this->nameFile, array('Attachment' => 0));
    }

    public function saveFile()
    {
        $output = $this->dompdf->output();
        $documento = ECIDADE_PATH . "tmp/capa-processo.pdf";

        file_put_contents($documento, $output);
        return $documento;
    }

    /**
     * @throws \Exception
     */
    public function __construct(
        Processo      $processo,
        Transferencia $transferencia = null,
        $nameFile = null
    ) {
        $data = [];
        /**
         * dados prefeitura
         */
        $instituicao = DBConfig::find($processo->getInstituicao());
        if (!$instituicao) {
            throw new \Exception("Instituição não encontrada ao gerar capa do processo!");
        }
        $data["instituicao_telefone"] = $instituicao->telef;
        $caminhoLogo = ECIDADE_PATH . 'imagens/files/' . $instituicao->logo;
        $arquivoLogoBase64 =  Imagem::caminhoArquivoParaBase64($caminhoLogo);
        if ($arquivoLogoBase64) {
            $data["instituicao_logo"] = $arquivoLogoBase64;
        }
        $data["instituicao_nome"] = $instituicao->nomeinst;
        $data["instituicao_municipio"] = $instituicao->munic;
        $data["instituicao_uf"] = $instituicao->uf;
        $instituicaoEstado = Estado::where("db71_sigla", $instituicao->uf)->first();
        $data["instituicao_estado"] = $instituicaoEstado->db71_descricao;

        $cgm = \CgmFactory::getInstanceByCgm($processo->getCgm());
        $data["titular_cgm"] = DBString::utf8_encode_all($cgm->getCodigo());
        $data["titular_nome"] = DBString::utf8_encode_all($cgm->getNome());
        $data["titular_cpfcnpj"] = DBString::utf8_encode_all(
            $cgm->isFisico() === true ? $cgm->getCpf() : $cgm->getCnpj()
        );
        $data["titular_logradouro"] = DBString::utf8_encode_all($cgm->getLogradouro());
        $data["titular_bairro"] = DBString::utf8_encode_all($cgm->getBairro());
        $data["titular_municipio"] = DBString::utf8_encode_all($cgm->getMunicipio());
        $data["requerente"] = $processo->getRequerente();

        $departamento = Departamento::find($processo->getDepartamento());
        $data["departamento_nome"] = DBString::utf8_encode_all(
            $processo->getDepartamento() . " - " . $departamento->getDescricao()
        );
        /**
         * dados processo
         */
        try {
            $data["data_processo"] = (new \DateTime($processo->getData()))->format("d/m/Y");
        } catch (\Exception $ex) {
            $data["data_processo"] = "-";
        }

        $data["numero_processo"] = "{$processo->getNumero()}/{$processo->getAno()}";
        $data["observacao"] = DBString::utf8_encode_all(stripslashes($processo->getObservacao()));
        $tipoProcesso = TipoProc::find($processo->getCodigo());
        if (!$tipoProcesso) {
            throw new \Exception("Tipo de processo não encontrado no processo!");
        }
        $data["tipo_processo_descricao"] = DBString::utf8_encode_all($tipoProcesso->p51_descr);
        $usuarioEmissor = Usuario::find(db_getsession("DB_id_usuario"));
        $data["nome_usuario_aprovou"] = $usuarioEmissor->nome;

        if ($transferencia) {
            $departamentoTransferencia = Departamento::find($transferencia->getDepartamentoRecebimento());
            if ($departamentoTransferencia) {
                $data["departamento_transferencia_codigo"] = $departamentoTransferencia->getCodigo();
                $data["departamento_transferencia_nome"] = DBString::utf8_encode_all(
                    $departamentoTransferencia->getCodigo() . " - " . $departamentoTransferencia->getDescricao()
                );
            }
        }


        $template = self::TIPOS_TEMPLATES[$processo->tipoProcesso->p51_prottipodocumentoprocesso];
        $this->dompdf = new Dompdf();
        $this->dompdf->loadHtml(
            view($template, $data)
        );
        $this->dompdf->setPaper('A4');
        $this->dompdf->render();
        $base = DB::connection()->getDatabaseName();
        $host = request()->getHttpHost() . request()->getBasePath();
        $menu = DB::selectOne("
            SELECT
            fc_montamenu(funcao) as menu
            from db_itensmenu
            where id_item =" . db_getsession("DB_itemmenu_acessado"));

        $this->dompdf->getCanvas()->page_text(
            30,
            811,
            "Base: {$base}",
            null,
            7,
            array(0, 0, 0)
        );

        $anousu = db_getsession("DB_anousu");
        $this->dompdf->getCanvas()->page_text(
            30,
            820,
            DBString::utf8_encode_all(
                "{$host} "
                . @$menu->menu .
                " Emissor: {$usuarioEmissor->nome} Exercício: {$anousu}"
                . " Data: "
                . date("d/m/Y") . " - "
                . date("H:i:s")
            ),
            null,
            7,
            array(0, 0, 0)
        );

        $this->dompdf->getCanvas()->page_text(
            530,
            820,
            DBString::utf8_encode_all("Página {PAGE_NUM} de {PAGE_COUNT}"),
            null,
            7,
            array(0, 0, 0)
        );

        if (empty($nameFile)) {
            $this->nameFile = "capa-processo.pdf";
        } else {
            $this->nameFile . ".pdf";
        }
    }
}
