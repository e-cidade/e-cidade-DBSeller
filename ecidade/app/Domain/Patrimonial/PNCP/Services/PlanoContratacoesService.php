<?php

namespace App\Domain\Patrimonial\PNCP\Services;

use App\Domain\Patrimonial\Material\Models\MaterialUnidade;

use App\Domain\Patrimonial\PNCP\Builders\ItensPlanoBuilder;
use App\Domain\Patrimonial\PNCP\Clients\PlanoContratacoesClient;
use App\Domain\Patrimonial\PNCP\Exceptions\CompraEditalAvisoExcpetion;
use App\Domain\Patrimonial\PNCP\Models\ComprasPncp;
use App\Domain\Patrimonial\PNCP\Models\PlanoContratacao;
use App\Domain\Patrimonial\PNCP\Models\PlanoContratacaoItem;
use App\Domain\Patrimonial\PNCP\Models\VerificaUnidadeRequisitante;
use App\Domain\Patrimonial\PNCP\Relatorios\ItensPlanoPDF;
use Illuminate\Http\Request;
use \Exception;
use InstituicaoRepository;

class PlanoContratacoesService
{
    /**
     * @var PlanoContratacoesClient
     */
    private $http;

    public function __construct()
    {
        $this->http = new PlanoContratacoesClient();
    }

    /**
     * @return array
     */
    public function buscarUnidades()
    {
        return MaterialUnidade::all(['m61_codmatunid', 'm61_descr'])->toArray();
    }

    /**
     * @param Request $request
     * @return string[]
     * @throws \Exception
     */
    public function incluir(Request $request)
    {
        $instituicao = InstituicaoRepository::getInstituicaoByCodigo($request->DB_instit);
        $itensPlano = (object) $this->buscarItens($request);

        $itens = [];
        foreach ($itensPlano as $item) {
            $item = (object) $item;
            $itens[] = (object) [
                'numeroItem' => $item->pn06_item,
                'categoriaItemPca' => $item->pn06_categoriaitem,
                'unidadeFornecimento' => $item->m61_descr,
                'quantidade' => $item->pn06_quantidade,
                'valorUnitario' => $item->pn06_valorunitario,
                'valorTotal' => $item->pn06_valortotal,
                'valorOrcamentoExercicio' => $item->pn06_valororcamento,
                'dataDesejada' => $item->pn06_datadesejada,
                'catalogo' => 1,
                'classificacaoCatalogo' => $item->pn06_classificacaocatalogo,
                'codigoItem' => $item->pn06_codmater,
                'classificacaoSuperiorCodigo' => $item->pn06_classificacaosuperiorcodigo,
                'classificacaoSuperiorNome' => utf8_encode($item->pc03_descrgrupo),
            ];
        }

        $dados = (object) [
            'codigoUnidade' => $request->unidadeCodigo,
            'anoPca' => $request->ano,
            'itensPlano' => $itens,
        ];

        $link = "https://pncp.gov.br/app/pca/{$instituicao->getCNPJ()}/";
        try {
            $response = $this->http->incluir($instituicao->getCNPJ(), $dados);
            $response = explode('/', $response);
            PlanoContratacao::where('pn05_codigo', $request->codigoPca)->update([
                'pn05_status' => 3,
                'pn05_pncp' => $response[9]
            ]);
            return [
                'link' => $link . $response[8] . '/' . $response[9],
            ];
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }
    }

    public function elaboracao(Request $request)
    {
        $pca = new PlanoContratacao();
        try {
            $pca->pn05_unidade = $request->unidade;
            $pca->pn05_ano = $request->ano;
            $pca->pn05_instituicao = $request->DB_instit;
            $pca->pn05_status = $request->status;
            $pca->pn05_datacadastro = date('Y-m-d');
            $pca->save();
            return PlanoContratacao::where('pn05_instituicao', $request->DB_instit)
                ->join('unidadespncp', 'pn02_unidade', 'pn05_unidade')
                ->orderBy('pn05_codigo', 'DESC')
                ->get()->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return void
     */
    public function incluirItem(Request $request)
    {
        $sequencial = 1;
        $numeroItem = PlanoContratacaoItem::where('pn06_planocontratacao', $request->codigoPca)
            ->orderBy('pn06_item', 'desc')->first();
        if (!is_null($numeroItem)) {
            $sequencial += $numeroItem->toArray()['pn06_item'];
        }

        try {
            $itemPlano = (json_decode(stripslashes(utf8_encode($request->itensPlano))));
            $item = new PlanoContratacaoItem();
            $item->pn06_planocontratacao = $itemPlano->codigoPca;
            $item->pn06_item = $sequencial;
            $item->pn06_codmater = $itemPlano->codigoItem;
            $item->pn06_categoriaitem = $itemPlano->categoriaItemPca;
            $item->pn06_classificacaocatalogo = $itemPlano->classificacaoCatalogo;
            $item->pn06_classificacaosuperiorcodigo = $itemPlano->classificacaoSuperiorCodigo;
            $item->pn06_unidadefornecimentocodigo = $itemPlano->unidadeFornecimentoCodigo;
            $item->pn06_quantidade = str_replace(',', '.', str_replace(
                '.',
                '',
                $itemPlano->quantidade
            ));
            $item->pn06_valorunitario = str_replace(',', '.', str_replace(
                '.',
                '',
                $itemPlano->valorUnitario
            ));
            $item->pn06_valortotal = str_replace(',', '.', str_replace(
                '.',
                '',
                $itemPlano->valorTotal
            ));
            $item->pn06_valororcamento = str_replace(',', '.', str_replace(
                '.',
                '',
                $itemPlano->valorOrcamentoExercicio
            ));
            $item->pn06_unidaderequisitante = $itemPlano->codigoDepartamento;
            $item->pn06_datadesejada = $itemPlano->dataDesejada;
            $item->save();

            return $sequencial;
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function buscarItens(Request $request)
    {
        $parametroUnidadeRequisitante = new VerificaUnidadeRequisitante();
        $parametroUnidadeRequisitante = $parametroUnidadeRequisitante
            ->select('pn07_habilitado')
            ->where('pn07_instit', $request->DB_instit)
            ->first();

        $parametroUnidadeRequisitante = empty($parametroUnidadeRequisitante)
            ? false
            : $parametroUnidadeRequisitante->pn07_habilitado;

        $validaDepartamentoRequisitante = false;
        if ($parametroUnidadeRequisitante && $request->visualizar !== 'true') {
            $validaDepartamentoRequisitante = true;
        }

        return PlanoContratacaoItem::join(
            'matunid',
            'm61_codmatunid',
            'pn06_unidadefornecimentocodigo'
        )
            ->join('pcmater', 'pc01_codmater', 'pn06_codmater')
            ->join('pcsubgrupo', 'pc04_codsubgrupo', 'pc01_codsubgrupo')
            ->join('pcgrupo', 'pc03_codgrupo', 'pc04_codgrupo')
            ->join('db_depart', 'pn06_unidaderequisitante', 'coddepto')
            ->where('pn06_planocontratacao', $request->codigoPca)
            ->when(
                $validaDepartamentoRequisitante,
                function ($query) use ($request) {
                    return $query->where('pn06_unidaderequisitante', $request->DB_coddepto);
                }
            )
            ->orderBy('pn06_item')
            ->get()
            ->toArray();
    }

    public function removerItem(Request $request)
    {
        try {
            return PlanoContratacaoItem::where('pn06_item', $request->numeroItem)->delete();
        } catch (CompraEditalAvisoExcpetion $exception) {
            throw new \Exception($exception->getErros());
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function editarItem(Request $request)
    {
        try {
            $itemPlano = (json_decode(stripslashes(utf8_encode($request->item))));

            PlanoContratacaoItem::where('pn06_item', $itemPlano->itemUpdate)
                ->update([
                    'pn06_planocontratacao' => $itemPlano->codigoPca,
                    'pn06_codmater' => $itemPlano->codigoItem,
                    'pn06_categoriaitem' => $itemPlano->categoriaItemPca,
                    'pn06_classificacaocatalogo' => $itemPlano->classificacaoCatalogo,
                    'pn06_classificacaosuperiorcodigo' => $itemPlano->classificacaoSuperiorCodigo,
                    'pn06_unidadefornecimentocodigo' => $itemPlano->unidadeFornecimentoCodigo,
                    'pn06_quantidade' => str_replace(',', '.', str_replace(
                        '.',
                        '',
                        $itemPlano->quantidade
                    )),
                    'pn06_valorunitario' => str_replace(',', '.', str_replace(
                        '.',
                        '',
                        $itemPlano->valorUnitario
                    )),
                    'pn06_valortotal' => str_replace(',', '.', str_replace(
                        '.',
                        '',
                        $itemPlano->valorTotal
                    )),
                    'pn06_valororcamento' => str_replace(',', '.', str_replace(
                        '.',
                        '',
                        $itemPlano->valorOrcamentoExercicio
                    )),
                    'pn06_unidaderequisitante' => $itemPlano->codigoDepartamento,
                    'pn06_datadesejada' => $itemPlano->dataDesejada
                ]);
            return $this->buscarItens($request);
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }
    }

    public function buscarPlanos(Request $request)
    {
        try {
            return PlanoContratacao::where('pn05_instituicao', $request->DB_instit)
                ->join('unidadespncp', 'pn02_unidade', 'pn05_unidade')
                ->orderBy('pn05_codigo')
                ->get()->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function editarPlano(Request $request)
    {
        try {
            PlanoContratacao::where('pn05_codigo', $request->codigoPca)
                ->update([
                    'pn05_unidade' => $request->unidade,
                    'pn05_ano' => $request->ano,
                    'pn05_instituicao' => $request->DB_instit,
                    'pn05_status' => $request->status,
                ]);
            return $this->buscarPlanos($request);
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws \Exception
     */
    public function excluirPlano(Request $request)
    {
        try {
            $item = PlanoContratacaoItem::where('pn06_planocontratacao', $request->codigoPca)->get()->toArray();
            if (!empty($item)) {
                throw new \Exception('Exclua os itens do plano antes de remover o mesmo.');
            }
            PlanoContratacao::where('pn05_codigo', $request->codigoPca)->delete();
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }
    }


    /**
     * @param Request $request
     * @return ItensPlanoPDF
     */
    public function emitirRelatorio(Request $request)
    {
        $itens = $this->buscarItens($request);
        $builder = new ItensPlanoBuilder();
        $builder->setDados($itens);
        $dados = $builder->build();
        $pdfItensPlano = new ItensPlanoPDF();
        $pdfItensPlano->addDados($dados);
        return $pdfItensPlano;
    }
}
