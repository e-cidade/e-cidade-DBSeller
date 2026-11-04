<?php

namespace App\Domain\Tributario\Cadastro\Repositories;

use App\Domain\Tributario\Cadastro\Models\DbSyscampo;
use App\Domain\Tributario\Cadastro\Models\Iptubase;

class IptubaseRepository
{
    private $iptubase;

    public function __construct()
    {
        $this->iptubase = new Iptubase();
    }

    public function getDadosRegImovByMatric($matricula)
    {
        $cliptubase = new \cl_iptubase();

        $rsConsultaDadosMatric = $cliptubase->sql_record($cliptubase->sql_query_regmovel($matricula));

        return \db_utils::fieldsMemory($rsConsultaDadosMatric, 0);
    }

    public function getCamposDadosRegImovByMatric($matricula, $campos)
    {
        $cliptubase = new \cl_iptubase();

        $rsConsultaDadosMatric = $cliptubase->sql_record($cliptubase->sql_query_regmovel($matricula, $campos));

        return \db_utils::fieldsMemory($rsConsultaDadosMatric, 0);
    }

    /**
     * Metodo para buscar uma lista de imoveis de acordo com
     * os parametros fornecidos
     *
     * @param integer $porPagina,
     * @param integer $matricula,
     * @param integer $codCondominio,
     * @param integer $codLoteamento,
     * @param integer $codLogradouro,
     * @param string $nome,
     * @param string $setor,
     * @param string $quadra,
     * @param string $lote,
     * @param string $setorLocalizacao,
     * @param string $quadraLocalizacao,
     * @param string $loteLocalizacao,
     * @param string $refAnterior,
     * @param string $registroCartografico,
     * @param boolean $matriculasBaixadas
     */
    public function getListaImoveis(
        $porPagina,
        $matricula,
        $codCondominio,
        $codLoteamento,
        $codLogradouro,
        $nome,
        $setor,
        $quadra,
        $lote,
        $setorLocalizacao,
        $quadraLocalizacao,
        $loteLocalizacao,
        $refAnterior,
        $registroCartografico,
        $matriculasBaixadas
    ) {
        $where = " 1 = 1";

        if (trim($matricula) != "") {
            $where .= " and j01_matric = {$matricula} ";
        }

        if (trim($codCondominio) != "") {
            $where .= " and j108_condominio = {$codCondominio} ";
        }

        if (trim($codLoteamento) != "") {
            $where .= "  and loteam.j34_loteam = {$codLoteamento} ";
        }

        if (trim($codLogradouro) != "") {
            $where .= " and ruas.j14_codigo = {$codLogradouro} ";
        }

        if (trim($nome != "")) {
            $where .= " and z01_nome ilike '%{$nome}%' ";
        }

        if (trim($setor)) {
            $where .= " and j34_setor ilike '{$setor}' ";
        }

        if (trim($quadra) != "") {
            $where .= " and j34_quadra ilike '{$quadra}' ";
        }

        if (trim($lote) != "") {
            $where .= " and j34_lote ilike '{$lote}' ";
        }

        if (trim($setorLocalizacao) != "") {
            $where .= " and j05_codigoproprio ilike '{$setorLocalizacao}' ";
        }

        if (trim($quadraLocalizacao)) {
            $where .= " and j06_quadraloc ilike '{$quadraLocalizacao}' ";
        }

        if (trim($loteLocalizacao) != "") {
            $where .= " and j06_lote ilike '{$loteLocalizacao}' ";
        }

        if (trim($refAnterior)) {
            $where .= " and j40_refant ilike '{$refAnterior}' ";
        }

        if (trim($registroCartografico) != "") {
            $where .= " and j40_registrocartografico ilike '{$registroCartografico}' ";
        }

        if ($matriculasBaixadas) {
            $where .= " and j01_baixa is not null ";
        } else {
            $where .= " and j01_baixa is null ";
        }

        $resultados = Iptubase::select(
            'j01_matric',
            'iptubasecondominio.j108_condominio',
            'j40_refant',
            'j40_registrocartografico',
            'ruas.j14_nome',
            'j39_compl',
            'j34_setor',
            'j34_quadra',
            'j34_lote',
            'j01_baixa'
        )
            ->selectRaw('case                                               
                when j15_numero > 0                            
                and j39_matric is null then j15_numero         
                else j39_numero                                
            end as j39_numero')
            ->selectRaw("case                                   
                when j39_numero is null then 'Terr'
                else 'Pred'                        
            end as Tipo")
            ->selectRaw(" (
                select                                
                    rvnome as z01_nome                
                from                                  
                    fc_busca_envolvidos(              
                        false,                        
                        (                             
                            select                    
                                fc_regrasconfig       
                            from                      
                                fc_regrasconfig(1)    
                        ),                            
                        'M',                          
                        iptubase.j01_matric           
                    )                                 
                limit                                 
                    1                                 
            ), z01_numcgm as db_z01_numcgm")
            ->join("lote", 'j34_idbql', '=', 'j01_idbql')
            ->leftJoin('iptubasecondominio', 'j108_matric', '=', 'j01_matric')
            ->join('testpri', 'j49_idbql', '=', 'j01_idbql', 'left outer')
            ->join('testadanumero', 'testadanumero.j15_idbql', '=', 'testpri.j49_idbql', 'left outer')
            ->join('ruas', 'j14_codigo', '=', 'j49_codigo', 'left outer')
            ->join('cgm', 'z01_numcgm', '=', 'j01_numcgm')
            ->join('iptuconstr', 'iptuconstr.j39_matric', '=', 'j01_matric', 'left outer')
            ->join('iptuant', 'j01_matric', '=', 'j40_matric', 'left outer')
            ->join('loteloc', 'j06_idbql', '=', 'j01_idbql', 'left outer')
            ->leftJoin('setorloc', 'j05_codigo', '=', 'j06_setorloc')
            ->leftJoin('loteloteam', 'loteloteam.j34_idbql', '=', 'lote.j34_idbql')
            ->leftJoin('loteam', 'loteam.j34_loteam', '=', 'loteloteam.j34_loteam')
            ->whereRaw($where)
            ->orderBy('z01_nome', 'ASC')
            ->orderBy('j01_matric')
            ->orderBy('j14_nome')
            ->orderBy('j39_numero')
            ->paginate($porPagina);

        return $resultados;
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     * @param array $nomeCampos
     * @return array
     */
    public function getLabelsListaImoveis($nomeCampos)
    {
        $labels = array_map(function ($nomeCampo) {
            return DbSyscampo::select("rotulo")->where('nomecam', '=', $nomeCampo)->first()->toArray();
        }, $nomeCampos);

        return $labels;
    }

    /**
     * Metodo para buscar informacoes atuais do imovel
     * @param integer $matricula
     * @return object
     */
    public function getInformacoesImovel($matricula)
    {
        $cliptubase = new \cl_iptubase();
        $rsConsultaDadosMatric = $cliptubase->sql_record($cliptubase->sql_query_informacoesImovel($matricula));
        return \db_utils::fieldsMemory($rsConsultaDadosMatric, 0);
    }
}
