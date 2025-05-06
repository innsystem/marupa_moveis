<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CommanderController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'namespace' => 'required',
            'friendly_name' => 'required',
            'column_name' => 'required|array',
            'column_type' => 'required|array',
            'column_options' => 'nullable|array',
        ]);

        $name = $data['name'];
        $namespace = $data['namespace'];
        $friendlyName = $data['friendly_name'];
        $columns = collect($data['column_name'])
            ->map(function ($name, $index) use ($data) {
                $type = $data['column_type'][$index];
                $options = $data['column_options'][$index] ?? '';
                return "{$name}:{$type}:{$options}";
            })
            ->implode(',');

        // Constrói o comando
        $command = "make:crud {$name} {$namespace} {$friendlyName} --columns=\"{$columns}\"";

        // Executa o comando Artisan
        try {
            Artisan::call($command);
            return response()->json(['message' => 'Recurso criado com sucesso']);
        } catch (\Exception $e) {
            Log::error('CommanderController :: create' . $e->getMessage());
            return response()->json(['error' => 'Erro ao criar o recurso - ' . $e->getMessage()], 500);
        }
    }

    public function migrate()
    {
        try {
            Artisan::call('migrate');
            Artisan::call('optimize:clear');
            return response()->json(['message' => 'Migração de Tabelas realizado com sucesso']);
        } catch (\Exception $e) {
            Log::error('CommanderController :: migrate' . $e->getMessage());
            return response()->json(['error' => 'Erro ao rodar a migrate - ' . $e->getMessage()], 500);
        }
    }
} 