# Laravel Mercado Livre Integration

## Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-repositorio.git
   cd nome-do-projeto

## Instalando as dependencias

2. Dentro da path execute o comando 
    ```bash
    composer install

## Configurando as variaveis de ambiente
3. Agora configure o arquivo .env com o seu banco de dados e com suas credenciais do mercado-livre
    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=Nome_Banco 
    DB_USERNAME=Usuario
    DB_PASSWORD=Senha

    MERCADO_LIVRE_CLIENT_ID=seu_client_id
    MERCADO_LIVRE_CLIENT_SECRET=seu_client_secret
    MERCADO_LIVRE_REDIRECT_URI=sua_url_de_redirect
    MERCADO_LIVRE_ACCESS_TOKEN=seu_token_access

## Gerando key da aplicação - migration e subindo o servidor
4. agora execute o comando para criar a chave da sua aplicação e sua migration subir o servidor do Laravel
    ```bash
    php artisan key:generate
    php artisan migrate
    php artisan serve

## Acessando a aplicação
5. Após rodar todos os comando a api estara disponivel em 
    ```bash
    http://localhost:8000/
