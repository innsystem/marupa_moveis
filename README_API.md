# Documentação da API - Sistema de Gerenciamento Empresarial

## Visão Geral

Este documento descreve a API RESTful do Sistema de Gerenciamento Empresarial, que permite acesso programático a todas as principais entidades e recursos do sistema. A API segue os princípios RESTful e utiliza JSON como formato padrão para transferência de dados.

## Autenticação

> **Nota:** A autenticação via access_token e secret_key está planejada para implementação futura.

Atualmente, a API utiliza o Laravel Sanctum para autenticação. Para obter acesso, você deve:

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

## Importante: Ordem das Rotas

Ao definir rotas no arquivo `routes/api.php`, é fundamental que as rotas customizadas (como `get`, `post`, `put`, `delete` para endpoints específicos) sejam declaradas **antes** das rotas automáticas criadas por `Route::apiResource`. Isso garante que as rotas customizadas não sejam sobrescritas pelas rotas RESTful padrão do Laravel.

**Exemplo correto:**

```php
// Rotas customizadas
Route::get('customers/{id}/addresses', [CustomerController::class, 'getAddresses']);
Route::post('customers/{id}/addresses', [CustomerController::class, 'storeAddress']);
// ... outras rotas customizadas ...

// Rotas RESTful
Route::apiResource('customers', CustomerController::class);
```

Se as rotas customizadas forem declaradas **após** o `apiResource`, elas podem não funcionar corretamente, pois o Laravel irá considerar apenas as rotas RESTful padrão.

## Endpoints Disponíveis

A API fornece acesso a todas as entidades principais do sistema, incluindo:

### Status

```
GET    /api/statuses              - Lista todos os status
POST   /api/statuses              - Cria um novo status
GET    /api/statuses/{id}         - Obtém um status específico
PUT    /api/statuses/{id}         - Atualiza um status específico
DELETE /api/statuses/{id}         - Remove um status específico
```

### Usuários

```
GET    /api/users                 - Lista todos os usuários
POST   /api/users                 - Cria um novo usuário
GET    /api/users/{id}            - Obtém um usuário específico
PUT    /api/users/{id}            - Atualiza um usuário específico
DELETE /api/users/{id}            - Remove um usuário específico
```

### Clientes

```
GET    /api/customers                                 - Lista todos os clientes
POST   /api/customers                                 - Cria um novo cliente
GET    /api/customers/{id}                            - Obtém um cliente específico
PUT    /api/customers/{id}                            - Atualiza um cliente específico
DELETE /api/customers/{id}                            - Remove um cliente específico
GET    /api/customers/{id}/addresses                  - Lista endereços de um cliente
POST   /api/customers/{id}/addresses                  - Adiciona endereço a um cliente
PUT    /api/customers/{id}/addresses/{addressId}      - Atualiza endereço de um cliente
DELETE /api/customers/{id}/addresses/{addressId}      - Remove endereço de um cliente
POST   /api/customers/{id}/addresses/{addressId}/default - Define endereço padrão
GET    /api/customers/{id}/preferences                - Obtém preferências do cliente
PUT    /api/customers/{id}/preferences                - Atualiza preferências do cliente
```

### Serviços de Usuários

```
GET    /api/user-services                  - Lista todos os serviços de usuários
POST   /api/user-services                  - Cria um novo serviço de usuário
GET    /api/user-services/{id}             - Obtém um serviço de usuário específico
PUT    /api/user-services/{id}             - Atualiza um serviço de usuário específico
DELETE /api/user-services/{id}             - Remove um serviço de usuário específico
POST   /api/user-services/{id}/generate-invoice - Gera fatura para um serviço
```

### Páginas

```
GET    /api/pages                  - Lista todas as páginas
POST   /api/pages                  - Cria uma nova página
GET    /api/pages/{id}             - Obtém uma página específica
PUT    /api/pages/{id}             - Atualiza uma página específica
DELETE /api/pages/{id}             - Remove uma página específica
```

### Serviços

```
GET    /api/services               - Lista todos os serviços
POST   /api/services               - Cria um novo serviço
GET    /api/services/{id}          - Obtém um serviço específico
PUT    /api/services/{id}          - Atualiza um serviço específico
DELETE /api/services/{id}          - Remove um serviço específico
```

### Portfólio

```
GET    /api/portfolios             - Lista todos os itens de portfólio
POST   /api/portfolios             - Cria um novo item de portfólio
GET    /api/portfolios/{id}        - Obtém um item de portfólio específico
PUT    /api/portfolios/{id}        - Atualiza um item de portfólio específico
DELETE /api/portfolios/{id}        - Remove um item de portfólio específico
```

### Integrações

```
GET    /api/integrations           - Lista todas as integrações
POST   /api/integrations           - Cria uma nova integração
GET    /api/integrations/{id}      - Obtém uma integração específica
PUT    /api/integrations/{id}      - Atualiza uma integração específica
DELETE /api/integrations/{id}      - Remove uma integração específica
```

### Depoimentos

```
GET    /api/testimonials           - Lista todos os depoimentos
POST   /api/testimonials           - Cria um novo depoimento
GET    /api/testimonials/{id}      - Obtém um depoimento específico
PUT    /api/testimonials/{id}      - Atualiza um depoimento específico
DELETE /api/testimonials/{id}      - Remove um depoimento específico
```

### Sliders

```
GET    /api/sliders                - Lista todos os sliders
POST   /api/sliders                - Cria um novo slider
GET    /api/sliders/{id}           - Obtém um slider específico
PUT    /api/sliders/{id}           - Atualiza um slider específico
DELETE /api/sliders/{id}           - Remove um slider específico
```

### Grupos de Usuários

```
GET    /api/usergroups             - Lista todos os grupos de usuários
POST   /api/usergroups             - Cria um novo grupo de usuários
GET    /api/usergroups/{id}        - Obtém um grupo de usuários específico
PUT    /api/usergroups/{id}        - Atualiza um grupo de usuários específico
DELETE /api/usergroups/{id}        - Remove um grupo de usuários específico
```

### Permissões

```
GET    /api/permissions            - Lista todas as permissões
POST   /api/permissions            - Cria uma nova permissão
GET    /api/permissions/{id}       - Obtém uma permissão específica
PUT    /api/permissions/{id}       - Atualiza uma permissão específica
DELETE /api/permissions/{id}       - Remove uma permissão específica
```

### Faturas

```
GET    /api/invoices               - Lista todas as faturas
POST   /api/invoices               - Cria uma nova fatura
GET    /api/invoices/{id}          - Obtém uma fatura específica
PUT    /api/invoices/{id}          - Atualiza uma fatura específica
DELETE /api/invoices/{id}          - Remove uma fatura específica
```

### Transações

```
GET    /api/transactions           - Lista todas as transações
POST   /api/transactions           - Cria uma nova transação
GET    /api/transactions/{id}      - Obtém uma transação específica
PUT    /api/transactions/{id}      - Atualiza uma transação específica
DELETE /api/transactions/{id}      - Remove uma transação específica
```

### Contas Bancárias

```
GET    /api/bank_account           - Lista todas as contas bancárias
POST   /api/bank_account           - Cria uma nova conta bancária
GET    /api/bank_account/{id}      - Obtém uma conta bancária específica
PUT    /api/bank_account/{id}      - Atualiza uma conta bancária específica
DELETE /api/bank_account/{id}      - Remove uma conta bancária específica
```

### Logs do Sistema

```
GET    /api/logs                   - Obtém logs do sistema
POST   /api/logs/clear             - Limpa logs do sistema
GET    /api/logs/download          - Baixa arquivo de log
```

### Dashboard

```
GET    /api/dashboard              - Obtém métricas do dashboard
GET    /api/settings               - Obtém configurações do sistema
POST   /api/settings               - Atualiza configurações do sistema
POST   /api/settings/update-images - Atualiza imagens (logo, favicon)
```

### Comandos do Sistema

```
POST   /api/commander/create       - Cria novos recursos via linha de comando
POST   /api/commander/migrate      - Executa migrações do banco de dados
```

### Importação de Dados

```
POST   /api/import/data            - Importa dados via arquivo JSON
```

## Estrutura de Dados

### Status
```json
{
  "id": 1,
  "name": "Ativo",
  "type": "general",
  "description": "Item está ativo no sistema",
  "color": "#28a745",
  "icon": "fa-check-circle",
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

### Usuário
```json
{
  "id": 1,
  "name": "Nome do Usuário",
  "email": "usuario@exemplo.com", 
  "user_group_id": 2,
  "document": "12345678901",
  "phone": "(11) 98765-4321",
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

### Cliente (Usuário com tipo cliente)
```json
{
  "id": 3,
  "name": "Nome do Cliente",
  "email": "cliente@exemplo.com",
  "user_group_id": 3,
  "document": "98765432101",
  "phone": "(11) 98765-4321",
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z",
  "addresses": [
    {
      "id": 1,
      "user_id": 3,
      "street": "Rua Exemplo",
      "number": "123",
      "complement": "Apto 45",
      "district": "Centro",
      "city": "São Paulo",
      "state": "SP",
      "zipcode": "01234567",
      "is_default": true,
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  ],
  "preferences": {
    "id": 1,
    "user_id": 3,
    "payment_default": "pix",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
}
```

### Serviço de Usuário
```json
{
  "id": 1,
  "user_id": 3,
  "service_id": 2,
  "start_date": "2023-01-01",
  "end_date": "2023-12-31",
  "price": 99.90,
  "period": "monthly",
  "status": 3,
  "metadata": "{\"custom_field\":\"value\"}",
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z",
  "service": {
    "id": 2,
    "title": "Hospedagem de Site",
    "description": "Descrição do serviço",
    "status": 1
  },
  "statusRelation": {
    "id": 3,
    "name": "Ativo",
    "color": "#28a745"
  },
  "user": {
    "id": 3,
    "name": "Nome do Cliente",
    "email": "cliente@exemplo.com"
  }
}
```

### Fatura
```json
{
  "id": 1,
  "user_id": 3,
  "integration_id": 1,
  "method_type": "pix",
  "total": 99.90,
  "status": 23,
  "due_at": "2023-01-15",
  "paid_at": null,
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z",
  "items": [
    {
      "id": 1,
      "invoice_id": 1,
      "target_type": "user_service",
      "target_id": 1,
      "description": "Hospedagem de Site - Jan/2023",
      "quantity": 1,
      "price_unit": 99.90,
      "price_total": 99.90,
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  ]
}
```

## Códigos de Resposta HTTP

| Código | Descrição                                                    |
|--------|------------------------------------------------------------|
| 200    | OK - Requisição foi bem sucedida                           |
| 201    | Created - Recurso criado com sucesso                       |
| 400    | Bad Request - Erro na requisição do cliente                |
| 401    | Unauthorized - Autenticação necessária                     |
| 403    | Forbidden - Cliente não tem permissão para esse recurso    |
| 404    | Not Found - Recurso não encontrado                         |
| 422    | Unprocessable Entity - Validação falhou                    |
| 500    | Internal Server Error - Erro no servidor                   |

## Paginação

Endpoints que retornam múltiplos recursos (ex: listagem de clientes) suportam paginação através dos seguintes parâmetros:

- `page`: Número da página (começa em 1)
- `per_page`: Número de itens por página (padrão: 10)

Exemplo:
```
GET /api/customers?page=2&per_page=15
```

## Filtragem

Vários endpoints suportam filtragem de resultados através de parâmetros na URL:

- `name`: Filtra por nome (parcial)
- `status`: Filtra por status
- `start_date`: Filtra por data inicial
- `end_date`: Filtra por data final

Exemplo:
```
GET /api/invoices?status=23&start_date=2023-01-01&end_date=2023-01-31
```

## Próximos Passos

1. Implementação de autenticação via access_token e secret_key
2. Documentação interativa da API com Swagger/OpenAPI
3. Versionamento da API
4. Suporte a rate limiting
5. Cache de respostas 