<?php

namespace App\Domain\Tributario\ITBI\Repositories;

use App\Domain\Tributario\ITBI\Models\Itbi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

final class ItbiRepository
{
    /**
     * @var Itbi
     */
    private $itbi;

    public function __construct()
    {
        $this->itbi = new Itbi();
    }

    /**
     * Busca os dados com base no número da guia
     *
     * @param $guia
     *
     * @return Itbi|Model
     * @throws \Exception
     */
    public function getByGuia($guia)
    {
        $oItbi = $this->itbi->where(
            "it01_guia",
            "=",
            $guia
        )->first();

        if (empty($oItbi)) {
            throw new \Exception("Erro ao buscar os dados da guia de ITBI.");
        }

        return $oItbi;
    }

    /**
     * Retorna todos os dados necessários para a guia de ITBI informada
     *
     * @param $guia
     * @param string[] $campos
     *
     * @return Itbi|Model|Builder
     * @throws \Exception
     */
    public function getAllByGuia($guia, $campos = ["*"])
    {
        $oItbi = $this->itbi->leftJoin(
            "itburbano",
            "itburbano.it05_guia",
            "=",
            "itbi.it01_guia"
        )->leftJoin(
            "itbirural",
            "itbirural.it18_guia",
            "=",
            "itbi.it01_guia"
        )->leftJoin(
            "itbidadosimovel",
            "itbidadosimovel.it22_itbi",
            "=",
            "itbi.it01_guia"
        )->leftJoin(
            "itbimatric",
            "itbimatric.it06_guia",
            "=",
            "itbi.it01_guia"
        )->leftJoin(
            "itbitransacao",
            "itbitransacao.it04_codigo",
            "=",
            "itbi.it01_tipotransacao"
        )->leftJoin(
            "itbiavalia",
            "itbiavalia.it14_guia",
            "=",
            "itbi.it01_guia"
        )->leftJoin(
            "itbisituacao",
            "itbisituacao.it07_codigo",
            "=",
            "itburbano.it05_itbisituacao"
        )->leftJoin(
            "iptubase",
            "iptubase.j01_matric",
            "=",
            "itbimatric.it06_matric"
        )->leftJoin(
            "lote",
            "lote.j34_idbql",
            "=",
            "iptubase.j01_idbql"
        )->leftJoin(
            "bairro",
            "bairro.j13_codi",
            "=",
            "lote.j34_bairro"
        )->leftJoin(
            "lotesetorfiscal",
            "lotesetorfiscal.j91_idbql",
            "=",
            "lote.j34_idbql"
        )->leftJoin(
            "setorfiscal",
            "setorfiscal.j90_codigo",
            "=",
            "lotesetorfiscal.j91_codigo"
        )->leftJoin(
            "loteloc",
            "loteloc.j06_idbql",
            "=",
            "lote.j34_idbql"
        )->leftJoin(
            "setorloc",
            "setorloc.j05_codigo",
            "=",
            "loteloc.j06_setorloc"
        )->leftJoin(
            "itbidadosimovelsetorloc",
            "itbidadosimovelsetorloc.it29_itbidadosimovel",
            "=",
            "itbidadosimovel.it22_sequencial"
        )->where(
            "itbi.it01_guia",
            "=",
            $guia
        )->select($campos)->first();

        if (empty($oItbi)) {
            throw new \Exception("Erro ao buscar todos os dados da guia de ITBI.");
        }

        return $oItbi;
    }

    public function salvar(Itbi $entity)
    {
        $clitbi = new \cl_itbi();

        $clitbi->it01_guia = $entity->getGuia();
        $clitbi->it01_data = $entity->getData();
        $clitbi->it01_hora = $entity->getHora();
        $clitbi->it01_tipotransacao = $entity->getTipotransacao();
        $clitbi->it01_areaterreno = $entity->getAreaterreno();
        $clitbi->it01_areaedificada = $entity->getAreaedificada();
        $clitbi->it01_obs = $entity->getObs();
        $clitbi->it01_valortransacao = $entity->getValortransacao();
        $clitbi->it01_areatrans = $entity->getAreatrans();
        $clitbi->it01_mail = $entity->getMail();
        $clitbi->it01_finalizado = $entity->getFinalizado();
        $clitbi->it01_origem = $entity->getOrigem();
        $clitbi->it01_id_usuario = $entity->getIdUsuario();
        $clitbi->it01_coddepto = $entity->getCoddepto();
        $clitbi->it01_valorterreno = $entity->getValorterreno();
        $clitbi->it01_valorconstr = $entity->getValorconstr();
        $clitbi->it01_envia = $entity->getEnvia();
        $clitbi->it01_percentualareatransmitida = $entity->getPercentualareatransmitida();
        $clitbi->it01_notificado = $entity->getNotificado();
        $clitbi->it01_processo = $entity->getProcesso();
        $clitbi->it01_tituprocesso = $entity->getTituprocesso();
        $clitbi->it01_dtprocesso = $entity->getDtprocesso();
        $clitbi->it01_cartorioextra = $entity->getCartorioextra();
        $clitbi->it01_protprocesso = $entity->getProcessoSequencial();

        if (!empty($clitbi->it01_guia)) {
            $clitbi->alterar($clitbi->it01_guia);
        } else {
            $clitbi->incluir(null);
        }

        if ($clitbi->erro_status == "0") {
            throw new \Exception($clitbi->erro_msg);
        }

        return $clitbi->it01_guia;
    }
}
