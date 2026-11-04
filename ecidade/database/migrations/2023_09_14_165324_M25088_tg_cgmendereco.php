<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25088TgCgmendereco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER FUNCTION fc_cgmendereco_incalt SET SCHEMA protocolo;

CREATE OR REPLACE FUNCTION protocolo.fc_cgmendereco_incalt()
  RETURNS TRIGGER AS
$$

    declare

        iCodigoEstado       integer default 0;
        iCodigoMunicipio    integer default 0;
        iCodigoBairro       integer default 0;
        iCodigoRua          integer default 0;
        iCodigoBairroRua    integer default 0;
        iCodigoLocal        integer default 0;
        iCodigoEndereco	    integer default 0;
        iCodigoRuasTipo	    integer default 0;
        iCodigoCgm          integer default 0;
        iCodigoCgmEndereco  integer default 0;
        iNumCgmEndereco     integer default 0;

        lTriggerHabilitada  boolean default true;
        lRaise              boolean default false;

        sOperacao           text := '';

        rCgm                record;
        rCadEnderParam      record;
        rEndereco           record;

    begin

        lRaise  := ( case when fc_getsession('DB_debugon') is null then false else true end );

        lTriggerHabilitada := ( case when fc_getsession('DB_habilita_trigger_endereco') is null then true else false end );

        if not lTriggerHabilitada then
            return NEW;
        end if;

        sOperacao := upper(TG_OP);
        if (sOperacao = 'INSERT') then
            iCodigoCgm := NEW.z01_numcgm;
            insert into personacgm (p121_cgm,p121_persona) values (NEW.z01_numcgm ,1);
        else
            iCodigoCgm := OLD.z01_numcgm;
        end if;

        /* Verificar se o CGM alterado esta incluído na cgmendereco
             se estiver tem que verificar campo a campo se houve alteração
             se não estiver tem que gerar um endereco novo e fazer a ligação
             da cgmendereco
        */

        select z01_numcgm,
               z01_ender,
               z01_numero::varchar,
               z01_compl,
               z01_bairro,
               z01_munic,
               z01_uf,
               z01_cep,
               z01_endcon,
               z01_numcon,
               z01_comcon,
               z01_baicon,
               z01_muncon,
               z01_ufcon,
               z01_cepcon
        into rCgm
        from cgm
        where z01_numcgm = iCodigoCgm;

        if not found then
            if lRaise then
                raise notice 'Nenhum registro encontrado para o CGM {%}', iCodigoCgm;
            end if;

            return null;
        end if;

        if lRaise then
            raise notice 'Cgm encontrado ';
        end if;

        if (rCgm.z01_ender = '') then
            if lRaise then
                raise notice 'Endereço informado vazio ';
            end if;

            return null;
        end if;

        /* Leitura dos parâmetros do cadastro de endereço cadenderparam */

        select db99_cadenderpais,
               db99_cadenderestado,
               db99_cadendermunicipio,
               db70_descricao,
               db71_descricao,
               db71_sigla,
               db72_descricao
        into rCadEnderParam
        from cadenderparam
                 join cadenderpais      on cadenderpais.db70_sequencial      = cadenderparam.db99_cadenderpais
                 join cadenderestado    on cadenderestado.db71_sequencial    = cadenderparam.db99_cadenderestado
                 join cadendermunicipio on cadendermunicipio.db72_sequencial = cadenderparam.db99_cadendermunicipio
                 join cadendermunicipiosistema on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                                              and cadendermunicipiosistema.db125_db_sistemaexterno = 4;

        if not found then
            if lRaise then
                raise notice 'Parâmetros do endereço não configurados {cadenderparam}';
            end if;

            return null;
        end if;

        if lRaise then
            raise notice 'Tabela de parâmetros ok !';
        end if;

        if (rCgm.z01_ender != '') then
            /* Pesquisa para verificar se existe relação do cgm com o endereco {cgmendereco} */

            select z07_sequencial,
                   z07_endereco
            into iCodigoCgmEndereco,
                iCodigoEndereco
            from cgmendereco
            where z07_numcgm = iCodigoCgm
              and z07_tipo   = 'P';

            /* Se o cgm não existir na tabela de ligação gerar o endereço e vincular o cidadao ao cgm */
            if not found then
                if lRaise then
                    raise notice 'Cgm não encontrado na cgmendereco ';
                end if;

                /* ---------------------------- Inicio do tratameno do estado do endereço -----------------------------*/
                /* Atribuindo o codigo do estado da tabela de parametros do endereço {cadenderparam} */

                iCodigoEstado := rCadEnderParam.db99_cadenderestado;

                /* Verificar se z01_uf e z01_munic são diferentes de ''
                 *  se não for atribuir o estado default para o municipio não informado RS, 0-Não Informado
                 */

                if (rCgm.z01_munic = '' or rCgm.z01_uf = '' or rCgm.z01_bairro = '') then
                    select db71_sequencial
                    into iCodigoEstado
                    from cadendermunicipio
                             join cadendermunicipiosistema
                                  on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                                      and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                             join cadenderestado on cadenderestado.db71_sequencial = cadendermunicipio.db72_cadenderestado
                    where cadenderestado.db71_sigla = rCadEnderParam.db71_sigla;

                    if not found then
                        if lRaise then
                            raise notice 'Falha ao atribuir estado padrão!';
                        end if;

                        return null;
                    end if;
                else /* Aqui pesquisa pela sigla do z01_uf informado no cgm */
                    select db71_sequencial
                    into iCodigoEstado
                    from cadenderestado
                    where db71_sigla = trim(rCgm.z01_uf);

                    /* Se não localizar o estado atribuir o estado dos parametros do endereço */
                    if not found then
                        if lRaise then
                            raise notice 'Estado não encontrado pela sigla fornecida atribuido dos Parametros do endereco';
                        end if;

                        iCodigoEstado := rCadEnderParam.db99_cadenderestado;
                    end if;

                end if; /* Fechamento do if do estado */

                /* ---------------------------- Fim do tratameno do estado do endereço -----------------------------*/
	            /* ---------------------------- Inicio do tratameno do estado do Municipio -------------------------*/
                /* Se o z01_munic for igual a vazio */

                if (rCgm.z01_munic = '') then
                    if lRaise then
                        raise notice 'Definido municipio 0-Não Informado para o endereço';
                    end if;

                    iCodigoMunicipio := 0;
                else /* Se o z01_munic diferente de vazio pesquisar se existe senão incluir*/
                    select db72_sequencial
                    into iCodigoMunicipio
                    from cadendermunicipio
                             join cadendermunicipiosistema
                                  on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                                      and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                    where lower(to_ascii(db72_descricao)) = lower(to_ascii(rCgm.z01_munic))
                      and db72_cadenderestado = iCodigoEstado;

                    /* Se não encontrou o municipio entao tem que incluir o mesmo */
                    if not found then
                        if lRaise then
                            raise notice 'Municipio não encontrado, incluindo .....';
                        end if;

                        iCodigoMunicipio := nextval('cadendermunicipio_db72_sequencial_seq');

                        insert into cadendermunicipio (db72_sequencial,
                                                       db72_descricao,
                                                       db72_cadenderestado
                        )
                        values (iCodigoMunicipio,
                                rCgm.z01_munic,
                                iCodigoEstado
                               );
                    end if;
                end if;

                /* ---------------------------- Fim do tratamento do Municipio ----------------------------*/
                /* ---------------------------- Inicio do tratamento do Bairro ----------------------------*/
                /* Se o z01_bairro for igual a vazio */

                if (rCgm.z01_bairro = '') then
                    if lRaise then
                        raise notice 'Definindo bairro 0-Não Informado para o endereço';
                    end if;

                    iCodigoBairro := 0;
                else /* Se o z01_bairro diferente de vazio pesquisar se existe senão incluir */
                    select db73_sequencial
                    into iCodigoBairro
                    from cadenderbairro
                    where lower(to_ascii(db73_descricao)) = lower(to_ascii(rCgm.z01_bairro))
                      and db73_cadendermunicipio = iCodigoMunicipio;

                    if not found then
                        if lRaise then
                            raise notice 'Bairro não encontrado, incluindo .....';
                        end if;

                        iCodigoBairro := nextval('cadenderbairro_db73_sequencial_seq');
                        insert into cadenderbairro (db73_sequencial,
                                                    db73_descricao,
                                                    db73_cadendermunicipio
                        )
                        values (iCodigoBairro,
                                rCgm.z01_bairro,
                                iCodigoMunicipio
                               );
                    end if;
                end if;

                /* ---------------------------- Fim do tratamento do Bairro -------------------------------*/
                /* ---------------------------- Inicio do tratamento da Rua  -------------------------------*/
                /* Se o bairro for igual a vazio */

                if (rCgm.z01_ender = '') then
                    if lRaise then
                        raise notice 'Endereco não informado -- Inclusão Cancelada';
                    end if;

                    return null;
                else /* Se o z01_ender for diferente de vazio pesquisar se existe senão incluir */
                    select db74_sequencial
                    into iCodigoRua
                    from cadenderrua
                    where lower(to_ascii(db74_descricao)) = lower(to_ascii(rCgm.z01_ender))
                      and db74_cadendermunicipio = iCodigoMunicipio;

                    if not found then
                        if lRaise then
                            raise notice 'Rua não encontrado ! incluindo ..... ';
                        end if;

                        iCodigoRua := nextval('cadenderrua_db74_sequencial_seq');
                        insert into cadenderrua (db74_sequencial,
                                                 db74_descricao,
                                                 db74_cadendermunicipio
                        )
                        values (iCodigoRua,
                                rCgm.z01_ender,
                                iCodigoMunicipio
                               );
                    end if;
                end if;

                /* ---------------------------- Fim do tratamento da Rua -------------------------------*/
                /* ---------------------------- Inicio do tratamento da RuasTipo -----------------------*/

                perform db85_sequencial
                from cadenderruaruastipo
                where db85_cadenderrua = iCodigoRua;

                if not found then
                    if lRaise then
                        raise notice 'Incluindo na cadenderruaruastipo';
                    end if;

                    insert into cadenderruaruastipo (db85_sequencial,
                                                     db85_cadenderrua,
                                                     db85_ruastipo
                    )
                    values (nextval('cadenderruaruastipo_db85_sequencial_seq'),
                            iCodigoRua,
                            3
                           );
                end if;

                /* ---------------------------- Fim do tratamento da RuasTipo --------------------------*/
                /* ---------------------------- Inicio do tratamento do Vinculo da Rua com Bairro ------*/

                select db87_sequencial
                into iCodigoBairroRua
                from cadenderbairrocadenderrua
                where db87_cadenderrua    = iCodigoRua
                  and db87_cadenderbairro = iCodigoBairro;

                if not found then
                    if lRaise then
                        raise notice 'Incluindo na cadenderbairrocadenderrua';
                    end if;

                    iCodigoBairroRua := nextval('cadenderbairrocadenderrua_db87_sequencial_seq');
                    insert into cadenderbairrocadenderrua (db87_sequencial,
                                                           db87_cadenderrua,
                                                           db87_cadenderbairro
                    )
                    values (iCodigoBairroRua,
                            iCodigoRua,
                            iCodigoBairro
                           );
                end if;

                /* ---------------------------- Fim do tratamento do Vinculo da Rua com Bairro ---------*/
                /* ---------------------------- Inicio do tratamento da Local --------------------------*/

                select db75_sequencial
                into iCodigoLocal
                from cadenderlocal
                where db75_cadenderbairrocadenderrua = iCodigoBairroRua;

                if not found then
                    if lRaise then
                        raise notice 'Icluindo na cadenderlocal';
                    end if;

                    iCodigoLocal := nextval('cadenderlocal_db75_sequencial_seq');
                    insert into cadenderlocal (db75_sequencial,
                                               db75_cadenderbairrocadenderrua,
                                               db75_numero
                    )
                    values (iCodigoLocal,
                            iCodigoBairroRua,
                            rCgm.z01_numero
                           );
                end if;

                /* ---------------------------- Fim do tratamento da Local -----------------------------*/
                /* ---------------------------- Inicio do tratamento da Endereco -----------------------*/

                iCodigoEndereco := nextval('endereco_db76_sequencial_seq');

                if lRaise then
                    raise notice 'Inserindo na Endereco {%}',iCodigoEndereco;
                end if;

                insert into endereco (db76_sequencial,
                                      db76_cadenderlocal,
                                      db76_complemento,
                                      db76_cep
                )
                values (iCodigoEndereco,
                        iCodigoLocal,
                        rCgm.z01_compl,
                        rCgm.z01_cep
                       );

                /* ---------------------------- Fim do tratamento da Endereco --------------------------*/
                /* ---------------------------- Inicio do tratamento da cgmendereco --------------------*/

                if lRaise then
                    raise notice 'Inserindo na cgmendereco';
                end if;

                insert into cgmendereco (z07_sequencial,
                                         z07_endereco,
                                         z07_numcgm,
                                         z07_tipo
                )
                values (nextval('cgmendereco_z07_sequencial_seq'),
                        iCodigoEndereco,
                        iCodigoCgm,
                        'P'
                       );
                /* ---------------------------- Fim do tratamento da cgmendereco -----------------------*/
            else  /* aqui se ja exisitir na cgmendereco */
                select db74_sequencial,
                       db74_descricao,
                       db75_numero,
                       db73_sequencial,
                       db73_descricao,
                       db72_sequencial,
                       db72_descricao,
                       db71_sequencial,
                       db71_descricao,
                       db71_sigla,
                       db76_sequencial,
                       db76_cep,
                       db76_pontoref,
                       db76_condominio,
                       db76_loteamento,
                       db76_caixapostal,
                       db76_complemento
                into rEndereco
                from endereco
                         join cadenderlocal             on cadenderlocal.db75_sequencial = endereco.db76_cadenderlocal
                         join cadenderbairrocadenderrua on cadenderbairrocadenderrua.db87_sequencial = cadenderlocal.db75_cadenderbairrocadenderrua
                         join cadenderrua               on cadenderrua.db74_sequencial = cadenderbairrocadenderrua.db87_cadenderrua
                         join cadenderbairro            on cadenderbairro.db73_sequencial = cadenderbairrocadenderrua.db87_cadenderbairro
                         join cadendermunicipio         on cadendermunicipio.db72_sequencial = cadenderrua.db74_cadendermunicipio
                         join cadendermunicipiosistema
                           on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                          and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                         join cadenderestado            on cadenderestado.db71_sequencial = cadendermunicipio.db72_cadenderestado
                where db76_sequencial = iCodigoEndereco;

                if not found then
                    if lRaise then
                        raise notice 'Falha ao ler endereco completo para o cgm{%} e  endereco{%}',iCodigoCgm,iCodigoEndereco;
                    end if;

                    return null;
                end if;

                /* Verificar se houve mudança no estado */
                if (rEndereco.db71_sigla != rCgm.z01_uf) then
                    select db71_sequencial
                    into iCodigoEstado
                    from cadenderestado
                    where db71_sigla = rCgm.z01_uf;

                    if not found then
                        if lRaise then
                            raise notice 'Falha ao ler estado para o cgm';
                        end if;

                        return null;
                    end if;
                else
                    select db71_sequencial
                    into iCodigoEstado
                    from cadenderestado
                    where db71_sigla = rEndereco.db71_sigla;

                    if not found then
                        if lRaise then
                            raise notice 'Falha ao ler estado do endereco';
                        end if;

                        return null;
                    end if;
                end if;/*Fim do if do estado*/
                /* Fim da Verificação da mudança no estado */

                /* Inicio da verificação de mudança no municipio */

                /* se z01_munic vazio atribui 0-Não Informado e busca codigo do estado default 'RS' */
                if (rCgm.z01_munic = '') then
                    iCodigoMunicipio := 0;
                    select db71_sequencial
                    into iCodigoEstado
                    from cadenderestado
                    where db71_sigla = rCadEnderParam.db71_sigla;

                    if not found then
                        if lRaise then
                            raise notice 'Falha ao ler codigo do estado para municipio NI';
                        end if;

                        return null;
                    end if;
                    /* Verifica se houve mudança no municipio cadastrado
                     * procurar pelo z01_munic se existe se não cadastrar
                     */
                else
                    select db72_sequencial
                    into iCodigoMunicipio
                    from cadendermunicipio
                             join cadendermunicipiosistema
                                  on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                                      and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                    where lower(to_ascii(db72_descricao)) = lower(to_ascii(rCgm.z01_munic));

                    if not found then
                        if lRaise then
                            raise notice 'Municipio não encontrado para o estado cadastrado incluindo....';
                        end if;

                        iCodigoMunicipio := nextval('cadendermunicipio_db72_sequencial_seq');
                        insert into cadendermunicipio (db72_sequencial,
                                                       db72_descricao,
                                                       db72_cadenderestado
                        )
                        values  (iCodigoMunicipio,
                                 rCgm.z01_munic,
                                 iCodigoEstado
                                );

                    end if;
                end if;/*Fim do if do municipio*/

                /* Fim da verificação de mudança no municipio */
                /* Inicio da verificação de mudança no bairro */
                /* se z01_bairro vazio atribui 0-Não Informado */

                if (rCgm.z01_bairro = '') then
                    iCodigoBairro := 0;
                    /* Verifica se houve mudança no municipio cadastrado
                     * procurar pelo z01_munic se existe se não cadastrar
                     */
                else
                    select db73_sequencial
                    into iCodigoBairro
                    from cadenderbairro
                    where lower(to_ascii(db73_descricao)) = lower(to_ascii(rCgm.z01_bairro))
                      and db73_cadendermunicipio = iCodigoMunicipio;

                    if not found then
                        if lRaise then
                            raise notice 'Bairro não encontrado para o municipio cadastrado incluindo....';
                        end if;

                        iCodigoBairro := nextval('cadenderbairro_db73_sequencial_seq');
                        insert into cadenderbairro (db73_sequencial,
                                                    db73_descricao,
                                                    db73_cadendermunicipio
                        )
                        values  (iCodigoBairro,
                                 rCgm.z01_bairro,
                                 iCodigoMunicipio
                                );
                    end if;
                end if;/*Fim do if do bairro*/

                /* Fim da verificação de mudança no bairro */
                /* Inicio da verificação de mudança na Rua */

                if (rCgm.z01_ender ='') then
                    if lRaise then
                        raise notice 'Campo z01_ender vazio';
                    end if;

                    return null;
                else
                    select db74_sequencial
                    into iCodigoRua
                    from cadenderrua
                    where lower(to_ascii(db74_descricao)) = lower(to_ascii(rCgm.z01_ender))
                      and db74_cadendermunicipio = iCodigoMunicipio;

                    if not found then
                        if lRaise then
                            raise notice 'Rua não encontrada para o municipio cadastrado incluindo....';
                        end if;

                        iCodigoRua := nextval('cadenderrua_db74_sequencial_seq');
                        insert into cadenderrua (db74_sequencial,
                                                 db74_descricao,
                                                 db74_cadendermunicipio
                        )
                        values  (iCodigoRua,
                                 rCgm.z01_ender,
                                 iCodigoMunicipio
                                );
                    end if;
                end if;/* fim do if da Rua*/

                /* Fim da verificação de mudança na Rua */
                /* Inicio da verificação de mudança na RuasTipo */

                perform db85_sequencial
                from cadenderruaruastipo
                where db85_cadenderrua = iCodigoRua;

                if not found then
                    if lRaise then
                        raise notice 'Incluindo na ruastipo';
                    end if;

                    iCodigoRuasTipo := nextval('cadenderruaruastipo_db85_sequencial_seq');
                    insert into cadenderruaruastipo (db85_sequencial,
                                                     db85_cadenderrua,
                                                     db85_ruastipo
                    )
                    values (iCodigoRuasTipo,
                            iCodigoRua,
                            3
                           );
                end if;

                /* Fim da verificação de mudança na RuasTipo */
                /* Inicio da verificação de mudança na BairroRua */

                select db87_sequencial
                into iCodigoBairroRua
                from cadenderbairrocadenderrua
                where db87_cadenderrua = iCodigoRua
                  and db87_cadenderbairro = iCodigoBairro;

                if not found then

                    if lRaise then
                        raise notice 'Incluindo na BairroRua';
                    end if;

                    iCodigoBairroRua := nextval('cadenderbairrocadenderrua_db87_sequencial_seq');
                    insert into cadenderbairrocadenderrua (db87_sequencial,
                                                           db87_cadenderrua,
                                                           db87_cadenderbairro
                    )
                    values (iCodigoBairroRua,
                            iCodigoRua,
                            iCodigoBairro
                           );

                end if;
                /* Fim da verificação de mudança na BairroRua */

                /* Inicio da verificação de mudança na Local */

                select db75_sequencial
                into iCodigoLocal
                from cadenderlocal
                where db75_cadenderbairrocadenderrua = iCodigoBairroRua
                  and db75_numero = cast(rCgm.z01_numero as text);

                if not found then

                    iCodigoLocal := nextval('cadenderlocal_db75_sequencial_seq');
                    insert into cadenderlocal (db75_sequencial,
                                               db75_cadenderbairrocadenderrua,
                                               db75_numero
                    )
                    values (iCodigoLocal,
                            iCodigoBairroRua,
                            rCgm.z01_numero
                           );

                end if;
                /* Fim da verificação de mudança na Local */

                /* Inicio da verificação de mudança na Endereco */

                select count(*)
                into iNumCgmEndereco
                from cgmendereco
                where z07_endereco = iCodigoEndereco
                having count(*) > 1;

                /*delete na cgmendereco*/
                delete from cgmendereco
                where z07_numcgm = iCodigoCgm
                  and z07_tipo   = 'P';

                if (iNumCgmEndereco > 0 and (rCgm.z01_compl != rEndereco.db76_complemento)) then

                    if lRaise then
                        raise notice 'Existe mais de um cgm no mesmo endereco inserindo endereco novo';
                    end if;

                    iCodigoEndereco := nextval('endereco_db76_sequencial_seq');
                    insert into endereco (db76_sequencial,
                                          db76_cadenderlocal,
                                          db76_complemento,
                                          db76_caixapostal,
                                          db76_loteamento,
                                          db76_condominio,
                                          db76_pontoref,
                                          db76_cep
                    )
                    values (iCodigoEndereco,
                            iCodigoLocal,
                            rCgm.z01_compl,
                            rEndereco.db76_caixapostal,
                            rEndereco.db76_loteamento,
                            rEndereco.db76_condominio,
                            rEndereco.db76_pontoref,
                            rCgm.z01_cep
                           );
                else
                    perform db76_sequencial
                    from endereco
                    where db76_sequencial    = iCodigoEndereco
                      and db76_cadenderlocal = iCodigoLocal;

                    if not found then
                        iCodigoEndereco := nextval('endereco_db76_sequencial_seq');
                        insert into endereco (db76_sequencial,
                                              db76_cadenderlocal,
                                              db76_complemento,
                                              db76_caixapostal,
                                              db76_loteamento,
                                              db76_condominio,
                                              db76_pontoref,
                                              db76_cep
                        )
                        values (iCodigoEndereco,
                                iCodigoLocal,
                                rCgm.z01_compl,
                                rEndereco.db76_caixapostal,
                                rEndereco.db76_loteamento,
                                rEndereco.db76_condominio,
                                rEndereco.db76_pontoref,
                                rCgm.z01_cep
                               );


                    else
                        update endereco set db76_cadenderlocal = iCodigoLocal,
                                            db76_complemento   = rCgm.z01_compl,
                                            db76_cep           = rCgm.z01_cep
                        where db76_sequencial = rEndereco.db76_sequencial;
                    end if;
                end if;

                /* Inserindo na cgmendereco */
                insert into cgmendereco(z07_sequencial,
                                        z07_endereco,
                                        z07_numcgm,
                                        z07_tipo
                )
                values (nextval('cgmendereco_z07_sequencial_seq'),
                        iCodigoEndereco,
                        iCodigoCgm,
                        'P'
                       );


                /* Fim da verificação de mudança na Endereco */
                if lRaise then
                    raise notice 'Fim da atualização do cgm {%} endereco {%}',iCodigoCgm,iCodigoEndereco;
                end if;

            end if;/* Finaliza bloco de quando não existe ligação com a tabela cgmendereco para tipo 'P' */
        end if; /* Fecha o if do endereço primario tipo 'P' */

        /*-----------------------------------  Aqui inicia no endereço secundario  ---------------------------------*/
        if (rCgm.z01_endcon != '') then
            /* Pesquisa para verificar se existe relação do cgm com o endereco {cgmendereco} */
            iCodigoEstado      := 0;
            iCodigoMunicipio   := 0;
            iCodigoBairro      := 0;
            iCodigoRua         := 0;
            iCodigoBairroRua   := 0;
            iCodigoLocal       := 0;
            iCodigoEndereco    := 0;
            iCodigoRuasTipo    := 0;
            iCodigoCgmEndereco := 0;
            iNumCgmEndereco    := 0;

            select z07_sequencial,
                   z07_endereco
            into iCodigoCgmEndereco,
                iCodigoEndereco
            from cgmendereco
            where z07_numcgm = iCodigoCgm
              and z07_tipo   = 'S';

            /* Se o cgm não existir na tabela de ligação gerar o endereço e vincular o cidadao ao cgm */
            if not found then

                if lRaise then
                    raise notice 'Cgm não encontrado na cgmendereco ';
                end if;

                /* ---------------------------- Inicio do tratameno do estado do endereço -----------------------------*/
                /* Atribuindo o codigo do estado da tabela de parametros do endereço {cadenderparam} */
                iCodigoEstado := rCadEnderParam.db99_cadenderestado;

                /* Verificar se z01_uf e z01_munic são diferentes de ''
                 *  se não for atribuir o estado default para o municipio não informado RS, 0-Não Informado
                 */
                if (rCgm.z01_muncon = '' or rCgm.z01_ufcon = '' or rCgm.z01_baicon = '') then

                    select db71_sequencial
                    into iCodigoEstado
                    from cadendermunicipio
                             join cadendermunicipiosistema
                                  on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                                      and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                             join cadenderestado on cadenderestado.db71_sequencial = cadendermunicipio.db72_cadenderestado
                    where cadenderestado.db71_sigla = rCadEnderParam.db71_sigla;

                    if not found then
                        if lRaise then
                            raise notice 'Falha ao atribuir estado padrão!';
                        end if;

                        return null;
                    end if;

                else /* Aqui pesquisa pela sigla do z01_uf informado no cgm*/
                    select db71_sequencial
                    into iCodigoEstado
                    from cadenderestado
                    where db71_sigla = trim(rCgm.z01_ufcon);
                    /* Se não localizar o estado atribuir o estado dos parametros do endereço */
                    if not found then

                        if lRaise then
                            raise notice 'Estado não encontrado pela sigla fornecida atribuido dos Parametros do endereco';
                        end if;

                        iCodigoEstado := rCadEnderParam.db99_cadenderestado;
                    end if;

                end if;/*Fechamento do if do estado*/

                /* ---------------------------- Fim do tratameno do estado do endereço -----------------------------*/
                /* ---------------------------- Inicio do tratameno do estado do Municipio -------------------------*/
                /* Se o z01_munic for igual a vazio */
                if (rCgm.z01_muncon = '') then

                    if lRaise then
                        raise notice 'Definido municipio 0-Não Informado para o endereço';
                    end if;

                    iCodigoMunicipio := 0;
                else /* Se o z01_munic diferente de vazio pesquisar se existe senão incluir*/

                    select db72_sequencial
                    into iCodigoMunicipio
                    from cadendermunicipio
                             join cadendermunicipiosistema
                                  on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                                      and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                    where lower(to_ascii(db72_descricao)) = lower(to_ascii(rCgm.z01_muncon))
                      and db72_cadenderestado = iCodigoEstado;
                    /* Se não encontrou o municipio entao tem que incluir o mesmo */
                    if not found then

                        if lRaise then
                            raise notice 'Municipio não encontrado ! incluindo .....';
                        end if;

                        iCodigoMunicipio := nextval('cadendermunicipio_db72_sequencial_seq');

                        insert into cadendermunicipio (db72_sequencial,
                                                       db72_descricao,
                                                       db72_cadenderestado
                        )
                        values (iCodigoMunicipio,
                                rCgm.z01_muncon,
                                iCodigoEstado
                               );
                    end if;

                end if;
                /* ---------------------------- Fim do tratamento do Municipio ----------------------------*/

                /* ---------------------------- Inicio do tratamento do Bairro ----------------------------*/
                /* Se o z01_bairro for igual a vazio */
                if (rCgm.z01_baicon = '') then

                    if lRaise then
                        raise notice 'Definindo bairro 0-Não Informado para o endereço';
                    end if;

                    iCodigoBairro := 0;

                else /* Se o z01_bairro diferente de vazio pesquisar se existe senão incluir */

                    select db73_sequencial
                    into iCodigoBairro
                    from cadenderbairro
                    where lower(to_ascii(db73_descricao)) = lower(to_ascii(rCgm.z01_baicon))
                      and db73_cadendermunicipio = iCodigoMunicipio;

                    if not found then

                        if lRaise then
                            raise notice 'Bairro não encontrado ! incluindo .....';
                        end if;

                        iCodigoBairro := nextval('cadenderbairro_db73_sequencial_seq');
                        insert into cadenderbairro (db73_sequencial,
                                                    db73_descricao,
                                                    db73_cadendermunicipio
                        )
                        values (iCodigoBairro,
                                rCgm.z01_baicon,
                                iCodigoMunicipio
                               );
                    end if;
                end if;

                /* ---------------------------- Fim do tratamento do Bairro -------------------------------*/

                /* ---------------------------- Inicio do tratamento da Rua  -------------------------------*/
                /* Se o bairro for igual a vazio */
                if (rCgm.z01_endcon = '') then

                    if lRaise then
                        raise notice 'Endereco não informado -- Inclusão Cancelada';
                    end if;

                    return null;
                else /* Se o z01_ender for diferente de vazio pesquisar se existe senão incluir */

                    select db74_sequencial
                    into iCodigoRua
                    from cadenderrua
                    where lower(to_ascii(db74_descricao)) = lower(to_ascii(rCgm.z01_endcon))
                      and db74_cadendermunicipio = iCodigoMunicipio;

                    if not found then

                        if lRaise then
                            raise notice 'Rua não encontrado ! incluindo ..... ';
                        end if;

                        iCodigoRua := nextval('cadenderrua_db74_sequencial_seq');
                        insert into cadenderrua (db74_sequencial,
                                                 db74_descricao,
                                                 db74_cadendermunicipio
                        )
                        values (iCodigoRua,
                                rCgm.z01_endcon,
                                iCodigoMunicipio
                               );
                    end if;

                end if;

                /* ---------------------------- Fim do tratamento da Rua -------------------------------*/
                /* ---------------------------- Inicio do tratamento da RuasTipo -----------------------*/
                perform db85_sequencial
                from cadenderruaruastipo
                where db85_cadenderrua = iCodigoRua;

                if not found then

                    if lRaise then
                        raise notice 'Incluindo na cadenderruaruastipo';
                    end if;

                    insert into cadenderruaruastipo (db85_sequencial,
                                                     db85_cadenderrua,
                                                     db85_ruastipo
                    )
                    values (nextval('cadenderruaruastipo_db85_sequencial_seq'),
                            iCodigoRua,
                            3
                           );
                end if;

                /* ---------------------------- Fim do tratamento da RuasTipo --------------------------*/

                /* ---------------------------- Inicio do tratamento do Vinculo da Rua com Bairro ------*/
                select db87_sequencial
                into iCodigoBairroRua
                from cadenderbairrocadenderrua
                where db87_cadenderrua    = iCodigoRua
                  and db87_cadenderbairro = iCodigoBairro;

                if not found then

                    if lRaise then
                        raise notice 'Incluindo na cadenderbairrocadenderrua';
                    end if;

                    iCodigoBairroRua := nextval('cadenderbairrocadenderrua_db87_sequencial_seq');
                    insert into cadenderbairrocadenderrua (db87_sequencial,
                                                           db87_cadenderrua,
                                                           db87_cadenderbairro
                    )
                    values (iCodigoBairroRua,
                            iCodigoRua,
                            iCodigoBairro
                           );

                end if;
                /* ---------------------------- Fim do tratamento do Vinculo da Rua com Bairro ---------*/
                /* ---------------------------- Inicio do tratamento da Local --------------------------*/
                select db75_sequencial
                into iCodigoLocal
                from cadenderlocal
                where db75_cadenderbairrocadenderrua = iCodigoBairroRua;

                if not found then

                    if lRaise then
                        raise notice 'Icluindo na cadenderlocal';
                    end if;

                    iCodigoLocal := nextval('cadenderlocal_db75_sequencial_seq');
                    insert into cadenderlocal (db75_sequencial,
                                               db75_cadenderbairrocadenderrua,
                                               db75_numero
                    )
                    values (iCodigoLocal,
                            iCodigoBairroRua,
                            rCgm.z01_numcon
                           );

                end if;
                /* ---------------------------- Fim do tratamento da Local -----------------------------*/

                /* ---------------------------- Inicio do tratamento da Endereco -----------------------*/
                iCodigoEndereco := nextval('endereco_db76_sequencial_seq');

                if lRaise then
                    raise notice 'Inserindo na Endereco {%}',iCodigoEndereco;
                end if;

                insert into endereco (db76_sequencial,
                                      db76_cadenderlocal,
                                      db76_complemento,
                                      db76_cep
                )
                values (iCodigoEndereco,
                        iCodigoLocal,
                        rCgm.z01_comcon,
                        rCgm.z01_cepcon
                       );
                /* ---------------------------- Fim do tratamento da Endereco --------------------------*/

                /* ---------------------------- Inicio do tratamento da cgmendereco --------------------*/
                if lRaise then
                    raise notice 'Inserindo na cgmendereco';
                end if;

                insert into cgmendereco (z07_sequencial,
                                         z07_endereco,
                                         z07_numcgm,
                                         z07_tipo
                )
                values (nextval('cgmendereco_z07_sequencial_seq'),
                        iCodigoEndereco,
                        iCodigoCgm,
                        'S'
                       );
                /* ---------------------------- Fim do tratamento da cgmendereco -----------------------*/
            else  /* aqui se ja exisitir na cgmendereco */

                select db74_sequencial,
                       db74_descricao,
                       db75_numero,
                       db73_sequencial,
                       db73_descricao,
                       db72_sequencial,
                       db72_descricao,
                       db71_sequencial,
                       db71_descricao,
                       db71_sigla,
                       db76_sequencial,
                       db76_cep,
                       db76_pontoref,
                       db76_condominio,
                       db76_loteamento,
                       db76_caixapostal,
                       db76_complemento
                into rEndereco
                from endereco
                         join cadenderlocal             on cadenderlocal.db75_sequencial = endereco.db76_cadenderlocal
                         join cadenderbairrocadenderrua on cadenderbairrocadenderrua.db87_sequencial = cadenderlocal.db75_cadenderbairrocadenderrua
                         join cadenderrua               on cadenderrua.db74_sequencial = cadenderbairrocadenderrua.db87_cadenderrua
                         join cadenderbairro            on cadenderbairro.db73_sequencial = cadenderbairrocadenderrua.db87_cadenderbairro
                         join cadendermunicipio         on cadendermunicipio.db72_sequencial = cadenderrua.db74_cadendermunicipio
                         join cadendermunicipiosistema
                           on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                          and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                         join cadenderestado            on cadenderestado.db71_sequencial = cadendermunicipio.db72_cadenderestado
                where db76_sequencial = iCodigoEndereco;

                if not found then

                    if lRaise then
                        raise notice 'Falha ao ler endereco completo para o cgm{%} e  endereco{%}',iCodigoCgm,iCodigoEndereco;
                    end if;

                    return null;
                end if;

                /* Verificar se houve mudança no estado */
                if (rEndereco.db71_sigla != rCgm.z01_ufcon) then
                    select db71_sequencial
                    into iCodigoEstado
                    from cadenderestado
                    where db71_sigla = rCgm.z01_ufcon;

                    if not found then

                        if lRaise then
                            raise notice 'Falha ao ler estado para o cgm';
                        end if;

                        return null;
                    end if;

                else
                    select db71_sequencial
                    into iCodigoEstado
                    from cadenderestado
                    where db71_sigla = rEndereco.db71_sigla;

                    if not found then

                        if lRaise then
                            raise notice 'Falha ao ler estado do endereco';
                        end if;

                        return null;
                    end if;

                end if;/*Fim do if do estado*/
                /* Fim da Verificação da mudança no estado*/

                /* Inicio da verificação de mudança no municipio */
                /* se z01_munic vazio atribui 0-Não Informaado e busca codigo do estado default 'RS' */
                if (rCgm.z01_muncon = '') then
                    iCodigoMunicipio := 0;
                    select db71_sequencial
                    into iCodigoEstado
                    from cadenderestado
                    where db71_sigla = rCadEnderParam.db71_sigla;

                    if not found then

                        if lRaise then
                            raise notice 'Falha ao ler codigo do estado para municipio NI';
                        end if;

                        return null;
                    end if;
                    /* Verifica se houve mudança no municipio cadastrado
                     * procurar pelo z01_munic se existe se não cadastrar
                     */
                else
                    select db72_sequencial
                    into iCodigoMunicipio
                    from cadendermunicipio
                         join cadendermunicipiosistema
                           on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                          and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                    where lower(to_ascii(db72_descricao)) = lower(to_ascii(rCgm.z01_muncon));

                    if not found then

                        if lRaise then
                            raise notice 'Municipio não encontrado para o estado cadastrado incluindo....';
                        end if;

                        iCodigoMunicipio := nextval('cadendermunicipio_db72_sequencial_seq');
                        insert into cadendermunicipio (db72_sequencial,
                                                       db72_descricao,
                                                       db72_cadenderestado
                        )
                        values  (iCodigoMunicipio,
                                 rCgm.z01_muncon,
                                 iCodigoEstado
                                );

                    end if;

                end if;/*Fim do if do municipio*/

                /* Fim da verificação de mudança no municipio */

                /* Inicio da verificação de mudança no bairro */
                /* se z01_bairro vazio atribui 0-Não Informado */
                if (rCgm.z01_baicon = '') then

                    iCodigoBairro := 0;

                    /* Verifica se houve mudança no municipio cadastrado
                     * procurar pelo z01_munic se existe se não cadastrar
                     */
                else
                    select db73_sequencial
                    into iCodigoBairro
                    from cadenderbairro
                    where lower(to_ascii(db73_descricao)) = lower(to_ascii(rCgm.z01_baicon))
                      and db73_cadendermunicipio = iCodigoMunicipio;

                    if not found then

                        if lRaise then
                            raise notice 'Bairro não encontrado para o municipio cadastrado incluindo....';
                        end if;

                        iCodigoBairro := nextval('cadenderbairro_db73_sequencial_seq');
                        insert into cadenderbairro (db73_sequencial,
                                                    db73_descricao,
                                                    db73_cadendermunicipio
                        )
                        values  (iCodigoBairro,
                                 rCgm.z01_baicon,
                                 iCodigoMunicipio
                                );

                    end if;

                end if;/*Fim do if do bairro*/

                /* Fim da verificação de mudança no bairro */

                /* Inicio da verificação de mudança na Rua */
                if (rCgm.z01_endcon ='') then

                    if lRaise then
                        raise notice 'Campo z01_ender vazio';
                    end if;

                    return null;
                else
                    select db74_sequencial
                    into iCodigoRua
                    from cadenderrua
                    where lower(to_ascii(db74_descricao)) = lower(to_ascii(rCgm.z01_endcon))
                      and db74_cadendermunicipio = iCodigoMunicipio;

                    if not found then

                        if lRaise then
                            raise notice 'Rua não encontrada para o municipio cadastrado incluindo....';
                        end if;

                        iCodigoRua := nextval('cadenderrua_db74_sequencial_seq');
                        insert into cadenderrua (db74_sequencial,
                                                 db74_descricao,
                                                 db74_cadendermunicipio
                        )
                        values  (iCodigoRua,
                                 rCgm.z01_endcon,
                                 iCodigoMunicipio
                                );

                    end if;
                end if;/* fim do if da Rua*/

                /* Fim da verificação de mudança na Rua */

                /* Inicio da verificação de mudança na RuasTipo */
                perform db85_sequencial
                from cadenderruaruastipo
                where db85_cadenderrua = iCodigoRua;

                if not found then

                    if lRaise then
                        raise notice 'Incluindo na ruastipo';
                    end if;

                    iCodigoRuasTipo := nextval('cadenderruaruastipo_db85_sequencial_seq');
                    insert into cadenderruaruastipo (db85_sequencial,
                                                     db85_cadenderrua,
                                                     db85_ruastipo
                    )
                    values (iCodigoRuasTipo,
                            iCodigoRua,
                            3
                           );

                end if;

                /* Fim da verificação de mudança na RuasTipo */

                /* Inicio da verificação de mudança na BairroRua */
                select db87_sequencial
                into iCodigoBairroRua
                from cadenderbairrocadenderrua
                where db87_cadenderrua    = iCodigoRua
                  and db87_cadenderbairro = iCodigoBairro;

                if not found then

                    if lRaise then
                        raise notice 'Incluindo na BairroRua';
                    end if;


                    iCodigoBairroRua := nextval('cadenderbairrocadenderrua_db87_sequencial_seq');
                    insert into cadenderbairrocadenderrua (db87_sequencial,
                                                           db87_cadenderrua,
                                                           db87_cadenderbairro
                    )
                    values (iCodigoBairroRua,
                            iCodigoRua,
                            iCodigoBairro::integer
                           );

                end if;
                /* Fim da verificação de mudança na BairroRua */

                /* Inicio da verificação de mudança na Local */
                select db75_sequencial
                into iCodigoLocal
                from cadenderlocal
                where db75_cadenderbairrocadenderrua = iCodigoBairroRua
                  and db75_numero = cast(rCgm.z01_numcon as text);

                if not found then

                    iCodigoLocal := nextval('cadenderlocal_db75_sequencial_seq');
                    insert into cadenderlocal (db75_sequencial,
                                               db75_cadenderbairrocadenderrua,
                                               db75_numero
                    )
                    values (iCodigoLocal,
                            iCodigoBairroRua,
                            rCgm.z01_numcon
                           );

                end if;
                /* Fim da verificação de mudança na Local */

                /* Inicio da verificação de mudança na Endereco */
                select count(*)
                into iNumCgmEndereco
                from cgmendereco
                where z07_endereco = iCodigoEndereco
                having count(*) > 1;

                /*delete na cgmendereco*/
                delete from cgmendereco
                where z07_numcgm = iCodigoCgm
                  and z07_tipo   = 'S';

                if (iNumCgmEndereco > 0 and (rCgm.z01_comcon != rEndereco.db76_complemento)) then
                    if lRaise then
                        raise notice 'Existe mais de um cgm no mesmo endereco inserindo endereco novo';
                    end if;

                    iCodigoEndereco := nextval('endereco_db76_sequencial_seq');
                    insert into endereco (db76_sequencial,
                                          db76_cadenderlocal,
                                          db76_complemento,
                                          db76_caixapostal,
                                          db76_loteamento,
                                          db76_condominio,
                                          db76_pontoref,
                                          db76_cep
                    )
                    values (iCodigoEndereco,
                            iCodigoLocal,
                            rCgm.z01_comcon,
                            rEndereco.db76_caixapostal,
                            rEndereco.db76_loteamento,
                            rEndereco.db76_condominio,
                            rEndereco.db76_pontoref,
                            rCgm.z01_cepcon
                           );



                else

                    perform db76_sequencial
                    from endereco
                    where db76_sequencial    = iCodigoEndereco
                      and db76_cadenderlocal = iCodigoLocal;

                    if not found then
                        iCodigoEndereco := nextval('endereco_db76_sequencial_seq');
                        insert into endereco (db76_sequencial,
                                              db76_cadenderlocal,
                                              db76_complemento,
                                              db76_caixapostal,
                                              db76_loteamento,
                                              db76_condominio,
                                              db76_pontoref,
                                              db76_cep
                        )
                        values (iCodigoEndereco,
                                iCodigoLocal,
                                rCgm.z01_comcon,
                                rEndereco.db76_caixapostal,
                                rEndereco.db76_loteamento,
                                rEndereco.db76_condominio,
                                rEndereco.db76_pontoref,
                                rCgm.z01_cepcon
                               );


                    else
                        update endereco set db76_cadenderlocal = iCodigoLocal,
                                            db76_complemento   = rCgm.z01_comcon,
                                            db76_cep           = rCgm.z01_cepcon
                        where db76_sequencial = rEndereco.db76_sequencial;
                    end if;
                end if;

                /* Inserindo na cgmendereco */
                insert into cgmendereco(z07_sequencial,
                                        z07_endereco,
                                        z07_numcgm,
                                        z07_tipo
                )
                values (nextval('cgmendereco_z07_sequencial_seq'),
                        iCodigoEndereco,
                        iCodigoCgm,
                        'S'
                       );

                /* Fim da verificação de mudança na Endereco */
                if lRaise then
                    raise notice 'Fim da atualização do cgm {%} endereco {%}',iCodigoCgm,iCodigoEndereco;
                end if;
            end if;/* Finaliza bloco de quando não existe ligação com a tabela cgmendereco para tipo 'P' */
        end if; /* Fecha o if do endereço primario tipo 'P' */

        return null;

    end;

$$ LANGUAGE plpgsql;

SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER FUNCTION fc_cgmendereco_incalt SET SCHEMA protocolo;

CREATE OR REPLACE FUNCTION protocolo.fc_cgmendereco_incalt()
  RETURNS TRIGGER AS
$$
declare

	iCodigoEstado       integer default 0;
	iCodigoMunicipio    integer default 0;
	iCodigoBairro       integer default 0;
	iCodigoRua          integer default 0;
	iCodigoBairroRua    integer default 0;
	iCodigoLocal        integer default 0;
	iCodigoEndereco	    integer default 0;
	iCodigoRuasTipo	    integer default 0;
  iCodigoCgm          integer default 0;
	iCodigoCgmEndereco  integer default 0;
  iNumCgmEndereco     integer default 0;

  lTriggerHabilitada  boolean default true;
  lRaise              boolean default false;

  sOperacao           text := '';

	rCgm                record;
  rCadEnderParam      record;
  rEndereco           record;

begin

   lRaise  := ( case when fc_getsession('DB_debugon') is null then false else true end );

   lTriggerHabilitada := ( case when fc_getsession('DB_habilita_trigger_endereco') is null then true else false end );

   if not lTriggerHabilitada then
     return NEW;
   end if;

  sOperacao := upper(TG_OP);
  if (sOperacao = 'INSERT') then
      iCodigoCgm := NEW.z01_numcgm;
      insert into personacgm (p121_cgm,p121_persona) values (NEW.z01_numcgm ,1);
  else
      iCodigoCgm := OLD.z01_numcgm;
  end if;

  /* Verificar se o CGM alterado esta incluído na cgmendereco
  	 se estiver tem que verificar campo a campo se houve alteração
  	 se não estiver tem que gerar um endereco novo e fazer a ligação
  	 da cgmendereco
  */

  select z01_numcgm,
         z01_ender,
         z01_numero::varchar,
         z01_compl,
         z01_bairro,
         z01_munic,
         z01_uf,
         z01_cep,
         z01_endcon,
         z01_numcon,
         z01_comcon,
         z01_baicon,
         z01_muncon,
         z01_ufcon,
         z01_cepcon
  into rCgm
  from cgm
  where z01_numcgm = iCodigoCgm;

  if not found then
     if lRaise then
        raise notice 'Nenhum registro encontrado para o CGM {%}', iCodigoCgm;
     end if;

     return null;
  end if;

  if lRaise then
     raise notice 'Cgm encontrado ';
  end if;

  if (rCgm.z01_ender = '') then
      if lRaise then
         raise notice 'Endereço informado vazio ';
      end if;

      return null;
  end if;

	/* Leitura dos parâmetros do cadastro de endereço cadenderparam */

  select db99_cadenderpais,
         db99_cadenderestado,
         db99_cadendermunicipio,
         db70_descricao,
         db71_descricao,
         db71_sigla,
         db72_descricao
  into rCadEnderParam
  from cadenderparam
       join cadenderpais      on cadenderpais.db70_sequencial      = cadenderparam.db99_cadenderpais
       join cadenderestado    on cadenderestado.db71_sequencial    = cadenderparam.db99_cadenderestado
       join cadendermunicipio on cadendermunicipio.db72_sequencial = cadenderparam.db99_cadendermunicipio
       join cadendermunicipiosistema on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                                    and cadendermunicipiosistema.db125_db_sistemaexterno = 4;

  if not found then
    if lRaise then
      raise notice 'Parâmetros do endereço não configurados {cadenderparam}';
    end if;

    return null;
  end if;

  if lRaise then
    raise notice 'Tabela de parâmetros ok !';
  end if;

  if (rCgm.z01_ender != '') then
	    /* Pesquisa para verificar se existe relação do cgm com o endereco {cgmendereco} */
	    select z07_sequencial,
	   		     z07_endereco
	   	  into iCodigoCgmEndereco,
	   		     iCodigoEndereco
	   	  from cgmendereco
	   	 where z07_numcgm = iCodigoCgm
	       and z07_tipo   = 'P';

	      /* Se o cgm não existir na tabela de ligação gerar o endereço e vincular o cidadao ao cgm */
		  if not found then
         if lRaise then
	          raise notice 'Cgm não encontrado na cgmendereco ';
         end if;

         /* ---------------------------- Inicio do tratameno do estado do endereço -----------------------------*/
         /* Atribuindo o codigo do estado da tabela de parametros do endereço {cadenderparam} */
	       iCodigoEstado := rCadEnderParam.db99_cadenderestado;

	       /* Verificar se z01_uf e z01_munic são diferentes de ''
	        *  se não for atribuir o estado default para o municipio não informado RS, 0-Não Informado
	        */
	       if (rCgm.z01_munic = '' or rCgm.z01_uf = '' or rCgm.z01_bairro = '') then
	          select db71_sequencial
	            into iCodigoEstado
	            from cadendermunicipio
                   join cadendermunicipiosistema
                     on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                    and cadendermunicipiosistema.db125_db_sistemaexterno = 4
	                 join cadenderestado on cadenderestado.db71_sequencial = cadendermunicipio.db72_cadenderestado
	           where cadenderestado.db71_sigla = rCadEnderParam.db71_sigla;

	          if not found then
               if lRaise then
                  raise notice 'Falha ao atribuir estado padrão!';
               end if;

               return null;
	          end if;

	       else /* Aqui pesquisa pela sigla do z01_uf informado no cgm */
	          select db71_sequencial
	            into iCodigoEstado
	            from cadenderestado
	           where db71_sigla = trim(rCgm.z01_uf);

	          /* Se não localizar o estado atribuir o estado dos parametros do endereço */
	          if not found then
               if lRaise then
                  raise notice 'Estado não encontrado pela sigla fornecida atribuido dos Parametros do endereco';
               end if;

	             iCodigoEstado := rCadEnderParam.db99_cadenderestado;
	          end if;

	       end if; /* Fechamento do if do estado */
	       /* ---------------------------- Fim do tratameno do estado do endereço -----------------------------*/
	       /* ---------------------------- Inicio do tratameno do estado do Municipio -------------------------*/
         /* Se o z01_munic for igual a vazio */
         if (rCgm.z01_munic = '') then
            if lRaise then
               raise notice 'Definido municipio 0-Não Informado para o endereço';
            end if;
 
            iCodigoMunicipio := 0;
         else /* Se o z01_munic diferente de vazio pesquisar se existe senão incluir*/
	          select db72_sequencial
	            into iCodigoMunicipio
	            from cadendermunicipio
                   join cadendermunicipiosistema
                     on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                    and cadendermunicipiosistema.db125_db_sistemaexterno = 4
	           where db72_descricao      = rCgm.z01_munic
	             and db72_cadenderestado = iCodigoEstado;

	          /* Se não encontrou o municipio entao tem que incluir o mesmo */
	          if not found then
                if lRaise then
                   raise notice 'Municipio não encontrado ! incluindo .....';
	              end if;

                iCodigoMunicipio := nextval('cadendermunicipio_db72_sequencial_seq');

                insert into cadendermunicipio (db72_sequencial,
	                                             db72_descricao,
	                                             db72_cadenderestado
	                                            )
                                       values (iCodigoMunicipio,
                                               rCgm.z01_munic,
                                               iCodigoEstado
                                              );
	          end if;

	       end if;
	       /* ---------------------------- Fim do tratamento do Municipio ----------------------------*/
	       /* ---------------------------- Inicio do tratamento do Bairro ----------------------------*/
	       /* Se o z01_bairro for igual a vazio */
	       if (rCgm.z01_bairro = '') then
            if lRaise then
               raise notice 'Definindo bairro 0-Não Informado para o endereço';
            end if;

            iCodigoBairro := 0;
         else /* Se o z01_bairro diferente de vazio pesquisar se existe senão incluir */
	          select db73_sequencial
	            into iCodigoBairro
	            from cadenderbairro
	           where db73_descricao = rCgm.z01_bairro
	             and db73_cadendermunicipio = iCodigoMunicipio;

	          if not found then
               if lRaise then
                  raise notice 'Bairro não encontrado ! incluindo .....';
               end if;

               iCodigoBairro := nextval('cadenderbairro_db73_sequencial_seq');
               insert into cadenderbairro (db73_sequencial,
                                           db73_descricao,
                                           db73_cadendermunicipio
	                                        )
                                   values (iCodigoBairro,
                                           rCgm.z01_bairro,
                                           iCodigoMunicipio
	                                        );
	          end if;
	       end if;
	       /* ---------------------------- Fim do tratamento do Bairro -------------------------------*/
	       /* ---------------------------- Inicio do tratamento da Rua  -------------------------------*/
         /* Se o bairro for igual a vazio */
         if (rCgm.z01_ender = '') then
            if lRaise then
               raise notice 'Endereco não informado -- Inclusão Cancelada';
            end if;

            return null;
         else /* Se o z01_ender for diferente de vazio pesquisar se existe senão incluir */
	          select db74_sequencial
	            into iCodigoRua
	            from cadenderrua
	           where db74_descricao = rCgm.z01_ender
	             and db74_cadendermunicipio = iCodigoMunicipio;

	          if not found then
               if lRaise then
                  raise notice 'Rua não encontrado ! incluindo ..... ';
               end if;

               iCodigoRua := nextval('cadenderrua_db74_sequencial_seq');
               insert into cadenderrua (db74_sequencial,
                                        db74_descricao,
                                        db74_cadendermunicipio
	                                     )
	                              values (iCodigoRua,
	                                      rCgm.z01_ender,
	                                      iCodigoMunicipio
	                                     );
	          end if;
	       end if;
	       /* ---------------------------- Fim do tratamento da Rua -------------------------------*/
	       /* ---------------------------- Inicio do tratamento da RuasTipo -----------------------*/
	       perform db85_sequencial
	          from cadenderruaruastipo
	         where db85_cadenderrua = iCodigoRua;

	       if not found then
            if lRaise then
               raise notice 'Incluindo na cadenderruaruastipo';
            end if;

	          insert into cadenderruaruastipo (db85_sequencial,
	                                           db85_cadenderrua,
	                                           db85_ruastipo
	                                          )
	                                   values (nextval('cadenderruaruastipo_db85_sequencial_seq'),
	                                           iCodigoRua,
	                                           3
	                                          );
	       end if;

	       /* ---------------------------- Fim do tratamento da RuasTipo --------------------------*/
	       /* ---------------------------- Inicio do tratamento do Vinculo da Rua com Bairro ------*/
         select db87_sequencial
           into iCodigoBairroRua
           from cadenderbairrocadenderrua
          where db87_cadenderrua    = iCodigoRua
            and db87_cadenderbairro = iCodigoBairro;

         if not found then
            if lRaise then
               raise notice 'Incluindo na cadenderbairrocadenderrua';
            end if;

            iCodigoBairroRua := nextval('cadenderbairrocadenderrua_db87_sequencial_seq');
	          insert into cadenderbairrocadenderrua (db87_sequencial,
	                                                 db87_cadenderrua,
	                                                 db87_cadenderbairro
	                                                )
	                                         values (iCodigoBairroRua,
	                                                 iCodigoRua,
	                                                 iCodigoBairro
	                                                );
	       end if;
         /* ---------------------------- Fim do tratamento do Vinculo da Rua com Bairro ---------*/
         /* ---------------------------- Inicio do tratamento da Local --------------------------*/
         select db75_sequencial
           into iCodigoLocal
           from cadenderlocal
          where db75_cadenderbairrocadenderrua = iCodigoBairroRua;

	       if not found then
            if lRaise then
               raise notice 'Icluindo na cadenderlocal';
            end if;

            iCodigoLocal := nextval('cadenderlocal_db75_sequencial_seq');
            insert into cadenderlocal (db75_sequencial,
                                       db75_cadenderbairrocadenderrua,
                                       db75_numero
                                      )
                               values (iCodigoLocal,
                                       iCodigoBairroRua,
                                       rCgm.z01_numero
                                      );

	       end if;
	       /* ---------------------------- Fim do tratamento da Local -----------------------------*/
	       /* ---------------------------- Inicio do tratamento da Endereco -----------------------*/
	       iCodigoEndereco := nextval('endereco_db76_sequencial_seq');

         if lRaise then
	          raise notice 'Inserindo na Endereco {%}',iCodigoEndereco;
         end if;

         insert into endereco (db76_sequencial,
                               db76_cadenderlocal,
                               db76_complemento,
                               db76_cep
                              )
                       values (iCodigoEndereco,
                               iCodigoLocal,
                               rCgm.z01_compl,
                               rCgm.z01_cep
                              );
	       /* ---------------------------- Fim do tratamento da Endereco --------------------------*/
	       /* ---------------------------- Inicio do tratamento da cgmendereco --------------------*/
         if lRaise then
  	        raise notice 'Inserindo na cgmendereco';
         end if;

	       insert into cgmendereco (z07_sequencial,
                                  z07_endereco,
                                  z07_numcgm,
                                  z07_tipo
                                 )
                          values (nextval('cgmendereco_z07_sequencial_seq'),
                                  iCodigoEndereco,
                                  iCodigoCgm,
                                  'P'
                                 );
	       /* ---------------------------- Fim do tratamento da cgmendereco -----------------------*/
	    else  /* aqui se ja exisitir na cgmendereco */
         select db74_sequencial,
                db74_descricao,
                db75_numero,
                db73_sequencial,
                db73_descricao,
                db72_sequencial,
                db72_descricao,
                db71_sequencial,
                db71_descricao,
                db71_sigla,
                db76_sequencial,
                db76_cep,
                db76_pontoref,
                db76_condominio,
                db76_loteamento,
                db76_caixapostal,
                db76_complemento
           into rEndereco
           from endereco
			          join cadenderlocal             on cadenderlocal.db75_sequencial = endereco.db76_cadenderlocal
			          join cadenderbairrocadenderrua on cadenderbairrocadenderrua.db87_sequencial = cadenderlocal.db75_cadenderbairrocadenderrua
			          join cadenderrua               on cadenderrua.db74_sequencial = cadenderbairrocadenderrua.db87_cadenderrua
			          join cadenderbairro            on cadenderbairro.db73_sequencial = cadenderbairrocadenderrua.db87_cadenderbairro
			          join cadendermunicipio         on cadendermunicipio.db72_sequencial = cadenderrua.db74_cadendermunicipio
                join cadendermunicipiosistema
                  on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                 and cadendermunicipiosistema.db125_db_sistemaexterno = 4
			          join cadenderestado            on cadenderestado.db71_sequencial = cadendermunicipio.db72_cadenderestado
         where db76_sequencial = iCodigoEndereco;

         if not found then
             if lRaise then
                raise notice 'Falha ao ler endereco completo para o cgm{%} e  endereco{%}',iCodigoCgm,iCodigoEndereco;
             end if;

             return null;
         end if;

         /* Verificar se houve mudança no estado */
         if (rEndereco.db71_sigla != rCgm.z01_uf) then
            select db71_sequencial
              into iCodigoEstado
              from cadenderestado
            where db71_sigla = rCgm.z01_uf;

            if not found then
               if lRaise then
                  raise notice 'Falha ao ler estado para o cgm';
               end if;

               return null;
            end if;
         else
            select db71_sequencial
              into iCodigoEstado
              from cadenderestado
            where db71_sigla = rEndereco.db71_sigla;

            if not found then
               if lRaise then
                  raise notice 'Falha ao ler estado do endereco';
               end if;

               return null;
            end if;
         end if;/*Fim do if do estado*/
         /* Fim da Verificação da mudança no estado*/

         /* Inicio da verificação de mudança no municipio */

         /* se z01_munic vazio atribui 0-Não Informaado e busca codigo do estado default 'RS' */
         if (rCgm.z01_munic = '') then
            iCodigoMunicipio := 0;
            select db71_sequencial
              into iCodigoEstado
              from cadenderestado
            where db71_sigla = rCadEnderParam.db71_sigla;

            if not found then
               if lRaise then
                  raise notice 'Falha ao ler codigo do estado para municipio NI';
               end if;

               return null;
            end if;
            /* Verifica se houve mudança no municipio cadastrado
             * procurar pelo z01_munic se existe se não cadastrar
             */
         else
            select db72_sequencial
              into iCodigoMunicipio
              from cadendermunicipio
                   join cadendermunicipiosistema
                     on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                    and cadendermunicipiosistema.db125_db_sistemaexterno = 4
            where db72_descricao = rCgm.z01_munic;

            if not found then
               if lRaise then
                  raise notice 'Municipio não encontrado para o estado cadastrado incluindo....';
               end if;

               iCodigoMunicipio := nextval('cadendermunicipio_db72_sequencial_seq');
               insert into cadendermunicipio (db72_sequencial,
                                              db72_descricao,
                                              db72_cadenderestado
                                             )
                                      values  (iCodigoMunicipio,
                                               rCgm.z01_munic,
                                               iCodigoEstado
                                              );

            end if;
         end if;/*Fim do if do municipio*/

         /* Fim da verificação de mudança no municipio */
         /* Inicio da verificação de mudança no bairro */
         /* se z01_bairro vazio atribui 0-Não Informado */
         if (rCgm.z01_bairro = '') then
            iCodigoBairro := 0;
            /* Verifica se houve mudança no municipio cadastrado
             * procurar pelo z01_munic se existe se não cadastrar
             */
         else
           select db73_sequencial
             into iCodigoBairro
             from cadenderbairro
            where db73_descricao = rCgm.z01_bairro
              and db73_cadendermunicipio = iCodigoMunicipio;

            if not found then
               if lRaise then
                  raise notice 'Bairro não encontrado para o municipio cadastrado incluindo....';
               end if;

               iCodigoBairro := nextval('cadenderbairro_db73_sequencial_seq');
               insert into cadenderbairro (db73_sequencial,
                                           db73_descricao,
                                           db73_cadendermunicipio
                                          )
                                  values  (iCodigoBairro,
                                           rCgm.z01_bairro,
                                           iCodigoMunicipio
                                          );
            end if;
         end if;/*Fim do if do bairro*/


         /* Fim da verificação de mudança no bairro */
         /* Inicio da verificação de mudança na Rua */
         if (rCgm.z01_ender ='') then
            if lRaise then
               raise notice 'Campo z01_ender vazio';
            end if;

            return null;
         else
            select db74_sequencial
              into iCodigoRua
              from cadenderrua
            where db74_descricao = rCgm.z01_ender
              and db74_cadendermunicipio = iCodigoMunicipio;

            if not found then
               if lRaise then
                  raise notice 'Rua não encontrada para o municipio cadastrado incluindo....';
               end if;

               iCodigoRua := nextval('cadenderrua_db74_sequencial_seq');
               insert into cadenderrua (db74_sequencial,
                                        db74_descricao,
                                        db74_cadendermunicipio
                                       )
                               values  (iCodigoRua,
                                        rCgm.z01_ender,
                                        iCodigoMunicipio
                                       );
            end if;
         end if;/* fim do if da Rua*/

         /* Fim da verificação de mudança na Rua */
         /* Inicio da verificação de mudança na RuasTipo */
         perform db85_sequencial
            from cadenderruaruastipo
         where db85_cadenderrua = iCodigoRua;

         if not found then
            if lRaise then
               raise notice 'Incluindo na ruastipo';
            end if;

            iCodigoRuasTipo := nextval('cadenderruaruastipo_db85_sequencial_seq');
            insert into cadenderruaruastipo (db85_sequencial,
                                             db85_cadenderrua,
                                             db85_ruastipo
                                            )
                                     values (iCodigoRuasTipo,
                                             iCodigoRua,
                                             3
                                            );
         end if;

            /* Fim da verificação de mudança na RuasTipo */
            /* Inicio da verificação de mudança na BairroRua */
             select db87_sequencial
               into iCodigoBairroRua
               from cadenderbairrocadenderrua
              where db87_cadenderrua = iCodigoRua
                and db87_cadenderbairro = iCodigoBairro;

            if not found then

              if lRaise then
                raise notice 'Incluindo na BairroRua';
              end if;

              iCodigoBairroRua := nextval('cadenderbairrocadenderrua_db87_sequencial_seq');
              insert into cadenderbairrocadenderrua (db87_sequencial,
                                                     db87_cadenderrua,
                                                     db87_cadenderbairro
                                                    )
                                             values (iCodigoBairroRua,
                                                     iCodigoRua,
                                                     iCodigoBairro
                                                    );

            end if;
            /* Fim da verificação de mudança na BairroRua */

            /* Inicio da verificação de mudança na Local */

             select db75_sequencial
               into iCodigoLocal
               from cadenderlocal
              where db75_cadenderbairrocadenderrua = iCodigoBairroRua
                and db75_numero = cast(rCgm.z01_numero as text);

            if not found then

              iCodigoLocal := nextval('cadenderlocal_db75_sequencial_seq');
              insert into cadenderlocal (db75_sequencial,
                                         db75_cadenderbairrocadenderrua,
                                         db75_numero
                                        )
                                 values (iCodigoLocal,
                                         iCodigoBairroRua,
                                         rCgm.z01_numero
                                        );

            end if;
            /* Fim da verificação de mudança na Local */

            /* Inicio da verificação de mudança na Endereco */
            select count(*)
              into iNumCgmEndereco
              from cgmendereco
             where z07_endereco = iCodigoEndereco
            having count(*) > 1;

            /*delete na cgmendereco*/
            delete from cgmendereco
                    where z07_numcgm = iCodigoCgm
                      and z07_tipo   = 'P';


            if (iNumCgmEndereco > 0 and (rCgm.z01_compl != rEndereco.db76_complemento)) then

              if lRaise then
                raise notice 'Existe mais de um cgm no mesmo endereco inserindo endereco novo';
              end if;

              iCodigoEndereco := nextval('endereco_db76_sequencial_seq');
              insert into endereco (db76_sequencial,
                                    db76_cadenderlocal,
                                    db76_complemento,
                                    db76_caixapostal,
                                    db76_loteamento,
                                    db76_condominio,
                                    db76_pontoref,
                                    db76_cep
                                   )
                            values (iCodigoEndereco,
                                    iCodigoLocal,
                                    rCgm.z01_compl,
                                    rEndereco.db76_caixapostal,
                                    rEndereco.db76_loteamento,
                                    rEndereco.db76_condominio,
                                    rEndereco.db76_pontoref,
                                    rCgm.z01_cep
                                   );



            else

              perform db76_sequencial
                 from endereco
                where db76_sequencial    = iCodigoEndereco
                  and db76_cadenderlocal = iCodigoLocal;

              if not found then
                iCodigoEndereco := nextval('endereco_db76_sequencial_seq');
                insert into endereco (db76_sequencial,
	                                  db76_cadenderlocal,
	                                  db76_complemento,
	                                  db76_caixapostal,
	                                  db76_loteamento,
	                                  db76_condominio,
	                                  db76_pontoref,
	                                  db76_cep
	                                 )
	                          values (iCodigoEndereco,
	                                  iCodigoLocal,
	                                  rCgm.z01_compl,
	                                  rEndereco.db76_caixapostal,
	                                  rEndereco.db76_loteamento,
	                                  rEndereco.db76_condominio,
	                                  rEndereco.db76_pontoref,
	                                  rCgm.z01_cep
	                                 );


              else
	              update endereco set db76_cadenderlocal = iCodigoLocal,
	                                  db76_complemento   = rCgm.z01_compl,
	                                  db76_cep           = rCgm.z01_cep
	                            where db76_sequencial = rEndereco.db76_sequencial;
              end if;
            end if;

            /* Inserindo na cgmendereco */
            insert into cgmendereco(z07_sequencial,
                                      z07_endereco,
                                      z07_numcgm,
                                      z07_tipo
                                     )
                              values (nextval('cgmendereco_z07_sequencial_seq'),
                                      iCodigoEndereco,
                                      iCodigoCgm,
                                      'P'
                                     );


            /* Fim da verificação de mudança na Endereco */
            if lRaise then
              raise notice 'Fim da atualização do cgm {%} endereco {%}',iCodigoCgm,iCodigoEndereco;
            end if;

	      end if;/* Finaliza bloco de quando não existe ligação com a tabela cgmendereco para tipo 'P' */
    end if; /* Fecha o if do endereço primario tipo 'P' */
/*----------------------------------------  Aqui inicia no endereço secundario  ----------------------------------*/
    if (rCgm.z01_endcon != '') then
        /* Pesquisa para verificar se existe relação do cgm com o endereco {cgmendereco} */
        iCodigoEstado      := 0;
        iCodigoMunicipio   := 0;
        iCodigoBairro      := 0;
        iCodigoRua         := 0;
        iCodigoBairroRua   := 0;
        iCodigoLocal       := 0;
        iCodigoEndereco    := 0;
        iCodigoRuasTipo    := 0;
        iCodigoCgmEndereco := 0;
        iNumCgmEndereco    := 0;

        select z07_sequencial,
               z07_endereco
          into iCodigoCgmEndereco,
               iCodigoEndereco
          from cgmendereco
         where z07_numcgm = iCodigoCgm
           and z07_tipo   = 'S';

          /* Se o cgm não existir na tabela de ligação gerar o endereço e vincular o cidadao ao cgm */
          if not found then

            if lRaise then
              raise notice 'Cgm não encontrado na cgmendereco ';
            end if;

           /* ---------------------------- Inicio do tratameno do estado do endereço -----------------------------*/
           /* Atribuindo o codigo do estado da tabela de parametros do endereço {cadenderparam} */
            iCodigoEstado := rCadEnderParam.db99_cadenderestado;

           /* Verificar se z01_uf e z01_munic são diferentes de ''
            *  se não for atribuir o estado default para o municipio não informado RS, 0-Não Informado
            */
            if (rCgm.z01_muncon = '' or rCgm.z01_ufcon = '' or rCgm.z01_baicon = '') then

              select db71_sequencial
                into iCodigoEstado
                from cadendermunicipio
                   join cadendermunicipiosistema
                     on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                    and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                   join cadenderestado on cadenderestado.db71_sequencial = cadendermunicipio.db72_cadenderestado
               where cadenderestado.db71_sigla = rCadEnderParam.db71_sigla;

               if not found then

                if lRaise then
                  raise notice 'Falha ao atribuir estado padrão!';
                end if;

                return null;
               end if;

            else /* Aqui pesquisa pela sigla do z01_uf informado no cgm*/
              select db71_sequencial
                into iCodigoEstado
                from cadenderestado
               where db71_sigla = trim(rCgm.z01_ufcon);
              /* Se não localizar o estado atribuir o estado dos parametros do endereço */
              if not found then

                if lRaise then
                  raise notice 'Estado não encontrado pela sigla fornecida atribuido dos Parametros do endereco';
                end if;

                iCodigoEstado := rCadEnderParam.db99_cadenderestado;
              end if;

            end if;/*Fechamento do if do estado*/
           /* ---------------------------- Fim do tratameno do estado do endereço -----------------------------*/
           /* ---------------------------- Inicio do tratameno do estado do Municipio -------------------------*/
            /* Se o z01_munic for igual a vazio */
            if (rCgm.z01_muncon = '') then

              if lRaise then
                raise notice 'Definido municipio 0-Não Informado para o endereço';
              end if;

              iCodigoMunicipio := 0;
            else /* Se o z01_munic diferente de vazio pesquisar se existe senão incluir*/

              select db72_sequencial
                into iCodigoMunicipio
                from cadendermunicipio
                     join cadendermunicipiosistema
                       on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                      and cadendermunicipiosistema.db125_db_sistemaexterno = 4
               where db72_descricao      = rCgm.z01_muncon
                 and db72_cadenderestado = iCodigoEstado;
              /* Se não encontrou o municipio entao tem que incluir o mesmo */
              if not found then

                if lRaise then
                  raise notice 'Municipio não encontrado ! incluindo .....';
                end if;

                iCodigoMunicipio := nextval('cadendermunicipio_db72_sequencial_seq');

                insert into cadendermunicipio (db72_sequencial,
                                               db72_descricao,
                                               db72_cadenderestado
                                               )
                                       values (iCodigoMunicipio,
                                               rCgm.z01_muncon,
                                               iCodigoEstado
                                              );
              end if;

            end if;
           /* ---------------------------- Fim do tratamento do Municipio ----------------------------*/
           /* ---------------------------- Inicio do tratamento do Bairro ----------------------------*/
            /* Se o z01_bairro for igual a vazio */
            if (rCgm.z01_baicon = '') then

              if lRaise then
                raise notice 'Definindo bairro 0-Não Informado para o endereço';
              end if;

              iCodigoBairro := 0;

            else /* Se o z01_bairro diferente de vazio pesquisar se existe senão incluir */

              select db73_sequencial
                into iCodigoBairro
                from cadenderbairro
               where db73_descricao = rCgm.z01_baicon
                 and db73_cadendermunicipio = iCodigoMunicipio;

              if not found then

                if lRaise then
                  raise notice 'Bairro não encontrado ! incluindo .....';
                end if;

                iCodigoBairro := nextval('cadenderbairro_db73_sequencial_seq');
                insert into cadenderbairro (db73_sequencial,
                                            db73_descricao,
                                            db73_cadendermunicipio
                                           )
                                    values (iCodigoBairro,
                                            rCgm.z01_baicon,
                                            iCodigoMunicipio
                                           );
              end if;

            end if;
           /* ---------------------------- Fim do tratamento do Bairro -------------------------------*/
           /* ---------------------------- Inicio do tratamento da Rua  -------------------------------*/
            /* Se o bairro for igual a vazio */
            if (rCgm.z01_endcon = '') then

              if lRaise then
                raise notice 'Endereco não informado -- Inclusão Cancelada';
              end if;

              return null;
            else /* Se o z01_ender for diferente de vazio pesquisar se existe senão incluir */

              select db74_sequencial
                into iCodigoRua
                from cadenderrua
               where db74_descricao = rCgm.z01_endcon
                 and db74_cadendermunicipio = iCodigoMunicipio;

              if not found then

                if lRaise then
                  raise notice 'Rua não encontrado ! incluindo ..... ';
                end if;

                iCodigoRua := nextval('cadenderrua_db74_sequencial_seq');
                insert into cadenderrua (db74_sequencial,
                                         db74_descricao,
                                         db74_cadendermunicipio
                                        )
                                 values (iCodigoRua,
                                         rCgm.z01_endcon,
                                         iCodigoMunicipio
                                        );
              end if;

            end if;
           /* ---------------------------- Fim do tratamento da Rua -------------------------------*/
           /* ---------------------------- Inicio do tratamento da RuasTipo -----------------------*/
            perform db85_sequencial
              from cadenderruaruastipo
             where db85_cadenderrua = iCodigoRua;

            if not found then

              if lRaise then
                raise notice 'Incluindo na cadenderruaruastipo';
              end if;

              insert into cadenderruaruastipo (db85_sequencial,
                                               db85_cadenderrua,
                                               db85_ruastipo
                                              )
                                       values (nextval('cadenderruaruastipo_db85_sequencial_seq'),
                                               iCodigoRua,
                                               3
                                              );
            end if;

           /* ---------------------------- Fim do tratamento da RuasTipo --------------------------*/
           /* ---------------------------- Inicio do tratamento do Vinculo da Rua com Bairro ------*/
             select db87_sequencial
               into iCodigoBairroRua
               from cadenderbairrocadenderrua
              where db87_cadenderrua    = iCodigoRua
                and db87_cadenderbairro = iCodigoBairro;

             if not found then

               if lRaise then
                 raise notice 'Incluindo na cadenderbairrocadenderrua';
               end if;

               iCodigoBairroRua := nextval('cadenderbairrocadenderrua_db87_sequencial_seq');
               insert into cadenderbairrocadenderrua (db87_sequencial,
                                                      db87_cadenderrua,
                                                      db87_cadenderbairro
                                                     )
                                              values (iCodigoBairroRua,
                                                      iCodigoRua,
                                                      iCodigoBairro
                                                     );

             end if;
           /* ---------------------------- Fim do tratamento do Vinculo da Rua com Bairro ---------*/
           /* ---------------------------- Inicio do tratamento da Local --------------------------*/
             select db75_sequencial
               into iCodigoLocal
               from cadenderlocal
              where db75_cadenderbairrocadenderrua = iCodigoBairroRua;

             if not found then

                if lRaise then
                  raise notice 'Icluindo na cadenderlocal';
                end if;

                iCodigoLocal := nextval('cadenderlocal_db75_sequencial_seq');
                insert into cadenderlocal (db75_sequencial,
                                           db75_cadenderbairrocadenderrua,
                                           db75_numero
                                          )
                                   values (iCodigoLocal,
                                           iCodigoBairroRua,
                                           rCgm.z01_numcon
                                          );

             end if;
           /* ---------------------------- Fim do tratamento da Local -----------------------------*/
           /* ---------------------------- Inicio do tratamento da Endereco -----------------------*/
             iCodigoEndereco := nextval('endereco_db76_sequencial_seq');

             if lRaise then
               raise notice 'Inserindo na Endereco {%}',iCodigoEndereco;
             end if;

             insert into endereco (db76_sequencial,
                                   db76_cadenderlocal,
                                   db76_complemento,
                                   db76_cep
                                  )
                           values (iCodigoEndereco,
                                   iCodigoLocal,
                                   rCgm.z01_comcon,
                                   rCgm.z01_cepcon
                                  );
           /* ---------------------------- Fim do tratamento da Endereco --------------------------*/
           /* ---------------------------- Inicio do tratamento da cgmendereco --------------------*/

            if lRaise then
              raise notice 'Inserindo na cgmendereco';
            end if;

            insert into cgmendereco (z07_sequencial,
                                     z07_endereco,
                                     z07_numcgm,
                                     z07_tipo
                                    )
                              values (nextval('cgmendereco_z07_sequencial_seq'),
                                     iCodigoEndereco,
                                     iCodigoCgm,
                                     'S'
                                    );
           /* ---------------------------- Fim do tratamento da cgmendereco -----------------------*/


          else  /* aqui se ja exisitir na cgmendereco */

           select db74_sequencial,
                  db74_descricao,
                  db75_numero,
                  db73_sequencial,
                  db73_descricao,
                  db72_sequencial,
                  db72_descricao,
                  db71_sequencial,
                  db71_descricao,
                  db71_sigla,
                  db76_sequencial,
                  db76_cep,
                  db76_pontoref,
                  db76_condominio,
                  db76_loteamento,
                  db76_caixapostal,
                  db76_complemento
             into rEndereco
             from endereco
                  join cadenderlocal             on cadenderlocal.db75_sequencial = endereco.db76_cadenderlocal
                  join cadenderbairrocadenderrua on cadenderbairrocadenderrua.db87_sequencial = cadenderlocal.db75_cadenderbairrocadenderrua
                  join cadenderrua               on cadenderrua.db74_sequencial = cadenderbairrocadenderrua.db87_cadenderrua
                  join cadenderbairro            on cadenderbairro.db73_sequencial = cadenderbairrocadenderrua.db87_cadenderbairro
                  join cadendermunicipio         on cadendermunicipio.db72_sequencial = cadenderrua.db74_cadendermunicipio
                  join cadendermunicipiosistema
                    on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                   and cadendermunicipiosistema.db125_db_sistemaexterno = 4
                  join cadenderestado            on cadenderestado.db71_sequencial = cadendermunicipio.db72_cadenderestado
            where db76_sequencial = iCodigoEndereco;

            if not found then

              if lRaise then
                raise notice 'Falha ao ler endereco completo para o cgm{%} e  endereco{%}',iCodigoCgm,iCodigoEndereco;
              end if;

              return null;
            end if;

            /* Verificar se houve mudança no estado */
            if (rEndereco.db71_sigla != rCgm.z01_ufcon) then
              select db71_sequencial
                into iCodigoEstado
                from cadenderestado
               where db71_sigla = rCgm.z01_ufcon;

              if not found then

                if lRaise then
                  raise notice 'Falha ao ler estado para o cgm';
                end if;

                return null;
              end if;

            else
              select db71_sequencial
                into iCodigoEstado
                from cadenderestado
               where db71_sigla = rEndereco.db71_sigla;

              if not found then

                if lRaise then
                  raise notice 'Falha ao ler estado do endereco';
                end if;

                return null;
              end if;

            end if;/*Fim do if do estado*/
            /* Fim da Verificação da mudança no estado*/

            /* Inicio da verificação de mudança no municipio */

            /* se z01_munic vazio atribui 0-Não Informaado e busca codigo do estado default 'RS' */
            if (rCgm.z01_muncon = '') then
              iCodigoMunicipio := 0;
              select db71_sequencial
                into iCodigoEstado
                from cadenderestado
               where db71_sigla = rCadEnderParam.db71_sigla;

              if not found then

                if lRaise then
                  raise notice 'Falha ao ler codigo do estado para municipio NI';
                end if;

                return null;
              end if;
            /* Verifica se houve mudança no municipio cadastrado
             * procurar pelo z01_munic se existe se não cadastrar
             */
            else
              select db72_sequencial
                into iCodigoMunicipio
                from cadendermunicipio
                     join cadendermunicipiosistema
                       on cadendermunicipiosistema.db125_cadendermunicipio = cadendermunicipio.db72_sequencial
                      and cadendermunicipiosistema.db125_db_sistemaexterno = 4
               where db72_descricao = rCgm.z01_muncon;

              if not found then

                if lRaise then
                  raise notice 'Municipio não encontrado para o estado cadastrado incluindo....';
                end if;

                iCodigoMunicipio := nextval('cadendermunicipio_db72_sequencial_seq');
                insert into cadendermunicipio (db72_sequencial,
                                               db72_descricao,
                                               db72_cadenderestado
                                              )
                                      values  (iCodigoMunicipio,
                                               rCgm.z01_muncon,
                                               iCodigoEstado
                                              );

              end if;

            end if;/*Fim do if do municipio*/

            /* Fim da verificação de mudança no municipio */

            /* Inicio da verificação de mudança no bairro */

            /* se z01_bairro vazio atribui 0-Não Informado */
            if (rCgm.z01_baicon = '') then

              iCodigoBairro := 0;

            /* Verifica se houve mudança no municipio cadastrado
             * procurar pelo z01_munic se existe se não cadastrar
             */
            else
              select db73_sequencial
                into iCodigoBairro
                from cadenderbairro
               where db73_descricao = rCgm.z01_baicon
                 and db73_cadendermunicipio = iCodigoMunicipio;

              if not found then

                if lRaise then
                  raise notice 'Bairro não encontrado para o municipio cadastrado incluindo....';
                end if;

                iCodigoBairro := nextval('cadenderbairro_db73_sequencial_seq');
                insert into cadenderbairro (db73_sequencial,
                                            db73_descricao,
                                            db73_cadendermunicipio
                                           )
                                   values  (iCodigoBairro,
                                            rCgm.z01_baicon,
                                            iCodigoMunicipio
                                           );

              end if;

            end if;/*Fim do if do bairro*/


            /* Fim da verificação de mudança no bairro */

            /* Inicio da verificação de mudança na Rua */
            if (rCgm.z01_endcon ='') then

              if lRaise then
                raise notice 'Campo z01_ender vazio';
              end if;

              return null;
            else
              select db74_sequencial
                into iCodigoRua
                from cadenderrua
               where db74_descricao = rCgm.z01_endcon
                 and db74_cadendermunicipio = iCodigoMunicipio;

              if not found then

                if lRaise then
                  raise notice 'Rua não encontrada para o municipio cadastrado incluindo....';
                end if;

                iCodigoRua := nextval('cadenderrua_db74_sequencial_seq');
                insert into cadenderrua (db74_sequencial,
                                         db74_descricao,
                                         db74_cadendermunicipio
                                        )
                                values  (iCodigoRua,
                                         rCgm.z01_endcon,
                                         iCodigoMunicipio
                                        );

              end if;

            end if;/* fim do if da Rua*/

            /* Fim da verificação de mudança na Rua */

            /* Inicio da verificação de mudança na RuasTipo */
            perform db85_sequencial
               from cadenderruaruastipo
              where db85_cadenderrua = iCodigoRua;

            if not found then

              if lRaise then
                raise notice 'Incluindo na ruastipo';
              end if;

              iCodigoRuasTipo := nextval('cadenderruaruastipo_db85_sequencial_seq');
              insert into cadenderruaruastipo (db85_sequencial,
                                               db85_cadenderrua,
                                               db85_ruastipo
                                              )
                                       values (iCodigoRuasTipo,
                                               iCodigoRua,
                                               3
                                              );

            end if;

            /* Fim da verificação de mudança na RuasTipo */
            /* Inicio da verificação de mudança na BairroRua */
             select db87_sequencial
               into iCodigoBairroRua
               from cadenderbairrocadenderrua
              where db87_cadenderrua    = iCodigoRua
                and db87_cadenderbairro = iCodigoBairro;

            if not found then

              if lRaise then
                raise notice 'Incluindo na BairroRua';
              end if;


              iCodigoBairroRua := nextval('cadenderbairrocadenderrua_db87_sequencial_seq');
              insert into cadenderbairrocadenderrua (db87_sequencial,
                                                     db87_cadenderrua,
                                                     db87_cadenderbairro
                                                    )
                                             values (iCodigoBairroRua,
                                                     iCodigoRua,
                                                     iCodigoBairro::integer
                                                    );

            end if;
            /* Fim da verificação de mudança na BairroRua */

            /* Inicio da verificação de mudança na Local */

             select db75_sequencial
               into iCodigoLocal
               from cadenderlocal
              where db75_cadenderbairrocadenderrua = iCodigoBairroRua
                and db75_numero = cast(rCgm.z01_numcon as text);

            if not found then

              iCodigoLocal := nextval('cadenderlocal_db75_sequencial_seq');
              insert into cadenderlocal (db75_sequencial,
                                         db75_cadenderbairrocadenderrua,
                                         db75_numero
                                        )
                                 values (iCodigoLocal,
                                         iCodigoBairroRua,
                                         rCgm.z01_numcon
                                        );

            end if;
            /* Fim da verificação de mudança na Local */

            /* Inicio da verificação de mudança na Endereco */
            select count(*)
              into iNumCgmEndereco
              from cgmendereco
             where z07_endereco = iCodigoEndereco
            having count(*) > 1;

            /*delete na cgmendereco*/
            delete from cgmendereco
                    where z07_numcgm = iCodigoCgm
                      and z07_tipo   = 'S';


            if (iNumCgmEndereco > 0 and (rCgm.z01_comcon != rEndereco.db76_complemento)) then

              if lRaise then
                raise notice 'Existe mais de um cgm no mesmo endereco inserindo endereco novo';
              end if;

              iCodigoEndereco := nextval('endereco_db76_sequencial_seq');
              insert into endereco (db76_sequencial,
                                    db76_cadenderlocal,
                                    db76_complemento,
                                    db76_caixapostal,
                                    db76_loteamento,
                                    db76_condominio,
                                    db76_pontoref,
                                    db76_cep
                                   )
                            values (iCodigoEndereco,
                                    iCodigoLocal,
                                    rCgm.z01_comcon,
                                    rEndereco.db76_caixapostal,
                                    rEndereco.db76_loteamento,
                                    rEndereco.db76_condominio,
                                    rEndereco.db76_pontoref,
                                    rCgm.z01_cepcon
                                   );



            else

              perform db76_sequencial
                 from endereco
                where db76_sequencial    = iCodigoEndereco
                  and db76_cadenderlocal = iCodigoLocal;

              if not found then
                iCodigoEndereco := nextval('endereco_db76_sequencial_seq');
                insert into endereco (db76_sequencial,
                                      db76_cadenderlocal,
                                      db76_complemento,
                                      db76_caixapostal,
                                      db76_loteamento,
                                      db76_condominio,
                                      db76_pontoref,
                                      db76_cep
                                     )
                              values (iCodigoEndereco,
                                      iCodigoLocal,
                                      rCgm.z01_comcon,
                                      rEndereco.db76_caixapostal,
                                      rEndereco.db76_loteamento,
                                      rEndereco.db76_condominio,
                                      rEndereco.db76_pontoref,
                                      rCgm.z01_cepcon
                                     );


              else
                  update endereco set db76_cadenderlocal = iCodigoLocal,
                                      db76_complemento   = rCgm.z01_comcon,
                                      db76_cep           = rCgm.z01_cepcon
                                where db76_sequencial = rEndereco.db76_sequencial;
              end if;
            end if;

            /* Inserindo na cgmendereco */
            insert into cgmendereco(z07_sequencial,
                                      z07_endereco,
                                      z07_numcgm,
                                      z07_tipo
                                     )
                              values (nextval('cgmendereco_z07_sequencial_seq'),
                                      iCodigoEndereco,
                                      iCodigoCgm,
                                      'S'
                                     );


            /* Fim da verificação de mudança na Endereco */
            if lRaise then
              raise notice 'Fim da atualização do cgm {%} endereco {%}',iCodigoCgm,iCodigoEndereco;
            end if;
          end if;/* Finaliza bloco de quando não existe ligação com a tabela cgmendereco para tipo 'P' */
    end if; /* Fecha o if do endereço primario tipo 'P' */

    return null;
end;

$$ LANGUAGE plpgsql;

SQL
        );
    }
}
