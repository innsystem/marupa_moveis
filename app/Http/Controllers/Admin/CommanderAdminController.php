<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;

class CommanderAdminController extends Controller
{
    public function index()
    {
        return view('admin.pages.commander');
    }

    public function create(Request $request)
    {
        $result = $request->all();

        $rules = [
            'name' => 'required',
            'namespace' => 'required',
            'friendly_name' => 'required',
        ];

        $messages = [
            'name.required' => 'nome é obrigatório',
            'namespace.required' => 'namespace é obrigatório',
            'friendly_name.required' => 'friendly_name é obrigatório',
        ];

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $name = $request->input('name');
        $namespace = $request->input('namespace');
        $friendlyName = $request->input('friendly_name');
        $columns = collect($request->input('column_name'))
            ->map(function ($name, $index) use ($request) {
                $type = $request->input('column_type')[$index];
                $options = $request->input('column_options')[$index] ?? '';
                return "{$name}:{$type}:{$options}";
            })
            ->implode(',');

        // Constrói o comando
        $command = "make:crud {$name} {$namespace} {$friendlyName} --columns=\"{$columns}\"";

        // Executa o comando Artisan
        try {
            Artisan::call($command);
            return response()->json('Recurso criado com sucesso', 200);
        } catch (\Exception $e) {
            \Log::error('CommanderAdminController :: create' . $e->getMessage());
            return response()->json('Erro ao criar o recurso - ' . $e->getMessage(), 500);
        }
    }

    public function migrate()
    {
        try {
            Artisan::call('migrate');
            Artisan::call('optimize:clear');
            return response()->json('Migração de Tabelas realizado com sucesso', 200);
        } catch (\Exception $e) {
            \Log::error('CommanderAdminController :: migrate' . $e->getMessage());
            return response()->json('Erro ao rodar a migrate - ' . $e->getMessage(), 500);
        }
    }
}
