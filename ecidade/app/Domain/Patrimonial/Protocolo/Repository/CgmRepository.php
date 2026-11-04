<?php

namespace App\Domain\Patrimonial\Protocolo\Repository;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\Cadastro\Models\DbSyscampo;

class CgmRepository
{
    private $cgm;

    public function __construct()
    {
        $this->cgm = new Cgm();
    }

    public function getByNumcgm($numcgm)
    {
        return $this->cgm->where(
            "z01_numcgm",
            "=",
            $numcgm
        )->first();
    }

    public function getByCpfCnpj($cpfCnpj)
    {
        return $this->cgm->where(
            "z01_cgccpf",
            "=",
            $cpfCnpj
        )->get();
    }

    /**
     * Retorna uma lista de registros com base em um ou mais parametros
     *
     * @param integer $cgm
     * @param string $nome
     * @param string $cgcpf
     * @param string $email
     * @param integer $porPagina
     */
    public function getByParams($cgm, $nome, $cgcpf, $email, $porPagina)
    {
        $where = " 1 = 1 ";

        if (trim($cgm) != "") {
            $where .= " and z01_numcgm = {$cgm} ";
        }

        if (trim($nome) != "") {
            $where .= " and z01_nome ilike '%{$nome}%' ";
        }

        if (trim($cgcpf) != "") {
            $where .= " and z01_cgccpf ilike '{$cgcpf}' ";
        }

        if (trim($email) != "") {
            $where .= " and z01_email ilike '{$email}' ";
        }

        $pesquisa = $this->cgm->whereRaw($where)
            ->orderBy('z01_nome', 'ASC')
            ->paginate($porPagina);

        return $pesquisa;
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     * @param array $nomeCampos
     * @return array
     */
    public function getRotulosPesquisaCgm($nomeCampos)
    {
        $labels = array_map(function ($nomeCampo) {
            return DbSyscampo::select("rotulo")->where('nomecam', '=', $nomeCampo)->first()->toArray();
        }, $nomeCampos);

        return $labels;
    }
}
