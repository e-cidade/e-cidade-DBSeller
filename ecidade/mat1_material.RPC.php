<?php

use App\Domain\Saude\Farmacia\Helpers\FarmaciaHelper;
use ECidade\Patrimonial\Material\Helpers\Material;
use ECidade\Patrimonial\Material\Repositories\DepositoRepository;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

$parametros = JSON::requestParameters();
$retorno = (object)['erro' => false, 'mensagem' => ''];

require_once(modification("classes/materialestoque.model.php"));
$instituicao = db_getsession("DB_instit");
$anousu = db_getsession("DB_anousu");

db_inicio_transacao();

try {
    switch ($parametros->acao) {
        case 'buscarEstoques':
            if (empty($parametros->codigo_material)) {
                throw new Exception("Informe o código do material.");
            }
            $whereDeposito = "";
            if (!empty($parametros->deposito)) {
                $whereDeposito = " and m91_codigo = {$parametros->deposito} ";
            }
            $codigoMaterial = $parametros->codigo_material;

            $sSqlTotaisTransferencias = "select sum(coalesce(case when m81_tipo = 4 then m82_quant end, 0)) as saida";
            $sSqlTotaisTransferencias .= "  from matestoqueinimei ";
            $sSqlTotaisTransferencias .= "       inner join matestoqueitem on m71_codlanc       = m82_matestoqueitem";
            $sSqlTotaisTransferencias .= "       inner join matestoque trans  on m71_codmatestoque = trans.m70_codigo ";
            $sSqlTotaisTransferencias .= "       inner join matestoqueini  on m80_codigo        = m82_matestoqueini ";
            $sSqlTotaisTransferencias .= "       left  join matestoqueinil on m80_codigo        = m86_matestoqueini ";
            $sSqlTotaisTransferencias .= "       inner join matestoquetipo on m80_codtipo       = m81_codtipo ";
            $sSqlTotaisTransferencias .= " where trans.m70_codigo  = matestoque.m70_codigo ";
            $sSqlTotaisTransferencias .= "   and m81_codtipo = 7";
            $sSqlTotaisTransferencias .= "   and m86_matestoqueini IS NULL";

            $sSql = "
        SELECT m91_codigo,
               m91_depto,
               descrdepto as descricao_deposito,
               m85_precomedio,
               m70_quant as m70_quant,
               (m70_quant * m85_precomedio) as m70_valor,
               dl_transferencias,
               m70_quant - dl_transferencias as dl_saldo_disponivel
        FROM (
            SELECT  m91_codigo,
                    m91_depto,
              descrdepto,
                   (select m85_precomedio
                        from matmaterprecomedio
                            where m85_precomedio > 0
                              and to_timestamp(m85_data || ' ' || m85_hora, 'YYYY-MM-DD HH24:MI:SS') < current_timestamp
                              and m85_matmater = m70_codmatmater
                              and m85_coddepto = m70_coddepto
                            order by to_timestamp(m85_data || ' ' || m85_hora, 'YYYY-MM-DD HH24:MI:SS') desc
                            limit 1) as m85_precomedio,
              (
                  Coalesce(Sum(CASE
                          WHEN matestoquetipo.m81_tipo = 1 THEN matestoqueinimei.m82_quant
                      end), 0) -
                  Coalesce(Sum(CASE
                              WHEN matestoquetipo.m81_tipo = 2 THEN m82_quant
                              end), 0)
              ) as m70_quant,
              coalesce(({$sSqlTotaisTransferencias}),0) as dl_transferencias
            FROM   matestoqueini
            INNER JOIN matestoquetipo ON m80_codtipo = m81_codtipo
            INNER JOIN matestoqueinimei ON m82_matestoqueini = m80_codigo
            left JOIN matestoqueinimeipm ON m82_codigo = m89_matestoqueinimei
            INNER JOIN matestoqueitem ON m82_matestoqueitem = m71_codlanc
            INNER JOIN matestoque ON m71_codmatestoque = m70_codigo
            INNER JOIN db_depart ON coddepto = m70_coddepto
            INNER JOIN db_departorg on db_departorg.db01_coddepto = db_depart.coddepto
                                        and db_departorg.db01_anousu = {$anousu}
            INNER JOIN orcunidade ON orcunidade.o41_orgao = db_departorg.db01_orgao
                                        and orcunidade.o41_unidade = db_departorg.db01_unidade
                                        and orcunidade.o41_anousu = db_departorg.db01_anousu
                                        and orcunidade.o41_instit = {$instituicao}
            INNER JOIN orcorgao on orcorgao.o40_orgao = orcunidade.o41_orgao
                                        and orcorgao.o40_anousu = orcunidade.o41_anousu
            INNER JOIN material.db_almox ON db_almox.m91_depto = db_depart.coddepto
                WHERE m70_codmatmater = {$codigoMaterial} {$whereDeposito}
            GROUP BY m91_codigo, descrdepto, m70_codigo
        ) as x
          ";
            $rs = db_query($sSql);
            if (!$rs) {
                throw new Exception("Erro ao buscar dados do estoque");
            }

            $retorno->estoques = [];
            while ($estoque = pg_fetch_array($rs)) {
                $transferencias = [];

                $whereTransferencia = [];
                $whereTransferencia[] = "matestoque.m70_codmatmater = {$codigoMaterial}";
                $whereTransferencia[] = "matestoque.m70_coddepto = {$estoque['m91_depto']}";
                $whereTransferencia[] = "matestoquetransferencia.m84_ativo is true";
                $whereTransferencia[] = "matestoquetransferencia.m84_transferido is false";
                $campos = "m84_sequencial, m84_coddepto, m84_quantidade, m80_codigo";

                $daoMatEstoqueTransferencia = new cl_matestoquetransferencia();
                $sqlTransferencias = $daoMatEstoqueTransferencia->sql_query_transferencia(
                    null,
                    $campos,
                    null,
                    implode(' and ', $whereTransferencia)
                );

                $rsTransferencias = db_query($sqlTransferencias);

                if (!$rsTransferencias) {
                    throw new Exception("Erro ao buscar transferências.");
                }

                $depositoRepository = new DepositoRepository();

                $transferencias = [];
                while ($linha = pg_fetch_object($rsTransferencias)) {
                    $depositoDestino = $depositoRepository->scopeDepartamento($linha->m84_coddepto)->first();
                    if (!array_key_exists($linha->m80_codigo, $transferencias)) {
                        $transferencias[$linha->m80_codigo] = (object)[
                            "codigo" => $linha->m80_codigo,
                            "codigo_deposito_destino" => $depositoDestino->getCodigo(),
                            "descricao_deposito_destino" => $depositoDestino->getDepartamento()->getNomeDepartamento(),
                            "quantidade_transferida" => $linha->m84_quantidade
                        ];
                        continue;
                    }
                    $transferencias[$linha->m80_codigo]->quantidade_transferida += $linha->m84_quantidade;
                }

                $retorno->estoques[] = (object)[
                    "codigo_deposito" => $estoque['m91_codigo'],
                    "descricao_deposito" => $estoque['descricao_deposito'],
                    "preco_medio" => db_formatar($estoque['m85_precomedio'], 'f'),
                    "quantidade_total" => Material::arredondarQuantidade($estoque['m70_quant']),
                    "valor_estoque" => db_formatar($estoque['m70_valor'], 'f'),
                    "transferencias" => array_values($transferencias),
                    "quantidade_disponivel" => Material::arredondarQuantidade($estoque['dl_saldo_disponivel'])
                ];
            }

            $retorno->mensagem = "Lista de estoques";
            break;
        case 'buscarLotes':
            if (empty($parametros->codigoMaterial)) {
                throw new Exception("Informe o código do material");
            }
            if (empty($parametros->codigoDeposito)) {
                throw new Exception("Informe o código do depósito de origem");
            }

            $quantidade = 0;
            if (!empty($parametros->quantidade)) {
                $quantidade = $parametros->quantidade;
            }
            $oMaterialEstoque = new materialEstoque($parametros->codigoMaterial);

            $daoDeposito = new cl_db_almox();
            $sqlDeposito = $daoDeposito->sql_query_file($parametros->codigoDeposito);
            $rsDeposito = db_query($sqlDeposito);
            if (!$rsDeposito) {
                throw new Exception("Erro ao buscar Depósito");
            }
            $codigoDepartamento = pg_fetch_object($rsDeposito)->m91_depto;

            $retorno->itens = $oMaterialEstoque->ratearLotes($quantidade, null, $codigoDepartamento);
            break;
        case 'efetuarTransferencia':
            if (empty($parametros->depositoDestino)) {
                throw new Exception("Informe o depósito de destino.");
            }
            $materiais = JSON::create()->parse($parametros->materiais);
            if (empty($materiais)) {
                throw new Exception("Nenhum material informado.");
            }
            if (empty($parametros->observacao)) {
                throw new Exception("Informe a Observação.");
            }

            $depositoDestino = $parametros->depositoDestino;

            $departamentoOrigem = db_getsession("DB_coddepto");
            $departamentoDestino = DepositoRepository::find($parametros->depositoDestino)->getDepartamento();
            $iMatestoqueini = '';
            $itensInconsistententes = [];

            $utilizaBnafar = FarmaciaHelper::utilizaIntegracaoBnafar();
            foreach ($materiais as $material) {
                $oMaterialEstoque = new materialEstoque($material->codigo_material);
                $isMaterialFarmacia = $utilizaBnafar && FarmaciaHelper::isMaterialFarmacia($material->codigo_material);
                foreach ($material->lotes as $lote) {
                    $materialEstoqueItem = new MaterialEstoqueItem($lote->m71_codlanc);
                    /**
                     * Caso seja um material da fármacia, valida se a quantidade não está fracionada.
                     */
                    if ($isMaterialFarmacia) {
                        FarmaciaHelper::validaQuantidade($lote->rateio);
                    }

                    $quantidadeSaida = (int)$lote->rateio;
                    if ($quantidadeSaida > $materialEstoqueItem->getSaldo()) {
                        if (!in_array($lote->m70_codmatmater, $itensInconsistententes)) {
                            $itensInconsistententes[] = $lote->m70_codmatmater;
                        }
                    }
                }

                $iMatestoqueini = $oMaterialEstoque->transferirMaterial(
                    $material->quantidade_total,
                    $departamentoOrigem,
                    $departamentoDestino->getCodigo(),
                    $iMatestoqueini,
                    $parametros->observacao,
                    null,
                    $material->lotes
                );
            }

            if (count($itensInconsistententes) > 0) {
                $itensInconsistententes = implode(', ', $itensInconsistententes);
                throw new Exception("Saldo inconsistente para os itens: {$itensInconsistententes}.\n
                Por favor, consulte o material e verifique os lançamentos, se aplicável exclua e inclua novamente!");
            }

            $retorno->mensagem = "Transferência realizada com sucesso.\nCódigo da transferência: {$iMatestoqueini}";
            $retorno->codigo_matestoqueini = $iMatestoqueini;
            break;
        case 'buscarDadosTransferencia':
            if (empty($parametros->codigo_transferencia)) {
                throw new Exception("Informe o código da transferência.");
            }

            $daoMatestoqueini = new cl_matestoqueini();
            $sqlTransferencia = "select transfere.m80_obs as observacao, *
                from matestoqueini transfere
                         inner join db_usuarios on db_usuarios.id_usuario = transfere.m80_login
                         inner join matestoquetransf on matestoquetransf.m83_matestoqueini = transfere.m80_codigo
                         inner join db_depart as b on b.coddepto = transfere.m80_coddepto
                         inner join matestoquetipo on matestoquetipo.m81_codtipo = transfere.m80_codtipo
                         inner join db_depart as a on a.coddepto = matestoquetransf.m83_coddepto
                where transfere.m80_codigo = {$parametros->codigo_transferencia} ";

            $rsTransferencia = db_query($sqlTransferencia);
            if (!$rsTransferencia) {
                throw new Exception("Erro ao buscar Dados da transferência");
            }
            $transferencia = pg_fetch_object($rsTransferencia);
            $depositoRepository = new DepositoRepository();
            $depositoDestino = $depositoRepository->scopeDepartamento($transferencia->coddepto)->first();
            $retorno->deposito_destino = (object)[
                "codigo" => $depositoDestino->getCodigo(),
                "descricao" => $depositoDestino->getDepartamento()->getCodigo()
            ];
            $retorno->observacao = $transferencia->observacao;

            $daoMatestoqueinimei = new cl_matestoqueinimei();
            $sql = $daoMatestoqueinimei->sql_query_matestoque(
                null,
                "m71_codlanc,
                        m71_quant,
                        m71_valor,
                        m71_codmatestoque,
                        m60_descr,
                        m70_codmatmater,
                        m77_lote,
                        m77_dtvalidade,
                        m71_quantatend,
                        m77_sequencial,
                        0 as saldo,
                        m70_quant,
                        m70_valor,
                        m82_quant as rateio",
                null,
                "matestoqueini.m80_codigo={$parametros->codigo_transferencia}"
            );
            $rs = db_query($sql);
            if (!$rs) {
                throw new Exception("Erro ao buscar Dados da Transferência");
            }

            $itens = [];
            while ($item = pg_fetch_object($rs)) {
                if (!array_key_exists($item->m70_codmatmater, $itens)) {
                    $itens[$item->m70_codmatmater] = (object)[
                        "codigo_material" => $item->m70_codmatmater,
                        "descricao_material" => $item->m60_descr,
                        "lotes" => [],
                        "quantidade_total" => $item->rateio,
                    ];
                }
                $itens[$item->m70_codmatmater]->lotes[] = $item;
            }
            $retorno->itens = array_values($itens);
            $retorno->codigo_matestoqueini = $parametros->codigo_transferencia;

            break;
    }
} catch (Exception $erro) {
    $retorno->mensagem = $erro->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);
