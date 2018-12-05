<?php

namespace Kjjdion\LaravelAdminPanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class CrudGenerate extends Command
{
    protected $signature = 'crud:generate {model}';
    protected $description = 'Generate CRUD using config file.';
    protected $files;
    protected $config;
    protected $replaces = [];

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        // ensure config file exists
        $config_file = 'config/crud/' . $this->argument('model') . '.php';
        if (!$this->files->exists($config_file)) {
            $this->error('Config file not found: <info>' . $config_file . '</info>');
            return;
        }

        // set class values
        $this->config = include $config_file;
        $this->setSimpleReplaces();
        $this->setAttributeReplaces();

        // generate crud
        $this->line('Generating <info>' . $this->argument('model') . '</info> CRUD...');
        $this->makeDirectories();
        $this->createControllerFile();
        $this->createModelFile();
        $this->createMigrationFile();
        $this->createViewFiles();
        $this->insertMenuItem();
        $this->insertRoutes();
        $this->line('CRUD generation for <info>' . $this->argument('model') . '</info> complete!');

        // ask to migrate
        if ($this->confirm('Migrate now?')) {
            Artisan::call('migrate', ['--path' => $this->config['paths']['migrations']]);
            $this->info('Migration complete!');
        }
    }

    public function setSimpleReplaces()
    {
        // set simple replacement searches for stubs
        $this->replaces = [
            '{controller_namespace}' => $controller_namespace = ucfirst(str_replace('/', '\\', $this->config['paths']['controller'])),
            '{controller_route}' => ltrim(str_replace('App\\Http\\Controllers', '', $controller_namespace) . '\\', '\\'),
            '{model_namespace}' => ucfirst(str_replace('/', '\\', $this->config['paths']['model'])),
            '{model_class}' => $model_class = $this->argument('model'),
            '{model_string}' => $model_string = trim(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $model_class)),
            '{model_strings}' => $model_strings = str_plural($model_string),
            '{model_variable}' => strtolower(str_replace(' ', '_', $model_string)),
            '{model_variables}' => strtolower(str_replace(' ', '_', $model_strings)),
            '{model_primary_attribute}' => 'id',
            '{model_icon}' => isset($this->config['icon']) ? $this->config['icon'] : 'fa-link',
            '{view_prefix_url}' => $view_prefix_url = ltrim(str_replace('resources/views', '', $this->config['paths']['views']) . '/', '/'),
            '{view_prefix_name}' => str_replace('/', '.', $view_prefix_url),
        ];
    }

    public function setAttributeReplaces()
    {
        // set replacement searches using attribute values
        $attributes = isset($this->config['attributes']) ? $this->config['attributes'] : [];
        $model_casts = [];
        $relationships = [];
        $relationships_query = [];
        $user_timezones = [];
        $migrations = [];
        $validations = [];
        $datatable = [];
        $read_attributes = [];
        $form_enctype = '';
        $inputs_create = [];
        $inputs_update = [];

        foreach ($attributes as $attribute => $values) {
            // model primary attribute
            if (!empty($values['primary'])) {
                $this->replaces['{model_primary_attribute}'] = $attribute;
            }

            // model casts attribute
            if (!empty($values['casts'])) {
                $model_casts[] = "'$attribute' => '" . $values['casts'] . "'";
            }

            // relationships
            if (!empty($values['relationship'])) {
                $relationships[] = $this->indent() . 'public function ' . array_keys($values['relationship'])[0] . '()';
                $relationships[] = $this->indent() . '{';
                $relationships[] = $this->indent() . '    return $this->' . $this->putInChains(array_values($values['relationship'])[0]) . ';';
                $relationships[] = $this->indent() . '}' . PHP_EOL;
                $relationships_query[] = array_keys($values['relationship'])[0];
            }

            // user timezones
            if (!empty($values['user_timezone'])) {
                $user_timezones[] = $this->indent() . 'public function get' . studly_case($attribute) . 'Attribute($value)';
                $user_timezones[] = $this->indent() . '{';
                $user_timezones[] = $this->indent() . '    return $this->inUserTimezone($value);';
                $user_timezones[] = $this->indent() . '}' . PHP_EOL;
            }

            // migrations
            if (!empty($values['migrations'])) {
                foreach ($values['migrations'] as $migration) {
                    $migrations[] = $this->indent(3) . '$table->' . $this->putInChains($migration) . ';';
                }
            }

            // validations (create & update)
            if (!empty($values['validations'])) {
                foreach ($values['validations'] as $method => $rules) {
                    $validations[$method][] = $this->indent(3) . '"' . $attribute . '" => "' . $rules . '",';
                }
            }

            // datatable
            if (!empty($values['datatable'])) {
                $datatable[] = $this->indent(3) . $this->flattenArray($values['datatable']) . ',';
            }

            // read attributes
            $attribute_label = ucwords(str_replace('_', ' ', $attribute));
            $attribute_value = '$' . $this->replaces['{model_variable}'] . '->' . $attribute;
            $read_stub = $this->files->get($this->config['paths']['stubs'] . '/views/layouts/read.stub');
            $read_stub = str_replace('{attribute_label}', $attribute_label, $read_stub);
            $read_stub = str_replace('{attribute_value}', '{{ ' . (isset($values['casts']) && $values['casts'] ? "implode(', ', $attribute_value)" : $attribute_value) . ' }}', $read_stub);
            $read_attributes[] = $read_stub . PHP_EOL;

            // form inputs
            if (!empty($values['input'])) {
                $input_stub = $this->files->get($this->config['paths']['stubs'] . '/views/layouts/input.stub');
                $input_stub = str_replace('{attribute}', $attribute, $input_stub);
                $input_stub = str_replace('{attribute_label}', $attribute_label, $input_stub);
                $inputs_create[] = str_replace('{attribute_input}', $this->inputContent($values['input'], 'create', $attribute, $form_enctype), $input_stub) . PHP_EOL;
                $inputs_update[] = str_replace('{attribute_input}', $this->inputContent($values['input'], 'update', $attribute, $form_enctype), $input_stub) . PHP_EOL;
            }
        }

        $this->replaces['{model_casts}'] = $model_casts ? 'protected $casts = [' . implode(', ', $model_casts) . '];' : '';
        $this->replaces['{relationships}'] = $relationships ? trim(implode(PHP_EOL, $relationships)) : '';
        $this->replaces['{relationships_query}'] = $relationships_query ? "->with('" . implode("', '", $relationships_query) . "')" : '';
        $this->replaces['{user_timezones}'] = $user_timezones ? trim(implode(PHP_EOL, $user_timezones)) : '';
        $this->replaces['{migrations}'] = $validations ? trim(implode(PHP_EOL, $migrations)) : '';
        $this->replaces['{validations_create}'] = isset($validations['create']) ? trim(implode(PHP_EOL, $validations['create'])) : '';
        $this->replaces['{validations_update}'] = isset($validations['update']) ? trim(implode(PHP_EOL, $validations['update'])) : '';
        $this->replaces['{datatable}'] = $datatable ? trim(implode(PHP_EOL, $datatable)) : '';
        $this->replaces['{read_attributes}'] = $read_attributes ? trim(implode(PHP_EOL, $read_attributes)) : '';
        $this->replaces['{form_enctype}'] = $form_enctype;
        $this->replaces['{inputs_create}'] = $inputs_create ? trim(implode(PHP_EOL, $inputs_create)) : '';
        $this->replaces['{inputs_update}'] = $inputs_create ? trim(implode(PHP_EOL, $inputs_update)) : '';
    }

    public function inputContent($input, $method, $attribute, &$form_enctype)
    {
        $replaces = [];

        if (in_array($input['type'], ['checkbox', 'radio'])) {
            $stub = $this->files->get($this->config['paths']['stubs'] . '/views/inputs/checkbox_radio.stub');
            $replaces['{input_type}'] = $input['type'];
            $replaces['{input_name}'] = $attribute . ($input['type'] == 'checkbox' && !empty($input['options']) ? '[]' : '');
            $replaces['{input_id}'] = $attribute . '_{{ $loop->index }}';
            $replaces = $this->inputCheckOptions($attribute, $input, $method, $replaces);
        }
        else if ($input['type'] == 'file') {
            $form_enctype = ' enctype="multipart/form-data"';
            $stub = $this->files->get($this->config['paths']['stubs'] . '/views/inputs/file.stub');
            $replaces['{input_name}'] = $attribute;
            $replaces['{input_id}'] = $attribute;
            $replaces['{input_multiple}'] = !empty($input['multiple']) ? ' multiple' : '';
        }
        else if ($input['type'] == 'select') {
            $stub = $this->files->get($this->config['paths']['stubs'] . '/views/inputs/select.stub');
            $replaces['{input_name}'] = $attribute;
            $replaces['{input_id}'] = $attribute;
            $replaces = $this->inputSelectOptions($attribute, $input, $method, $replaces);
        }
        else if ($input['type'] == 'textarea') {
            $stub = $this->files->get($this->config['paths']['stubs'] . '/views/inputs/textarea.stub');
            $replaces['{input_name}'] = $attribute;
            $replaces['{input_id}'] = $attribute;
            $replaces['{input_value}'] = $method == 'update' ? '{{ $' . $this->replaces['{model_variable}'] . '->' . $attribute . ' }}' : '';
        }
        else {
            $stub = $this->files->get($this->config['paths']['stubs'] . '/views/inputs/text.stub');
            $replaces['{input_type}'] = $input['type'];
            $replaces['{input_name}'] = $attribute;
            $replaces['{input_id}'] = $attribute;
            $replaces['{input_value}'] = $method == 'update' ? ' value="{{ $' . $this->replaces['{model_variable}'] . '->' . $attribute . ' }}"' : '';
        }

        foreach ($replaces as $search => $replace) {
            $stub = str_replace($search, $replace, $stub);
        }

        return trim($stub);
    }

    public function inputCheckOptions($attribute, $input, $method, $replaces)
    {
        if (empty($input['options'])) {
            // single check
            $replaces['{input_options}'] = '[' . $this->quoteVar($input['value']) . ']';
            $replaces['{input_option}'] = '$option';
            $replaces['{input_option_value}'] = '{{ $option }}';
            $replaces['{input_option_label}'] = !empty($input['label']) ? $input['label'] : ucwords(str_replace('_', ' ', $attribute));
            $replaces['{input_option_checked}'] = $this->inputOptionChecked($method, $input, $attribute, '$option');
        }
        else if (is_array(array_values($input['options'])[0])) {
            // relationship checks
            $key = array_keys($input['options'])[0];
            $value = array_keys($input['options'][$key])[0];
            $label = array_values($input['options'][$key])[0];

            $replaces['{input_options}'] = $this->putInChains($key);
            $replaces['{input_option}'] = '$model';
            $replaces['{input_option_value}'] = '{{ $model->' . $value . ' }}';
            $replaces['{input_option_label}'] = '{{ $model->' . $label . ' }}';
            $replaces['{input_option_checked}'] = $this->inputOptionChecked($method, $input, $attribute, '$model->' . $value);
        }
        else if (array_keys($input['options']) !== range(0, count($input['options']) - 1)) {
            // checks are associative array (key is defined)
            $replaces['{input_options}'] = $this->flattenArray($input['options']);
            $replaces['{input_option}'] = '$value => $label';
            $replaces['{input_option_value}'] = '{{ $value }}';
            $replaces['{input_option_label}'] = '{{ $label }}';
            $replaces['{input_option_checked}'] = $this->inputOptionChecked($method, $input, $attribute, '$value');
        }
        else {
            // checks are sequential array (key = 0, 1, 2, 3)
            $replaces['{input_options}'] = "['" . implode("', '", $input['options']) . "']";
            $replaces['{input_option}'] = '$option';
            $replaces['{input_option_value}'] = '{{ $option }}';
            $replaces['{input_option_label}'] = '{{ $option }}';
            $replaces['{input_option_checked}'] = $this->inputOptionChecked($method, $input, $attribute, '$option');
        }

        return $replaces;
    }

    public function inputOptionChecked($method, $input, $attribute, $value)
    {
        if ($method == 'update') {
            if (empty($input['options']) || $input['type'] == 'radio') {
                return '{{ ' . $value . ' == $' . $this->replaces['{model_variable}'] . '->' . $attribute . " ? ' checked' : '' }}";
            }
            else {
                return '{{ !empty($' . $this->replaces['{model_variable}'] . '->' . $attribute . ') && in_array(' . $value . ', $' . $this->replaces['{model_variable}']
                    . '->' . $attribute . ") ? ' checked' : '' }}";
            }
        }
        else {
            return '';
        }
    }

    public function inputSelectOptions($attribute, $input, $method, $replaces)
    {
        if (is_array(array_values($input['options'])[0])) {
            // relationship options
            $key = array_keys($input['options'])[0];
            $value = array_keys($input['options'][$key])[0];
            $label = array_values($input['options'][$key])[0];

            $replaces['{input_options}'] = $this->putInChains($key);
            $replaces['{input_option}'] = '$model';
            $replaces['{input_option_value}'] = '{{ $model->' . $value . ' }}';
            $replaces['{input_option_label}'] = '{{ $model->' . $label . ' }}';
            $replaces['{input_option_selected}'] = $method == 'update' ? '{{ $model->' . $value . ' == $' . $this->replaces['{model_variable}'] . '->' . $attribute . " ? ' selected' : '' }}" : '';
        }
        else if (array_keys($input['options']) !== range(0, count($input['options']) - 1)) {
            // options are associative array (key is defined)
            $replaces['{input_options}'] = $this->flattenArray($input['options']);
            $replaces['{input_option}'] = '$value => $label';
            $replaces['{input_option_value}'] = '{{ $value }}';
            $replaces['{input_option_label}'] = '{{ $label }}';
            $replaces['{input_option_selected}'] = $method == 'update' ? '{{ $value == $' . $this->replaces['{model_variable}'] . '->' . $attribute . " ? ' selected' : '' }}" : '';
        }
        else {
            // options are sequential array (key = 0, 1, 2, 3)
            $replaces['{input_options}'] = "['" . implode("', '", $input['options']) . "']";
            $replaces['{input_option}'] = '$option';
            $replaces['{input_option_value}'] = '{{ $option }}';
            $replaces['{input_option_label}'] = '{{ $option }}';
            $replaces['{input_option_selected}'] = $method == 'update' ? '{{ $option == $' . $this->replaces['{model_variable}'] . '->' . $attribute . " ? ' selected' : '' }}" : '';
        }

        return $replaces;
    }

    public function replace($content)
    {
        // replace all occurrences with $this->replaces
        foreach ($this->replaces as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

    public function makeDirectories()
    {
        // create directories recursively if they don't already exist
        $directories = [
            $this->config['paths']['controller'],
            $this->config['paths']['model'],
            $this->config['paths']['migrations'],
            $this->config['paths']['views'] . '/' . $this->replaces['{model_variables}'] . '/datatable',
        ];

        foreach ($directories as $directory) {
            if (!$this->files->exists($directory)) {
                $this->files->makeDirectory($directory, 0755, true);
            }
        }
    }

    public function createControllerFile()
    {
        // create controller file
        $controller_file = $this->config['paths']['controller'] . '/' . $this->replaces['{model_class}'] . 'Controller.php';
        $controller_stub = $this->files->get($this->config['paths']['stubs'] . '/controller.stub');
        $this->files->put($controller_file, $this->replace($controller_stub));
        $this->line('Controller file created: <info>' . $controller_file . '</info>');
    }

    public function createModelFile()
    {
        // create model file
        $model_file = $this->config['paths']['model'] . '/' . $this->replaces['{model_class}'] . '.php';
        $model_stub = $this->files->get($this->config['paths']['stubs'] . '/model.stub');
        $this->files->put($model_file, $this->replace($model_stub));
        $this->line('Model file created: <info>' . $model_file . '</info>');
    }

    public function createMigrationFile()
    {
        // create migration file
        $migrations_file = $this->config['paths']['migrations'] . '/' . date('Y_m_d') . '_000000_create_' . $this->replaces['{model_variable}'] . '_table.php';
        $migrations_stub = $this->files->get($this->config['paths']['stubs'] . '/migrations.stub');
        $this->files->put($migrations_file, $this->replace($migrations_stub));
        $this->line('Migration file created: <info>' . $migrations_file . '</info>');
    }

    public function createViewFiles()
    {
        // create view files
        $view_path = $this->config['paths']['views'] . '/' . $this->replaces['{model_variables}'];
        foreach ($this->files->allFiles($this->config['paths']['stubs'] . '/views/models') as $file) {
            $new_file = $view_path . '/' . ltrim($file->getRelativePath() . '/' . str_replace('.stub', '.blade.php', $file->getFilename()), '/');
            $this->files->put($new_file, $this->replace($file->getContents()));
        }
        $this->line('View files created: <info>' . $view_path . '/*.*</info>');
    }

    public function insertMenuItem()
    {
        // insert menu item
        $menu_file = $this->files->get($this->config['paths']['menu']);
        $menu_stub = $this->files->get($this->config['paths']['stubs'] . '/views/layouts/menu.stub');
        $menu_content = PHP_EOL . $this->replace($menu_stub);

        // insert after first </li> (dashboard) if doesn't already exist
        if (strpos($menu_file, $menu_content) === false) {
            $search = '</li>';
            $index = strpos($menu_file, $search);
            $this->files->put($this->config['paths']['menu'], substr_replace($menu_file, $search . $menu_content, $index, strlen($search)));
        }

        $this->line('Menu item inserted: <info>' . $this->config['paths']['menu'] . '</info>');
    }

    public function insertRoutes()
    {
        // insert routes
        $routes_file = $this->files->get($this->config['paths']['routes']);
        $routes_stub = $this->files->get($this->config['paths']['stubs'] . '/routes.stub');
        $routes_content = PHP_EOL . PHP_EOL . $this->replace($routes_stub);

        // insert at end of file if doesn't already exist
        if (strpos($routes_file, $routes_content) === false) {
            $this->files->append($this->config['paths']['routes'], $routes_content);
        }

        $this->line('Routes inserted: <info>' . $this->config['paths']['routes'] . '</info>');
    }

    public function indent($multiplier = 1)
    {
        // add indents to line
        return str_repeat('    ', $multiplier);
    }

    public function putInChains($value)
    {
        // convert string to chains using methods and parameters
        $chains = [];

        foreach (explode('|', $value) as $chain) {
            $method_params = explode(':', $chain);
            $method = $method_params[0];
            $params_typed = [];

            // add quotes to parameter if not boolean or numeric
            if (isset($method_params[1])) {
                foreach (explode(',', $method_params[1]) as $param) {
                    $params_typed[] = (in_array($param, ['true', 'false']) || is_numeric($param)) ? $param : "'$param'";
                }
            }

            $chains[] = $method . '(' . implode(', ', $params_typed) . ')';
        }

        return implode('->', $chains);
    }

    public function flattenArray($array)
    {
        $flat = [];

        foreach ($array as $key => $value) {
            $flat[] = "'$key' => " . $this->quoteVar($value);
        }

        return '[' . implode(', ', $flat) . ']';
    }

    public function quoteVar($value)
    {
        return is_bool($value) || is_numeric($value) ? var_export($value, true) : "'$value'";
    }
}