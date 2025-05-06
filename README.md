# Sistema de Gerenciamento Empresarial - Laravel

## Sobre o Sistema

Um sistema completo de gerenciamento empresarial desenvolvido em Laravel, projetado para empresas que precisam de uma plataforma integrada para administração de serviços, portfólio, clientes, faturamento e transações financeiras. A plataforma possui um site institucional na área pública e um painel administrativo completo com controle de acesso baseado em grupos e permissões.

## Características Principais

- **Painel Administrativo Completo** - Interface administrativa protegida por autenticação e sistema avançado de permissões baseado em grupos de usuários.
- **Gerenciamento de Usuários** - Sistema hierárquico com grupos de usuários e permissões granulares para cada funcionalidade do sistema.
- **Conteúdo do Site** - Gestão completa de:
  - Páginas institucionais com SEO (título, slug, palavras-chave)
  - Serviços com descrição e ordenação personalizada
  - Portfólio de trabalhos com múltiplas imagens e destaque
  - Depoimentos de clientes com avaliação e localização
  - Sliders para banners rotativos na home com links customizáveis
- **Sistema Financeiro** - Solução completa para:
  - Gestão de faturas com data de vencimento e status
  - Itens de fatura detalhados (descrição, quantidade, preço unitário, total)
  - Transações financeiras (entradas e saídas) com taxas de gateway
  - Relatórios financeiros e controle de fluxo de caixa
  - Contas bancárias para gestão financeira 
- **Integração com Gateway de Pagamento** - Suporte para integrações múltiplas:
  - Configuração flexível via JSON para diferentes gateways
  - MercadoPago nativo para processamento de pagamentos
  - Estrutura escalável para adicionar novos gateways
- **Webhooks** - Sistema robusto para:
  - Processamento automático de notificações de pagamentos
  - Atualização de status de faturas em tempo real
  - Registro detalhado de respostas das integrações
- **API RESTful** - Interface de programação completa:
  - Endpoints para todas as entidades principais
  - Autenticação via Sanctum
  - Documentação integrada

## Entidades do Sistema

### Área Administrativa
- **Configurações** - Armazenamento de configurações do sistema em formato chave-valor
- **Status** - Sistema centralizado de status com cores e ícones para diversas entidades
- **Grupos de Usuários** - Categorização de usuários com diferentes níveis de acesso
- **Permissões** - Controle granular de acesso às funcionalidades
- **Usuários** - Administradores e clientes com diferentes perfis de acesso
- **Endereços de Usuários** - Gestão de múltiplos endereços para usuários e clientes

### Conteúdo
- **Páginas** - Conteúdo institucional com suporte a SEO
- **Serviços** - Produtos ou serviços oferecidos pela empresa
- **Portfólio** - Trabalhos realizados com galeria de imagens
- **Depoimentos** - Avaliações e feedback de clientes
- **Sliders** - Banners rotativos para destaque na home com controle de datas

### Financeiro
- **Integrações** - Conexões com serviços externos (gateways de pagamento)
- **Faturas** - Documentos financeiros para cobrança de clientes
- **Itens de Fatura** - Detalhamento dos produtos/serviços cobrados
- **Transações** - Registro de entradas e saídas financeiras
- **Webhooks** - Registro de notificações de pagamento
- **Contas Bancárias** - Administração de contas para gestão financeira
- **Serviços de Usuários** - Associação entre usuários e serviços contratados

## Requisitos Técnicos

- PHP >= 8.1
- MySQL ou MariaDB
- Composer
- Node.js e NPM para compilação de assets
- Extensões PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## Instalação

1. Clone o repositório
   ```bash
   git clone [url-do-repositorio]
   ```

2. Instale as dependências do PHP
   ```bash
   composer install
   ```

3. Copie o arquivo de ambiente e gere a chave de aplicação
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan storage:link
   ```

4. Configure o arquivo .env com suas informações de banco de dados e configurações de email

5. Execute as migrações do banco de dados
   ```bash
   php artisan migrate
   ```

6. [Opcional] Popule o banco com dados iniciais
   ```bash
   php artisan db:seed
   ```

7. Limpa os caches
   ```bash
   php artisan optimize:clear
   ```

## Estrutura do Backend

O sistema utiliza uma arquitetura MVC completa com:

### Controladores
- **Admin**: Controladores para o painel administrativo com operações CRUD completas
- **Site**: Controladores para o site público com exibição de conteúdo
- **Api**: Controladores RESTful para acesso programático aos dados

### Modelos
- Implementação de relacionamentos entre entidades
- Escopos para filtragem de dados
- Mutators e accessors para formatação de dados

### Rotas
- **Web**: Rotas para o site público e painel administrativo
- **API**: Endpoints RESTful para acesso programático
- **Webhooks**: Endpoints para recebimento de notificações externas

## Fluxo de Trabalho

### Gerenciamento de Conteúdo
1. Criar e gerenciar páginas, serviços e portfólio no painel admin
2. Configurar os sliders e depoimentos para exibição no site
3. Publicar o conteúdo alterando seu status para "Ativo"

### Gestão Financeira
1. Cadastrar clientes no sistema com seus endereços
2. Criar faturas associadas a clientes com itens detalhados
3. Gerar links de pagamento via integração com gateways
4. Acompanhar status de pagamento via webhooks automatizados
5. Registro automático de transações após confirmação de pagamento
6. Associar serviços aos usuários para controle de contratações

## Estrutura do Sistema

### Área Pública
- Home com slider e destaques
- Páginas institucionais personalizáveis
- Listagem e detalhes de serviços
- Galeria de portfólio com trabalhos realizados
- Seção de depoimentos de clientes
- Sistema de login para área do cliente

### Área Administrativa
- Dashboard com visão geral e indicadores
- Gerenciamento de usuários, grupos e permissões
- Administração de conteúdo do site
- Sistema completo de faturamento
- Registro e controle de transações financeiras
- Configurações do sistema e integrações
- Ferramenta de linha de comando para operações avançadas

## Licença

Este projeto é licenciado sob a licença MIT - veja o arquivo LICENSE para mais detalhes.

## Contato

Para mais informações ou suporte, entre em contato com a equipe de desenvolvimento.
