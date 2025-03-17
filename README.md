## Passos de configuração

- git clone
`https://github.com/Lorraine-Olegario/api-product`

- Acesse a pasta recém criada:
`cd api-poduct`

- Instale as dependências do projeto:
`composer install`

- Caso o arquivo .env não exista ainda em seu projeto, crie-o a partir do .env.example:
`cp .env.example .env`

- Inicie os contêineres do projeto:
`docker compose up -d --build` 

- Dê permissões ao usuário correto para escrever logs na aplicação:
`docker compose exec app chown -R www-data:www-data /app/storage`

- Garanta que o contêiner de banco de dados está de pé. Os logs devem exibir a mensagem ready for connections nas últimas linhas:
`docker compose logs database`

- Com o contêiner de banco de dados de pé, configure o schema e dados do banco:
`docker compose exec -u $(id -u):$(id -g) app php artisan migrate`


- Use o comando para importar dados da api
`php artisan products:import`

`php artisan products:import --id=2`

- Para rodar a fila e consumir os dados importado use:
`php artisan queue:work`

