<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Saude\Farmacia\Helpers\FarmaciaHelper;
use App\Domain\Saude\Farmacia\Models\BnafarConferencia;
use App\Domain\Saude\Farmacia\Models\BnafarErro;
use App\Domain\Saude\Farmacia\Models\BnafarInconsistencia;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class InconsistenciasBnafarService
{
    /**
     * @param object $dados
     * @throws \Exception
     */
    public function salvarMovimentacao($dados)
    {
        if (!FarmaciaHelper::utilizaIntegracaoBnafar()) {
            throw new \Exception('Parâmetro BNAFAR não está ativo.');
        }

        $medicamentos = json_decode(str_replace('\\', '', utf8_encode($dados->medicamentos)));
        if (property_exists($dados, 'tipoMovimentacao')) {
            EstoqueMovimentacaoBnafarService::salvar((object)[
                'tipoMovimentacao' => $dados->tipoMovimentacao,
                'cgm' => property_exists($dados, 'cgm') ? $dados->cgm : '',
                'unidade' => property_exists($dados, 'unidade') ? $dados->unidade : '',
            ], $dados->lancamento);
        }
        if (property_exists($dados, 'notaFiscal')) {
            $this->salvarNotaFiscal($dados->notaFiscal, $dados->dataNotaFiscal, $medicamentos);
        }
        if (property_exists($dados, 'cnsPaciente') || property_exists($dados, 'cpfPaciente')) {
            $this->salvarDadosPaciente($dados);
        }

        $this->salvarDadosMedicamentos($medicamentos);
        $this->salvarConferencia($dados->lancamento, $dados->DB_id_usuario);
        $this->excluirErros($dados->lancamento);
    }

    /**
     * @param array $medicamentos
     * @throws \Exception
     */
    public function salvarDadosMedicamentos(array $medicamentos)
    {
        foreach ($medicamentos as $medicamento) {
            try {
                $this->salvarLoteMedicamento(
                    $medicamento->estoqueItem,
                    $medicamento->lote->valor,
                    $medicamento->validade->valor
                );
                $this->salvarFabricanteMedicamento($medicamento->estoqueItem, $medicamento->fabricante->valor);
            } catch (\Exception $e) {
                throw new \Exception("Medicamento: {$medicamento->descricao}\n{$e->getMessage()}");
            }
        }
    }

    /**
     * @param integer $idLancamento
     * @param integer $idUsuario
     */
    private function salvarConferencia($idLancamento, $idUsuario)
    {
        $model = BnafarInconsistencia::join('bnafarenvios', 'fa70_id', 'fa71_bnafarenvio')
            ->where('fa70_matestoqueini', $idLancamento)
            ->whereNotExists(function (Builder $query) {
                $query->select(DB::raw(1))
                    ->from('bnafarconferencias')
                    ->whereRaw('fa72_bnafarinconsistencia = fa71_id');
            })->first();
        if ($model === null) {
            return;
        }
        $idInconsistencia = $model->fa71_id;

        $model = new BnafarConferencia();
        $model->fa72_bnafarinconsistencia = $idInconsistencia;
        $model->fa72_usuario = $idUsuario;
        $model->save();
    }

    /**
     * @param string $nota
     * @param string $data
     * @param array $medicamentos
     * @throws \Exception
     */
    private function salvarNotaFiscal($nota, $data, array $medicamentos)
    {
        foreach ($medicamentos as $medicamento) {
            $dao = new \cl_matestoqueitemnota();
            $sql = $dao->sql_query_file($medicamento->estoqueItem, null, 'm74_codempnota');
            $rs = $dao->sql_record($sql);
            if ($dao->numrows > 0) {
                $id = \db_utils::fieldsMemory($rs, 0)->m74_codempnota;
                $this->alterarNotaEmpenho($id, $nota, $data);
                continue;
            }

            $dao = new \cl_matestoqueitemnotafiscalmanual();

            $dao->m79_notafiscal = $nota;
            $dao->m79_data = $data;
            $dao->m79_matestoqueitem = $medicamento->estoqueItem;

            $where = "m79_matestoqueitem = {$medicamento->estoqueItem}";
            $sql = $dao->sql_query_file(null, 'm79_sequencial', null, $where);
            $rs = $dao->sql_record($sql);
            if ($dao->numrows > 0) {
                $id = \db_utils::fieldsMemory($rs, 0)->m79_sequencial;
                $dao->m79_sequencial = $id;
                $dao->alterar($id);
            } else {
                $dao->incluir(null);
            }

            if ($dao->erro_status == '0') {
                throw new \Exception('Erro ao salvar nota fiscal manual.');
            }
        }
    }

    /**
     * @param object $dados
     * @return void
     * @throws \Exception
     */
    private function salvarDadosPaciente($dados)
    {
        if (property_exists($dados, 'cpfPaciente')) {
            $dao = new \cl_cgs_und();

            if (!empty($dados->cpfPaciente)) {
                $where = [];
                $where[] = "z01_v_cgccpf = '{$dados->cpfPaciente}'";
                $where[] = "z01_i_cgsund != {$dados->paciente}";
                $sql = $dao->sql_query_file('', '1', '', implode(' AND ', $where));
                $dao->sql_record($sql);
                if ($dao->numrows > 0) {
                    throw new \Exception('Já existe um CGS com esse número de CPF.');
                }
            }

            $dao->z01_v_cgccpf = $dados->cpfPaciente;
            $dao->z01_i_cgsund = $dados->paciente;
            $dao->alterar($dados->paciente);

            if ($dao->erro_status == '0') {
                throw new \Exception("Erro ao salvar dados do paciente.\n{$dao->erro_msg}");
            }
        }
        if (property_exists($dados, 'cnsPaciente')) {
            $cgs = new \Cgs($dados->paciente);
            $cgs->salvarCgsCartaoSus((object)[
                'dados_pessoais' => (object)['cns' => $dados->cnsPaciente]
            ]);
        }
    }

    /**
     * @param integer $id
     * @param string $nota
     * @param string $data
     * @throws \Exception
     */
    private function alterarNotaEmpenho($id, $nota, $data)
    {
        $dao = new \cl_empnota();
        $dao->e69_codnota = $id;
        $dao->e69_numero = $nota;
        $dao->e69_dtnota = $data;
        $dao->alterar($id);

        if ($dao->erro_status == '0') {
            throw new \Exception('Erro ao salvar nota fiscal do empenho.');
        }
    }

    /**
     * @param integer $estoqueItem
     * @param string $lote
     * @param string $dataValidade
     * @throws \Exception
     */
    private function salvarLoteMedicamento($estoqueItem, $lote, $dataValidade)
    {
        $dao = new \cl_matestoqueitemlote();

        $dao->m77_lote = $lote;
        $dao->m77_dtvalidade = $dataValidade ? \DBDate::create($dataValidade)->getDate() : null;
        $dao->m77_matestoqueitem = $estoqueItem;

        $sql = $dao->sql_query_file(null, 'm77_sequencial', null, "m77_matestoqueitem = {$estoqueItem}");
        $rs = $dao->sql_record($sql);
        if ($dao->numrows > 0) {
            $id = \db_utils::fieldsMemory($rs, 0)->m77_sequencial;
            $dao->m77_sequencial = $id;
            $dao->alterar($id);
        } else {
            $dao->incluir(null);
        }

        if ($dao->erro_status == '0') {
            throw new \Exception("Erro ao salvar lote {$lote}.");
        }
    }

    /**
     * @param integer $estoqueItem
     * @param string $fabricante
     * @throws \Exception
     */
    private function salvarFabricanteMedicamento($estoqueItem, $fabricante)
    {
        $dao = new \cl_matestoqueitemfabric();

        $dao->m78_matestoqueitem = $estoqueItem;
        $dao->m78_matfabricante = $fabricante;

        $sql = $dao->sql_query_file(null, 'm78_sequencial', null, "m78_matestoqueitem = {$estoqueItem}");
        $rs = $dao->sql_record($sql);
        if ($dao->numrows > 0) {
            $id = \db_utils::fieldsMemory($rs, 0)->m78_sequencial;
            $dao->m78_sequencial = $estoqueItem;
            $dao->alterar($id);
        } else {
            $dao->incluir(null);
        }

        if ($dao->erro_status == '0') {
            throw new \Exception('Erro ao víncular fabricante.');
        }
    }

    /**
     * @param $lancamento
     * @return void
     * @throws \Exception
     */
    private function excluirErros($lancamento)
    {
        BnafarErro::where('fa73_matestoqueini', $lancamento)->delete();
    }
}
