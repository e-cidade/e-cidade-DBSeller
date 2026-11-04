<?php

namespace App\Domain\Tributario\ITBI\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Domain\Tributario\ITBI\Models\AnexosItbi;
use App\Domain\Configuracao\Helpers\StorageHelper;

class AnexoArquivoItbiRepository
{

    public function uploadArquivo($arqPath, $arqName, $itbi)
    {
        $permiteUpload = DB::select("select it14_guia from itbi.itbiavalia where it14_guia = {$itbi}");
        if ($permiteUpload && $permiteUpload > 0) {
            return 'semPerm';
        }
        try {
            $storage = new StorageHelper;
            $idArquivo = $storage->uploadArquivo($arqPath, null, true, null, null, $arqName);
            return ['sucesso' => $idArquivo];
        } catch (Exception $e) {
            return ['error' => $e];
        }
    }

    public function getArquivosItbi($itbi)
    {
        if ($itbi == null) {
            return 'error';
        }

        $permissao = DB::select("select a.it39_sequencial,
        	   du.nome,
        	   a.it39_arquivo,
        	   a.it39_descricao, 
        	   a.it39_observacao,
               a.it39_idstorage,
               TO_CHAR(a.it39_data, 'DD/MM/YYYY HH24:MI') AS it39_data,
        		case 
        			when iv.it14_guia is not null then false
        			else true
        		end as permite_excluir
        from itbi.anexositbi a 
        left join itbi.itbiavalia iv
        on iv.it14_guia = a.it39_itbi 
        inner join configuracoes.db_usuarios du 
        on du.id_usuario = a.it39_usuario
        where a.it39_itbi = {$itbi}  
        and a.it39_usuarioexclusao is null
        and a.it39_dataexclusao is null");

        return $permissao;
    }

    public function deleteArquivo($sequencial, $idArq, $idUser, $itbi)
    {
        $permiteExcluir = $this->verificaExclusao($itbi, $sequencial);
        if ($permiteExcluir == true) {
            try {
                $anexo = AnexosItbi::where('it39_sequencial', $sequencial)
                    ->where('it39_idstorage', $idArq)->first();
                $storage = new StorageHelper;
                $status = $storage->deleteArquivo($idArq);
                $anexo->it39_usuarioexclusao = $idUser;
                $anexo->it39_dataexclusao = Carbon::now();
                $anexo->save();
                return ['status' => $status];
            } catch (Exception $e) {
                return ['error' => $e];
            }
        } else {
            return 'semPerm';
        }
    }

    public function verificaExclusao($itbi, $sequencial)
    {

        $verifica = DB::select("select a.it39_sequencial,
        	   du.nome,
        	   a.it39_arquivo,
        	   a.it39_descricao, 
        	   a.it39_observacao,
               a.it39_idstorage,
               TO_CHAR(a.it39_data, 'DD/MM/YYYY HH24:MI') AS it39_data,
        		case 
        			when iv.it14_guia is not null then false
        			else true
        		end as permite_excluir
        from itbi.anexositbi a 
        left join itbi.itbiavalia iv
        on iv.it14_guia = a.it39_itbi 
        inner join configuracoes.db_usuarios du 
        on du.id_usuario = a.it39_usuario
        where a.it39_itbi = {$itbi}
        and a.it39_sequencial = {$sequencial}  
        and a.it39_usuarioexclusao is null
        and a.it39_dataexclusao is null");

        if ($verifica !== null) {
            return $verifica[0]->permite_excluir;
        }
    }

    public function saveDadosUpload($itbi, $desc, $obs, $user, $arqName, $idStorage)
    {
        try {
            $anexo = new AnexosItbi();
            $anexo->it39_arquivo = $arqName;
            $anexo->it39_descricao = $desc;
            $anexo->it39_observacao = $obs;
            $anexo->it39_data = Carbon::now();
            $anexo->it39_usuario = $user;
            $anexo->it39_itbi = $itbi;
            $anexo->it39_idstorage = $idStorage;
            $anexo->save();
            return ['sucesso' => $anexo];
        } catch (Exception $e) {
            return ['error' => $e];
        }
    }

    public function updateDadosArquivo($sequencial, $itbi, $desc, $obs)
    {
        $permiteExcluir = $this->verificaExclusao($itbi, $sequencial);
        if ($permiteExcluir == true) {
            try {
                $anexo = AnexosItbi::where('it39_sequencial', $sequencial)->first();
                $anexo->it39_descricao = $desc;
                $anexo->it39_observacao = $obs;
                $anexo->save();
                return ['sucesso' => true];
            } catch (Exception $e) {
            }
        } else {
            return 'semPerm';
        }
    }

    public function downloadArquivo($idArq)
    {
        $storage = new StorageHelper;
        $storage = $storage->downloadArquivo($idArq);
        $anexo = AnexosItbi::where('it39_idstorage', $idArq)->first();
        $pathArquivo = "tmp/{$anexo->it39_arquivo}";

        if (!rename($storage, $pathArquivo)) {
            return 'erro';
        }

        return ['path' => $pathArquivo, 'nomeArquivo' => $anexo->it39_arquivo];
    }
}
