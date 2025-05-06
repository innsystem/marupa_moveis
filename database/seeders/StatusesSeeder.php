<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class StatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            // Cenário: Padrão
            ['type' => 'default', 'name' => 'Habilitado', 'description' => 'Habilitado .', 'color' => 'bg-success', 'icon' => 'fa fa-check'],
            ['type' => 'default', 'name' => 'Desabilitado', 'description' => 'Desabilitado.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-ban'],

            // Cenário: Clientes e Geral
            ['type' => 'general', 'name' => 'Ativo', 'description' => 'Cliente ativo.', 'color' => 'bg-success', 'icon' => 'fa fa-check'],
            ['type' => 'general', 'name' => 'Inativo', 'description' => 'Cliente inativo.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-ban'],
            ['type' => 'general', 'name' => 'Inadimplente', 'description' => 'Cliente com pendências financeiras.', 'color' => 'bg-danger', 'icon' => 'fa fa-times'],
            ['type' => 'general', 'name' => 'Bloqueado', 'description' => 'Cliente bloqueado para operações.', 'color' => 'bg-danger', 'icon' => 'fa fa-lock'],
            ['type' => 'general', 'name' => 'Suspenso', 'description' => 'Cliente temporariamente suspenso.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-pause'],
            ['type' => 'general', 'name' => 'Aguardando aprovação', 'description' => 'Cadastro do cliente aguardando aprovação.', 'color' => 'bg-info', 'icon' => 'fa fa-hourglass-half'],
            ['type' => 'general', 'name' => 'Excluído', 'description' => 'Cadastro do cliente excluído.', 'color' => 'bg-danger', 'icon' => 'fa fa-trash'],
            ['type' => 'general', 'name' => 'Cadastro incompleto', 'description' => 'Cadastro do cliente está incompleto.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-user-edit'],
            ['type' => 'general', 'name' => 'Aberto', 'description' => 'Ticket aberto.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-folder-open'],
            ['type' => 'general', 'name' => 'Em andamento', 'description' => 'Ticket em andamento.', 'color' => 'bg-info', 'icon' => 'fa fa-spinner'],
            ['type' => 'general', 'name' => 'Aguardando resposta', 'description' => 'Aguardando resposta do cliente.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-clock'],
            ['type' => 'general', 'name' => 'Encerrado', 'description' => 'Ticket encerrado.', 'color' => 'bg-success', 'icon' => 'fa fa-check-circle'],
            ['type' => 'general', 'name' => 'Cancelado', 'description' => 'Ticket cancelado.', 'color' => 'bg-danger', 'icon' => 'fa fa-times-circle'],
            ['type' => 'general', 'name' => 'Reaberto', 'description' => 'Ticket reaberto.', 'color' => 'bg-info', 'icon' => 'fa fa-undo'],
            ['type' => 'general', 'name' => 'Online', 'description' => 'Usuário está online.', 'color' => 'bg-success', 'icon' => 'fa fa-wifi'],
            ['type' => 'general', 'name' => 'Offline', 'description' => 'Usuário está offline.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-plug'],
            ['type' => 'general', 'name' => 'Bloqueado por tentativa de acesso', 'description' => 'Usuário bloqueado por tentativas inválidas de acesso.', 'color' => 'bg-danger', 'icon' => 'fa fa-user-lock'],
            ['type' => 'general', 'name' => 'Desativado pelo administrador', 'description' => 'Usuário desativado por um administrador.', 'color' => 'bg-danger', 'icon' => 'fa fa-user-times'],
            ['type' => 'general', 'name' => 'Senha expirada', 'description' => 'Usuário com senha expirada.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-key'],
            ['type' => 'general', 'name' => 'Aguardando ativação de e-mail', 'description' => 'Usuário aguardando ativação por e-mail.', 'color' => 'bg-info', 'icon' => 'fa fa-envelope'],

            // Cenário: Pagamentos
            ['type' => 'payments', 'name' => 'Pendente', 'description' => 'Pagamento pendente.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-clock'],
            ['type' => 'payments', 'name' => 'Pago', 'description' => 'Pagamento realizado.', 'color' => 'bg-success', 'icon' => 'fa fa-check'],
            ['type' => 'payments', 'name' => 'Não pago', 'description' => 'Pagamento não realizado.', 'color' => 'bg-danger', 'icon' => 'fa fa-times'],
            ['type' => 'payments', 'name' => 'Cancelado', 'description' => 'Pagamento cancelado.', 'color' => 'bg-danger', 'icon' => 'fa fa-ban'],
            ['type' => 'payments', 'name' => 'Estornado', 'description' => 'Pagamento estornado.', 'color' => 'bg-info', 'icon' => 'fa fa-undo'],
            ['type' => 'payments', 'name' => 'Aguardando confirmação', 'description' => 'Aguardando confirmação do pagamento.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-hourglass'],
            ['type' => 'payments', 'name' => 'Parcialmente pago', 'description' => 'Pagamento realizado parcialmente.', 'color' => 'bg-info', 'icon' => 'fa fa-percent'],
            ['type' => 'payments', 'name' => 'Falha no pagamento', 'description' => 'Falha no processamento do pagamento.', 'color' => 'bg-danger', 'icon' => 'fa fa-exclamation-triangle'],
            ['type' => 'payments', 'name' => 'Expirado', 'description' => 'Pagamento expirado.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-clock'],
            ['type' => 'payments', 'name' => 'Aguardando reembolso', 'description' => 'Reembolso em processamento.', 'color' => 'bg-info', 'icon' => 'fa fa-credit-card'],

            // Cenário: Estoque e Pedidos
            ['type' => 'sales', 'name' => 'Em estoque', 'description' => 'Produto disponível em estoque.', 'color' => 'bg-success', 'icon' => 'fa fa-box'],
            ['type' => 'sales', 'name' => 'Sem estoque', 'description' => 'Produto indisponível em estoque.', 'color' => 'bg-danger', 'icon' => 'fa fa-box-open'],
            ['type' => 'sales', 'name' => 'Baixo estoque', 'description' => 'Produto com baixo estoque.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-layer-group'],
            ['type' => 'sales', 'name' => 'Reservado', 'description' => 'Produto reservado para pedido.', 'color' => 'bg-info', 'icon' => 'fa fa-calendar-check'],
            ['type' => 'sales', 'name' => 'Produção em andamento', 'description' => 'Produção do pedido em andamento.', 'color' => 'bg-primary', 'icon' => 'fa fa-cogs'],
            ['type' => 'sales', 'name' => 'Não enviado', 'description' => 'Pedido ainda não enviado.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-truck-loading'],
            ['type' => 'sales', 'name' => 'Enviado', 'description' => 'Pedido enviado.', 'color' => 'bg-info', 'icon' => 'fa fa-truck'],
            ['type' => 'sales', 'name' => 'Em trânsito', 'description' => 'Pedido em trânsito.', 'color' => 'bg-info', 'icon' => 'fa fa-shipping-fast'],
            ['type' => 'sales', 'name' => 'Entregue', 'description' => 'Pedido entregue ao cliente.', 'color' => 'bg-success', 'icon' => 'fa fa-check-circle'],
            ['type' => 'sales', 'name' => 'Devolvido', 'description' => 'Pedido devolvido.', 'color' => 'bg-warning text-black', 'icon' => 'fa fa-undo'],
            ['type' => 'sales', 'name' => 'Extraviado', 'description' => 'Pedido extraviado.', 'color' => 'bg-danger', 'icon' => 'fa fa-exclamation'],
            ['type' => 'sales', 'name' => 'Cancelado', 'description' => 'Pedido cancelado.', 'color' => 'bg-danger', 'icon' => 'fa fa-ban'],
        ];

        DB::table('statuses')->insert($statuses);
    }
}
