<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Configuracao\Departamento\Models\Departamento;
use App\Domain\Configuracao\Endereco\Model\Estado;
use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Model\Processo\TipoProc;
use App\Helpers\Imagem;
use DBString;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;

class CapaDoDespacho
{
    private $dompdf;
    private $nameFile;

    public function render()
    {
        $this->dompdf->stream($this->nameFile, array('Attachment' => 0));
    }

    public function saveFile()
    {
        $output = $this->dompdf->output();
        $documento = ECIDADE_PATH . "tmp/{$this->nameFile}.pdf";

        file_put_contents($documento, $output);
        return $documento;
    }

    public function __construct(Processo $processo, $textoDespacho, $nameFile = null)
    {
        if (isset($nameFile)) {
            $this->nameFile = $nameFile;
        }

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

        $data["nome_atendente"] = $this->buscarAtendente($processo->getCodigoProcesso());
        $data["hora_processo"] = $processo->getHora();
        $data["numero_processo"] = "{$processo->getNumero()}/{$processo->getAno()}";
        $data["observacao"] = DBString::utf8_encode_all(stripslashes($textoDespacho));
        $tipoProcesso = TipoProc::find($processo->getCodigo());
        if (!$tipoProcesso) {
            throw new \Exception("Tipo de processo não encontrado no processo!");
        }
        $data["tipo_processo_descricao"] = DBString::utf8_encode_all($tipoProcesso->p51_descr);
        $usuarioEmissor = Usuario::find(db_getsession("DB_id_usuario"));
        $data["nome_usuario_aprovou"] = $usuarioEmissor->nome;

        $this->dompdf = new Dompdf();
        $this->dompdf->loadHtml(
            view("layouts.documentos-template.capa-do-despacho", $data)
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
            $this->nameFile = "capa-despacho.pdf";
        } else {
            $this->nameFile . ".pdf";
        }
    }

    public function buscarAtendente($codProcesso)
    {
        $result = DB::select("select nome from protprocesso
    		inner join db_usuarios  on
    		    db_usuarios.id_usuario = protprocesso.p58_id_usuario
            where p58_codproc = {$codProcesso}");

        if (isset($result[0]->nome)) {
            return $result[0]->nome;
        }
        return "";
    }
}
