<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            // general
            ['title' => 'Acessar painel administrativo', 'key' => 'admin.index', 'type' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Visualizar configurações', 'key' => 'admin.settings', 'type' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar configurações', 'key' => 'admin.settings.update', 'type' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar imagens de configurações', 'key' => 'admin.settings.updateImages', 'type' => 'general', 'created_at' => now(), 'updated_at' => now()],
            // logs
            ['title' => 'Visualizar logs', 'key' => 'admin.logs.index', 'type' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Limpar logs', 'key' => 'admin.logs.clear', 'type' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Download logs', 'key' => 'admin.logs.download', 'type' => 'general', 'created_at' => now(), 'updated_at' => now()],
            // commandar
            ['title' => 'Acessar comandante', 'key' => 'admin.commander', 'type' => 'developer', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar comando', 'key' => 'admin.commander.create', 'type' => 'developer', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Executar migração', 'key' => 'admin.commander.migrate', 'type' => 'developer', 'created_at' => now(), 'updated_at' => now()],
            // status
            ['title' => 'Visualizar status', 'key' => 'admin.statuses.index', 'type' => 'statuses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar status', 'key' => 'admin.statuses.load', 'type' => 'statuses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar status', 'key' => 'admin.statuses.create', 'type' => 'statuses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar status', 'key' => 'admin.statuses.store', 'type' => 'statuses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar status', 'key' => 'admin.statuses.edit', 'type' => 'statuses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar status', 'key' => 'admin.statuses.update', 'type' => 'statuses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir status', 'key' => 'admin.statuses.delete', 'type' => 'statuses', 'created_at' => now(), 'updated_at' => now()],
            // user groups
            ['title' => 'Visualizar grupos de usuários', 'key' => 'admin.user_groups.index', 'type' => 'user_groups', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar grupos de usuários', 'key' => 'admin.user_groups.load', 'type' => 'user_groups', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar grupo de usuários', 'key' => 'admin.user_groups.create', 'type' => 'user_groups', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar grupo de usuários', 'key' => 'admin.user_groups.store', 'type' => 'user_groups', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar grupo de usuários', 'key' => 'admin.user_groups.edit', 'type' => 'user_groups', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar grupo de usuários', 'key' => 'admin.user_groups.update', 'type' => 'user_groups', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir grupo de usuários', 'key' => 'admin.user_groups.delete', 'type' => 'user_groups', 'created_at' => now(), 'updated_at' => now()],
            // permissions
            ['title' => 'Visualizar permissões', 'key' => 'admin.permissions.index', 'type' => 'permissions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar permissões', 'key' => 'admin.permissions.load', 'type' => 'permissions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar permissão', 'key' => 'admin.permissions.create', 'type' => 'permissions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar permissão', 'key' => 'admin.permissions.store', 'type' => 'permissions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar permissão', 'key' => 'admin.permissions.edit', 'type' => 'permissions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar permissão', 'key' => 'admin.permissions.update', 'type' => 'permissions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir permissão', 'key' => 'admin.permissions.delete', 'type' => 'permissions', 'created_at' => now(), 'updated_at' => now()],
            // users
            ['title' => 'Visualizar usuários', 'key' => 'admin.users.index', 'type' => 'users', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar usuários', 'key' => 'admin.users.load', 'type' => 'users', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar usuário', 'key' => 'admin.users.create', 'type' => 'users', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar usuário', 'key' => 'admin.users.store', 'type' => 'users', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar usuário', 'key' => 'admin.users.edit', 'type' => 'users', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar usuário', 'key' => 'admin.users.update', 'type' => 'users', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir usuário', 'key' => 'admin.users.delete', 'type' => 'users', 'created_at' => now(), 'updated_at' => now()],
            // user services global
            ['title' => 'Visualizar serviços de clientes', 'key' => 'admin.users.services.index', 'type' => 'users', 'created_at' => now(), 'updated_at' => now()],
            // cusomers
            ['title' => 'Visualizar clientes', 'key' => 'admin.customers.index', 'type' => 'customers', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar clientes', 'key' => 'admin.customers.load', 'type' => 'customers', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar cliente', 'key' => 'admin.customers.create', 'type' => 'customers', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar cliente', 'key' => 'admin.customers.store', 'type' => 'customers', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Detalhes cliente', 'key' => 'admin.customers.show', 'type' => 'customers', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar cliente', 'key' => 'admin.customers.edit', 'type' => 'customers', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar cliente', 'key' => 'admin.customers.update', 'type' => 'customers', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir cliente', 'key' => 'admin.customers.delete', 'type' => 'customers', 'created_at' => now(), 'updated_at' => now()],
            // addresses
            ['title' => 'Visualizar endereços', 'key' => 'admin.addresses.index', 'type' => 'addresses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar endereços', 'key' => 'admin.addresses.load', 'type' => 'addresses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar endereço', 'key' => 'admin.addresses.create', 'type' => 'addresses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar endereço', 'key' => 'admin.addresses.store', 'type' => 'addresses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar endereço', 'key' => 'admin.addresses.edit', 'type' => 'addresses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar endereço', 'key' => 'admin.addresses.update', 'type' => 'addresses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir endereço', 'key' => 'admin.addresses.delete', 'type' => 'addresses', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Definir endereço padrão', 'key' => 'admin.addresses.define.default', 'type' => 'addresses', 'created_at' => now(), 'updated_at' => now()],
            // pages
            ['title' => 'Visualizar páginas', 'key' => 'admin.pages.index', 'type' => 'pages', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar páginas', 'key' => 'admin.pages.load', 'type' => 'pages', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar página', 'key' => 'admin.pages.create', 'type' => 'pages', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar página', 'key' => 'admin.pages.store', 'type' => 'pages', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar página', 'key' => 'admin.pages.edit', 'type' => 'pages', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar página', 'key' => 'admin.pages.update', 'type' => 'pages', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir página', 'key' => 'admin.pages.delete', 'type' => 'pages', 'created_at' => now(), 'updated_at' => now()],
            // services
            ['title' => 'Visualizar serviços', 'key' => 'admin.services.index', 'type' => 'services', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar serviços', 'key' => 'admin.services.load', 'type' => 'services', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar serviço', 'key' => 'admin.services.create', 'type' => 'services', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar serviço', 'key' => 'admin.services.store', 'type' => 'services', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar serviço', 'key' => 'admin.services.edit', 'type' => 'services', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar serviço', 'key' => 'admin.services.update', 'type' => 'services', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir serviço', 'key' => 'admin.services.delete', 'type' => 'services', 'created_at' => now(), 'updated_at' => now()],
            // portfolios
            ['title' => 'Visualizar portfólios', 'key' => 'admin.portfolios.index', 'type' => 'portfolios', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar portfólios', 'key' => 'admin.portfolios.load', 'type' => 'portfolios', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar portfólio', 'key' => 'admin.portfolios.create', 'type' => 'portfolios', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar portfólio', 'key' => 'admin.portfolios.store', 'type' => 'portfolios', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar portfólio', 'key' => 'admin.portfolios.edit', 'type' => 'portfolios', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar portfólio', 'key' => 'admin.portfolios.update', 'type' => 'portfolios', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir portfólio', 'key' => 'admin.portfolios.delete', 'type' => 'portfolios', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir imagem do portfólio', 'key' => 'admin.portfolios.delete.image', 'type' => 'portfolios', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Definir imagem do portfólio', 'key' => 'admin.portfolios.define.image', 'type' => 'portfolios', 'created_at' => now(), 'updated_at' => now()],
            // integrations
            ['title' => 'Visualizar integrações', 'key' => 'admin.integrations.index', 'type' => 'integrations', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar integrações', 'key' => 'admin.integrations.load', 'type' => 'integrations', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar integração', 'key' => 'admin.integrations.create', 'type' => 'integrations', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar integração', 'key' => 'admin.integrations.store', 'type' => 'integrations', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar integração', 'key' => 'admin.integrations.edit', 'type' => 'integrations', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar integração', 'key' => 'admin.integrations.update', 'type' => 'integrations', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir integração', 'key' => 'admin.integrations.delete', 'type' => 'integrations', 'created_at' => now(), 'updated_at' => now()],
            // testimonials
            ['title' => 'Visualizar depoimentos', 'key' => 'admin.testimonials.index', 'type' => 'testimonials', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar depoimentos', 'key' => 'admin.testimonials.load', 'type' => 'testimonials', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar depoimento', 'key' => 'admin.testimonials.create', 'type' => 'testimonials', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar depoimento', 'key' => 'admin.testimonials.store', 'type' => 'testimonials', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar depoimento', 'key' => 'admin.testimonials.edit', 'type' => 'testimonials', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar depoimento', 'key' => 'admin.testimonials.update', 'type' => 'testimonials', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir depoimento', 'key' => 'admin.testimonials.delete', 'type' => 'testimonials', 'created_at' => now(), 'updated_at' => now()],
            // sliders
            ['title' => 'Visualizar sliders', 'key' => 'admin.sliders.index', 'type' => 'sliders', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar sliders', 'key' => 'admin.sliders.load', 'type' => 'sliders', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar slider', 'key' => 'admin.sliders.create', 'type' => 'sliders', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar slider', 'key' => 'admin.sliders.store', 'type' => 'sliders', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar slider', 'key' => 'admin.sliders.edit', 'type' => 'sliders', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar slider', 'key' => 'admin.sliders.update', 'type' => 'sliders', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir slider', 'key' => 'admin.sliders.delete', 'type' => 'sliders', 'created_at' => now(), 'updated_at' => now()],
            // invoices
            ['title' => 'Visualizar invoices', 'key' => 'admin.invoices.index', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar invoices', 'key' => 'admin.invoices.load', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar invoice', 'key' => 'admin.invoices.create', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar invoice', 'key' => 'admin.invoices.store', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar invoice', 'key' => 'admin.invoices.edit', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Abrir invoice', 'key' => 'admin.invoices.show', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar invoice', 'key' => 'admin.invoices.update', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir invoice', 'key' => 'admin.invoices.delete', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Cancelar invoice', 'key' => 'admin.invoices.cancel', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Confirmar pagamento invoice', 'key' => 'admin.invoices.confirmPayment', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Enviar lembrete invoice', 'key' => 'admin.invoices.sendReminder', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Gerar novo pagamento invoice', 'key' => 'admin.invoices.generatePayment', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar parcelas', 'key' => 'admin.invoices.loadInstallments', 'type' => 'invoices', 'created_at' => now(), 'updated_at' => now()],
            // transactions
            ['title' => 'Visualizar transactions', 'key' => 'admin.transactions.index', 'type' => 'transactions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar transactions', 'key' => 'admin.transactions.load', 'type' => 'transactions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar transaction', 'key' => 'admin.transactions.create', 'type' => 'transactions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar transaction', 'key' => 'admin.transactions.store', 'type' => 'transactions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar transaction', 'key' => 'admin.transactions.edit', 'type' => 'transactions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar transaction', 'key' => 'admin.transactions.update', 'type' => 'transactions', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir transaction', 'key' => 'admin.transactions.delete', 'type' => 'transactions', 'created_at' => now(), 'updated_at' => now()],
            // bank_accounts
            ['title' => 'Visualizar bancos', 'key' => 'admin.bank_accounts.index', 'type' => 'bank_accounts', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Carregar bancos', 'key' => 'admin.bank_accounts.load', 'type' => 'bank_accounts', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Criar banco', 'key' => 'admin.bank_accounts.create', 'type' => 'bank_accounts', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Salvar banco', 'key' => 'admin.bank_accounts.store', 'type' => 'bank_accounts', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Editar banco', 'key' => 'admin.bank_accounts.edit', 'type' => 'bank_accounts', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atualizar banco', 'key' => 'admin.bank_accounts.update', 'type' => 'bank_accounts', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Excluir banco', 'key' => 'admin.bank_accounts.delete', 'type' => 'bank_accounts', 'created_at' => now(), 'updated_at' => now()],
            // import
            ['title' => 'Visualizar importação', 'key' => 'admin.import.index', 'type' => 'import', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Importar dados', 'key' => 'admin.import.data', 'type' => 'import', 'created_at' => now(), 'updated_at' => now()],

        ]);

        $countPermissions = Permission::count();

        for ($i = 1; $i <= $countPermissions; $i++) {
            DB::table('group_permissions')->insert([
                ['user_group_id' => 1, 'permission_id' => $i, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        for ($i = 1; $i <= $countPermissions; $i++) {
            DB::table('group_permissions')->insert([
                ['user_group_id' => 2, 'permission_id' => $i, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
