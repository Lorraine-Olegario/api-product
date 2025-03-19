# 📝 Documentação de Configuração e Uso da API de Produtos

Esta documentação detalha os passos necessários para configurar e executar o projeto `api-product`, clonado do repositório GitHub, bem como informações básicas sobre sua API. Siga os passos abaixo para garantir uma configuração correta e o funcionamento adequado do sistema. 🚀

---

## ⚙️ Passos de Configuração

### 1. Clonar o Repositório 📥

Execute o comando abaixo para clonar o projeto do GitHub:

```sh
git clone https://github.com/Lorraine-Olegario/api-product
```

Acesse a pasta recém criada:

```sh
cd api-product
```

### 2. Instalar as Dependências

```sh
composer install
```

### 3. Configuração do Arquivo `.env`

Caso o arquivo `.env` não exista ainda em seu projeto, crie-o a partir do `.env.example`:

```sh
cp .env.example .env
```

### 4. Iniciar os Contêineres do Projeto

```sh
docker compose up -d --build
```

### 5. Permissões para Logs

```sh
docker compose exec app chown -R www-data:www-data /app/storage
```

### 6. Verificar se o Banco de Dados está Funcionando

Garanta que o contêiner de banco de dados está de pé. Os logs devem exibir a mensagem `ready for connections` nas últimas linhas:

```sh
docker compose logs database
```

### 7. Configurar o Schema e Dados do Banco

```sh
docker compose exec app php artisan migrate
```

### 8. Importar Dados da API

```sh
docker compose exec app php artisan products:import
```

Importar um produto específico:

```sh
docker compose exec app php artisan products:import --id=2
```

### 9. Rodar a Fila para Consumir os Dados Importados

```sh
docker compose exec app php artisan queue:work
```

---

## 📜 Documentação da API

[Acessar Documentação](http://127.0.0.1:9090/docs)
