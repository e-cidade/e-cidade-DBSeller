<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Model\AtividadeExecucao;
use App\Domain\Patrimonial\Protocolo\Model\DocumentoAndamento;
use App\Domain\Patrimonial\Protocolo\Model\TipoProcesso;
use ECidade\Financeiro\Orcamento\Repository\ElementoRepository;
use EmpenhoFinanceiro;
use Exception;

class EmpenhoDocumentoService extends DocumentoAndamentoService
{
    /**
     * @var EmpenhoFinanceiro
     */
    protected $empenho;
    /**
     * @var TipoProcesso
     */
    protected $tipoProcesso;

    /**
     * @var string
     */
    protected $descricao;

    /**
     * @throws Exception
     */
    public function __construct(EmpenhoFinanceiro $empenho, DocumentoAndamento $documento = null)
    {
        parent::__construct();
        $this->empenho = $empenho;
        $this->tipoProcesso = DocumentoAndamentoService::getTipoProcesso(6, $this->empenho->getNumero());
        if (empty($documento)) {
            $documento = DocumentoAndamento::where('p116_codigo_origem', $this->empenho->getNumero())->first();
        }
        if (!is_null($documento)) {
            $this->setDocumento($documento);
        }
    }

    /**
     * @throws Exception
     */
    public function gerar($arquivo, $uuid)
    {
        $descricao = "Empenho {$this->empenho->getCodigo()}/{$this->empenho->getAno()}";
        $this->processo = $this->gerarProcessoDocumento($this->tipoProcesso, $descricao);
        $this->criarNovoDocumentoAndamento($this->empenho->getNumero(), $descricao, $uuid);
        $this->buscarUsuariosPermissoes();
        $this->vincularDocumento($arquivo, "Gerado");
        $this->salvarDocumentoAndamento();
        $this->salvarMovimentacao();
        return $this->processo;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function buscarUsuariosPermissoes()
    {
        $dotacao = $this->empenho->getDotacao();

        $elementoRepository = new ElementoRepository();
        $elemento = $elementoRepository->scopeFonte($dotacao->getElemento())->first();

        $usuario = new Usuario();
        $usuario->join('db_usupermemp', 'db_usuarios.id_usuario', '=', 'db_usupermemp.db21_id_usuario')
            ->join('db_permemp', 'db_permemp.db20_codperm', '=', 'db_usupermemp.db21_codperm')
            ->join('orctiporec', 'orctiporec.o15_codigo', '=', 'db_permemp.db20_codigo')
            ->join('db_permemp_atividadesexecucao', 'db69_codperm', 'db20_codperm')
            ->where('db69_tipoprocesso', '=', $this->tipoProcesso->p51_codigo)
            ->where('db20_anousu', '=', $dotacao->getAno())
            ->where('db20_orgao', '=', $dotacao->getOrgao())
            ->whereIn("db20_unidade", [$dotacao->getUnidade(), 0])
            ->whereIn("db20_funcao", [$dotacao->getFuncao(), 0])
            ->whereIn("db20_subfuncao", [$dotacao->getSubFuncao(), 0])
            ->whereIn("db20_programa", [$dotacao->getPrograma(), 0])
            ->whereIn("db20_projativ", [$dotacao->getProjAtiv(), 0])
            ->whereIn("db20_codele", [$elemento->getCodigo(), 0])
            ->distinct()
            ->get(['db_usuarios.*', 'db69_atividadesexecucao'])
            ->map(function ($usuarioPermitido) {
                $atividadeExecucao = AtividadeExecucao::find($usuarioPermitido->db69_atividadesexecucao);
                $this->adicionarUsuario($usuarioPermitido, $atividadeExecucao);
            });
    }

    /**
     * @throws Exception
     */
    public function montarObjetoTela()
    {
        $documento = $this->documento->toArray();
        $documento['consulta'] = (object)[
            'name' => 'db_iframe_pesquisaempenho',
            'funcao' => "func_empempenho001.php?e60_numemp={$this->empenho->getNumero()}",
            'label' => 'Dados do Empenho',
        ];
        $documento['documento_estorage'] = $this->documento->processoDocumento->p01_documento;
        $valorEmpenhoFormatado = 'R$ ' . number_format($this->empenho->getValorEmpenho(), 2, ',', '.');
        $documento['descricao_extra'] = "{$this->empenho->getCgm()->getNome()} - {$valorEmpenhoFormatado}";

        $linhas = [];
        $linhas[0] = [
            [
                "label" => 'Credor:',
                "valor" => $this->empenho->getCgm()->getNome()
            ]
        ];
        $linhas[1] = [
            [
                "label" => 'Seq. Empenho: ',
                "valor" => $this->empenho->getNumero()
            ], [
                "label" => 'Valor:',
                "valor" => $valorEmpenhoFormatado
            ]];
        $documento['detalhes'] = $linhas;
        $documento['isDevolvido'] = $this->isDevolvido();
        return (object)$documento;
    }

    /**
     * @return DocumentoAndamento
     */
    public function getDocumentoAndamento()
    {
        return $this->documento;
    }
}
