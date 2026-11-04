<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M24031FunctionEstruturais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        DO
\$do$
declare
	sequencial_db_estruturavalor_pai db_estruturavalor.db121_sequencial%TYPE;
 	sequencial_db_estruturavalor db_estruturavalor.db121_sequencial%TYPE;
 	sequencial_db_estruturavalor_next int;
 	sequencial_issgruposervico issgruposervico.q126_sequencial%TYPE;
 	sequencial_issgruposervico_next int;
 	sequencial_issconfiguracaogruposervico issconfiguracaogruposervico.q136_sequencial%TYPE;
 	sequencial_issconfiguracaogruposervico_next int;
  	result_split  text;
  	PAI  JSON[] = ARRAY[
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "01.00",
		"db121_descricao": "Serviços de informática e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 54.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "02.00",
		"db121_descricao": "Serviços de pesquisas e desenvolvimento de qualquer natureza.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 53.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "03.00",
		"db121_descricao": "Serviços prestados mediante locação, cessão de direito de uso e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 52.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "04.00",
		"db121_descricao": "Serviços de saúde, assistência médica e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 51.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "05.00",
		"db121_descricao": "Serviços de medicina e assistência veterinária e congêneres. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 50.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "06.00",
		"db121_descricao": "Serviços de cuidados pessoais, estética, atividades físicas e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 49.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "07.00",
		"db121_descricao": "Serviços relativos a engenharia, arquitetura, geologia, urbanismo, construção civil, manutenção, limpeza, meio ambiente, saneamento e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 48.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "08.00",
		"db121_descricao": "Serviços de educação, ensino, orientação pedagógica e educacional, instrução, treinamento e avaliação pessoal de qualquer grau ou natureza.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 47.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "09.00",
		"db121_descricao": "Serviços relativos a hospedagem, turismo, viagens e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 46.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "10.00",
		"db121_descricao": "Serviços de intermediação e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 45.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "11.00",
		"db121_descricao": "Serviços de guarda, estacionamento, armazenamento, vigilância e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 44.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "12.00",
		"db121_descricao": "Serviços de diversões, lazer, entretenimento e congêneres. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 43.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "13.00",
		"db121_descricao": "Serviços relativos a fonografia, fotografia, cinematografia e reprografia. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 42.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "14.00",
		"db121_descricao": "Serviços relativos a bens de terceiros.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 41.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "15.00",
		"db121_descricao": "Serviços relacionados ao setor bancário ou financeiro, inclusive aqueles prestados por instituições financeiras autorizadas a funcionar pela União ou por quem de direito. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 40.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "16.00",
		"db121_descricao": "Serviços de transporte de natureza municipal.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 39.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "17.00",
		"db121_descricao": "Serviços de apoio técnico, administrativo, jurídico, contábil, comercial e congêneres. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 38.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "18.00",
		"db121_descricao": "Serviços de regulação de sinistros vinculados a contratos de seguros; inspeção e avaliação de riscos para cobertura de contratos de seguros; prevenção e gerência de riscos seguráveis e congêneres. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 37.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "19.00",
		"db121_descricao": "Serviços de distribuição e venda de bilhetes e demais produtos de loteria, bingos, cartões, pules ou cupons de apostas, sorteios, prêmios, inclusive os decorrentes de títulos de capitalização e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 36.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "20.00",
		"db121_descricao": "Serviços portuários, aeroportuários, ferroportuários, de terminais rodoviários, ferroviários e metroviários. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 35.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "21.00",
		"db121_descricao": "Serviços de registros públicos, cartorários e notariais. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 34.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "22.00",
		"db121_descricao": "Serviços de exploração de rodovia. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 33.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "23.00",
		"db121_descricao": "Serviços de programação e comunicação visual, desenho industrial e congêneres. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 32.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "24.00",
		"db121_descricao": "Serviços de chaveiros, confecção de carimbos, placas, sinalização visual, banners, adesivos e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 31.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "25.00",
		"db121_descricao": "Serviços funerários. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 30.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "26.00",
		"db121_descricao": "Serviços de coleta, remessa ou entrega de correspondências, documentos, objetos, bens ou valores, inclusive pelos correios e suas agências franqueadas; courrier e congêneres. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 29.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "27.00",
		"db121_descricao": "Serviços de assistência social.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 28.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "28.00",
		"db121_descricao": "Serviços de avaliação de bens e serviços de qualquer natureza. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 27.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "29.00",
		"db121_descricao": "Serviços de biblioteconomia. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 26.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "30.00",
		"db121_descricao": "Serviços de biologia, biotecnologia e química. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 25.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "31.00",
		"db121_descricao": "Serviços técnicos em edificações, eletrônica, eletrotécnica, mecânica, telecomunicações e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 24.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "32.00",
		"db121_descricao": "Serviços de desenhos técnicos. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 23.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "33.00",
		"db121_descricao": "Serviços de desembaraço aduaneiro, comissários, despachantes e congêneres. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 22.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "34.00",
		"db121_descricao": "Serviços de investigações particulares, detetives e congêneres.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 21.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "35.00",
		"db121_descricao": "Serviços de reportagem, assessoria de imprensa, jornalismo e relações públicas.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 20.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "36.00",
		"db121_descricao": "Serviços de meteorologia.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 19.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "37.00",
		"db121_descricao": "Serviços de artistas, atletas, modelos e manequins.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 18.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "38.00",
		"db121_descricao": "Serviços de museologia.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 17.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "39.00",
		"db121_descricao": "Serviços de ourivesaria e lapidação. ",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 16.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura": 150000,
		"db121_estrutural": "40.00",
		"db121_descricao": "Serviços relativos a obras de arte sob encomenda.",
		"db121_estruturavalorpai": 0,
		"db121_nivel": 1,
		"db121_tipoconta": 1,
		"q136_tipotributacao": 2,
		"q136_valor": 15.0,
		"q136_localpagamento": 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "99.99",
		"db121_descricao" : "MIGRACAO",
		"db121_estruturavalorpai" : null,
		"db121_nivel" : 1,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 3,
		"q136_valor" : 61.0,
		"q136_localpagamento" : 2
	}'
];

FILHOS  JSON[] = ARRAY[
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "01.01",
		"db121_descricao" : "Análise e desenvolvimento de sistemas. ",
		"db121_estruturavalorpai" : 17,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "01.02",
		"db121_descricao" : "Programação. ",
		"db121_estruturavalorpai" : 17,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "01.03",
		"db121_descricao" : "Processamento, armazenamento ou hospedagem de dados, textos, imagens, vídeos, páginas eletrônicas, aplicativos e sistemas de informação, entre outros formatos, e congêneres.",
		"db121_estruturavalorpai" : 17,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "01.04",
		"db121_descricao" : "Elaboração de programas de computadores, inclusive de jogos eletrônicos, independentemente da arquitetura construtiva da máquina em que o programa será executado, incluindo tablets, smartphones e congêneres.",
		"db121_estruturavalorpai" : 17,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "01.05",
		"db121_descricao" : "Licenciamento ou cessão de direito de uso de programas de computação.",
		"db121_estruturavalorpai" : 17,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "01.06",
		"db121_descricao" : "Assessoria e consultoria em informática. ",
		"db121_estruturavalorpai" : 17,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "01.07",
		"db121_descricao" : "Suporte técnico em informática, inclusive instalação, configuração e manutenção de programas de computação e bancos de dados.",
		"db121_estruturavalorpai" : 17,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "01.08",
		"db121_descricao" : "Planejamento, confecção, manutenção e atualização de páginas eletrônicas.",
		"db121_estruturavalorpai" : 17,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "01.09",
		"db121_descricao" : "Disponibilização, sem cessão definitiva, de conteúdos de áudio, vídeo, imagem e texto por meio da internet, respeitada a imunidade de livros, jornais e periódicos (exceto a distribuição de conteúdos pelas prestadoras de Serviço de Acesso Condicionado, de que trata a Lei no 12.485, de 12 de setembro de 2011, sujeita ao ICMS).",
		"db121_estruturavalorpai" : 17,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "02.01",
		"db121_descricao" : "Serviços de pesquisas e desenvolvimento de qualquer natureza.",
		"db121_estruturavalorpai" : 26,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "03.01",
		"db121_descricao" : "(VETADO) ",
		"db121_estruturavalorpai" : 28,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 3,
		"q136_valor" : 0.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "03.02",
		"db121_descricao" : "Cessão de direito de uso de marcas e de sinais de propaganda.",
		"db121_estruturavalorpai" : 28,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "03.03",
		"db121_descricao" : "Exploração de salões de festas, centro de convenções, escritórios virtuais, stands, quadras esportivas, estádios, ginásios, auditórios, casas de espetáculos, parques de diversões, canchas e congêneres, para realização de eventos ou negócios de qualquer natureza. ",
		"db121_estruturavalorpai" : 28,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "03.04",
		"db121_descricao" : "Locação, sublocação, arrendamento, direito de passagem ou permissão de uso, compartilhado ou não, de ferrovia, rodovia, postes, cabos, dutos e condutos de qualquer natureza.",
		"db121_estruturavalorpai" : 28,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "03.05",
		"db121_descricao" : "Cessão de andaimes, palcos, coberturas e outras estruturas de uso temporário.",
		"db121_estruturavalorpai" : 28,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.01",
		"db121_descricao" : "Medicina e biomedicina.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.02",
		"db121_descricao" : "Análises clínicas, patologia, eletricidade médica, radioterapia, quimioterapia, ultra-sonografia, ressonância magnética, radiologia, tomografia e congêneres.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.03",
		"db121_descricao" : "Hospitais, clínicas, laboratórios, sanatórios, manicômios, casas de saúde, prontos-socorros, ambulatórios e congêneres.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.04",
		"db121_descricao" : "Instrumentação cirúrgica.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.05",
		"db121_descricao" : "Acupuntura.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.06",
		"db121_descricao" : "Enfermagem, inclusive serviços auxiliares. ",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.07",
		"db121_descricao" : "Serviços farmacêuticos.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.08",
		"db121_descricao" : "Terapia ocupacional, fisioterapia e fonoaudiologia.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.09",
		"db121_descricao" : "Terapias de qualquer espécie destinadas ao tratamento físico, orgânico e mental. ",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.10",
		"db121_descricao" : "Nutrição.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.11",
		"db121_descricao" : "Obstetrícia. ",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.12",
		"db121_descricao" : "Odontologia. ",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.13",
		"db121_descricao" : "Ortóptica. ",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.14",
		"db121_descricao" : "Próteses sob encomenda.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.15",
		"db121_descricao" : "Psicanálise. ",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.16",
		"db121_descricao" : "Psicologia.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.17",
		"db121_descricao" : "Casas de repouso e de recuperação, creches, asilos e congêneres. ",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.18",
		"db121_descricao" : "Inseminação artificial, fertilização in vitro e congêneres.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.19",
		"db121_descricao" : "Bancos de sangue, leite, pele, olhos, óvulos, sêmen e congêneres.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.20",
		"db121_descricao" : "Coleta de sangue, leite, tecidos, sêmen, órgãos e materiais biológicos de qualquer espécie.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.21",
		"db121_descricao" : "Unidade de atendimento, assistência ou tratamento móvel e congêneres.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.22",
		"db121_descricao" : "Planos de medicina de grupo ou individual e convênios para prestação de assistência médica, hospitalar, odontológica e congêneres. ",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 3
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "04.23",
		"db121_descricao" : "Outros planos de saúde que se cumpram através de serviços de terceiros contratados, credenciados, cooperados ou apenas pagos pelo operador do plano mediante indicação do beneficiário.",
		"db121_estruturavalorpai" : 34,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 3
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "05.01",
		"db121_descricao" : "Medicina veterinária e zootecnia.",
		"db121_estruturavalorpai" : 58,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "05.02",
		"db121_descricao" : "Hospitais, clínicas, ambulatórios, prontos-socorros e congêneres, na área veterinária. ",
		"db121_estruturavalorpai" : 58,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "05.03",
		"db121_descricao" : "Laboratórios de análise na área veterinária. ",
		"db121_estruturavalorpai" : 58,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "05.04",
		"db121_descricao" : "Inseminação artificial, fertilização in vitro e congêneres.",
		"db121_estruturavalorpai" : 58,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "05.05",
		"db121_descricao" : "Bancos de sangue e de órgãos e congêneres. ",
		"db121_estruturavalorpai" : 58,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "05.06",
		"db121_descricao" : "Coleta de sangue, leite, tecidos, sêmen, órgãos e materiais biológicos de qualquer espécie.",
		"db121_estruturavalorpai" : 58,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "05.07",
		"db121_descricao" : "Unidade de atendimento, assistência ou tratamento móvel e congêneres.",
		"db121_estruturavalorpai" : 58,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "05.08",
		"db121_descricao" : "Guarda, tratamento, amestramento, embelezamento, alojamento e congêneres.",
		"db121_estruturavalorpai" : 58,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "05.09",
		"db121_descricao" : "Planos de atendimento e assistência médico-veterinária.",
		"db121_estruturavalorpai" : 58,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 3
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "06.01",
		"db121_descricao" : "Barbearia, cabeleireiros, manicuros, pedicuros e congêneres. ",
		"db121_estruturavalorpai" : 68,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "06.02",
		"db121_descricao" : "Esteticistas, tratamento de pele, depilação e congêneres.",
		"db121_estruturavalorpai" : 68,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "06.03",
		"db121_descricao" : "Banhos, duchas, sauna, massagens e congêneres. ",
		"db121_estruturavalorpai" : 68,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "06.04",
		"db121_descricao" : "Ginástica, dança, esportes, natação, artes marciais e demais atividades físicas. ",
		"db121_estruturavalorpai" : 68,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "06.05",
		"db121_descricao" : "Centros de emagrecimento, spa e congêneres.",
		"db121_estruturavalorpai" : 68,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "06.06",
		"db121_descricao" : "Aplicação de tatuagens, piercings e congêneres.",
		"db121_estruturavalorpai" : 68,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.01",
		"db121_descricao" : "Engenharia, agronomia, agrimensura, arquitetura, geologia, urbanismo, paisagismo e congêneres. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.02",
		"db121_descricao" : "Execução, por administração, empreitada ou subempreitada, de obras de construção civil, hidráulica ou elétrica e de outras obras semelhantes, inclusive sondagem, perfuração de poços, escavação, drenagem e irrigação, terraplanagem, pavimentação, concretagem e a instalação e montagem de produtos, peças e equipamentos (exceto o fornecimento de mercadorias produzidas pelo prestador de serviços fora do local da prestação dos serviços, que fica sujeito ao ICMS). ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 4.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.03",
		"db121_descricao" : "Elaboração de planos diretores, estudos de viabilidade, estudos organizacionais e outros, relacionados com obras e serviços de engenharia; elaboração de anteprojetos, projetos básicos e projetos executivos para trabalhos de engenharia.",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.04",
		"db121_descricao" : "Demolição. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.05",
		"db121_descricao" : "Reparação, conservação e reforma de edifícios, estradas, pontes, portos e congêneres (exceto o fornecimento de mercadorias produzidas pelo prestador dos serviços, fora do local da prestação dos serviços, que fica sujeito ao ICMS). ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 4.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.06",
		"db121_descricao" : "Colocação e instalação de tapetes, carpetes, assoalhos, cortinas, revestimentos de parede, vidros, divisórias, placas de gesso e congêneres, com material fornecido pelo tomador do serviço. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.07",
		"db121_descricao" : "Recuperação, raspagem, polimento e lustração de pisos e congêneres.",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.08",
		"db121_descricao" : "Calafetação. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.09",
		"db121_descricao" : "Varrição, coleta, remoção, incineração, tratamento, reciclagem, separação e destinação final de lixo, rejeitos e outros resíduos quaisquer.",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.10",
		"db121_descricao" : "Limpeza, manutenção e conservação de vias e logradouros públicos, imóveis, chaminés, piscinas, parques, jardins e congêneres.",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.11",
		"db121_descricao" : "Decoração e jardinagem, inclusive corte e poda de árvores. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.12",
		"db121_descricao" : "Controle e tratamento de efluentes de qualquer natureza e de agentes físicos, químicos e biológicos. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.13",
		"db121_descricao" : "Dedetização, desinfecção, desinsetização, imunização, higienização, desratização, pulverização e congêneres. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.14",
		"db121_descricao" : "(VETADO) ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.15",
		"db121_descricao" : "(VETADO) ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.16",
		"db121_descricao" : "Florestamento, reflorestamento, semeadura, adubação, reparação de solo, plantio, silagem, colheita, corte e descascamento de árvores, silvicultura, exploração florestal e dos serviços congêneres indissociáveis da formação, manutenção e colheita de florestas, para quaisquer fins e por quaisquer meios.",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.17",
		"db121_descricao" : "Escoramento, contenção de encostas e serviços congêneres.",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.18",
		"db121_descricao" : "Limpeza e dragagem de rios, portos, canais, baías, lagos, lagoas, represas, açudes e congêneres. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.19",
		"db121_descricao" : "Acompanhamento e fiscalização da execução de obras de engenharia, arquitetura e urbanismo. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.20",
		"db121_descricao" : "Aerofotogrametria (inclusive interpretação), cartografia, mapeamento, levantamentos topográficos, batimétricos, geográficos, geodésicos, geológicos, geofísicos e congêneres.",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.21",
		"db121_descricao" : "Pesquisa, perfuração, cimentação, mergulho, perfilagem, concretação, testemunhagem, pescaria, estimulação e outros serviços relacionados com a exploração e explotação de petróleo, gás natural e de outros recursos minerais. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "07.22",
		"db121_descricao" : "Nucleação e bombardeamento de nuvens e congêneres. ",
		"db121_estruturavalorpai" : 74,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "08.01",
		"db121_descricao" : "Ensino regular pré-escolar, fundamental, médio e superior. ",
		"db121_estruturavalorpai" : 97,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "08.02",
		"db121_descricao" : "Instrução, treinamento, orientação pedagógica e educacional, avaliação de conhecimentos de qualquer natureza.",
		"db121_estruturavalorpai" : 97,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "09.01",
		"db121_descricao" : "Hospedagem de qualquer natureza em hotéis, apart-service condominiais, flat, apart-hotéis, hotéis residência, residence-service, suite service, hotelaria marítima, motéis, pensões e congêneres; ocupação por temporada com fornecimento de serviço (o valor da alimentação e gorjeta, quando incluído no preço da diária, fica sujeito ao Imposto Sobre Serviços). ",
		"db121_estruturavalorpai" : 100,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "09.02",
		"db121_descricao" : "Agenciamento, organização, promoção, intermediação e execução de programas de turismo, passeios, viagens, excursões, hospedagens e congêneres. ",
		"db121_estruturavalorpai" : 100,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "09.03",
		"db121_descricao" : "Guias de turismo.",
		"db121_estruturavalorpai" : 100,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.01",
		"db121_descricao" : "Agenciamento, corretagem ou intermediação de câmbio, de seguros, de cartões de crédito, de planos de saúde e de planos de previdência privada. ",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.02",
		"db121_descricao" : "Agenciamento, corretagem ou intermediação de títulos em geral, valores mobiliários e contratos quaisquer.",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.03",
		"db121_descricao" : "Agenciamento, corretagem ou intermediação de direitos de propriedade industrial, artística ou literária. ",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.04",
		"db121_descricao" : "Agenciamento, corretagem ou intermediação de contratos de arrendamento mercantil (leasing), de franquia (franchising) e de faturização (factoring).",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.05",
		"db121_descricao" : "Agenciamento, corretagem ou intermediação de bens móveis ou imóveis, não abrangidos em outros itens ou subitens, inclusive aqueles realizados no âmbito de Bolsas de Mercadorias e Futuros, por quaisquer meios. ",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.06",
		"db121_descricao" : "Agenciamento marítimo. ",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.07",
		"db121_descricao" : "Agenciamento de notícias.",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.08",
		"db121_descricao" : "Agenciamento de publicidade e propaganda, inclusive o agenciamento de veiculação por quaisquer meios.",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.09",
		"db121_descricao" : "Representação de qualquer natureza, inclusive comercial. ",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "10.10",
		"db121_descricao" : "Distribuição de bens de terceiros. ",
		"db121_estruturavalorpai" : 104,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "11.01",
		"db121_descricao" : "Guarda e estacionamento de veículos terrestres automotores, de aeronaves e de embarcações. ",
		"db121_estruturavalorpai" : 115,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "11.02",
		"db121_descricao" : "Vigilância, segurança ou monitoramento de bens, pessoas e semoventes.",
		"db121_estruturavalorpai" : 115,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "11.03",
		"db121_descricao" : "Escolta, inclusive de veículos e cargas. ",
		"db121_estruturavalorpai" : 115,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "11.04",
		"db121_descricao" : "Armazenamento, depósito, carga, descarga, arrumação e guarda de bens de qualquer espécie.",
		"db121_estruturavalorpai" : 115,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "11.05",
		"db121_descricao" : " Serviços relacionados ao monitoramento e rastreamento a distância, em qualquer via ou local, de veículos, cargas, pessoas e semoventes em circulação ou movimento, realizados por meio de telefonia móvel, transmissão de satélites, rádio ou qualquer outro meio, inclusive pelas empresas de Tecnologia da Informação Veicular, independentemente de o prestador de serviços ser proprietário ou não da infraestrutura de telecomunicações que utiliza",
		"db121_estruturavalorpai" : 115,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.01",
		"db121_descricao" : "Espetáculos teatrais.",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.02",
		"db121_descricao" : "Exibições cinematográficas.",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.03",
		"db121_descricao" : "Espetáculos circenses. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.04",
		"db121_descricao" : "Programas de auditório.",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.05",
		"db121_descricao" : "Parques de diversões, centros de lazer e congêneres. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.06",
		"db121_descricao" : "Boates, taxi-dancing e congêneres. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.07",
		"db121_descricao" : "Shows, ballet, danças, desfiles, bailes, óperas, concertos, recitais, festivais e congêneres.",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.08",
		"db121_descricao" : "Feiras, exposições, congressos e congêneres. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.09",
		"db121_descricao" : "Bilhares, boliches e diversões eletrônicas ou não. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.10",
		"db121_descricao" : "Corridas e competições de animais. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.11",
		"db121_descricao" : "Competições esportivas ou de destreza física ou intelectual, com ou sem a participação do espectador.",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.12",
		"db121_descricao" : "Execução de música.",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.13",
		"db121_descricao" : "Produção, mediante ou sem encomenda prévia, de eventos, espetáculos, entrevistas, shows, ballet, danças, desfiles, bailes, teatros, óperas, concertos, recitais, festivais e congêneres. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.14",
		"db121_descricao" : "Fornecimento de música para ambientes fechados ou não, mediante transmissão por qualquer processo. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.15",
		"db121_descricao" : "Desfiles de blocos carnavalescos ou folclóricos, trios elétricos e congêneres. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.16",
		"db121_descricao" : "Exibição de filmes, entrevistas, musicais, espetáculos, shows, concertos, desfiles, óperas, competições esportivas, de destreza intelectual ou congêneres. ",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "12.17",
		"db121_descricao" : "Recreação e animação, inclusive em festas e eventos de qualquer natureza.",
		"db121_estruturavalorpai" : 120,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "13.01",
		"db121_descricao" : "(VETADO) ",
		"db121_estruturavalorpai" : 138,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 3.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "13.02",
		"db121_descricao" : "Fonografia ou gravação de sons, inclusive trucagem, dublagem, mixagem e congêneres.",
		"db121_estruturavalorpai" : 138,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "13.03",
		"db121_descricao" : "Fotografia e cinematografia, inclusive revelação, ampliação, cópia, reprodução, trucagem e congêneres. ",
		"db121_estruturavalorpai" : 138,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "13.04",
		"db121_descricao" : "Reprografia, microfilmagem e digitalização.",
		"db121_estruturavalorpai" : 138,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "13.05",
		"db121_descricao" : "Composição gráfica, inclusive confecção de impressos gráficos, fotocomposição, clicheria, zincografia, litografia e fotolitografia, exceto se destinados a posterior operação de comercialização ou industrialização, ainda que incorporados, de qualquer forma, a outra mercadoria que deva ser objeto de posterior circulação, tais como bulas, rótulos, etiquetas, caixas, cartuchos, embalagens e manuais técnicos e de instrução, quando ficarão sujeitos ao ICMS.",
		"db121_estruturavalorpai" : 138,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.01",
		"db121_descricao" : "Lubrificação, limpeza, lustração, revisão, carga e recarga, conserto, restauração, blindagem, manutenção e conservação de máquinas, veículos, aparelhos, equipamentos, motores, elevadores ou de qualquer objeto (exceto peças e partes empregadas, que ficam sujeitas ao ICMS). ",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.02",
		"db121_descricao" : "Assistência técnica. ",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.03",
		"db121_descricao" : "Recondicionamento de motores (exceto peças e partes empregadas, que ficam sujeitas ao ICMS). ",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.04",
		"db121_descricao" : "Recauchutagem ou regeneração de pneus. ",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.05",
		"db121_descricao" : "Restauração, recondicionamento, acondicionamento, pintura, beneficiamento, lavagem, secagem, tingimento, galvanoplastia, anodização, corte, recorte, plastificação, costura, acabamento, polimento e congêneres de objetos quaisquer.",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.06",
		"db121_descricao" : "Instalação e montagem de aparelhos, máquinas e equipamentos, inclusive montagem industrial, prestados ao usuário final, exclusivamente com material por ele fornecido. ",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.07",
		"db121_descricao" : "Colocação de molduras e congêneres.",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.08",
		"db121_descricao" : "Encadernação, gravação e douração de livros, revistas e congêneres.",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.09",
		"db121_descricao" : "Alfaiataria e costura, quando o material for fornecido pelo usuário final, exceto aviamento. ",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.10",
		"db121_descricao" : "Tinturaria e lavanderia. ",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.11",
		"db121_descricao" : "Tapeçaria e reforma de estofamentos em geral.",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.12",
		"db121_descricao" : "Funilaria e lanternagem. ",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.13",
		"db121_descricao" : "Carpintaria e serralheria. ",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "14.14",
		"db121_descricao" : "Guincho intramunicipal, guindaste e içamento.",
		"db121_estruturavalorpai" : 144,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.01",
		"db121_descricao" : "Administração de fundos quaisquer, de consórcio, de cartão de crédito ou débito e congêneres, de carteira de clientes, de cheques pré-datados e congêneres.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 3
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.02",
		"db121_descricao" : "Abertura de contas em geral, inclusive conta-corrente, conta de investimentos e aplicação e caderneta de poupança, no País e no exterior, bem como a manutenção das referidas contas ativas e inativas.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.03",
		"db121_descricao" : "Locação e manutenção de cofres particulares, de terminais eletrônicos, de terminais de atendimento e de bens e equipamentos em geral.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.04",
		"db121_descricao" : "Fornecimento ou emissão de atestados em geral, inclusive atestado de idoneidade, atestado de capacidade financeira e congêneres. ",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.05",
		"db121_descricao" : "Cadastro, elaboração de ficha cadastral, renovação cadastral e congêneres, inclusão ou exclusão no Cadastro de Emitentes de Cheques sem Fundos - CCF ou em quaisquer outros bancos cadastrais. ",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.06",
		"db121_descricao" : "Emissão, reemissão e fornecimento de avisos, comprovantes e documentos em geral; abono de firmas; coleta e entrega de documentos, bens e valores; comunicação com outra agência ou com a administração central; licenciamento eletrônico de veículos; transferência de veículos; agenciamento fiduciário ou depositário; devolução de bens em custódia.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.07",
		"db121_descricao" : "Acesso, movimentação, atendimento e consulta a contas em geral, por qualquer meio ou processo, inclusive por telefone, fac-símile, internet e telex, acesso a terminais de atendimento, inclusive vinte e quatro horas; acesso a outro banco e a rede compartilhada; fornecimento de saldo, extrato e demais informações relativas a contas em geral, por qualquer meio ou processo. ",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.08",
		"db121_descricao" : "Emissão, reemissão, alteração, cessão, substituição, cancelamento e registro de contrato de crédito; estudo, análise e avaliação de operações de crédito; emissão, concessão, alteração ou contratação de aval, fiança, anuência e congêneres; serviços relativos a abertura de crédito, para quaisquer fins.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.09",
		"db121_descricao" : "Arrendamento mercantil (leasing) de quaisquer bens, inclusive cessão de direitos e obrigações, substituição de garantia, alteração, cancelamento e registro de contrato, e demais serviços relacionados ao arrendamento mercantil (leasing). ",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 3
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.10",
		"db121_descricao" : "Serviços relacionados a cobranças, recebimentos ou pagamentos em geral, de títulos quaisquer, de contas ou carnês, de câmbio, de tributos e por conta de terceiros, inclusive os efetuados por meio eletrônico, automático ou por máquinas de atendimento; fornecimento de posição de cobrança, recebimento ou pagamento; emissão de carnês, fichas de compensação, impressos e documentos em geral. ",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.11",
		"db121_descricao" : "Devolução de títulos, protesto de títulos, sustação de protesto, manutenção de títulos, reapresentação de títulos, e demais serviços a eles relacionados.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.12",
		"db121_descricao" : "Custódia em geral, inclusive de títulos e valores mobiliários. ",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.13",
		"db121_descricao" : "Serviços relacionados a operações de câmbio em geral, edição, alteração, prorrogação, cancelamento e baixa de contrato de câmbio; emissão de registro de exportação ou de crédito; cobrança ou depósito no exterior; emissão, fornecimento e cancelamento de cheques de viagem; fornecimento, transferência, cancelamento e demais serviços relativos a carta de crédito de importação, exportação e garantias recebidas; envio e recebimento de mensagens em geral relacionadas a operações de câmbio.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.14",
		"db121_descricao" : "Fornecimento, emissão, reemissão, renovação e manutenção de cartão magnético, cartão de crédito, cartão de débito, cartão salário e congêneres.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.15",
		"db121_descricao" : "Compensação de cheques e títulos quaisquer; serviços relacionados a depósito, inclusive depósito identificado, a saque de contas quaisquer, por qualquer meio ou processo, inclusive em terminais eletrônicos e de atendimento.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.16",
		"db121_descricao" : "Emissão, reemissão, liquidação, alteração, cancelamento e baixa de ordens de pagamento, ordens de crédito e similares, por qualquer meio ou processo; serviços relacionados à transferência de valores, dados, fundos, pagamentos e similares, inclusive entre contas em geral.",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.17",
		"db121_descricao" : "Emissão, fornecimento, devolução, sustação, cancelamento e oposição de cheques quaisquer, avulso ou por talão. ",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "15.18",
		"db121_descricao" : "Serviços relacionados a crédito imobiliário, avaliação e vistoria de imóvel ou obra, análise técnica e jurídica, emissão, reemissão, alteração, transferência e renegociação de contrato, emissão e reemissão do termo de quitação e demais serviços relacionados a crédito imobiliário. ",
		"db121_estruturavalorpai" : 158,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "16.01",
		"db121_descricao" : "Serviços de transporte coletivo municipal rodoviário, metroviário, ferroviário e aquaviário de passageiros.",
		"db121_estruturavalorpai" : 177,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "16.02",
		"db121_descricao" : "Outros serviços de transporte de natureza municipal",
		"db121_estruturavalorpai" : 177,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.01",
		"db121_descricao" : "Assessoria ou consultoria de qualquer natureza, não contida em outros itens desta lista; análise, exame, pesquisa, coleta, compilação e fornecimento de dados e informações de qualquer natureza, inclusive cadastro e similares.",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.02",
		"db121_descricao" : "Datilografia, digitação, estenografia, expediente, secretaria em geral, resposta audível, redação, edição, interpretação, revisão, tradução, apoio e infra-estrutura administrativa e congêneres.",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.03",
		"db121_descricao" : "Planejamento, coordenação, programação ou organização técnica, financeira ou administrativa. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.04",
		"db121_descricao" : "Recrutamento, agenciamento, seleção e colocação de mão-de-obra.",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.05",
		"db121_descricao" : "Fornecimento de mão-de-obra, mesmo em caráter temporário, inclusive de empregados ou trabalhadores, avulsos ou temporários, contratados pelo prestador de serviço. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.06",
		"db121_descricao" : "Propaganda e publicidade, inclusive promoção de vendas, planejamento de campanhas ou sistemas de publicidade, elaboração de desenhos, textos e demais materiais publicitários. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.07",
		"db121_descricao" : "(VETADO) ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 3,
		"q136_valor" : 0.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.08",
		"db121_descricao" : "Franquia (franchising).",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.09",
		"db121_descricao" : "Perícias, laudos, exames técnicos e análises técnicas. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.10",
		"db121_descricao" : "Planejamento, organização e administração de feiras, exposições, congressos e congêneres.",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.11",
		"db121_descricao" : "Organização de festas e recepções; bufê (exceto o fornecimento de alimentação e bebidas, que fica sujeito ao ICMS).",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.12",
		"db121_descricao" : "Administração em geral, inclusive de bens e negócios de terceiros. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.13",
		"db121_descricao" : "Leilão e congêneres. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.14",
		"db121_descricao" : "Advocacia. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.15",
		"db121_descricao" : "Arbitragem de qualquer espécie, inclusive jurídica.",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.16",
		"db121_descricao" : "Auditoria. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.17",
		"db121_descricao" : "Análise de Organização e Métodos.",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.18",
		"db121_descricao" : "Atuária e cálculos técnicos de qualquer natureza.",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.19",
		"db121_descricao" : "Contabilidade, inclusive serviços técnicos e auxiliares. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.20",
		"db121_descricao" : "Consultoria e assessoria econômica ou financeira.",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.21",
		"db121_descricao" : "Estatística. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.22",
		"db121_descricao" : "Cobrança em geral. ",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.23",
		"db121_descricao" : "Assessoria, análise, avaliação, atendimento, consulta, cadastro, seleção, gerenciamento de informações, administração de contas a receber ou a pagar e em geral, relacionados a operações de faturização (factoring).",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.24",
		"db121_descricao" : "Apresentação de palestras, conferências, seminários e congêneres.",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "17.25",
		"db121_descricao" : "Inserção de textos, desenhos e outros materiais de propaganda e publicidade, em qualquer meio (exceto em livros, jornais, periódicos e nas modalidades de serviços de radiodifusão sonora e de sons e imagens de recepção livre e gratuita).",
		"db121_estruturavalorpai" : 179,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "18.01",
		"db121_descricao" : "Serviços de regulação de sinistros vinculados a contratos de seguros; inspeção e avaliação de riscos para cobertura de contratos de seguros; prevenção e gerência de riscos seguráveis e congêneres. ",
		"db121_estruturavalorpai" : 204,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "19.01",
		"db121_descricao" : "Serviços de distribuição e venda de bilhetes e demais produtos de loteria, bingos, cartões, pules ou cupons de apostas, sorteios, prêmios, inclusive os decorrentes de títulos de capitalização e congêneres.",
		"db121_estruturavalorpai" : 206,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "20.01",
		"db121_descricao" : "Serviços portuários, ferroportuários, utilização de porto, movimentação de passageiros, reboque de embarcações, rebocador escoteiro, atracação, desatracação, serviços de praticagem, capatazia, armazenagem de qualquer natureza, serviços acessórios, movimentação de mercadorias, serviços de apoio marítimo, de movimentação ao largo, serviços de armadores, estiva, conferência, logística e congêneres. ",
		"db121_estruturavalorpai" : 208,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "20.02",
		"db121_descricao" : "Serviços aeroportuários, utilização de aeroporto, movimentação de passageiros, armazenagem de qualquer natureza, capatazia, movimentação de aeronaves, serviços de apoio aeroportuários, serviços acessórios, movimentação de mercadorias, logística e congêneres. ",
		"db121_estruturavalorpai" : 208,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "20.03",
		"db121_descricao" : "Serviços de terminais rodoviários, ferroviários, metroviários, movimentação de passageiros, mercadorias, inclusive.00  suas operações, logística e congêneres. ",
		"db121_estruturavalorpai" : 208,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 1
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "21.01",
		"db121_descricao" : "Serviços de registros públicos, cartorários e notariais. ",
		"db121_estruturavalorpai" : 212,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "22.01",
		"db121_descricao" : "Serviços de exploração de rodovia mediante cobrança de preço ou pedágio dos usuários, envolvendo execução de serviços de conservação, manutenção, melhoramentos para adequação de capacidade e segurança de trânsito, operação, monitoração, assistência aos usuários e outros serviços definidos em contratos, atos de concessão ou de permissão ou em.00.00normas oficiais.",
		"db121_estruturavalorpai" : 214,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "23.01",
		"db121_descricao" : "Serviços de programação e comunicação visual, desenho industrial e congêneres. ",
		"db121_estruturavalorpai" : 216,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "24.01",
		"db121_descricao" : "Serviços de chaveiros, confecção de carimbos, placas, sinalização visual, banners, adesivos e congêneres.",
		"db121_estruturavalorpai" : 218,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "25.01",
		"db121_descricao" : "Funerais, inclusive fornecimento de caixão, urna ou esquifes; aluguel de capela; transporte do corpo cadavérico; fornecimento de flores, coroas e outros paramentos; desembaraço de certidão de óbito; fornecimento de véu, essa e outros adornos; embalsamento, embelezamento, conservação ou restauração de cadáveres. ",
		"db121_estruturavalorpai" : 220,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "25.02",
		"db121_descricao" : "Translado intramunicipal e cremação de corpos e partes de corpos cadavéricos.",
		"db121_estruturavalorpai" : 220,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "25.03",
		"db121_descricao" : "Planos ou convênio funerários. ",
		"db121_estruturavalorpai" : 220,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "25.04",
		"db121_descricao" : "Manutenção e conservação de jazigos e cemitérios.",
		"db121_estruturavalorpai" : 220,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "25.05",
		"db121_descricao" : "Cessão de uso de espaços em cemitérios para sepultamento.",
		"db121_estruturavalorpai" : 220,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "26.01",
		"db121_descricao" : "Serviços de coleta, remessa ou entrega de correspondências, documentos, objetos, bens ou valores, inclusive pelos correios e suas agências franqueadas; courrier e congêneres. ",
		"db121_estruturavalorpai" : 225,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "27.01",
		"db121_descricao" : "Serviços de assistência social.",
		"db121_estruturavalorpai" : 227,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "28.01",
		"db121_descricao" : "Serviços de avaliação de bens e serviços de qualquer natureza. ",
		"db121_estruturavalorpai" : 229,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "29.01",
		"db121_descricao" : "Serviços de biblioteconomia. ",
		"db121_estruturavalorpai" : 231,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "30.01",
		"db121_descricao" : "Serviços de biologia, biotecnologia e química. ",
		"db121_estruturavalorpai" : 233,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "31.01",
		"db121_descricao" : "Serviços técnicos em edificações, eletrônica, eletrotécnica, mecânica, telecomunicações e congêneres.",
		"db121_estruturavalorpai" : 235,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "32.01",
		"db121_descricao" : "Serviços de desenhos técnicos. ",
		"db121_estruturavalorpai" : 237,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "33.01",
		"db121_descricao" : "Serviços de desembaraço aduaneiro, comissários, despachantes e congêneres. ",
		"db121_estruturavalorpai" : 239,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "34.01",
		"db121_descricao" : "Serviços de investigações particulares, detetives e congêneres.",
		"db121_estruturavalorpai" : 241,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "35.01",
		"db121_descricao" : "Serviços de reportagem, assessoria de imprensa, jornalismo e relações públicas.",
		"db121_estruturavalorpai" : 243,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "36.01",
		"db121_descricao" : "Serviços de meteorologia.",
		"db121_estruturavalorpai" : 245,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "37.01",
		"db121_descricao" : "Serviços de artistas, atletas, modelos e manequins.",
		"db121_estruturavalorpai" : 247,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "38.01",
		"db121_descricao" : "Serviços de museologia.",
		"db121_estruturavalorpai" : 249,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "39.01",
		"db121_descricao" : "Serviços de ourivesaria e lapidação (quando o material for fornecido pelo tomador do serviço). ",
		"db121_estruturavalorpai" : 251,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 2.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "40.01",
		"db121_descricao" : "Obras de arte sob encomenda..00",
		"db121_estruturavalorpai" : 253,
		"db121_nivel" : 2,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 2,
		"q136_valor" : 5.0,
		"q136_localpagamento" : 2
	}',
	'{
		"db121_db_estrutura" : 150000,
		"db121_estrutural" : "99.99",
		"db121_descricao" : "MIGRACAO",
		"db121_estruturavalorpai" : null,
		"db121_nivel" : 1,
		"db121_tipoconta" : 2,
		"q136_tipotributacao" : 3,
		"q136_valor" : 61.0,
		"q136_localpagamento" : 2
	}'
];
begin
	
	FOR i IN array_lower(PAI, 1) .. array_upper(PAI, 1)
		loop
			SELECT db121_sequencial into sequencial_db_estruturavalor FROM configuracoes.db_estruturavalor where db121_db_estrutura = TO_NUMBER(PAI[i]->>'db121_db_estrutura','999999') and  db121_estrutural = PAI[i]->>'db121_estrutural';
			IF sequencial_db_estruturavalor is null then
				select  nextval('db_estruturavalor_db121_sequencial_seq') into sequencial_db_estruturavalor_next;
				INSERT INTO configuracoes.db_estruturavalor VALUES(sequencial_db_estruturavalor_next, TO_NUMBER(PAI[i]->>'db121_db_estrutura','999999'), PAI[i]->>'db121_estrutural', PAI[i]->>'db121_descricao', 0, TO_NUMBER(PAI[i]->>'db121_nivel','999999'), TO_NUMBER(PAI[i]->>'db121_tipoconta','999999'));
			END IF;
			SELECT db121_sequencial into sequencial_db_estruturavalor FROM configuracoes.db_estruturavalor where db121_db_estrutura = TO_NUMBER(PAI[i]->>'db121_db_estrutura','999999') and  db121_estrutural = PAI[i]->>'db121_estrutural';
			
			SELECT q126_sequencial into sequencial_issgruposervico from issqn.issgruposervico where q126_db_estruturavalor = sequencial_db_estruturavalor;
			IF sequencial_issgruposervico is null then
				select  nextval('issgruposervico_q126_sequencial_seq') into sequencial_issgruposervico_next;
				INSERT INTO issgruposervico VALUES(sequencial_issgruposervico_next, sequencial_db_estruturavalor);
			END IF;
			SELECT q126_sequencial into sequencial_issgruposervico from issqn.issgruposervico where q126_db_estruturavalor = sequencial_db_estruturavalor;
			
			select q136_sequencial into sequencial_issconfiguracaogruposervico from issconfiguracaogruposervico where q136_issgruposervico = sequencial_issgruposervico and q136_exercicio = '2023';
			IF sequencial_issconfiguracaogruposervico is null then
				select  nextval('issconfiguracaogruposervico_q136_sequencial_seq') into sequencial_issconfiguracaogruposervico_next;
				INSERT INTO issqn.issconfiguracaogruposervico VALUES(sequencial_issconfiguracaogruposervico_next, sequencial_issgruposervico, 2023, 2, 2, 2, false, false);
			END IF;
	   	END LOOP;		
	
	   	FOR i IN array_lower(FILHOS, 1) .. array_upper(FILHOS, 1)
		loop
			SELECT db121_sequencial into sequencial_db_estruturavalor FROM configuracoes.db_estruturavalor where db121_db_estrutura = TO_NUMBER(FILHOS[i]->>'db121_db_estrutura','999999') and  db121_estrutural = FILHOS[i]->>'db121_estrutural';
			IF sequencial_db_estruturavalor is null then
				SELECT split_part(FILHOS[i]->>'db121_estrutural', '.', 1) into result_split;
				SELECT db121_sequencial into sequencial_db_estruturavalor_pai FROM configuracoes.db_estruturavalor where db121_db_estrutura = TO_NUMBER(FILHOS[i]->>'db121_db_estrutura','999999') and  db121_estrutural = result_split||'.00';
				IF sequencial_db_estruturavalor_pai is not null then
					select  nextval('db_estruturavalor_db121_sequencial_seq') into sequencial_db_estruturavalor_next;
					INSERT INTO configuracoes.db_estruturavalor VALUES(sequencial_db_estruturavalor_next, TO_NUMBER(FILHOS[i]->>'db121_db_estrutura','999999'), FILHOS[i]->>'db121_estrutural', FILHOS[i]->>'db121_descricao', sequencial_db_estruturavalor_pai, TO_NUMBER(FILHOS[i]->>'db121_nivel','999999'), TO_NUMBER(FILHOS[i]->>'db121_tipoconta','999999'));
				END IF;

			END IF;
			SELECT db121_sequencial into sequencial_db_estruturavalor FROM configuracoes.db_estruturavalor where db121_db_estrutura = TO_NUMBER(FILHOS[i]->>'db121_db_estrutura','999999') and  db121_estrutural = FILHOS[i]->>'db121_estrutural';
			
			SELECT q126_sequencial into sequencial_issgruposervico from issqn.issgruposervico where q126_db_estruturavalor = sequencial_db_estruturavalor;
			IF sequencial_issgruposervico is null then
				select  nextval('issgruposervico_q126_sequencial_seq') into sequencial_issgruposervico_next;
				INSERT INTO issgruposervico VALUES(sequencial_issgruposervico_next, sequencial_db_estruturavalor);
			END IF;
			SELECT q126_sequencial into sequencial_issgruposervico from issqn.issgruposervico where q126_db_estruturavalor = sequencial_db_estruturavalor;
			
			select q136_sequencial into sequencial_issconfiguracaogruposervico from issconfiguracaogruposervico where q136_issgruposervico = sequencial_issgruposervico and q136_exercicio = '2023';
			IF sequencial_issconfiguracaogruposervico is null then
				select  nextval('issconfiguracaogruposervico_q136_sequencial_seq') into sequencial_issconfiguracaogruposervico_next;
				INSERT INTO issqn.issconfiguracaogruposervico VALUES(sequencial_issconfiguracaogruposervico_next, sequencial_issgruposervico, 2023, 2, 2, 2, false, false);
			END IF;		
	   	END LOOP;
END
\$do$;
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
        //
    }
}
