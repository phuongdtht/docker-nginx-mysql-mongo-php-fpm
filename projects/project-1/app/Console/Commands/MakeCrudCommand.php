<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make {module} {component} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lệnh tự tạo Component CRUD';

    protected $pluralName, $pluralSnakeName, $pluralStudlyName, $pluralSlugName;

    protected $singularName, $singularStudlyName, $singularSnakeName, $singularSlugName;

    protected $modelName, $repositoryName, $validatorName, $controllerName;

    protected $folder = 'Modules\NewModule';

    protected $module = 'NewModule';

    protected $search = [
        '$PLURAL_CAMEL$',
        '$PLURAL_SNAKE$',
        '$PLURAL_STUDLY$',
        '$PLURAL_SLUG$',
        '$SINGULAR_CAMEL$',
        '$SINGULAR_SNAKE$',
        '$SINGULAR_STUDLY$',
        '$SINGULAR_SLUG$',
        '$MODEL$',
        '$REPOSITORY$',
        '$VALIDATOR$',
        '$CONTROLLER$',
        '$MODULE$',
        '$MODULE_LOWER$',
        '$MODULE_SLUG$',
        '$NAMESPACE$',
    ];

    protected $replace = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->processInputs();
        $this->createFolder($this->folder);

        $component = (string)$this->argument('component');
        switch (strtolower($component)) {
            case 'migration':
                $this->generateMigrationFile();
                break;
            case 'model':
                $this->generateModelFile();
                break;
            case 'repository':
                $this->generateRepositoryFile();
                break;
            case 'validator':
                $this->generateValidatorFile();
                break;
            case 'controller':
                $this->generateControllerFile();
                break;
                // case 'view':
                //     $this->generateViewFiles();
                //     break;
                // case 'datatable':
                //     $this->generateDatatableFiles();
                //     break;
            case 'observer':
                $this->generateObserverFile();
                break;
            case 'factory':
                $this->generateFactoryFile();
                break;
            case 'test':
                $this->generateTestFiles();
                break;
            case 'crud':
                $this->generateMigrationFile();
                $this->generateModelFile();
                $this->generateRepositoryFile();
                $this->generateValidatorFile();
                $this->generateControllerFile();
                $this->generateObserverFile();
                $this->generateFactoryFile();
                $this->generateTestFiles();
                $this->generateResourceFiles();
                // $this->generateViewFiles();
                // $this->generateDatatableFiles();
                break;
            default:
                $this->error('Không tìm thấy từ khóa, xin vui lòng thử lại với một trong các từ khóa migration, module, component, model, controller, repository, validator');
                break;
        }
    }

    public function processInputs($arguments = []): void
    {
        if (empty($arguments)) {
            $arguments = $this->arguments();
        }

        $namespace = 'Modules\\' . (string)$arguments['module'];
        // Xu ly text
        $this->singularName = Str::singular(Str::camel($arguments['name']));
        $this->singularStudlyName = $this->modelName = Str::studly($this->singularName);
        $this->singularSnakeName = Str::snake($this->singularName);
        $this->singularSlugName = Str::slug($this->singularSnakeName);
        /**----**/
        $this->pluralName = Str::plural($this->singularName);
        $this->pluralSnakeName = Str::snake($this->pluralName);
        $this->pluralStudlyName = Str::studly($this->pluralName);
        $this->pluralSlugName = Str::slug($this->pluralSnakeName);
        /**----**/
        $this->repositoryName = $this->modelName . 'Repository';
        $this->validatorName = $this->modelName . 'Validator';
        $this->controllerName = $this->modelName . 'Controller';
        /**----**/
        $this->module = str_replace('Modules\\', '', $namespace);
        $this->folder = 'modules' . DIRECTORY_SEPARATOR . $this->module;
        /**----**/
        $this->replace = [
            $this->pluralName,
            $this->pluralSnakeName,
            $this->pluralStudlyName,
            $this->pluralSlugName,
            $this->singularName,
            $this->singularSnakeName,
            $this->singularStudlyName,
            $this->singularSlugName,
            $this->modelName,
            $this->repositoryName,
            $this->validatorName,
            $this->controllerName,
            $this->module,
            Str::lower(Str::snake($this->module)),
            Str::slug(Str::lower(Str::snake($this->module))),
            $namespace,
        ];
    }

    public function generateTestFiles(): void
    {
        $this->generateFileByName('Tests/Feature', 'tests/feature.stub', "{$this->modelName}CrudTest.php");
    }

    public function generateDatatableFiles(): void
    {
        $this->generateFileByName('DataTables', 'classes/datatable.stub', "{$this->modelName}Datatable.php");
        // $this->info("Copy đoạn code sau vào trong hàm registerLivewireComponents() trong modules\\$this->module\\Providers\\{$this->module}ServiceProvider:");
        // $this->info("Livewire::component('{$this->singularSlugName}-datatable', {$this->singularStudlyName}Datatable::class);");
    }

    public function generateObserverFile(): void
    {
        $this->generateFileByName('Observers', 'classes/observer.stub', "{$this->modelName}Observer.php");
        $this->info("Copy đoạn code sau vào trong hàm registerObservers() trong modules\\{$this->module}\\Providers\\{$this->module}ServiceProvider:");
        $this->warn("{$this->modelName}::observe({$this->modelName}Observer::class);");
    }

    /**
     * Tao file model
     */
    public function generateModelFile(): void
    {
        $this->generateFileByName('Models', 'classes/model.stub', "{$this->modelName}.php");
    }

    /**
     * Tao file model
     */
    public function generateFactoryFile(): void
    {
        $this->generateFileByName('Database/Factories', 'classes/factory.stub', "{$this->modelName}Factory.php");
    }

    /**
     * Tao file repository
     */
    public function generateRepositoryFile(): void
    {
        $this->generateFileByName('Repositories/Contracts', 'classes/contract.stub', "{$this->repositoryName}.php");
        $this->generateFileByName('Repositories/Eloquents', 'classes/repository.stub', "{$this->repositoryName}Eloquent.php");
    }

    /**
     * Tao file validator
     */
    public function generateValidatorFile(): void
    {
        $this->generateFileByName('Http/Requests', 'requests/create.stub', "{$this->modelName}CreateRequest.php");
        $this->generateFileByName('Http/Requests', 'requests/update.stub', "{$this->modelName}UpdateRequest.php");
    }

    /**
     * Tao file validator
     */
    public function generateControllerFile(): void
    {
        $this->generateFileByName('Http/Controllers', 'classes/controller.stub', "{$this->controllerName}.php");
    }

    /**
     * Tao file validator
     */
    public function generateResourceFiles(): void
    {
        $this->generateFileByName('Http/Resources', 'resources/collection.stub', "{$this->modelName}Collection.php");
        $this->generateFileByName('Http/Resources', 'resources/resource.stub', "{$this->modelName}Resource.php");
    }

    /**
     * Tao 2 file view cho index va edit
     */
    public function generateViewFiles(): void
    {
        $this->generateFileByName("Resources/Views/pages/{$this->pluralSnakeName}", 'views/index.stub', "list_{$this->pluralSnakeName}.blade.php");
        $this->generateFileByName("Resources/Views/pages/{$this->pluralSnakeName}", 'views/create.stub', "create_new_{$this->singularSnakeName}.blade.php");
        $this->generateFileByName("Resources/Views/pages/{$this->pluralSnakeName}", 'views/edit.stub', "edit_{$this->singularSnakeName}.blade.php");
        $this->generateFileByName("Resources/Views/pages/{$this->pluralSnakeName}", 'views/detail.stub', "{$this->singularSnakeName}_detail.blade.php");
        $this->generateFileByName("Resources/Views/pages/{$this->pluralSnakeName}", 'views/components/edit_form.stub', "components/edit_{$this->singularSnakeName}_form.blade.php");
    }

    /**
     * Tao file migration
     */
    private function generateMigrationFile(): void
    {
        try {
            $this->info("----------------------- Bắt đầu xử lý tạo migration -----------------------");

            $className = 'Create' . $this->pluralStudlyName . 'Table';
            $destinationFileName = date('Y_m_d_His') . "_create_{$this->pluralSnakeName}_table";
            $sourceFilePath = $this->getSourcePath('migration/create.stub');
            $destinationFolder = $this->folder . DIRECTORY_SEPARATOR . 'Database/Migrations';
            $destinationFilePath = base_path($destinationFolder . DIRECTORY_SEPARATOR . "{$destinationFileName}.php");
            $this->createFolder($this->folder . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations');

            $this->info("Tiến hành tạo migration $destinationFileName");
            if (!File::exists($sourceFilePath)) {
                $this->error('Không tìm thấy mấu dữ liệu cho việc tạo migration');
                $this->separator();
                return;
            }

            // replace agruments
            $migrationText = File::get($sourceFilePath);
            $migrationText = str_replace([
                '$CLASS$',
                '$TABLE$',
            ], [
                $className,
                $this->pluralSnakeName,
            ], $migrationText);

            // create folder and write migration text
            $this->putText($destinationFolder, $destinationFilePath, $migrationText);
            $this->info("Da tao thanh cong file migration $destinationFileName.");

            $this->separator();
        } catch (Exception $ex) {
            $this->error('Qua trinh tao migration that bai. ' . $ex->getMessage());
        }
    }

    protected function generateFileByName(string $folder, string $sourceName, string $destinationName): void
    {
        try {
            $sourceFilePath = $this->getSourcePath($sourceName);
            $destinationFolder = $this->folder;
            $this->createFolder($destinationFolder);

            if (strpos($folder, '/') !== false) {
                $folderParts = explode('/', $folder);
                foreach ($folderParts as $folderPart) {
                    $destinationFolder .= DIRECTORY_SEPARATOR . $folderPart;
                    $this->createFolder($destinationFolder);
                }
            } else {
                $destinationFolder .= DIRECTORY_SEPARATOR . $folder;
                $this->createFolder($destinationFolder);
            }

            if (strpos($destinationName, '/') !== false) {
                $nameParts = explode('/', $destinationName);
                $destinationName = $nameParts[count($nameParts) - 1];
                foreach ($nameParts as $namePart) {
                    if ($namePart !== $destinationName) {
                        $destinationFolder .= DIRECTORY_SEPARATOR . $namePart;
                        $this->createFolder($destinationFolder);
                    }
                }
            }

            $destinationFilePath = base_path($destinationFolder . DIRECTORY_SEPARATOR . $destinationName);

            if (!File::exists($sourceFilePath)) {
                $this->error("Không tìm thấy dữ liẹu mẫu $sourceName.");
                return;
            }

            if (File::exists($destinationFilePath)) {
                return;
            }

            $text = str_replace($this->search, $this->replace, File::get($sourceFilePath));
            $this->putText($destinationFolder, $destinationFilePath, $text);

            $this->info("Da tao thanh cong tập tin $destinationName");
            $this->separator();
        } catch (Exception $ex) {
            $this->error('Quá trình tạo tập tin thất bại,' . $ex->getMessage());
        }
    }

    protected function createFolder($folder)
    {
        if (!is_dir(base_path($folder))) {
            File::makeDirectory(base_path($folder), 0755, false, true);
            $this->info("Tạo thành công folder " . base_path($folder));
            $this->separator();
        }
    }

    private function getSourcePath($fileName): string
    {
        return __DIR__ . "/Stubs/{$fileName}";
    }

    private function putText($path, $fileName, $text)
    {
        File::makeDirectory(base_path($path), 0755, true, true);
        File::put($fileName, $text);
    }

    private function separator()
    {
        $this->info('---------------------------------------------------------------------');
    }
}
