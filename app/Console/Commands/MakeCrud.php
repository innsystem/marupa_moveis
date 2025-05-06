<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrud extends Command
{
    // example:
    // php artisan make:crud Posts Admin Postagem --columns="title:string:unique,content:text:nullable,views:integer:default:0,is_published:boolean:default:false,status:select_status:default:1"

    protected $signature = 'make:crud {name} {folder=Admin} {title=NomeNoSingular} {--columns="name:string:unique,age:integer:nullable,is_active:boolean:default:true"}';
    protected $description = 'Cria uma estrutura CRUD básica';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name');
        $folder = $this->argument('folder');
        $title = $this->argument('title');
        $columnsInput = $this->option('columns');
        // Transformando a string de colunas em um array
        $columns = $this->parseColumns(explode(',', $columnsInput));

        $this->createModel($name, $columns);
        $this->createMigration($name, $columns);
        $this->createController($name, $title, $folder, $columns);
        $this->createService($name, $folder);
        $this->createApiController($name, $folder, $columns);

        $this->createViews($name, $folder, $title, $columns);
        $this->createRoutes($name, $folder);
        $this->appendMenu($name, $title, $folder);

        $this->info("--------------- || ************** || ---------------");
        $this->info("--------------- || ************** || ---------------");
        $this->info("SUCCESS -- CRUD {$name} -> {$folder}");
        $this->info("SUCCESS -- CRUD {$name} -> {$folder}");
        $this->info("SUCCESS -- CRUD {$name} -> {$folder}");
        $this->info("SUCCESS -- CRUD {$name} -> {$folder}");
        $this->info("SUCCESS -- CRUD {$name} -> {$folder}");
        $this->info("--------------- || ************** || ---------------");
        $this->info("--------------- || ************** || ---------------");
    }

    protected function parseColumns($columnsInput)
    {
        $columns = [];
        foreach ($columnsInput as $column) {
            $parts = explode(':', $column); // Divide cada coluna no formato name:type:options
            $columns[] = [
                'name' => $parts[0], // Nome da coluna
                'type' => $parts[1] ?? 'string', // Tipo da coluna (padrão 'string')
                'unique' => in_array('unique', $parts), // Verifica se é único
                'nullable' => in_array('nullable', $parts), // Verifica se é nulo
                'default' => $this->getDefault($parts), // Verifica o valor default
            ];
        }
        return $columns;
    }

    protected function getDefault($parts)
    {
        foreach ($parts as $part) {
            if (str_starts_with($part, 'default:')) {
                return substr($part, 8); // Retorna o valor após 'default:'
            }
        }
        return null; // Se não houver default, retorna null
    }

    protected function createModel($name, $columns)
    {
        $nameSingular = Str::singular($name);
        $fillable = array_column($columns, 'name');
        $modelTemplate = str_replace(
            ['{{modelName}}', '{{fillable}}'],
            [$nameSingular, "'" . implode("', '", $fillable) . "'"],
            $this->getStub('Model')
        );

        File::put(app_path("Models/{$nameSingular}.php"), $modelTemplate);
    }

    protected function createMigration($name, $columns)
    {
        $tableName = Str::lower(Str::plural(Str::snake($name)));
        $migrationTemplate = str_replace(
            ['{{tableName}}', '{{columns}}'],
            [$tableName, $this->generateMigrationColumns($columns)],
            $this->getStub('Migration')
        );

        $fileName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
        File::put(database_path("migrations/{$fileName}"), $migrationTemplate);
    }

    protected function generateMigrationColumns($columns)
    {
        $migrationColumns = '';
        foreach ($columns as $column) {
            if ($column['type'] === 'select_status') {
                $column['type'] = 'integer';
            }

            $line = "\$table->{$column['type']}('{$column['name']}')";
            if ($column['unique']) $line .= "->unique()";
            if ($column['nullable']) $line .= "->nullable()";
            if ($column['default']) $line .= "->default()";
            $migrationColumns .= "            {$line};\n";
        }
        return $migrationColumns;
    }

    protected function createController($name, $title, $folder, $columns)
    {
        // Arrays para armazenar as regras e as mensagens
        $rules = [];
        $messages = [];
        $assignments = [];

        // Iterando pelas colunas para construir as regras e mensagens
        foreach ($columns as $column) {
            $columnName = $column['name'];
            $columnType = $column['type'];

            // Construindo as regras de validação
            $rule = [];

            // A coluna é obrigatória se não for nula
            if (!$column['nullable']) {
                $rule[] = 'required';
            }

            // A coluna pode ser única
            if ($column['unique']) {
                $rule[] = "unique:{$this->getTableName($name)},{$columnName}";
            }

            // A coluna é nullable
            if ($column['nullable']) {
                $rule[] = 'nullable';
            }

            // Se não tiver regras, o tipo de dado será definido como padrão
            if (empty($rule)) {
                $rule[] = $this->getColumnTypeValidation($columnType);
            }

            // Salvando a regra no array de regras
            $rules[$columnName] = implode('|', $rule);

            // Construindo as mensagens de erro
            $messages["{$columnName}.required"] = "{$columnName} é obrigatório";

            if ($column['unique']) {
                $messages["{$columnName}.unique"] = "{$columnName} já existe";
            }

            if ($column['nullable']) {
                $messages["{$columnName}.nullable"] = "{$columnName} pode ser nulo";
            }

            // Gerando as atribuições dinâmicas para o modelo
            $assignments[] = "\$model->{$columnName} = \$result['{$columnName}'];";
        }

        // Convertendo os arrays em strings formatadas
        $rulesString = var_export($rules, true);
        $messagesString = var_export($messages, true);
        $assignmentsString = implode("\n        ", $assignments);

        // Agora, vamos substituir as variáveis no template do controller
        $controllerTemplate = str_replace(
            [
                '{{titlePage}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelName}}',
                '{{modelNameSingular}}',
                '{{modelNameSingularLowerCase}}',
                '{{folder}}',
                '{{folderUppercase}}',
                '{{rules}}',
                '{{messages}}',
                '{{assignments}}'
            ],
            [
                $title,
                Str::lower(Str::plural(Str::snake($name))),
                $name,
                Str::singular($name),
                Str::lower(Str::singular($name)),
                strtolower($folder),
                ucfirst($folder),
                $rulesString,
                $messagesString,
                $assignmentsString
            ],
            $this->getStub('Controller') // Passando o stub
        );

        // Criando o arquivo do controller
        $folder = ucfirst($folder);
        File::put(app_path("/Http/Controllers/{$folder}/{$name}Controller.php"), $controllerTemplate);
    }

    protected function createService($name)
    {
        $nameSingular = Str::singular($name);
        $serviceMethods = '';

        // Criando os métodos dinâmicos para o serviço
        foreach (['getAll', 'getById', 'create', 'update', 'delete'] as $action) {
            $methodTemplate = match ($action) {
                'getAll' => "public function getAll{$name}(\$filters = []) \n	{\n\t\t\$query = {$nameSingular}::query();\n\n\t\tif (!empty(\$filters['name'])) {\n\t\t\t\$query->where('title', 'LIKE', '%' . \$filters['name'] . '%');\n\t\t}\n\n\t\tif (!empty(\$filters['status'])) {\n\t\t\t\$query->where('status', \$filters['status']);\n\t\t}\n\n\t\tif (!empty(\$filters['start_date']) && !empty(\$filters['end_date'])) {\n\t\t\t\$query->whereBetween('created_at', [\$filters['start_date'], \$filters['end_date']]);\n\t\t}\n\n\t\treturn \$query->get(); \n\t}",
                'getById' => "public function get{$nameSingular}ById(\$id) \n	{\n\t\treturn {$nameSingular}::findOrFail(\$id);\n\t}",
                'create' => "public function create{$nameSingular}(\$data) \n	{\n\t\treturn {$nameSingular}::create(\$data);\n\t}",
                'update' => "public function update{$nameSingular}(\$id, \$data) \n	{\n\t\t\$model = {$nameSingular}::findOrFail(\$id);\n\t\t\$model->update(\$data);\n\t\treturn \$model;\n\t}",
                'delete' => "public function delete{$nameSingular}(\$id) \n	{\n\t\t\$model = {$nameSingular}::findOrFail(\$id);\n\t\treturn \$model->delete();\n\t}",
            };
            $serviceMethods .= "\n\t{$methodTemplate}\n";
        }

        // Criar o arquivo de serviço
        $serviceTemplate = str_replace(
            ['{{modelName}}', '{{nameSingular}}', '{{serviceMethods}}'],
            [$name, $nameSingular, $serviceMethods],
            $this->getStub('Service')
        );

        File::put(app_path("Services/{$nameSingular}Service.php"), $serviceTemplate);
    }

    protected function createApiController($name, $folder, $columns)
    {
        $nameSingular = Str::singular($name);

        // Criando as validações para o controlador API
        $rules = $this->generateValidationRules($name, $columns);

        // Criar o arquivo do controlador API
        $apiControllerTemplate = str_replace(
            ['{{modelName}}', '{{nameSingular}}', '{{modelNameSingularLowerCase}}', '{{rules}}', '{{folder}}'],
            [$name, $nameSingular, Str::lower(Str::singular(Str::snake($name))), $rules, ucfirst($folder)],
            $this->getStub('ApiController')
        );

        $folder = ucfirst($folder);
        File::put(app_path("Http/Controllers/Api/{$nameSingular}Controller.php"), $apiControllerTemplate);

        // Adicionar rotas à API
        $this->addApiRoutes($name);
    }

    protected function addApiRoutes($name)
    {
        $nameSingular = Str::singular($name);
        $route = "\n// {$nameSingular}\nuse App\Http\Controllers\Api\\{$nameSingular}Controller;\n\nRoute::apiResource('" . Str::lower($name) . "', {$nameSingular}Controller::class);\n";
        File::append(base_path('routes/api.php'), $route);
    }

    protected function generateValidationRules($modelName, $columns)
    {
        $rules = [];
        foreach ($columns as $column) {
            $rule = [];
            if (!$column['nullable']) {
                $rule[] = 'required';
            }
            if ($column['unique']) {
                // Corrigir a regra para especificar a tabela e a coluna
                $tableName = Str::snake(Str::plural($modelName));
                $rule[] = "unique:{$tableName},{$column['name']}";
            }
            $rule[] = $this->getColumnTypeValidation($column['type']);
            $rules[$column['name']] = implode('|', array_filter($rule));
        }
        return var_export($rules, true);
    }

    protected function getTableName($name)
    {
        return Str::lower(Str::plural(Str::snake($name)));
    }

    protected function getColumnTypeValidation($type)
    {
        $validationTypes = [
            'string' => 'string',
            'text' => 'string',
            'integer' => 'integer',
            'boolean' => 'boolean',
        ];

        return $validationTypes[$type] ?? 'string'; // Retorna 'string' por padrão
    }

    protected function createViews($name, $folder, $title, $columns)
    {
        $nameToLower = Str::lower($name);
        $folder = strtolower($folder);
        $path = resource_path("views/{$folder}/pages/{$nameToLower}");
        File::makeDirectory($path, 0755, true, true);

        // Gerando os inputs para o formulário
        $formInputs = '';
        foreach ($columns as $column) {
            $columnName = $column['name'];
            $columnType = $column['type'];
            $nullable = $column['nullable'];
            $unique = $column['unique'];
            $default = $column['default'];

            // Criando os campos com base no tipo de dado
            $inputHtml = '';

            // Se a coluna for de texto, usamos input de texto, se for email, usamos o tipo 'email', etc.
            switch ($columnType) {
                case 'string':
                    $inputHtml = '<input type="text" class="form-control" id="' . $columnName . '" name="' . $columnName . '" placeholder="Digite o ' . $columnName . '" value="{{ isset($result->' . $columnName . ') ? $result->' . $columnName . ' : \'\' }}">';
                    break;
                case 'text':
                    $inputHtml = '<textarea class="form-control" id="' . $columnName . '" name="' . $columnName . '" placeholder="Digite o ' . $columnName . '">{{ isset($result->' . $columnName . ') ? $result->' . $columnName . ' : \'\' }}</textarea>';
                    break;
                case 'integer':
                    $inputHtml = '<input type="number" class="form-control" id="' . $columnName . '" name="' . $columnName . '" placeholder="Digite o ' . $columnName . '" value="{{ isset($result->' . $columnName . ') ? $result->' . $columnName . ' : \'0\' }}">';
                    break;
                case 'boolean':
                    $inputHtml = '<label for="' . $columnName . '" class="form-label cursor-pointer"><input type="checkbox" class="form-check-input cursor-pointer" id="' . $columnName . '" name="' . $columnName . '" value="1" {{ isset($result->' . $columnName . ') && $result->' . $columnName . ' ? \'checked\' : \'\' }}> Selecionar</label>';
                    break;
                case 'decimal':
                        $inputHtml = '<input type="text" class="form-control mask-money" id="' . $columnName . '" name="' . $columnName . '" placeholder="Digite o ' . $columnName . '" value="{{ isset($result->' . $columnName . ') ? $result->' . $columnName . ' : \'\' }}">';
                        break;
                case 'select_status':
                    $inputHtml= '<select name="status" id="status" class="form-select">
                                @foreach($statuses as $status)
                                <option value="{{$status->id}}" @if (isset($result->status) && $result->status == $status->id) selected @endif>{{$status->name}}</option>
                                @endforeach
                            </select>';
                    break;
                default:
                    $inputHtml = '<input type="text" class="form-control" id="' . $columnName . '" name="' . $columnName . '" placeholder="Digite ' . $columnName . '" value="{{ isset($result->' . $columnName . ') ? $result->' . $columnName . ' : \'\' }}">';
                    break;
            }

            // Adicionando o campo ao formulário
            $formInputs .= '<div class="form-group mb-3">
                                <label for="' . $columnName . '" class="col-sm-12">' . ucfirst(str_replace('_', ' ', $columnName)) . ':</label>
                                <div class="col-sm-12">
                                    ' . $inputHtml . '
                                </div>
                            </div>'; // Adiciona o campo à variável $formInputs
        }

        // Gerar a view de index (exemplo)
        $indexTemplate = str_replace(
            ['{{titlePage}}', '{{modelName}}', '{{modelNamePluralLowerCase}}', '{{modelNameSingularLowerCase}}', '{{folder}}'],
            [$title, $name, $nameToLower, Str::singular(strtolower($name)), strtolower($folder)],
            $this->getStub('Index') // Aqui você pode usar seu stub para o Index
        );

        // Gerar a view de index (exemplo)
        $indexLoadTemplate = str_replace(
            ['{{titlePage}}', '{{modelName}}', '{{modelNamePluralLowerCase}}', '{{modelNameSingularLowerCase}}', '{{folder}}'],
            [$title, $name, $nameToLower, Str::singular(strtolower($name)), strtolower($folder)],
            $this->getStub('IndexLoad') // Aqui você pode usar seu stub para o Index Load
        );

        // Gerar a view do formulário
        $formTemplate = str_replace(
            ['{{titlePage}}', '{{modelName}}', '{{modelNamePluralLowerCase}}', '{{modelNameSingularLowerCase}}', '{{folder}}', '{{formInputs}}'],
            [$title, $name, $nameToLower, Str::singular(strtolower($name)), strtolower($folder), $formInputs],
            $this->getStub('Form') // Passando o template do Form
        );

        // Salvando as views geradas
        File::put("{$path}/index.blade.php", $indexTemplate);
        File::put("{$path}/index_load.blade.php", $indexLoadTemplate);
        File::put("{$path}/form.blade.php", $formTemplate);
    }

    protected function createRoutes($name, $folder)
    {
        $routeTemplate = str_replace(
            ['{{modelNamePluralLowerCase}}', '{{modelNamePlural}}', '{{modelName}}', '{{folder}}', '{{folderUppercase}}'],
            [Str::lower(Str::plural(Str::snake($name))), Str::plural($name), $name, strtolower($folder), ucfirst($folder)],
            $this->getStub('Routes')
        );

        File::append(base_path('routes/web.php'), $routeTemplate);

        Artisan::call('route:cache');
    }

    protected function appendMenu($name, $title, $folder)
    {
        $routeTemplate = str_replace(
            ['{{modelNamePluralLowerCase}}', '{{titlePage}}'],
            [Str::lower(Str::plural(Str::snake($name))), $title],
            $this->getStub('Menu')
        );

        File::append(base_path('resources/views/' . $folder . '/includes/sidebar.blade.php'), $routeTemplate);
    }

    protected function getStub($type)
    {
        return File::get(resource_path("stubs/{$type}.stub"));
    }
}
