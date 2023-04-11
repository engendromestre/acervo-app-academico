# Acervo Galileu

<p align="center" style="background: rgb(88,156,78);
background: linear-gradient(90deg, rgba(88,156,78,1) 9%, rgba(255,102,0,1) 53%, rgba(32,91,124,1) 91%);">
<img class="hidden h-16 w-auto lg:block" src="https://faculdadegalileu.com.br/content/loans4/images/fatec_logo.png?color=white&amp;shade=500" alt="Faculdade Galileu" width="60%">
</p>
<p align="center">
<img src="https://img.shields.io/badge/license-MIT-green">
<img src="https://img.shields.io/badge/npm-v8.19.2-blue">
<img src="https://img.shields.io/badge/release date-Abr/2023-yellow">
</p>
<p align="center">
<img src="https://img.shields.io/badge/PHP 8.1.10-777BB4?style=for-the-badge&logo=php&logoColor=white">
<img src="https://img.shields.io/badge/Laravel 9.31.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
<img src="https://img.shields.io/badge/MySQL 8.0-316192?style=for-the-badge&logo=mysql&logoColor=white">
<img src="https://img.shields.io/badge/Vue.js v.3-35495E?style=for-the-badge&logo=vue.js&logoColor=4FC08D">
<img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white">
</p>
<p align="center">
<a href="https://linktr.ee/prbo" target="_blank"><img src="https://img.shields.io/badge/linktree-39E09B?style=for-the-badge&logo=linktree&logoColor=white"></a>
</p>

## Descrição do Projeto

Desenvolver e disponibilizar um acervo virtual de documentos em formato PDF registrados segundo a sua coleção (monografia, tese, artigo científico, etc.), cursos (Enfermagem, Engenharia, etc.), entre outros requisitos como: autor, orientador título e subtítulo.
A disponibilização é feita pela página principal, a qual possui um design minimalista e 100% compatível com a Web, nível avançado de adaptação a dispositivos móveis (mobile first), sendo corretamente apresentado em tablets, celulares e desktops.

### Repositório dos Documentos

O respositório de documentos é armazenado em sistema de arquivos local, tendo a possibilidade de alterar para o serviço de armazenamento de objetos - Amazon S3.
Para tornar os arquivos públicos na web, deve ser criado um link simbólico.

## Deploy Local e Publicação

### Ambiente de Desenvolvimento
1. Clone e acesse o código-fonte em um ambiente Linux ou no WSL

```bash
https://github.com/engendromestre/acervo-galileu-app.git
cd acervo-galileu-app
```
2. Instalar todas as depedências requeridas pelo Laravel Sail

```bash
sudo docker run --rm -v $(pwd):/opt -w /opt laravelsail/php81-composer:latest composer install
```

3. Configurar as permissões de diretório

```bash
sudo chown -R $USER: .
sudo chgrp -R $USER .
```

4. Crie e defina as variáveis de ambiente

```bash
cp .env.example .env
```

```bash
APP_NAME="Acervo Galileu"
APP_KEY=
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=acervo-galileu-app
DB_USERNAME=sail
DB_PASSWORD=password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=engendro.mestre@gmail.com
MAIL_PASSWORD=nmbyokrbzqxhhybc
MAIL_ENCRYPTION=TLS
MAIL_FROM_ADDRESS=engendro.mestre@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

5. Rodar o ambiente de desenvolvimento Sail

```bash
sail up -d 
```

6. Gerar a chage APP_KEY

```bash
sail artisan key:generate 
```

7. Migrar o banco de dados com as sementes

```bash
sail artisan migrate --seed
```

### Ambiente de Produção 
8. Instalar depedências de produção. Caso decida hospedar em um servidor compartilhado que não possua acesso root, gere as dependências do projeto

```bash
composer update
npm install --production
```

Obs.: verifique a engine package.json para ver as versões compatíveis.

9. Compilar o aplicativo

```bash
npm build
```

Obs.: Neste ponto está pronto para subir para a hospedagem, caso não possua acesso root do servidor.

## Documentação da API

#### Autenticação

```http
  POST /login
```

| Parâmetro  | Tipo     | Descrição        |
| :--------- | :------- | :--------------- |
| `email`    | `string` | **Obrigatório**. |
| `password` | `string` | **Obrigatório**. |
| `remember` | `string` | **Opcional**.    |

#### Exibe o menu

```http
  GET /dashboard
```

#### Lista os registros

```http
  GET admin/{entity}
```

#### Exibe/modifica um registro

```http
  GET admin/{entity}/{id}
```
