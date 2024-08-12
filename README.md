# Acervo App

<p align="center" style="">
<img class="hidden h-16 w-auto lg:block" src="public/image/logo.png" alt="Acervo App" width="60%">
</p>
<p align="center">
<img src="https://img.shields.io/badge/license-MIT-green">
<img src="https://img.shields.io/badge/npm-v8.19.2-blue">
<img src="https://img.shields.io/badge/release date-Abr/2024-yellow">
</p>
<p align="center">
<img src="https://img.shields.io/badge/PHP 8.1.29-777BB4?style=for-the-badge&logo=php&logoColor=white">
<img src="https://img.shields.io/badge/Laravel 9.52.16-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
<img src="https://img.shields.io/badge/MySQL 8.0-316192?style=for-the-badge&logo=mysql&logoColor=white">
<img src="https://img.shields.io/badge/Inertia_JS-9553E9?style=for-the-badge&logo=Inertia&logoColor=white">
<img src="https://img.shields.io/badge/Vue.js v.3-35495E?style=for-the-badge&logo=vue.js&logoColor=4FC08D">
<img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white">
</p>
<p align="center">
<a href="https://linktr.ee/prbo" target="_blank"><img src="https://img.shields.io/badge/linktree-39E09B?style=for-the-badge&logo=linktree&logoColor=white"></a>
</p>

## Descrição do Projeto

Este projeto visa em criar uma aplicação para disponibilizar um acervo virtual de documentos em formato PDF registrados segundo a sua coleção (monografia, tese, artigo científico, etc.), cursos (Enfermagem, Engenharia, etc.), entre outros requisitos como: autor, orientador título e subtítulo.
A disponibilização é feita pela página principal, a qual possui um design minimalista e 100% compatível com a Web, nível avançado de adaptação a dispositivos móveis (mobile first), sendo corretamente apresentado em tablets, celulares e desktops.

### Repositório dos Documentos

O respositório de documentos é armazenado em sistema de arquivos local, tendo a possibilidade de alterar para o serviço de armazenamento de objetos - Amazon S3.
Para tornar os arquivos públicos na web, deve ser criado um link simbólico.

## Deploy Local e Publicação

### Ambiente de Desenvolvimento

1. Clone e acesse o código-fonte em um ambiente Linux ou no WSL

```bash
https://github.com/engendromestre/acervo-app-academico.git
cd acervo-app-academico
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
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
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
7. Limpar e gerar cache otimizado

```bash
sail artisan config:clear
sail artisan cache:clear
sail artisan route:clear
sail artisan config:cache
```

8. Migrar o banco de dados com as seeds

```bash
sail artisan migrate --seed
```

9. Criar link simbólico

```bash
sail artisan storage:link
```

10. Instalar dependências do NPM

```bash
sail npm install
```

11. Manter a compilação dos assets atualizadas durante o desenvolvimento

```bash
sail npm run dev
```

### Ambiente de Produção

Este repositório utiliza GitHub Actions para automatizar o processo de teste e implantação do Aplicativo Acervo-App para o Azure. O workflow é dividido em duas partes principais: test e deploy.

#### Workflow Overview

O workflow é acionado em três situações:

 - Push: Quando há um push para o branch main.
 - Pull Request: Quando um pull request é criado ou atualizado para o branch main.
 - Workflow Dispatch: Permite iniciar o workflow manualmente.

##### Jobs

1. Test
 - Executa em Ubuntu: Configura o PHP e MySQL, e realiza testes.
 - Etapas:
   - Checkout do código
   - Configuração do PHP com extensões necessárias
   - Verificação da conexão MySQL.
   - Instalação das dependências do Composer se composer.json existir
   - Configuração do Laravel
   - Instalação e construção dos assets NPM
   - Criação e configuração de diretórios de cache
   - Execução de testes com Pest

2. Deploy
 - Executa após os testes: Faz o deploy para o Azure Web App.
 - Etapas:
    - Checkout do código.
    - Configuração do PHP e Node.js.
    - Validação e instalação do Composer.
    - Criação e upload de um arquivo ZIP com o projeto.
    - Download e descompactação do artefato.
    - Instalação de dependências NPM e construção dos assets.
    - Login no Azure e implantação do aplicativo.

3. Configurações das Variáveis de Ambiente
 - Adicione os seguintes secrets no GitHub:

    - Configurações do MySQL e credenciais do AWS.
    - Credenciais de acesso ao Azure.

#### Azure App Service

 1.  Criar as seguintes variáveis de ambiente com seus respectivos valores:
- APP_ADMIN
- APP_DEBUG
- APP_ENV
- APP_KEY
- APP_NAME
- APP_SUPERADMIN
- APP_URL
- APP_USER
- AWS_ACCESS_KEY_ID
- AWS_BUCKET
- AWS_DEFAULT_REGION
- AWS_SECRET_ACCESS_KEY
- DB_CONNECTION
- DB_DATABASE
- DB_HOST
- DB_PASSWORD
- DB_PORT
- DB_USERNAME
- FILESYSTEM_DISK
- GOOGLE_CALLBACK_REDIRECT
- GOOGLE_CLIENT_ID
- GOOGLE_CLIENT_SECRET
- MAIL_ENCRYPTION
- MAIL_FROM_ADDRESS
- MAIL_FROM_NAME
- MAIL_HOST
- MAIL_MAILER
- MAIL_PASSWORD
- MAIL_PORT
- MAIL_USERNAME
- VITE_ENV
- VITE_PUSHER_APP_CLUSTER
- VITE_PUSHER_APP_KEY
- VITE_PUSHER_HOST
- VITE_PUSHER_PORT
- VITE_PUSHER_SCHEME

2. Configuração

Crie um comando de iniciação para configurar o PHP, Nginx e Laravel.

**Observação:** os arquivos com os scripts dos comandos estão no diretório .github/actions/azure.


## Links do Projeto

<p>
<a href="https://miro.com/app/board/uXjVKAdsdKI=/?share_link_id=549149955540" target="_blank" />Lean Canvas
</p>

<p>
<a href="https://miro.com/app/board/uXjVK_jLHG0=/?share_link_id=263872928237" target="_blank" />Diagrama C4
</p>

<p>
<a href="https://acervo-app2.azurewebsites.net/" target="_blank" />Endereço do site
</p>
