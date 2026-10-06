<!-- ecidade-mirror:begin -->

> [!IMPORTANT]
> Este repositório é um **mirror** de [`DBSeller/e-cidade`](https://github.com/DBSeller/e-cidade),
> mantido por **DBSeller**.
>
> A organização e-Cidade sincroniza este repositório para facilitar a descoberta
> das diferentes linhas de desenvolvimento do projeto. A presença deste mirror
> não transfere a autoria ou a manutenção do código para a organização e-Cidade
> e não indica que esta distribuição seja oficial ou preferencial.
>
> Para alterações no código proveniente do upstream, contribua diretamente em
> [`DBSeller/e-cidade`](https://github.com/DBSeller/e-cidade).

<!-- ecidade-mirror:end -->

# 🏙️ e-Cidade

O **e-Cidade** é um sistema integrado desenvolvido para a gestão municipal. Ele atende as áreas financeira, patrimonial, tributária, de recursos humanos, de educação e de saúde. A ferramenta é destinada ao uso diário por servidores públicos do município e também pelos cidadãos. Seu principal objetivo é automatizar, agilizar e simplificar os processos da administração pública, promovendo uma gestão mais transparente, eficiente e que melhore o atendimento à população.

---

## 🚀 Tecnologias e Requisitos

Este projeto é considerado legado e utiliza as seguintes tecnologias fundamentais:
*   **Backend:** PHP 5.6
*   **Frontend:** HTML e Javascript
*   **Banco de Dados:** PostgreSQL
*   **Servidor Web:** Apache ou Nginx

> ⚠️ **Atenção:** Devido aos requisitos legados (como o PHP 5.6), o uso do **Docker** é o ambiente de desenvolvimento fortemente recomendado. A instalação manual convencional encontra-se atualmente em construção.

---

## 📌 Repositórios de Código

O e-Cidade trabalha com um modelo de repositório oficial e um repositório espelho para facilitar as contribuições:

*   **Repositório Oficial (Portal do Software Público Brasileiro):** Localizado em http://softwarepublico.gov.br/gitlab/e-cidade/. Este é o ambiente institucional de referência, servindo sempre como a fonte oficial onde são mantidas as versões do projeto.
*   **Repositório Espelho (GitHub):** Disponibilizado em https://github.com/DBSeller/e-cidade, com o apoio e manutenção da DBSeller Serviços de Informática Ltda. Ele funciona como um ambiente colaborativo adicional que permite maior interação da comunidade e facilita o envio de contribuições via *issues* e *pull requests*.
*   **Sincronização:** Todo o código-fonte enviado e mantido no GitHub é refletido e incorporado no repositório oficial.

---

## 🐳 Instalação com Docker (Recomendado)

O projeto está configurado em containers para simplificar ao máximo sua execução e isolar o ambiente.

**Pré-requisitos:** Certifique-se de ter instalado o [Docker](https://docs.docker.com/engine/installation/linux/docker-ce/ubuntu/) e o [Docker Compose](https://docs.docker.com/compose/install/#install-compose).

### Passo a Passo da Instalação

1.  **Clonar o projeto:**
    Baixe o repositório oficial em sua máquina e acesse a pasta gerada:
    ```bash
    git clone http://softwarepublico.gov.br/gitlab/e-cidade/e-cidade.git
    cd e-cidade
    ```
2.  **Subir os containers:**
    A partir do diretório raiz do e-Cidade, construa e inicie os serviços executando o comando abaixo:
    ```bash
    docker-compose up -d --build
    ```
    *Nota: Todos os containers possuem um volume mapeado da raiz do e-Cidade diretamente para a pasta `/var/www/html/` de cada container respectivo.*
3.  **Descompactar o banco de dados:**
    ```bash
    docker exec -it ecidade_php56 gunzip docker/ecidade_base.sql.gz
    ```
4.  **Executar o Script de Instalação:**
    Rode o script de instalação por dentro do container PHP:
    ```bash
    docker exec -it ecidade_php56 bash docker/install.sh
    ```
5.  **Ajustar permissões do sistema:**
    Na raiz do e-Cidade, aplique as permissões adequadas na pasta para garantir o funcionamento correto:
    ```bash
    docker exec -it ecidade_php56 chmod -R 775 /var/www/html
    ```
6.  **Acessar a Aplicação:**
    Após finalizar, o sistema estará pronto para acesso através do navegador nos seguintes endereços:
    *   http://localhost:8080
    *   http://127.0.0.1:8080
    
    **Credenciais Padrão de Acesso:**
    *   **Login:** `dbseller`
    *   **Senha:** `dbseller`

---

## 🧰 Solução de Problemas (Troubleshooting)

*   **A porta 8080 já está em uso?** Altere o arquivo `docker-compose.yml`, mudando a configuração de `8080:80` para outra porta disponível (ex: `8081:80`) e acesse através de http://localhost:8081.
*   **O script `install.sh` não foi encontrado?** Verifique se o seu terminal está no diretório correto (raiz do projeto) e se o arquivo reside no caminho `docker/install.sh`.
*   **Ocorreu erro de permissão no script?** Você pode conceder a permissão de execução usando o comando: `chmod +x docker/install.sh`.
*   **Deseja reinstalar (refazer o sistema e o banco)?** Remova tudo (volumes e imagens) com o comando `docker-compose down -v --rmi all` e, após isso, repita os passos 4 e 5 da instalação.

### Estrutura dos Containers

A arquitetura das imagens no Docker funciona com o seguinte roteamento:
```text
                              +----------+
                              |  browser |          
                              +----------+
                                |       |
                                |       |
                            +----------------+  
                            | localhost:8080 |
                            +----------------+  
                                |       |               
                                |       |             
                            +----------------+            
                            | ecidade-apache |            
                            +----------------+            
                                |        |                
                                |        |         
                            ____|        |_____           
                          /                   \          
                          /                     \         
                  +------------------------------------+
                  |           POSTGRES 5432            |
                  +------------------------------------+
```

---

## 🤝 Como Contribuir

A evolução contínua da ferramenta depende da comunidade, e contribuições são muito bem-vindas! Para contribuir, sugerimos utilizar o repositório espelho no GitHub seguindo este fluxo:

1.  Acesse o GitHub e clique em **Fork** no projeto.
2.  Crie uma nova *branch* para o seu desenvolvimento:
    ```bash
    git checkout -b minha-feature
    ```
3.  Implemente as correções ou melhorias necessárias.
4.  Realize o *commit* descrevendo a sua alteração:
    ```bash
    git commit -m "feat: descrição da alteração"
    ```
5.  Envie o código para o seu repositório (*fork*):
    ```bash
    git push origin minha-feature
    ```
6.  Acesse a página do repositório original e clique em **Compare & Pull Request**, fornecendo uma descrição clara da sua modificação.

---

## 📢 Comunicação e Suporte

*   **Grupo Oficial do Telegram:** Para comunicação direta, alinhamentos da comunidade, suporte e dúvidas, participe do canal através do link https://t.me/eCidadeCE.
*   **Registro de Problemas (Issues):** Para demandas técnicas específicas ou bugs, recomenda-se registrar uma *Issue* nos repositórios, a fim de manter a rastreabilidade do problema sempre que possível.
