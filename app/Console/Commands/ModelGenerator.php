<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ModelGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:generate {table_name} {class_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate models with docs';

    private $path;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->path = public_path() . '/../app/Models';

        if (!file_exists($this->path))
            mkdir($this->path, 0777, true);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table_name = $this->argument('table_name');
        $class_name = $this->argument('class_name');// ? str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($table_name)))) : $this->argument('class_name');
        $database = env('DB_DATABASE');

        if (is_null($class_name))
            $class_name = $this->className($table_name);

        $columns = DB::select('SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY, COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table', [':db' => $database, ':table' => $table_name]);

        if (empty($columns))
            dd('no columns found');

        $class = "<?php\n\nnamespace App\\Models;\n\n";//\n\n/***\n * Class $class_name\n * @package App\\Models\n * \n";
        $class .= "use Carbon\Carbon;\nuse Illuminate\Database\Eloquent\Model;\n\n";
        $class .= "/***\n * Class $class_name\n * @package App\\Models\n * \n";
        $dates = [];
        $booleans = [];

        foreach ($columns as $column) {
            $long = (int)filter_var($column->COLUMN_TYPE, FILTER_SANITIZE_NUMBER_INT);
            $class .= " * @property ";
            $type = $this->getType($column->DATA_TYPE, $long);
            if ($type === 'Carbon' && !in_array($column->COLUMN_NAME, ['created_at', 'updated_at']))
                $dates[] = $column->COLUMN_NAME;
            if ($type === 'boolean')
                $booleans[] = $column->COLUMN_NAME;
            $class .= $type . ' ';
            $class .= '$' . $column->COLUMN_NAME;
            $class .= "\n";
        }

        $relations_from_me = DB::select('select
            table_name, column_name, referenced_table_name, referenced_column_name
            from
            information_schema.key_column_usage
            where
            referenced_table_name is not null
            and table_schema = :db
            and table_name = :table', [':db' => $database, ':table' => $table_name]);

        if (!empty($relations_from_me)) {
            $class .= " * \n";
            foreach ($relations_from_me as $rel) {
                $class_relation = $this->className($rel->referenced_table_name);
                $column_relation = str_replace('_id', '', $rel->column_name);
                $class .= " * @property $class_relation \$$column_relation\n";
            }
        }

        $relations_to_me = DB::select('SELECT
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_SCHEMA = :db
            AND REFERENCED_TABLE_NAME = :table', [':db' => $database, ':table' => $table_name]);

        if (!empty($relations_to_me)) {
            $class .= " * \n";
            foreach ($relations_to_me as $rel) {
                $class_relation = $this->className($rel->TABLE_NAME);
                $function = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $rel->TABLE_NAME))));
                $class .= " * @property {$class_relation}[] \${$function}\n";
            }
        }

        $class .= " */\nclass $class_name extends Model\n{\n\n";

        if (!empty($dates)) {
            $class .= "\tprotected \$dates = [\n";
            foreach ($dates as $date) {
                $class .= "\t\t'$date',\n";
            }
            $class .= "\t];\n";
        }

        if (!empty($booleans)) {
            $class .= "\tprotected \$casts = [\n";
            foreach ($booleans as $boolean) {
                $class .= "\t\t'$boolean' => 'boolean',\n";
            }
            $class .= "\t];\n";
        }

        if (!empty($relations_from_me)) {
            foreach ($relations_from_me as $rel) {
                $class_relation = $this->className($rel->referenced_table_name);
                $function = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', str_replace('_id', '', $rel->column_name)))));
                $class .= "\n\tpublic function $function()\n\t{\n\t\treturn \$this->belongsTo($class_relation::class);\n\t}\n";
            }
        }

        if (!empty($relations_to_me)) {
            foreach ($relations_to_me as $rel) {
                $class_relation = $this->className($rel->TABLE_NAME);
                $function = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $rel->TABLE_NAME))));
                $class .= "\n\tpublic function $function()\n\t{\n\t\treturn \$this->hasMany($class_relation::class);\n\t}\n";
            }
        }

        $class .= "}\n";

        file_put_contents($this->path . "/$class_name.php", $class);

        dd('OK');
    }

    private function getType($type, $long = null)
    {
        switch (strtolower($type)) {
            case 'bigint':
            case 'mediumint':
            case 'int':
                return 'integer';
                break;
            case 'tinyint':
                if (is_numeric($long) && $long === 1)
                    return 'boolean';
                return 'integer';
                break;
            case 'date':
            case 'timestamp':
            case 'datetime':
                return 'Carbon';
                break;
            case 'varchar':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'string';
                break;
            default:
                return 'unknown';
        }
    }

    private function singularize($params)
    {
        if (is_string($params)) {
            $word = $params;
        } else if (!$word = $params['word']) {
            return false;
        }
        $singular = array(
            '/(quiz)zes$/i' => '\\1',
            '/(matr)ices$/i' => '\\1ix',
            '/(vert|ind)ices$/i' => '\\1ex',
            '/^(ox)en/i' => '\\1',
            '/(alias|status)es$/i' => '\\1',
            '/([octop|vir])i$/i' => '\\1us',
            '/(cris|ax|test)es$/i' => '\\1is',
            '/(shoe)s$/i' => '\\1',
            '/(o)es$/i' => '\\1',
            '/(bus)es$/i' => '\\1',
            '/([m|l])ice$/i' => '\\1ouse',
            '/(x|ch|ss|sh)es$/i' => '\\1',
            '/(m)ovies$/i' => '\\1ovie',
            '/(s)eries$/i' => '\\1eries',
            '/([^aeiouy]|qu)ies$/i' => '\\1y',
            '/([lr])ves$/i' => '\\1f',
            '/(tive)s$/i' => '\\1',
            '/(hive)s$/i' => '\\1',
            '/([^f])ves$/i' => '\\1fe',
            '/(^analy)ses$/i' => '\\1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\\1\\2sis',
            '/([ti])a$/i' => '\\1um',
            '/(n)ews$/i' => '\\1ews',
            '/s$/i' => ''
        );
        $irregular = array(
            'person' => 'people',
            'man' => 'men',
            'child' => 'children',
            'sex' => 'sexes',
            'move' => 'moves'
        );
        $ignore = array(
            'equipment',
            'information',
            'rice',
            'money',
            'species',
            'series',
            'fish',
            'sheep',
            'press',
            'sms',
        );
        $lower_word = strtolower($word);
        foreach ($ignore as $ignore_word) {
            if (substr($lower_word, (-1 * strlen($ignore_word))) == $ignore_word) {
                return $word;
            }
        }
        foreach ($irregular as $singular_word => $plural_word) {
            if (preg_match('/(' . $plural_word . ')$/i', $word, $arr)) {
                return preg_replace('/(' . $plural_word . ')$/i', substr($arr[0], 0, 1) . substr($singular_word, 1), $word);
            }
        }
        foreach ($singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }
        return $word;
    }

    private function className($table_name)
    {
        if (strpos($table_name, '_') > 0) {
            $words = explode('_', strtolower($table_name));
            $end_word = $this->singularize(array_pop($words));
            $class_name = str_replace(' ', '', ucwords(implode(' ', array_merge($words, [$end_word]))));
        } else
            $class_name = ucwords($this->singularize(strtolower($table_name)));

        return $class_name;
    }
}
