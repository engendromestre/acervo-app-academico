# Acervo App

<p align="center" style="">
<img class="hidden h-16 w-auto lg:block" src="public/doc/16835.png" alt="Faculdade Galileu" width="60%">
</p>
<p align="center">
<img src="https://img.shields.io/badge/license-MIT-green">
<img src="https://img.shields.io/badge/npm-v8.19.2-blue">
<img src="https://img.shields.io/badge/release date-Abr/2023-yellow">
</p>
<p align="center">
<img src="https://img.shields.io/badge/PHP 8.1.29-777BB4?style=for-the-badge&logo=php&logoColor=white">
<img src="https://img.shields.io/badge/Laravel 9.52.16-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
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
APP_NAME="Acervo App"
APP_KEY=
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=acervo-app
DB_USERNAME=sail
DB_PASSWORD=password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=TLS
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"
```

5. Rodar o ambiente de desenvolvimento Sail

```bash
sail up -d 
```

6. Gerar a chave APP_KEY

```bash
sail artisan key:generate 
```

7. Migrar o banco de dados com as seeds

```bash
sail artisan migrate --seed
```

8. Criar link simbólico

```bash
php artisan storage:link
```

9. Manter a compilação dos assets atualizadas durante o desenvolvimento

```bash
npm run dev
```

### Ambiente de Produção 
1. Instalar depedências de produção. Caso decida hospedar em um servidor compartilhado que não possua acesso root, gere as dependências do projeto

```bash
composer update
npm install --production
```

Obs.: verifique a engine package.json para ver as versões compatíveis.

2. Compilar o aplicativo

```bash
npm build
```

Obs.: Neste ponto está pronto para subir para a hospedagem, caso não possua acesso root do servidor.
