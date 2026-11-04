<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use cl_termo;
use cl_termoreparc;
use DBException;
use ECidade\Tributario\Caixa\Model\Arrepaga;
use ECidade\Tributario\Caixa\Repository\ArrepagaRepository;
use ECidade\Tributario\Divida\Factory\TermoOrigemRepositoryFactory;
use ECidade\Tributario\Divida\Termo\Termo as Entity;
use ECidade\Tributario\Divida\Termo\Repository\TermoInicial as TermoInicialRepository;
use ECidade\Tributario\Divida\Divida;
use Exception;

/**
 * Repository para opera��es em termo de parcelamento.
 *
 * @method static Termo getInstance()
 *
 * @author Leonardo Oliveira <leonardo.malia@dbseller.com.br>
 */
class TermoRepository extends \BaseClassRepository
{
    /**
     * @var bool
     */
    private $returnFullItem;

    /**
     * @var bool
     */
    private $persistPropagation;

    protected static $oInstance;

    private $scopes;

    /**
     * @param Entity $entity
     *
     * @return Entity
     *
     * @throws Exception
     */
    public function persist(Entity $entity)
    {
        $dao = new \cl_termo();

        $dataLancamento = $entity->getDataLancamento();
        if (!empty($dataLancamento)) {
            $dao->v07_dtlanc = $dataLancamento->format('Y-m-d');
        }

        $dao->v07_valor = $entity->getValor();
        $dao->v07_numpre = $entity->getNumpre();
        $dao->v07_totpar = $entity->getTotalParcelas();
        $dao->v07_vlrpar = $entity->getValorParcela();

        $dataVencimento = $entity->getDataVencimento();
        if (!empty($dataVencimento)) {
            $dao->v07_dtvenc = $dataVencimento->format('Y-m-d');
        }

        $dao->v07_vlrent = $entity->getValorEntrada();

        $dataPrimeiraParcela = $entity->getDataPrimeiraParcela();
        if (!empty($dataPrimeiraParcela)) {
            $dao->v07_datpri = $dataPrimeiraParcela->format('Y-m-d');
        }

        $dao->v07_vlrmul = $entity->getValorMulta();
        $dao->v07_vlrjur = $entity->getValorJuros();
        $dao->v07_perjur = $entity->getPercentualJuros();
        $dao->v07_permul = $entity->getPercentualMulta();
        $dao->v07_login = $entity->getLogin();
        $dao->v07_mtermo = $entity->getTextoTermo();
        $dao->v07_numcgm = $entity->getCgm();
        $dao->v07_hist = $entity->getHistorico();
        $dao->v07_ultpar = $entity->getValorUltimaParcela();
        $dao->v07_desconto = $entity->getDesconto();
        $dao->v07_descjur = $entity->getDescontoJuros();
        $dao->v07_descmul = $entity->getDescontoMulta();
        $dao->v07_situacao = $entity->getSituacao();
        $dao->v07_instit = $entity->getInstituicao();
        $dao->v07_vlrhis = $entity->getValorHistorico();
        $dao->v07_vlrcor = $entity->getValorCorrigido();
        $dao->v07_vlrdes = $entity->getValorDesconto();

        $codigo = $entity->getCodigo();

        if (empty($codigo)) {
            $dao->incluir(null);
            $entity->setCodigo($dao->v07_parcel);
        } else {
            $dao->v07_parcel = $codigo;
            $dao->alterar($dao->v07_parcel);
        }

        if ($dao->erro_status == 0) {
            throw new Exception($dao->erro_msg);
        }

        if ($this->isPersistPropagation() && $entity->getTermoIniciais()) {
            $termoInicialRepository = TermoInicialRepository::getInstance()
                ->setPersistPropagation(true);

            foreach ($entity->getTermoIniciais() as $termoInicial) {
                $termoInicialRepository->persist($termoInicial, $dao->v07_parcel);
            }
        }

        return $entity;
    }

    /**
     * @param \stdClass $data
     *
     * @return Entity
     * @throws Exception
     */
    protected function make($data)
    {
        $entity = new Entity();
        $entity
            ->setCodigo($data->v07_parcel)
            ->setDataLancamento(new \DateTime($data->v07_dtlanc))
            ->setValor($data->v07_valor)
            ->setNumpre($data->v07_numpre)
            ->setTotalParcelas($data->v07_totpar)
            ->setValorParcela($data->v07_vlrpar)
            ->setDataVencimento(new \DateTime($data->v07_dtvenc))
            ->setValorEntrada($data->v07_vlrent)
            ->setDataPrimeiraParcela(new \DateTime($data->v07_datpri))
            ->setValorMulta($data->v07_vlrmul)
            ->setValorJuros($data->v07_vlrjur)
            ->setPercentualJuros($data->v07_perjur)
            ->setPercentualMulta($data->v07_permul)
            ->setLogin($data->v07_login)
            ->setTextoTermo($data->v07_mtermo)
            ->setCgm($data->v07_numcgm)
            ->setHistorico($data->v07_hist)
            ->setValorUltimaParcela($data->v07_ultpar)
            ->setDesconto($data->v07_desconto)
            ->setDescontoJuros($data->v07_descjur)
            ->setDescontoMulta($data->v07_descmul)
            ->setSituacao($data->v07_situacao)
            ->setInstituicao($data->v07_instit)
            ->setValorHistorico($data->v07_vlrhis)
            ->setValorCorrigido($data->v07_vlrcor)
            ->setValorDesconto($data->v07_vlrdes)
            ->setDescontoCorrigido($data->v07_desccor);

        if ($this->isReturnFullItem()) {
            $termoInicialRepository = TermoInicial::getInstance()
                ->setReturnFullItem(true);

            $termosIniciais = $termoInicialRepository->getByTermo($data->v07_parcel);

            if (!empty($termosIniciais)) {
                foreach ($termosIniciais as $termoInicial) {
                    $entity->addTermoInicial($termoInicial);
                }
            }
        }

        return $entity;
    }

    /**
     * @return bool
     */
    public function isReturnFullItem()
    {
        return $this->returnFullItem;
    }

    /**
     * @param  bool $returnFullItem
     * @return Termo
     */
    public function setReturnFullItem($returnFullItem)
    {
        $this->returnFullItem = $returnFullItem;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPersistPropagation()
    {
        return $this->persistPropagation;
    }

    /**
     * @param  bool $persistPropagation
     * @return Termo
     */
    public function setPersistPropagation($persistPropagation)
    {
        $this->persistPropagation = $persistPropagation;
        return $this;
    }

    /**
     * @param  Entity $termo
     * @return Arrepaga[]
     * @throws Exception
     */
    public function getPagamentosPorTermo(Entity $termo)
    {
        $pagamentos = ArrepagaRepository::getInstance()
            ->scopeNumpre($termo->getNumpre())
            ->get();
        return $pagamentos;
    }

    /**
     * @param integer $code
     *
     * @return Entity|null
     *
     * @throws Exception
     */
    public function getByCode($code)
    {
        $dao = new \cl_termo();
        $sql = $dao->sql_query($code);

        $result = \db_query($sql);

        if (!$result) {
            throw new Exception('N�o foi poss�vel consultar o termo');
        }

        if (!pg_num_rows($result)) {
            return null;
        }

        return $this->make(pg_fetch_object($result, 0))->withPagamentos()->withUltimoPagamentoEfetivo();
    }

    /**
     *
     * Fun��o criada como um alias para getByCode para manter o padr�o dos outros repositorys
     *
     * @param integer $code
     *
     * @return Entity|null
     *
     * @throws Exception
     */
    public function find($codigo)
    {
        return $this->getByCode($codigo);
    }

    /**
     *
     * Fun�~ao que busca uma colection de parcelamentos
     *
     * @param  Integer[]
     * @return Entity[]
     * @throws Exception
     */
    public function getParcelamentosPorNumpre(array $numpres)
    {
        $parcelamentos = array();
        $dao = new \cl_termo();
        $sql = $dao->sql_query_file(null, "DISTINCT *", null, "v07_numpre in (".implode(', ', $numpres).")");

        $result = \db_query($sql);

        if (!$result) {
            throw new Exception('N�o foi poss�vel consultar o termo.');
        }

        if (!pg_num_rows($result)) {
            return null;
        }

        while ($parcelamento = pg_fetch_object($result)) {
            $parcelamentos[] = $this->make($parcelamento)->withPagamentos()->withUltimoPagamentoEfetivo();
        }

        return $parcelamentos;
    }

    /**
     * @param integer $numpre
     *
     * @return Entity|null
     *
     * @throws Exception
     */
    public function getByNumpre($numpre)
    {
        $dao = new \cl_termo();
        $sql = $dao->sql_query_file(null, "*", null, "v07_numpre = $numpre");

        $result = \db_query($sql);

        if (!$result) {
            throw new Exception('N�o foi poss�vel consultar o termo');
        }

        if (!pg_num_rows($result)) {
            return null;
        }

        return $this->make(pg_fetch_object($result, 0))->withPagamentos()->withUltimoPagamentoEfetivo();
    }

    /**
     * @param  Entity $reparcelamento
     * @return Entity
     * @throws Exception
     */
    public function buscaPrimeiroParcelamento(Entity $reparcelamento)
    {
        $termoRepository = Termo::getInstance();
        $primeiroParcelamento = $reparcelamento;
        $possuiReparcelamento = true;

        $dao = new cl_termoreparc();

        while ($possuiReparcelamento) {
            $where = " v08_parcel = {$primeiroParcelamento->getCodigo()} ";
            $campos = 'termo.v07_parcel as parcelamento, termo_origem.v07_parcel as parcelamento_origem';
            $sql = $dao->sql_query_termos(null, $campos, null, $where);
            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception('N�o foi poss�vel buscar o parcelamento de origem.');
            }

            if (pg_num_rows($rs) > 0) {
                $fetchDados = pg_fetch_object($rs);
                $primeiroParcelamento = $termoRepository
                    ->getByCode($fetchDados->parcelamento_origem)
                    ->withPagamentos()
                    ->withUltimoPagamentoEfetivo();
            } else {
                $possuiReparcelamento = false;
            }
        }

        return $primeiroParcelamento;
    }

    /**
     * @param  Entity $parcelamento
     * @return null|boolean
     * @throws DBException
     * @throws Exception
     */
    public function adicionarObservacaoOrigem(Entity $parcelamento)
    {
        if (!empty($repository)) {
            return $repository->atualizarObservacaoOrigemPorNumpreAoAnular($numpres, $parcelamento);
        }
        return null;
    }

    private function getOrigemNumpresAndRepository(Entity $parcelamento)
    {
        $primeiroParcelamento = $this->buscaPrimeiroParcelamento($parcelamento);

        $dao = new cl_termo();
        $sqlTabelaOrigem = $dao->sql_query_tabela_origem_parcelamento($primeiroParcelamento->getCodigo());
        $rsTabelaOrigem = db_query($sqlTabelaOrigem);

        if (!$rsTabelaOrigem) {
            throw new DBException('Erro ao buscar tipo de origem do parcelamento');
        }

        if (pg_num_rows($rsTabelaOrigem) === 0) {
            throw new DBException('Tipo de parcelamento n�o encontrado.');
        }

        $tipoParcelamento = pg_fetch_object($rsTabelaOrigem);

        $tiposParcelamento = TermoOrigemRepositoryFactory::getTipoPorTabela();
        $tipo = $tiposParcelamento[$tipoParcelamento->tabela];

        $sqlOrigem = $dao->sql_query_origem($primeiroParcelamento->getCodigo(), $tipo);
        $rsOrigem = db_query($sqlOrigem);

        if (!$rsOrigem) {
            throw new DBException("Erro ao buscar tipo de origem do parcelamento");
        }

        if (pg_num_rows($rsOrigem) === 0) {
            throw new DBException("Tipo de parcelamento n�o encontrado.");
        }

        $numpres = array();

        while ($origem = pg_fetch_object($rsOrigem)) {
            $numpres[] = $origem->k00_numpre;
        }

        $repository = TermoOrigemRepositoryFactory::get($tipoParcelamento->tabela);

        return array(
            'numpres' => $numpres,
            'repository' => $repository
        );
    }

    /**
     * Retorna o d�bito de origem do parcelamento
     *
     * @return Diversos|Inicial|Divida
     */
    public function getOrigem(Entity $parcelamento)
    {
        $dados = $this->getOrigemNumpresAndRepository($parcelamento);

        $repository = $dados['repository'];
        $numpres = $dados['numpres'];

        return $repository
            ->scopeNumpre('(' . implode(',', $numpres) . ')', 'in')
            ->get();
    }

    /**
     * @return Entity[]
     * @throws Exception
     */
    public function get($campos = array('*'))
    {
        $dao = new cl_termo();
        $sql = $dao->sql_query_file(
            null,
            implode(', ', $campos),
            null,
            implode(' AND ', $this->scopes)
        );
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("N�o foi poss�vel buscar os d�bitos.");
        }

        $this->scopes = array();

        $arrTermo = array();

        if (pg_num_rows($rs) === 0) {
            return $arrTermo;
        }

        while ($inicial = pg_fetch_array($rs)) {
            $arrTermo[] = Entity::fromState($inicial);
        }

        return $arrTermo;
    }

    /**
     * @return Entity|null
     */
    public function first()
    {
        $all = $this->get();

        if (empty($all)) {
            return null;
        }

        return $all[0];
    }

    /**
     * @param string $valor
     * @param string $operacao
     * @return $this
     */
    public function scopeNumpre($valor, $operacao = '=')
    {
        $this->scopes['numpre'] = "v07_numpre {$operacao} {$valor}";
        return $this;
    }
}
