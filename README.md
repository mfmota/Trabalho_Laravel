# Pizzaria API

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![Docker](https://img.shields.io/badge/Docker-20.10+-2496ED?style=for-the-badge&logo=docker)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)

API RESTful completa desenvolvida em Laravel para o gerenciamento de uma pizzaria ou restaurante. O projeto serve como um backend robusto, pronto para ser consumido por aplicações frontend (Web, Mobile, etc.).

## 📖 Sobre o Projeto

Esta API foi criada para centralizar e controlar todas as operações de um estabelecimento do ramo alimentício. Ela permite o gerenciamento completo do cardápio, o recebimento e acompanhamento de pedidos, garantindo um fluxo de trabalho organizado e eficiente.

O público-alvo são desenvolvedores que irão construir ou manter a interface do cliente (site, aplicativo de delivery) ou o sistema de gestão interno (painel de cozinha, administração).

### ✨ Principais Funcionalidades

-   **Autenticação**: Sistema de login seguro com Bearer Tokens via Laravel Sanctum.
-   **Gerenciamento de Categorias**: CRUD completo para as categorias de produtos (ex: Pizzas, Bebidas, Sobremesas).
-   **Gerenciamento de Produtos**: CRUD completo para produtos, incluindo upload de imagens para o banner.
-   **Fluxo de Pedidos**: Sistema completo para criação, adição e remoção de itens, envio para preparo e finalização de pedidos.
-   **Testes Automatizados**: Suíte de testes de feature e unitários para garantir a estabilidade e o correto funcionamento da API.

## 📚 Endpoints da API

Abaixo estão os principais endpoints disponíveis. Todas as rotas (exceto `/login`) exigem um token de autenticação no cabeçalho `Authorization: Bearer <seu_token>`.

| Método HTTP | Endpoint                               | Descrição                                 |
| :---------- | :------------------------------------- | :---------------------------------------- |
| `POST`      | `/login`                               | Realiza o login e retorna um token de acesso. |
| `POST`      | `/logout`                              | Invalida o token de acesso do usuário.    |
| `POST`      | `/users`                               | Cria um novo usuário.                     |
| `GET`       | `/api/categories`                      | Lista todas as categorias.                |
| `POST`      | `/api/categories`                      | Cria uma nova categoria.                  |
| `GET`       | `/api/products`                        | Lista todos os produtos (pode filtrar por `category_id`). |
| `POST`      | `/api/products`                        | Cria um novo produto (usa `multipart/form-data`). |
| `DELETE`    | `/api/products/{id}`                   | Deleta um produto.                        |
| `POST`      | `/api/orders`                          | Cria um novo pedido (em modo rascunho).   |
| `GET`       | `/api/orders/{id}`                     | Exibe os detalhes de um pedido.           |
| `POST`      | `/api/orders/{id}/items`               | Adiciona um item a um pedido.             |
| `DELETE`    | `/api/orders/{id}/items/{item_id}`     | Remove um item de um pedido.              |
| `PUT`       | `/api/orders/{id}/send`                | Envia o pedido para a cozinha.            |
| `PUT`       | `/api/orders/{id}/finish`              | Finaliza o pedido.                        |

## 🚀 Tecnologias Utilizadas

-   **PHP 8.2+**
-   **Laravel 11**
-   **Laravel Sail**: Ambiente de desenvolvimento local baseado em Docker.
-   **MySQL 8.0**: Banco de dados relacional.
-   **Laravel Sanctum**: Para autenticação de API.
-   **PHPUnit**: Para testes automatizados.

## ⚙️ Pré-requisitos

Antes de começar, garanta que você tenha o seguinte software instalado na sua máquina:

-   [Docker Desktop](https://www.docker.com/products/docker-desktop/)

O Docker irá gerenciar todos os outros serviços, como o PHP, Nginx e MySQL, através do Laravel Sail.

## 🛠️ Instalação e Execução

Siga os passos abaixo para configurar e executar a aplicação em seu ambiente de desenvolvimento.

1.  **Clone o repositório:**
    ```bash
    git clone [https://github.com...]
    ```

2.  **Copie o arquivo de ambiente:**
    ```bash
    cp .env.example .env
    ```

3.  **Instale as dependências do Composer:**
    O Sail irá baixar uma imagem do PHP e executar o `composer install` dentro de um container Docker.
    ```bash
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs
    ```

4.  **Inicie os containers do Sail:**
    Este comando irá construir e iniciar os containers da aplicação, banco de dados, etc., em background (`-d`).
    ```bash
    ./vendor/bin/sail up -d
    ```

5.  **Gere a chave da aplicação:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

6.  **Execute as migrations e popule o banco de dados:**
    O comando `migrate:fresh` irá apagar todas as tabelas e recriá-las. A flag `--seed` irá executar os seeders para popular o banco com dados iniciais.
    ```bash
    ./vendor/bin/sail artisan migrate:fresh --seed
    ```

7.  **Crie o link simbólico do Storage:**
    Isso torna os arquivos de upload (como os banners dos produtos) acessíveis publicamente.
    ```bash
    ./vendor/bin/sail artisan storage:link
    ```

8.  **(Opcional) Gere os arquivos do IDE Helper:**
    Para melhorar a autocompletação na sua IDE.
    ```bash
    ./vendor/bin/sail artisan ide-helper:generate
    ```

🎉 **Pronto!** Sua API está em execução e acessível em `http://localhost`.

## ✅ Executando os Testes

Para garantir que tudo está funcionando corretamente, execute a suíte de testes automatizados.

```bash
# Rodar todos os testes (Unit e Feature)
./vendor/bin/sail test

# Rodar apenas os testes de Feature
./vendor/bin/sail test tests/Feature

# Rodar um arquivo de teste específico
./vendor/bin/sail test tests/Feature/ProductCrudTest.php
