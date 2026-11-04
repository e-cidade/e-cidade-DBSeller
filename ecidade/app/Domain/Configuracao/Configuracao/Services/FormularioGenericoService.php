<?php


namespace App\Domain\Configuracao\Configuracao\Services;

use ECidade\Configuracao\FormularioGenerico\FormularioTransformer;
use ECidade\Configuracao\Formulario\Resposta\Repository\Resposta;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Domain\Configuracao\Configuracao\Model\FormularioGenerico;
use Symfony\Component\HttpFoundation\ParameterBag;
use League\Fractal;
use ECidade\Configuracao\FormularioGenerico\Page;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;
use ECidade\Configuracao\FormularioGenerico\Paginator;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;


class FormularioGenericoService extends FormularioGenerico{


    const FILTRO_INSTITUICAO = 'instituicao';
    
    public function __construct($filters,$page){

        $filters = ($filters == "null") ? array() : $filters;
        $this->filters = new ParameterBag($filters);
        $this->fields = new ParameterBag(array());

        if (!empty($page)) {
            $this->page = new Page();
            $this->page->setNumber($page["number"]);
            $this->page->setSize($page["size"]);
        }
    }

    public function getAll($id, $instituicao = null)
    {
        $offsetLimit  = null;
        if (!empty($this->page)) {
            $offSet = $this->page->getNumber() <= 1 ? 0 : ($this->page->getSize()) * ($this->page->getNumber() - 1);
            $offsetLimit = " limit {$this->page->getSize()} offset {$offSet};";
        }

        $formulario = \ECidade\Configuracao\Formulario\Repository\Formulario::getById($id);
        if (empty($formulario)) {
            throw new NotFoundHttpException("Formulário {$id} não encontrado.");
        }

        $dados = new \stdClass();
        $dados->fields = array();
        $dados->respostas =array();
        $campoId  = new \stdClass();
        $campoId->identificador = "id_grupo_resposta";
        $campoId->descricao = "Resposta";
        $campoId->tipo = 1;
        $dados->fields[] = $campoId;
        $iColunas = 1;
        $listaColunas = array();
        $filtrarPorInstituicao = false;

        foreach ($formulario->getPerguntasIdentificadoras() as $perguntaIdentificadora) {
            if ($perguntaIdentificadora->getIdentificadorCampo() == self::FILTRO_INSTITUICAO) {
                $filtrarPorInstituicao = true;
            }
        }

        foreach ($formulario->getPerguntas() as $pergunta) {
            if ($iColunas > 5) {
                break;
            }

            $listaColunas[] = $pergunta->getIdentificador();
            $campo = new \stdClass();
            $campo->identificador = $pergunta->getIdentificador();
            $campo->descricao     = $this->removeHtmlContent($pergunta->getDescricao());
            $campo->tipo          = $pergunta->getTipo();
            $dados->fields[]      = $campo;
            $iColunas++;
        }

        $oDaoAvaliacaoResposta = new \cl_avaliacaogrupoperguntaresposta;

        $aWhere   = array("db101_sequencial = {$formulario->getCodigo()}");
        if ($this->filters->has("filtro")) {
            $filtro = $this->filters->get("filtro");

            $aWhere[] = "(case when db103_avaliacaotiporesposta in(1,3) then (db106_avaliacaoperguntaopcao = " . ((int) $filtro) . " or db104_descricao ilike '%{$filtro}%')
                        else db106_resposta ilike '%{$filtro}%' end) ";
        }

        $sSqlRespostas  = $oDaoAvaliacaoResposta->sql_query_avaliacao(
            null,
            "distinct db107_sequencial, db107_usuario, db107_datalancamento, db107_hora",
            null,
            implode(" and ", $aWhere)
        );

        if ($filtrarPorInstituicao && !empty($instituicao)) {

            $sSqlRespostas  = "select * from ($sSqlRespostas) as x";
            $sSqlRespostas .= " where exists(select 1
                from
                    avaliacaogruporesposta as a
                    join avaliacaogrupoperguntaresposta as b on a.db107_sequencial = b.db108_avaliacaogruporesposta
                    join avaliacaoresposta as c on c.db106_sequencial = b.db108_avaliacaoresposta
                    join avaliacaoperguntaopcao as d on d.db104_sequencial = c.db106_avaliacaoperguntaopcao
                    join avaliacaopergunta as e on e.db103_sequencial = d.db104_avaliacaopergunta
                where a.db107_sequencial = x.db107_sequencial
                    and e.db103_identificadorcampo = '". self::FILTRO_INSTITUICAO ."'
                    and c.db106_resposta = '{$instituicao}')" ;
        }

        if (!empty($this->page)) {
            $rsTotal = db_query(sprintf("select count(*) as total from (%s) as total", $sSqlRespostas));
            if (!$rsTotal) {
                throw new \BusinessException("Erro ao buscar total de respostas do formulário {$formulario->getNome()}.");
            }
            $this->page->setTotal(ceil(pg_fetch_result($rsTotal, 0, 'total') / $this->page->getSize()));
        }

        $sSqlRespostas .= " order by db107_sequencial {$offsetLimit}";

        $rsRespostas = db_query($sSqlRespostas);
        if (!$rsRespostas) {
            throw new \BusinessException("Erro ao pesquisar as respostas do formulário {$formulario->getNome()}.");
        }

        $dados->respostas = \db_utils::makeCollectionFromRecord($rsRespostas, function ($dados) use ($formulario, $listaColunas) {

            $oResposta = Resposta::make($dados, $formulario);
            $oRespostaRetorno = new \stdClass();
            $oRespostaRetorno->id_grupo_resposta = $oResposta->getCodigo();

            foreach ($oResposta->getRespostas() as $valorResposta) {
                if (!in_array($valorResposta->getPergunta()->getIdentificador(), $listaColunas)) {
                    continue;
                }

                $valor = $valorResposta->getValor();
                $pergunta = $valorResposta->getPergunta();
                if (in_array($pergunta->getTipoResposta(), array(1,3))) {
                    $valor = $valorResposta->getOpcao()->getDescricao();
                }
                if ($valorResposta->getPergunta()->getIdentificador() == '') {
                    continue;
                }

                /**
                 * formatar campos:
                 */
                if ($pergunta->getTipo() == 5) {
                    $valor = db_formatar(substr($valor, 0, 10), 'd');
                }
                $oRespostaRetorno->{$pergunta->getIdentificador()} = $valor;
            }

            return $oRespostaRetorno;
        });
        /**
         * Ordernar os campos
         */
        $perguntasIdentificadoras = $formulario->getPerguntasIdentificadoras();
        $respostas = $dados->respostas;
        if (count($perguntasIdentificadoras) > 0) {
            usort($respostas, function ($atual, $anterior) use ($perguntasIdentificadoras) {

                $identificador = $perguntasIdentificadoras[0]->getIdentificador();
                if (isset($atual->{$identificador})) {
                    return strcasecmp($atual->{$identificador}, $anterior->{$identificador});
                }
                return 1;
            });
        }

        $dados->respostas  = $respostas;
        $transformer = new FormularioTransformer();
        $transformer->setFields($this->fields);
        $result = new Fractal\Resource\Collection($dados->respostas, $transformer);
        $result->setMetaValue('fields', $dados->fields);

        return $this->format($result);
    }

    protected function removeHtmlContent($text, $tags = '', $invert = false)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) && count($tags) > 0) {
            if (!$invert) {
                return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif (!$invert) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }

        return $text;
    }

    protected function format($data)
    {
        $fractal = new Fractal\Manager();
        $fractal->setSerializer(new DataArraySerializer());

        // define a paginacao do resource
        if ($data instanceof Collection && $this->page instanceof Page) {
            $data->setPaginator(new Paginator($this->page, $data->getData()));
        }

        $output = $fractal->createData($data)->toArray();

        return new DBJsonResponse($output);
    }



}