<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class M24478CriandoTabelasNovasConfiguracoes extends Migration
{
    protected $tabelas;
    protected $campos;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->buildComentarios();
        $schema = DB::table('information_schema.schemata')->where('schema_name', 'matriculaonline')->get();
        if($schema->count() === 0) {
            DB::statement(\DB::raw("CREATE SCHEMA matriculaonline"));
        }

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");
        Schema::create('matriculaonline.duvidas_frequentes', function (Blueprint $table) {
            $table->increments('mo15_id');
            $table->text('mo15_pergunta');
            $table->boolean('mo15_ativo');
            $table->integer('mo15_ordem');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.duvidas_frequentes');");
        DB::statement("COMMENT ON TABLE matriculaonline.duvidas_frequentes IS '{$this->getDicionarioTabela('duvidas_frequentes')}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'duvidas_frequentes')");

        Schema::create('matriculaonline.respostas_duvidas_frequentes', function (Blueprint $table) {
            $table->increments('mo25_id');
            $table->integer('mo25_pergunta');
            $table->text('mo25_resposta');

            $table->foreign('mo25_pergunta', 'respostas_duvidas_frequentes_duvidas_frequentes')
                ->references('mo15_id')
                ->on('matriculaonline.duvidas_frequentes');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.respostas_duvidas_frequentes');");
        DB::statement("COMMENT ON TABLE matriculaonline.respostas_duvidas_frequentes IS '{$this->getDicionarioTabela('respostas_duvidas_frequentes')}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'respostas_duvidas_frequentes')");

        Schema::create('matriculaonline.tipos_mensagens_personalizadas', function (Blueprint $table) {
            $table->increments('mo18_id');
            $table->text('mo18_descricao');
            $table->text('mo18_ajuda');
        });
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.tipos_mensagens_personalizadas');");
        DB::statement("COMMENT ON TABLE matriculaonline.tipos_mensagens_personalizadas IS '{$this->getDicionarioTabela('tipos_mensagens_personalizadas')}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'tipos_mensagens_personalizadas')");

        $this->insereTiposMensagens();

        Schema::create('matriculaonline.mensagens_personalizadas', function (Blueprint $table) {
            $table->increments('mo19_id');
            $table->integer('mo19_tipo');
            $table->text('mo19_conteudo');

            $table->foreign('mo19_tipo', 'mensagens_personalizadas_tipos_mensagens_personalizadas_fk')
                ->references('mo18_id')
                ->on('matriculaonline.tipos_mensagens_personalizadas');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.mensagens_personalizadas');");
        DB::statement("COMMENT ON TABLE matriculaonline.mensagens_personalizadas IS '{$this->getDicionarioTabela('mensagens_personalizadas')}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'mensagens_personalizadas')");

        Schema::create('matriculaonline.documentos', function (Blueprint $table) {
            $table->increments('mo20_id');
            $table->integer('mo20_tipo');
            $table->integer('mo20_estorage');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.documentos');");
        DB::statement("COMMENT ON TABLE matriculaonline.documentos IS '{$this->getDicionarioTabela('documentos')}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'documentos')");

        Schema::create('matriculaonline.imagens_personalizadas', function (Blueprint $table) {
            $table->increments('mo21_id');
            $table->integer('mo21_tipo');
            $table->integer('mo21_estorage');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.imagens_personalizadas');");
        DB::statement("COMMENT ON TABLE matriculaonline.imagens_personalizadas IS '{$this->getDicionarioTabela('imagens_personalizadas')}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'imagens_personalizadas')");

        Schema::create('matriculaonline.noticias', function (Blueprint $table) {
            $table->increments('mo22_id');
            $table->text('mo22_titulo');
            $table->date('mo22_data');
            $table->text('mo22_texto');
            $table->integer('mo22_estorage');
            $table->boolean('mo22_ativa');
        });


        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.noticias');");
        DB::statement("COMMENT ON TABLE matriculaonline.noticias IS '{$this->getDicionarioTabela('noticias')}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'noticias')");

        Schema::create('matriculaonline.itens_cores_personalizadas', function (Blueprint $table) {
            $table->increments('mo23_id');
            $table->text('mo23_nome');
            $table->text('mo23_cordefault');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.itens_cores_personalizadas');");
        DB::statement("COMMENT ON TABLE matriculaonline.noticias IS '{$this->getDicionarioTabela('itens_cores_personalizadas')}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'itens_cores_personalizadas')");

        $this->inserItenCoresDefaults();

        Schema::create('matriculaonline.cores_personalizadas', function (Blueprint $table) {
            $table->increments('mo24_id');
            $table->integer('mo24_item');
            $table->text('mo24_cor');

            $table->foreign('mo24_item', 'cores_personalizadas_itens_cores_personalizadas_fk')
                ->references('mo23_id')
                ->on('matriculaonline.itens_cores_personalizadas');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('matriculaonline.cores_personalizadas');");
        DB::statement("COMMENT ON TABLE matriculaonline.noticias IS '{$this->getDicionarioTabela('cores_personalizadas')}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('matriculaonline', 'cores_personalizadas')");

        $this->inserCoresDefaults();

        $this->comentaCampos();

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }


    public function upDicionario()
    {

        $modulo_schema = DB::table('configuracoes.db_sysmodulo')->where('nomemod', 'matriculaonline')->get();
         if ($modulo_schema->count() === 0) {
             DB::table('configuracoes.db_sysmodulo')->insert([
                 'codmod' => 80,
                 'nomemod' => 'matriculaonline',
                 'descricao' => 'Módulo de matrícula on-line',
                 'dataincl' => '2015-06-11',
                 'ativo' => 't'
             ]);
         }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'duvidas_frequentes');");
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'respostas_duvidas_frequentes');");
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'mensagens_personalizadas');");
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'tipos_mensagens_personalizadas');");
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'documentos');");
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'imagens_personalizadas');");
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'noticias');");
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'itens_cores_personalizadas');");
        DB::statement("SELECT fc_remove_dicionario_tabela('matriculaonline', 'cores_personalizadas');");

        Schema::dropIfExists('matriculaonline.respostas_duvidas_frequentes');
        Schema::dropIfExists('matriculaonline.duvidas_frequentes');
        Schema::dropIfExists('matriculaonline.mensagens_personalizadas');
        Schema::dropIfExists('matriculaonline.tipos_mensagens_personalizadas');
        Schema::dropIfExists('matriculaonline.documentos');
        Schema::dropIfExists('matriculaonline.imagens_personalizadas');
        Schema::dropIfExists('matriculaonline.noticias');
        Schema::dropIfExists('matriculaonline.cores_personalizadas');
        Schema::dropIfExists('matriculaonline.itens_cores_personalizadas');
    }

    public function insereTiposMensagens()
    {
        $dados = [
            [
                "mo18_descricao" => "Inscrição Lista Espera",
                "mo18_ajuda" => "Mensagem a ser exibida ao clicar no botão 'Faça Sua Inscrição' para a lista de espera (fase já processada)."
            ],
            [
                "mo18_descricao" => "Sistema Fechado",
                "mo18_ajuda" => "Mensagem a ser exibida ao clicar no botão 'Faça Sua Inscrição' quando a fase (lista de espera) estiver fechada/inativa e não houver outra fase aberta."
            ],
            [
                "mo18_descricao" => "Candidato possui matrícula",
                "mo18_ajuda" => "Mensagem a ser exibida quando informado o CPF de um candidato que já possui matrícula na rede e a fase for exclusiva para candidatos de fora da rede."
            ],
            [
                "mo18_descricao" => "Consulta Inscrição",
                "mo18_ajuda" => "Mensagem a ser exibida ao clicar no menu Consulta Inscrição."
            ],
            [
                "mo18_descricao" => "Critério Inscrição Anterior não alocada/não possui",
                "mo18_ajuda" => "Mensagem a ser exibida quando o critério 'Inscrição anterior não alocada' estiver configurado e o candidato não se enquadra no critério por não possuir inscrição."
            ],
            [
                "mo18_descricao" => "Critério Inscrição Anterior não alocada/possui",
                "mo18_ajuda" => "Mensagem a ser exibida quando o critério   Inscrição anterior não alocada' estiver configurado e o candidato se enquadra no critério, pois possui inscrição não alocada na fase anterior. Possui variável do número do protocolo. Mensagem também deve aparecer no comprovante de inscrição."
            ],
            [
                "mo18_descricao" => "Observação tela de confirmação",
                "mo18_ajuda" => "Mensagem a ser exibida na tela em destaque no ato de confirmação da inscrição. Mensagem também deve aparecer no comprovante de inscrição."
            ],
            [
                "mo18_descricao" => "Inscrição Editada",
                "mo18_ajuda" => "Mensagem a ser exibida quando o usuário clicar no botão confirmar após editar a inscrição onde os campos opção de escola, turno, bairro, data de nascimento e/ou etapa forem alterados. Mensagem também deve aparecer no comprovante de inscrição."
            ],
            [
                "mo18_descricao" => "Declaração de Veracidade",
                "mo18_ajuda" => "Mensagem a ser exibida na tela de confirmação da inscrição e no comprovante de inscrição."
            ],
            [
                "mo18_descricao" => "Seleção de Escolas",
                "mo18_ajuda" => "Mensagem a ser exibida na tela onde serão selecionadas as escolas às quais o candidato irá pleitear a vaga."
            ],
            [
                "mo18_descricao" => "Documentos para matrícula",
                "mo18_ajuda" => "Mensagem a ser exibida na tela de emissão do comprovante e no comprovante."
            ],
            [
                "mo18_descricao" => "Mensagem Termo de Aceite",
                "mo18_ajuda" => "Mensagem a ser exibida no campo do termo de aceite ao realizar inscrição."
            ],
            [
                "mo18_descricao" => "Candidato não possui matrícula",
                "mo18_ajuda" => "Mensagem a ser exibida quando informado o CPF de um candidato que não possui matrícula na rede e a fase for exclusiva para candidatos da rede."
            ]
        ];

        foreach ($dados as $dado) {
            DB::table('matriculaonline.tipos_mensagens_personalizadas')->insert($dado);
        }
    }

    public function buildComentarios()
    {
        $tabelas = [
            'duvidas_frequentes' => [mb_convert_encoding('Duvidas Frequentes', 'UTF-8'), 'mo15', '2023-10-16', mb_convert_encoding('Duvidas Frequentes', 'UTF-8'), 0, false, false, false, false],
            'respostas_duvidas_frequentes' => [mb_convert_encoding('Respostas Duvidas Frequentes', 'UTF-8'), 'mo125', '2023-10-16', mb_convert_encoding('Respostas Duvidas Frequentes', 'UTF-8'), 0, false, false, false, false],
            'tipos_mensagens_personalizadas' => ['Tipos de Mensagens Personalizadas', 'mo18', '2023-10-17', 'Tipos de Mensagens Personalizadas', 0, false, false, false, false],
            'mensagens_personalizadas' =>  ['Mensagens Personalizadas', 'mo19', '2023-10-17', 'Mensagens Personalizadas', 0, false, false, false, false],
            'documentos' =>  ['Documentos', 'mo20', '2023-10-18', 'Documentos', 0, false, false, false, false],
            'imagens_personalizadas' => ['Imagens Personalizadas', 'mo21', '2023-10-19', 'Imagens Personalizadas', 0, false, false, false, false ],
            'noticias' => ['Noticias', 'mo22', '2023-10-19', 'Noticias', 0, false, false, false, false],
            'itens_cores_personalizadas' => ['Itens Cores Personalizaveis', 'mo23', '2023-10-26', 'Itens Cores Personalizaveis', 0, false, false, false, false],
            'cores_personalizadas' => ['Cores Personalizaveis', 'mo24', '2023-10-26', 'Cores Personalizaveis', 0, false, false, false, false]
        ];

        foreach ($tabelas as $key => $tabela) {
            $this->tabelas[$key] = [
                "descricao" => $tabela[0],
                "sigla" => $tabela[1],
                "dataincl" => $tabela[2],
                "rotulo" => $tabela[3],
                "tipotabela" => $tabela[4],
                "naolibclass" => $tabela[5],
                "naolibfunc" => $tabela[6],
                "naolibprog" => $tabela[7],
                "naolibform" => $tabela[8]
            ];
        }

        $campos['matriculaonline.duvidas_frequentes'] = [
            'mo15_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo15_pergunta' => ['Pergunta','Pergunta', 'Pergunta', false,false,0,500,'text'],
            'mo15_ativo' => ['Ativo','Ativo','Ativo',false,false,5,11,'text'],
            'mo15_ordem' => ['Ordem','Ordem','Ordem',false,false,1,11,'text']
        ];

        $campos['matriculaonline.respostas_duvidas_frequentes'] = [
            'mo25_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo25_pergunta' => ['Pergunta','Pergunta', 'Pergunta', false,false,1,11,'text'],
            'mo25_resposta' => ['Resposta','Resposta', 'Respostas',false,false,0,1000,'text']
        ];

        $campos['matriculaonline.tipos_mensagens_personalizadas'] = [
            'mo18_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo18_descricao' => ['Descricao','Descricao','Descricao',false,false,0,200,'text'],
            'mo18_ajuda' => ['Ajuda','Ajuda','Ajuda',false,false,0,500,'text'],
        ];

        $campos['matriculaonline.mensagens_personalizadas'] = [
            'mo19_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo19_tipo' => ['Tipo da Mensagem','Tipo da Mensagem','Tipo da Mensagem',false,false,1,11,'text'],
            'mo19_conteudo' => ['Conteudo da Mensagem','Conteudo da Mensagem','Conteudo da Mensagem',false,false,0,1000,'text']
        ];

        $campos['matriculaonline.documentos'] = [
            'mo20_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo20_tipo' => ['Tipo de Documento','Tipo de Documento','Tipo de Documento',false,false,1,11,'text'],
            'mo20_estorage' => ['ID Estorage','ID Estorage','ID Estorage',false,false,0,11,'text']
        ];

        $campos['matriculaonline.imagens_personalizadas'] = [
            'mo21_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo21_tipo' => ['Tipo da Imagem','Tipo da Imagem','Tipo da Imagem',false,false,1,11,'text',],
            'mo21_estorage' => ['ID Estorage','ID Estorage','ID Estorage',false,false,1,11,'text'],
        ];

        $campos['matriculaonline.noticias'] = [
            'mo22_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo22_titulo' => ['Titulo','Titulo','Titulo',false,false,0,11,'text'],
            'mo22_data' => ['Data','Data','Data',false,false,1,20,'text'],
            'mo22_texto' => ['Texto','Texto','Texto',false,false,0,1000,'text'],
            'mo22_estorage' => ['ID Estorage','ID Estorage','ID Estorage',false,false,1,11,'text'],
            'mo22_ativa' => ['Ativa','Ativa','Ativa',false,false, 1,11,'text']
        ];

        $campos['matriculaonline.itens_cores_personalizadas'] = [
            'mo23_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo23_nome' => ['Nome','Nome','Nome',false,false,0,200,'text'],
            'mo23_cordefault' => ['Cor Default','Cor Default','Cor Default',false,false,1,200,'text']
        ];

        $campos['matriculaonline.cores_personalizadas'] = [
            'mo24_id' => ['ID','ID','ID',false,false,1,11,'text'],
            'mo24_item' => ['Nome','Nome','Nome',false,false,0,11,'text'],
            'mo24_cor' => ['Cor','Cor','Cor',false,false,1,200,'text']
        ];

        foreach ($campos as $tabela => $campos) {
            foreach ($campos as $nomeCampo => $valoresCampo) {
                $this->campos[$tabela][$nomeCampo] = [
                    "descricao" => $valoresCampo[0],
                    "rotulo" => $valoresCampo[1],
                    "rotulorel" => $valoresCampo[2],
                    "maiusculo" => $valoresCampo[3],
                    "autocompl" => $valoresCampo[4],
                    "aceitatipo" => $valoresCampo[5],
                    "tamanho" => $valoresCampo[6],
                    "tipoobj" => $valoresCampo[7]
                ];
            }
        }
    }

    private function getDicionarioTabela($tabela)
    {
        if ($this->tabelas === null) {
            $this->buildComentarios();
        }

        return mb_convert_encoding(json_encode($this->tabelas[$tabela]), 'ISO-8859-1');
    }

    public function comentaCampos()
    {
        foreach ($this->campos as $tabela => $campos) {
            foreach ($campos as $nomeCampo => $omentario) {
                $cm = mb_convert_encoding(json_encode($omentario), 'ISO-8859-1');
                $sql = "COMMENT ON COLUMN {$tabela}.{$nomeCampo} IS '{$cm}'";
                DB::statement($sql);
            }
        }
    }

    public function inserItenCoresDefaults()
    {
        $dados = [
            ['mo23_nome' => 'Cabeçalho', 'mo23_cordefault' => '#00917b'],
            ['mo23_nome' => 'Menu Pré-Matricula', 'mo23_cordefault' => '#5ca3e1'],
            ['mo23_nome' => 'Menu Consulta Pré-Matricula', 'mo23_cordefault' => '#c0272d'],
            ['mo23_nome' => 'Menu Escolas', 'mo23_cordefault' => '#ff822b'],
            ['mo23_nome' => 'Menu Dúvidas', 'mo23_cordefault' => '#ffbe30'],
            ['mo23_nome' => 'Menu Legislação', 'mo23_cordefault' => '#009135'],
            ['mo23_nome' => 'Menu Lista de Espera', 'mo23_cordefault' => '#5b4db1']
        ];

        foreach ($dados as $dado) {
            DB::table('matriculaonline.itens_cores_personalizadas')->insert($dado);
        }
    }

    public function inserCoresDefaults()
    {
        $dados = [
            ['mo24_item' => 1, 'mo24_cor' => '#00917b'],
            ['mo24_item' => 2, 'mo24_cor' => '#5ca3e1'],
            ['mo24_item' => 3, 'mo24_cor' => '#c0272d'],
            ['mo24_item' => 4, 'mo24_cor' => '#ff822b'],
            ['mo24_item' => 5, 'mo24_cor' => '#ffbe30'],
            ['mo24_item' => 6, 'mo24_cor' => '#009135'],
            ['mo24_item' => 7, 'mo24_cor' => '#5b4db1']
        ];

        foreach ($dados as $dado) {
            DB::table('matriculaonline.cores_personalizadas')->insert($dado);
        }
    }
}
