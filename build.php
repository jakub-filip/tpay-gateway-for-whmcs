<?php

$moduleName = 'tpay';
$prefix = 'JakubFilip\\Tpay\\Vendor';

$rootDir = __DIR__ . DIRECTORY_SEPARATOR;
$srcDir = $rootDir . 'src' . DIRECTORY_SEPARATOR;
$composerJsonFile = $rootDir . 'composer.json';
$composerLockFile = $rootDir . 'composer.lock';
$additionalFiles = [
    $rootDir . 'whmcs.json',
    $rootDir . 'LICENSE',
    $rootDir . 'README.md',
];

$tmpDir = $rootDir . '.tmp-build' . DIRECTORY_SEPARATOR;
$srcTmpDir = $tmpDir . 'src' . DIRECTORY_SEPARATOR;
$vendorTmpDir = $tmpDir . 'vendor' . DIRECTORY_SEPARATOR;
$composerJsonTmpFile = $tmpDir . 'composer.json';
$composerLockTmpFile = $tmpDir . 'composer.lock';
$additionalTmpFiles = [
    $tmpDir . 'whmcs.json',
    $tmpDir . 'LICENSE',
    $tmpDir . 'README.md',
];

$scopedDir = $tmpDir . 'scoped' . DIRECTORY_SEPARATOR;
$scopedSrcDir = $scopedDir . 'src' . DIRECTORY_SEPARATOR;
$scopedVendorDir = $scopedDir . 'vendor' . DIRECTORY_SEPARATOR;
$scopedModuleFile = $scopedSrcDir . $moduleName . '.php';
$scopedCallbackFile = $scopedSrcDir . $moduleName . '_callback.php';

$buildDir = $rootDir . 'build' . DIRECTORY_SEPARATOR;
$gatewaysDir = $buildDir . 'modules' . DIRECTORY_SEPARATOR . 'gateways' . DIRECTORY_SEPARATOR;
$moduleDir = $gatewaysDir . $moduleName . DIRECTORY_SEPARATOR;
$composerJsonModuleFile = $moduleDir . 'composer.json';
$composerLockModuleFile = $moduleDir . 'composer.lock';
$vendorDir = $moduleDir . 'vendor' . DIRECTORY_SEPARATOR;
$callbackDir = $gatewaysDir . 'callback' . DIRECTORY_SEPARATOR;
$moduleFile = $gatewaysDir . $moduleName . '.php';
$callbackFile = $callbackDir . $moduleName . '.php';

$phpScoperUrl = 'https://github.com/humbug/php-scoper/releases/download/0.18.19/php-scoper.phar';
$phpScoperFile = $tmpDir . 'php-scoper.phar';
$phpScoperConfigurationFile = $tmpDir . 'scoper.inc.php';
$phpScoperConfiguration = <<<'PHP'
<?php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'output-dir' => __DIR__ . DIRECTORY_SEPARATOR . 'scoped',

    'prefix' => 'JakubFilip\\Tpay\\Vendor',

    'finders' => [
        Finder::create()->files()->in(__DIR__ . DIRECTORY_SEPARATOR . 'src'),
        Finder::create()->files()->ignoreVCS(true)->in(__DIR__ . DIRECTORY_SEPARATOR . 'vendor')
    ],

    'exclude-namespaces' => [
        'JakubFilip\\Tpay',
    ],

    'patchers' => [
        static function (string $filePath, string $prefix, string $content): string {
            if ($filePath === str_replace('\\', '/', __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'tpay.php') || $filePath === str_replace('\\', '/', __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'tpay_callback.php')) {
                return preg_replace('/^namespace\s+.*$/m', '', $content);
            }

            return $content;
        }
    ]
];
PHP;

$composerInstallCommand = sprintf('composer install --working-dir=%s --no-dev --optimize-autoloader --no-interaction', escapeshellarg($tmpDir));
$scopeCommand = sprintf('php %s add-prefix --config=%s', escapeshellarg($phpScoperFile), escapeshellarg($phpScoperConfigurationFile));
$composerDumpAutoloadCommand = sprintf('composer dump-autoload --working-dir=%s -o', escapeshellarg($moduleDir));

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline, array $errcontext): bool {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    banner('Building module package...');

    info('Removing temporary directory...');
    remove_directory($tmpDir);
    success('Temporary directory removed.');

    info('Creating temporary directory...');
    create_directory($tmpDir);
    success('Temporary directory created.');

    info('Removing build directory...');
    remove_directory($buildDir);
    success('Build directory removed.');

    info('Creating build directory...');
    create_directory($buildDir);
    success('Build directory created.');

    info('Copying module files to temporary directory...');
    copy_directory($srcDir, $srcTmpDir);

    copy($composerJsonFile, $composerJsonTmpFile);
    copy($composerLockFile, $composerLockTmpFile);

    foreach ($additionalFiles as $additionalFile) {
        $targetFile = str_replace($rootDir, $tmpDir, $additionalFile);
        copy($additionalFile, $targetFile);
    }
    success('Module files copied to temporary directory...');

    info('Updating Composer configuration...');
    $composerData = json_decode(file_get_contents($composerJsonTmpFile));

    if (isset($composerData->autoload->{'psr-4'})) {
        foreach ($composerData->autoload->{'psr-4'} as $namespace => $path) {
            $composerData->autoload->{'psr-4'}->{$namespace} = './';
        }
    }

    file_put_contents($composerJsonTmpFile, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);
    success('Composer configuration updated...');

    info('Running Composer install...');
    safe_exec($composerInstallCommand);
    success('Composer install completed.');

    info('Generating PHP-Scoper configuration file...');
    file_put_contents($phpScoperConfigurationFile, $phpScoperConfiguration);
    success('PHP-Scoper configuration file generated.');

    info('Downloading PHP-Scoper...');
    $scoperContent = file_get_contents($phpScoperUrl);
    file_put_contents($phpScoperFile, $scoperContent);
    success('PHP-Scoper downloaded.');

    info('Scoping code with PHP-Scoper...');
    safe_exec($scopeCommand);
    success('Code scoped.');

    info('Creating WHMCS module directory structure...');
    create_directory($moduleDir);
    create_directory($callbackDir);
    success('WHMCS module directory structure created.');

    info('Copying module files to build directory...');
    copy($scopedModuleFile, $moduleFile);
    copy($scopedCallbackFile, $callbackFile);

    unlink($scopedModuleFile);
    unlink($scopedCallbackFile);

    copy_directory($scopedSrcDir, $moduleDir);
    copy_directory($scopedVendorDir, $vendorDir);

    foreach ($additionalTmpFiles as $additionalTmpFile) {
        $targetFile = str_replace($tmpDir, $moduleDir, $additionalTmpFile);
        copy($additionalTmpFile, $targetFile);
    }

    copy($composerJsonTmpFile, $composerJsonModuleFile);
    copy($composerLockTmpFile, $composerLockModuleFile);
    success('Module files copied to build directory.');

    info('Removing temporary directory...');
    remove_directory($tmpDir);
    success('Temporary directory removed.');

    info('Regenerating Composer autoload...');
    safe_exec($composerDumpAutoloadCommand);
    success('Composer autoload regenerated.');

    info('Composer autoload regenerated.');
    unlink($moduleDir . 'composer.json');
    unlink($moduleDir . 'composer.lock');
    success('Composer files removed.');

    banner('Module package built.');
} catch (Throwable $e) {
    error($e->getMessage());
    exit();
}

function banner(string $message): void
{
    echo PHP_EOL . str_repeat('=', 50) . PHP_EOL;
    echo ' ' . $message . PHP_EOL;
    echo str_repeat('=', 50) . PHP_EOL . PHP_EOL;
}

function info(string $message): void
{
    echo '[*] ' . $message . PHP_EOL;
}

function success(string $message): void
{
    echo "[\u{2713}] " . $message . PHP_EOL;
}

function error(string $message): void
{
    echo "\033[31m[X] " . $message . "\033[0m" . PHP_EOL;
}

function remove_directory(string $directory): void
{
    if (is_dir($directory)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($directory);
    }
}

function create_directory(string $directory): void
{
    mkdir($directory, 0755, true);
}

function copy_directory(string $source, string $destination): void
{
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        $targetPath = str_replace($source, $destination, $file->getRealPath());

        if ($file->isDir()) {
            mkdir($targetPath, 0755, true);
        } else {
            copy($file->getRealPath(), $targetPath);
        }
    }
}

function safe_exec(string $command): void
{
    $output = [];
    $exitCode = 0;

    exec($command . ' 2>&1', $output, $exitCode);

    if ($exitCode !== 0) {
        $fullOutput = implode("\n", $output);

        throw new RuntimeException($fullOutput);
    }
}