<?php

use ECidade\Financeiro\Contabilidade\ContaCorrente\Services\Processamento;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Model\InformacaoComplementar;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Model\Lancamento as LancamentoModel;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Repository\Lancamento as LancamentoRepository;

/**
 * Class DBContaCorrenteAtributos
 *
 * @todo refatorar isso - desculpe
 */
class DBContaCorrenteAtributos
{
    /**
     * @param $clconlancamval
     * @param $codigoContaCorrente
     * @param $atributosIndexados
     * @param $contaDebito
     * @param $sinalNaturezaSalvar
     *
     * @throws DBException
     * @throws ParameterException
     */
    public static function salvarAtributos($clconlancamval, $codigoContaCorrente, $atributosIndexados, $contaDebito, $sinalNaturezaSalvar = null)
    {
        $dataSessao = new DBDate(date('Y-m-d', db_getsession('DB_datausu')));
        $competencia = new DBCompetencia($dataSessao->getAno(), $dataSessao->getMes());
        $instituicaoSessao = db_getsession('DB_instit');
        $contaParaBusca = $contaDebito ? $clconlancamval->c69_debito : $clconlancamval->c69_credito;
        if (empty($sinalNaturezaSalvar)) {
            $sinalNaturezaSalvar = $contaDebito ? 'D' : 'C';
        }

        $where = implode(' and ', array(
                "conplanoreduz.c61_reduz  = {$contaParaBusca}",
                "conplanoreduz.c61_anousu = " . db_getsession('DB_anousu'),
                "conplanoreduz.c61_instit = " . $instituicaoSessao,
                "conplanosistema.c122_sequencial = {$codigoContaCorrente}",
                /*"conplanosistema.c122_tipo = 2"*/
            )) . ' order by conplanosistemaatributos.c129_ordem ';

        $campos = implode(',', array(
            "c121_sequencial      as codigo_atributo",
            "c121_sigla           as sigla",
            "c121_nomepropriedade as nome_propriedade",
            "c121_descricao       as descricao",
            "c121_valorpadrao     as valor_padrao",
            "c120_conplanosistema as conta_corrente"
        ));
        $daoReduzidos = new cl_conplanoreduz();
        $consultaAtributos = $daoReduzidos->sql_query_infocomplementar_obrigatorias($campos, $where);
        $buscaAtributos = db_query($consultaAtributos);
        if (!$buscaAtributos) {
            throw new DBException('Ocorreu um erro ao consultar os atributos da conta contábil.');
        }

        $atributosDoLancamento = db_utils::makeCollectionFromRecord(
            $buscaAtributos,
            function ($stdAtributo) use (
                $clconlancamval,
                $atributosIndexados,
                $instituicaoSessao,
                $contaParaBusca,
                $contaDebito
            ) {

                $contaPlano = ContaPlanoPCASPRepository::getContaPorReduzido(
                    $contaParaBusca,
                    $clconlancamval->c69_anousu
                );
                $informacaoComplementar = new InformacaoComplementar();
                $informacaoComplementar->setSigla($stdAtributo->sigla);
                $informacaoComplementar->setDescricao($stdAtributo->descricao);
                $informacaoComplementar->setConta($contaPlano->getCodigoConta());
                $informacaoComplementar->setAnousu($clconlancamval->c69_anousu);
                $informacaoComplementar->setContaEstrutura($contaPlano->getEstrutural());
                $informacaoComplementar->setCodigoLancamento($clconlancamval->c69_codlan);
                $informacaoComplementar->setContaReduzida($contaParaBusca);
                $informacaoComplementar->setCodigoInstituicao($instituicaoSessao);
                $informacaoComplementar->setCodigoInformacaoComplementar($stdAtributo->codigo_atributo);
                $informacaoComplementar->setCodigoSistema($stdAtributo->conta_corrente);

                $valorAtributo = trim($atributosIndexados[$stdAtributo->sigla]);
                if ($valorAtributo == '') {
                    throw new Exception("Valor do atributo {$stdAtributo->sigla} não informado. Verifique.");
                }
                $informacaoComplementar->setValor($valorAtributo);

                return $informacaoComplementar;
            }
        );

        $lancamento = new LancamentoModel();
        $lancamento->setSistema($codigoContaCorrente);
        $lancamento->setData($dataSessao);
        $lancamento->setValor($clconlancamval->c69_valor);
        $lancamento->setCodigoLancamento($clconlancamval->c69_codlan);
        $lancamento->setNatureza($sinalNaturezaSalvar);
        $lancamento->setTipoLancamento(2);
        foreach ($atributosDoLancamento as $atributoLancamento) {
            $lancamento->addInfoComplementares($atributoLancamento);
        }

        $hash = Processamento::montarHashesDolancamento($lancamento);
        $chave = key($hash);
        $arrayProcessado = array();
        Processamento::calcularValorMovimentacaoDoHash(
            $hash[$chave],
            $lancamento->getValor(),
            $lancamento->getNatureza(),
            $arrayProcessado, $lancamento->getSistema()
        );
        $repository = LancamentoRepository::getInstance();
        Processamento::salvarLancamentos(array($lancamento), $repository);
        $chave = key($arrayProcessado);
        $registro = $arrayProcessado[$chave];
        Processamento::atualizaSaldoContaCorrente($registro, $registro->valor, $competencia->getAno(),
            $competencia->getMes(), $codigoContaCorrente, $competencia);

    }

    /**
     * @param cl_conlancamval $clconlancamval
     * @param integer $recurso deve ser o id do recurso
     * @param bool $contaDebito
     *
     * @return bool
     * @throws Exception
     */
    public static function salvarRecursoLancamento(
        $clconlancamval,
        $recurso,
        $contaDebito = true,
        $naturezaSalvar = null
    ) {

        if (empty($recurso)) {
            return false;
        }

        $recursoEcidade = RecursoRepository::getRecursoPorCodigo($recurso);
        if (empty($recursoEcidade)) {
            $mensagem = "O recurso com código {$recurso} não existe cadastrado no E-cidade.\n\n";
            $mensagem .= "Verifique o cadastro em: Orçamento > Cadastros > Tipos de Recursos";
            throw new Exception($mensagem);
        }

        if (empty($naturezaSalvar)) {
            $naturezaSalvar = $contaDebito ? 'D' : 'C';
        }

        $daoConlancamRecurso = new cl_conlancamrecurso();
        $daoConlancamRecurso->c130_sequencial = null;
        $daoConlancamRecurso->c130_conlancam = $clconlancamval->c69_codlan;
        $daoConlancamRecurso->c130_orctiporec = $recurso;
        $daoConlancamRecurso->c130_conta = $contaDebito ? $clconlancamval->c69_debito : $clconlancamval->c69_credito;
        $daoConlancamRecurso->c130_anousu = $clconlancamval->c69_anousu;
        $daoConlancamRecurso->c130_natureza = $naturezaSalvar;
        $daoConlancamRecurso->incluir(null);
        if ($daoConlancamRecurso->erro_status === "0") {
            throw new Exception("Não foi possivel incluir o recurso para conta.");
        }
    }

    /**
     * @param $codigoLancamento
     * @param $ano
     *
     * @return stdClass[]
     * @throws Exception
     */
    public static function getAtributosLancamento($codigoLancamento, $ano)
    {

        $sql = <<<SQL_BUSCA_LANCAMENTO

  select
         c124_natureza,
         c123_conplanosistema,
         c122_descricao,
         c123_reduzido,
         c60_codcon,
         c60_estrut,
         c60_descr,
         c124_tipo,
         c121_sequencial,
         c121_sigla,
         c121_descricao,
         c123_valor
    from conplanoatributolancamentos
         inner join infocomplementarvalor on infocomplementarvalor.c123_conplanoatributolancamentos = conplanoatributolancamentos.c124_sequencial
         inner join conplanoinfocomplementar on conplanoinfocomplementar.c121_sequencial = infocomplementarvalor.c123_infocomplementar
         inner join conplanosistema on conplanosistema.c122_sequencial = infocomplementarvalor.c123_conplanosistema
         inner join conplanoreduz on conplanoreduz.c61_reduz = infocomplementarvalor.c123_reduzido
                                 and conplanoreduz.c61_anousu = $ano
         inner join conplano on conplano.c60_codcon = conplanoreduz.c61_codcon
                            and conplano.c60_anousu = $ano
   where c124_lancamento = $codigoLancamento
     and c61_anousu = $ano
   order by c124_natureza, c122_sequencial, c123_infocomplementar

SQL_BUSCA_LANCAMENTO;
        $resBusca = db_query($sql);
        if (!$resBusca) {
            throw new Exception('Ocorreu um erro ao consultar os valores do conta corrente.');
        }

        $totalRegistros = pg_num_rows($resBusca);
        if ($totalRegistros === 0) {
            return;
        }

        $registros = [];
        for ($row = 0; $row < $totalRegistros; $row++) {

            $stdLinha = db_utils::fieldsMemory($resBusca, $row);
            if (empty($registros[$stdLinha->c124_natureza])) {

                $stdRegistro = (object)array(
                    'sinal' => $stdLinha->c124_natureza,
                    'conta_contabil' => (object)array(
                        'codigo' => $stdLinha->c60_codcon,
                        'reduzido' => $stdLinha->c123_reduzido,
                        'estrutural' => $stdLinha->c60_estrut,
                        'descricao' => $stdLinha->c60_descr
                    ),
                    'conta_corrente' => []
                );
                $registros[$stdLinha->c124_natureza] = $stdRegistro;
            }

            $linha = &$registros[$stdLinha->c124_natureza];
            if (empty($linha->conta_corrente[$stdLinha->c123_conplanosistema])) {

                $linha->conta_corrente[$stdLinha->c123_conplanosistema] = (object)array(
                    'codigo' => $stdLinha->c123_conplanosistema,
                    'descricao' => $stdLinha->c122_descricao,
                    'atributos' => []
                );
            }

            $valorAtributo = (object)array(
                "codigo" => $stdLinha->c121_sequencial,
                "descricao" => $stdLinha->c121_descricao,
                "sigla" => $stdLinha->c121_sigla,
                "valor" => $stdLinha->c123_valor
            );
            $linha->conta_corrente[$stdLinha->c123_conplanosistema]->atributos[] = $valorAtributo;
        }
        return array_values($registros);
    }
}
