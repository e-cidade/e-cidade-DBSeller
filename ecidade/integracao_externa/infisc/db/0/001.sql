
create schema integra_infisc;

-- ======================================================================

CREATE TABLE integra_infisc.integra_tipocadastro
  (
    sequencial  int4,
    descricao   varchar(100)  not null,

    primary key(sequencial)
  );


-- ======================================================================

CREATE TABLE integra_infisc.integra_receitas
  (
    sequencial  int4        unique not null,
    munic_ibge  int4        not null,
    codigo      int4        unique not null,
    descricao   varchar(50) not null,
    tipo        int4        	null,
    dataimp     date 	    not null,
    horaimp    	char(5)     not null,
    processado  bool,

    primary key(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_calend
  (
    sequencial  int4        unique not null,
    munic_ibge  int4        not null,
    data        date        not null,

    dataimp     date 	    not null,
    horaimp    	char(5)     not null,
    processado  bool,

    primary key(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_inflat
  (
    sequencial     int4       	 unique not null,
    munic_ibge     int4       	 not null,
    sigla          varchar(5)    unique not null,
    descricao      varchar(60)   not null,
    tipo_calc      char(1)    	 not null,
    tipo_atualiza  int4    	 not null,
    tipo_lancam    varchar(1)    not null,
    dataimp        date 	 not null,
    horaimp    	   char(5)       not null,
    processado     bool,
    

    primary key(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_inflat_detalhe
  (
    sequencial          int4           unique not null,
    munic_ibge          int4           not null,
    integra_inflat  int4           not null,
    data                date           not null,
    valor               numeric(15,2)  default 0,
    dataimp        	date 	       not null,
    horaimp    	   	char(5)        not null,
    processado     	bool,

    primary key(sequencial),

    foreign key(integra_inflat) references integra_infisc.integra_inflat(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_jm
  (
    sequencial           int4           unique not null,
    munic_ibge           int4           not null,
    codigo_jm            int4           not null,
    integra_inflat   int4           not null,
    juros                numeric(15,2)  default 0,
    jurdia               bool,
    multa_1              int4,
    multa_2              int4,
    multa_3              int4,
    multa_faixa_1        numeric(15,2)  default 0,
    multa_faixa_2        numeric(15,2)  default 0,
    multa_faixa_3        numeric(15,2)  default 0,
    multa_diaria         numeric(15,2)  default 0,
    limite_multa_diaria  numeric(15,2)  default 0,
    sabdom               bool,
    corr_venc            bool,
    dataimp        	 date   	not null,
    horaimp    	   	 char(5)       	not null,
    processado     	 bool,

    primary key(sequencial),

    foreign key(integra_inflat) references integra_infisc.integra_inflat(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_rec_jm
  (
    sequencial           int4      unique not null,
    munic_ibge           int4      not null,
    integra_receitas     int4      not null,
    integra_jm           int4      not null,
    data_inicial         date      not null,
    data_final           date      not null,
    dataimp        	 date      not null,
    horaimp    	   	 char(5)   not null,
    processado     	 bool,

    primary key(sequencial),

    foreign key(integra_receitas) references integra_infisc.integra_receitas(sequencial),
    foreign key(integra_jm) references integra_infisc.integra_jm(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_atividades 
  (
    sequencial      int4           unique not null,
    munic_ibge      int4           not null,
    atividade       int4           not null,
    descricao       varchar(200)   not null,
    codigo_cnae     varchar(50)        null,
    codigo_116      int4,
    descricao_116   varchar(200),
    descricao_cnae  varchar(200),
    aliqiss         numeric        default 0,
    dataimp         date      	   not null,
    horaimp    	    char(5)        not null,
    processado      bool,

    primary key(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_atividades_servicos
  (
    sequencial      	int4      unique not null,
    munic_ibge      	int4      not null,
    ano		    	int4      not null,
    integra_atividades  int4      not null,
    item_servico	char(5)   null,
    cnae		char(8)   null,
    aliquota	        numeric(15,2) default 0,

    dataimp         date      	   not null,
    horaimp    	    char(5)        not null,
    processado      bool,

    primary key(sequencial),
    foreign key(integra_atividades) references integra_infisc.integra_atividades (sequencial)

  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_socios
  (
    sequencial       int4        unique not null,
    munic_ibge       int4       	not null,
    codigo_socio     int4       	not null,
    cpf_cnpj         varchar(14)        not null,
    nome_socio       varchar(100)   	not null,
    tipo_logradouro  varchar(5),
    logradouro       varchar(150),
    numero           varchar(10),
    complemento      varchar(100),
    bairro           varchar(60),
    cep              varchar(8),
    cidade           varchar(50),
    estado           varchar(2),
    ddd_fone         varchar(3),
    telefone         varchar(15),
    ramal            varchar(15),
    ddd_fax          varchar(3),
    fax              varchar(15),
    email            varchar(100),
    dataimp          date	   not null,
    horaimp    	     char(5)       not null,
    processado       bool,

    primary key(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_eventuais
  (
    sequencial          int4    unique not null,
    munic_ibge          int4           not null,
    tipo_cadastro       int4    not null,
    tipo_empresa        varchar(1),
    cpf_cnpj            varchar(14),
    inscricao_estadual  varchar(50),
    nome_empresa        varchar(200),
    tipo_logradouro     varchar(5),
    logradouro          varchar(100),
    numero              varchar(5),
    complemento         varchar(50),
    bairro              varchar(60),
    cidade              varchar(100),
    estado              varchar(2),
    cep                 varchar(8),
    ddd_fone            varchar(3),
    telefone            varchar(15),
    ddd_fax             varchar(3),
    fax                 varchar(15),
    email               varchar(100),
    dataimp             date           not null,
    horaimp             char(5)        not null,
    processado          bool           not null,

    primary key(sequencial),

    foreign key(tipo_cadastro) references integra_infisc.integra_tipocadastro(sequencial)

  );


-- ======================================================================

CREATE TABLE integra_infisc.integra_escritorios
  (
    sequencial         int4       unique not null,
    munic_ibge         int4,
    codigo_escritorio  int4       	 not null,
    inscricao          int4 		     null,
    cpf_cnpj           varchar(14)       not null,
    nome_escritorio    varchar(100),
    data_abertura      date,
    data_encerramento  date,
    status_empresa     varchar(1),
    tipo_logradouro    varchar(5),
    logradouro         varchar(150),
    numero             varchar(10),
    complemento        varchar(50),
    bairro             varchar(60),
    cep                varchar(8),
    cidade             varchar(50),
    estado             varchar(2),
    ddd_fone           varchar(3),
    telefone           varchar(15),
    ramal              varchar(15),
    fax                varchar(15),
    ddd_fax            varchar(3),
    email              varchar(100),
    dataimp            date      not null,
    horaimp    	       char(5)   not null,
    processado         bool,

    primary key(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_config
  (
    sequencial            int4       unique not null,
    munic_ibge            int4       not null,
    faixa_inicial_numdoc  varchar(20)   not null,
    faixa_final_numdoc    varchar(20)   not null,
    cod_rec_iss           int4       not null,
    cod_rec_jur           int4       not null,
    cod_rec_mult          int4       not null,
    num_convenio          int4       not null,
    tipo_convenio         int4       not null,
    dataimp               date       not null,
    horaimp    	    	  char(5)    not null,
    processado            bool,

    primary key(sequencial),

    foreign key(cod_rec_iss) references integra_infisc.integra_receitas(sequencial),
    foreign key(cod_rec_jur) references integra_infisc.integra_receitas(sequencial),
    foreign key(cod_rec_mult) references integra_infisc.integra_receitas(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_empresas
  (
    sequencial              int4      unique not null,
    munic_ibge              int4           	 null,
    cpf_cnpj                varchar(14)      not null,
    inscricao               int4             not null,
    inscricao_estadual      varchar(50),
    nome_empresa            varchar(100)         null,
    nome_fantasia           varchar(100),
    num_processo            varchar(20),
    tipo_empresa            varchar(1)           null,
    tipo_inscricao          varchar(1),
    enquadramento_empresa   varchar(10),
    classificacao           varchar(1),
    regime_empresa          varchar(1),
    data_abertura           date                null,
    data_encerramento       date,
    tipo_logradouro         varchar(5),
    logradouro              varchar(150),
    numero                  varchar(10),
    complemento             varchar(50),
    bairro                  varchar(60),
    cep                     varchar(8),
    cidade                  varchar(50),
    estado                  varchar(2),
    ddd_fone                varchar(3),
    telefone                varchar(15),
    ramal                   varchar(4),
    ddd_fax                 varchar(3),
    fax                     varchar(15),
    email                   varchar(100),
    area_total              numeric default 0,
    area_ocupada            numeric default 0,
    status_empresa          varchar(1),
    logotipo                bytea,
    login                   varchar(30),
    senha                   varchar(100),
    dataimp                 date           not null,
    horaimp    	   	    char(5)   	   not null,
    processado    	    bool,
    
    primary key(sequencial)

  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_empresas_corresp
  (
    sequencial           int4        unique not null,
    munic_ibge           int4,
    integra_empresas     int4        not null,
    tipo_logradouro      varchar(5),
    logradouro           varchar(150),
    titulo_logradouro    varchar(5),
    num_imovel           varchar(10),
    complemento          varchar(50),
    bairro               varchar(60),
    cidade               int8,
    estado               varchar(2),
    cep                  varchar(8),
    ddd_fone             varchar(3),
    telefone             varchar(15),
    ramal                varchar(15),
    ddd_fax              varchar(3),
    fax                  varchar(15),
    email                varchar(100),
    dataimp              date      not null,
    horaimp    	   	 char(5)   not null,
    processado           bool,

    primary key(sequencial),

    foreign key(integra_empresas) references integra_infisc.integra_empresas (sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_empresas_atividades
  (
    sequencial             int4      unique not null,
    munic_ibge             int4      not null,
    integra_empresas       int4      not null,
    integra_atividades     int4      not null,
    atividade_principal    varchar(1),
    datainicio             date      	 null,
    datafim                date,
    exercicio              int4,
    dataimp                date      not null,
    horaimp    	   	   char(5)   not null,
    processado             bool,

    primary key(sequencial),

    foreign key(integra_empresas) references integra_infisc.integra_empresas (sequencial),
    foreign key(integra_atividades) references integra_infisc.integra_atividades (sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_empresas_simples
  (
    sequencial           int4      unique not null,
    munic_ibge           int4,
    integra_empresas     int4      not null,
    datainicial          date      not null,
    datafinal            date,
    tipo                 int4      not null,
    dataimp              date      not null,
    horaimp    	   	 char(5)   not null,
    processado           bool,

    primary key(sequencial),

    CONSTRAINT FK_integra_inter_empresas_sequencia foreign key(integra_empresas) references integra_infisc.integra_empresas (sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_empresas_escritorios
  (
    sequencial              int4      unique not null,
    munic_ibge              int4      not null,
    integra_escritorios     int4      not null,
    integra_empresas        int4      not null,
    datainicial             date          null,
    datafinal               date	  null,	  
    dataimp                 date      not null,
    horaimp    	   	    char(5)   not null,
    processado              bool,

    primary key(sequencial),

    foreign key(integra_escritorios) references integra_infisc.integra_escritorios(sequencial),
    foreign key(integra_empresas) references integra_infisc.integra_empresas(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_empresas_socios
  (
    sequencial           int4            unique not null,
    munic_ibge           int4            not null,
    integra_empresas     int4            not null,
    integra_socios       int4            not null,
    datainicial          date                null,
    datafinal            date                null,
    percentual           numeric 	 default 0,
    dataimp              date        	 not null,
    horaimp    	   	 char(5)         not null,
    processado           bool,

    primary key(sequencial),

    foreign key(integra_empresas) references integra_infisc.integra_empresas (sequencial),
    foreign key(integra_socios) references integra_infisc.integra_socios(sequencial)
  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_debitos
  (
    sequencial           int4            unique not null,
    munic_ibge           int4            not null,
    integra_empresas     int4            ,
    cnpj		 varchar(14)     not null,
    ano_competencia	 int4            not null,
    mes_competencia	 int4            not null,
    valor                numeric(15,2)   default 0,
    tipo_tributo         int4            not null,
    tipo_movimento       int4            not null,

    dataimp              date        	 not null,
    horaimp    	   	 char(5)         not null,
    status_processamento text,
    motivo_status 	 text,

    primary key(sequencial),

    foreign key(integra_empresas) references integra_infisc.integra_empresas (sequencial)

  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_debitos_movimentos_baixa
  (
    sequencial           int4            unique not null,
    munic_ibge           int4            not null,
    integra_debitos      int4            null,

    numpre		 int4            not null,
    numpar		 int4            not null,

    tipo_lancamento      int4            not null,
    data                 date        	 not null,

    valor_principal      numeric(15,2)   default 0,
    valor_correcao       numeric(15,2)   default 0,
    valor_juros          numeric(15,2)   default 0,
    valor_multa          numeric(15,2)   default 0,

    motivo		 varchar(200)    not null,
    cpf_cnpj_usuario     varchar(14)     not null,

    dataimp              date        	 not null,
    horaimp    	   	 char(5)         not null,
    status_processamento text,
    motivo_status 	 text,

    primary key(sequencial)

  );

-- ======================================================================

CREATE TABLE integra_infisc.integra_debitos_movimentos
  (
    sequencial           int4            unique not null,
    munic_ibge           int4            not null,
    integra_debitos      int4            not null,

    numpre		 int4            not null,
    numpar		 int4            not null,

    tipo_inscricao       int4            not null,
    data_lancamento      date        	 not null,

    dataimp              date        	 not null,
    horaimp    	   	 char(5)         not null,
    status_processamento text,
    motivo_status 	 text,

    primary key(sequencial),

    foreign key(integra_debitos) references integra_infisc.integra_debitos (sequencial)

  );

-- ======================================================================

-- Dados Extra
INSERT INTO integra_infisc.integra_tipocadastro (sequencial, descricao) VALUES (1, 'EVENTUAL FORA DO MUNICIPIO');

