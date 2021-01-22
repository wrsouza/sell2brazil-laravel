<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<img src="https://github.com/wrsouza/sell2brazil-laravel/workflows/ci-php/badge.svg">
</p>

### Instruções para Instalação do Backend

##### Efetuar o clone do repositório
- <b>git clone https://github.com/wrsouza/sell2brazil-laravel</b>

Arquivo .env

- Renomear o arquivo <b>.env.example</b> para <b>.env</b>
- Editar o arquivo <b>.env</b> com as configurações do banco de dados.
- Configurações no arquivo <b>docker-compose.yml</b>

Rodar o docker compose

- <b>docker-compose up -d</b>

Instalar as dependencias do laravel

- <b>docker exec -it sell2brazil composer install</b>

Executar as permissões de gravação da pasta storage

- <b>docker exec -it sell2brazil chmod 777 -R storage</b>

Executar os Testes

- <b>docker exec -it sell2brazil php artisan test</b>

Acessando o container <b>( PHP )</b>

- <b>docker exec -it sell2brazil bash</b>
