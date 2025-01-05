<?php

namespace Napse\BundleMaker\Maker;

use Napse\BundleMaker\Helper\BundleNameHelper;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\MakerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Maker command to generate a custom Symfony bundle with predefined structure.
 */
class MakeCustomBundleCommand extends AbstractMaker implements MakerInterface
{
    // Constants for default values and configuration
    private const DEFAULT_PATH = '../Bundles';
    private const DIRECTORY_NAMES = [
        'src',
        'config',
        'Resources',
        'tests',
        'public',
        'translations',
        'templates',
        'migrations',
    ];
    private const GITIGNORE_CONTENT = "/vendor/\n/.env";

    /**
     * Returns the name of the command.
     *
     * @return string The command name.
     */
    public static function getCommandName(): string
    {
        return 'make:bundle';
    }

    /**
     * Returns the description of the command.
     *
     * @return string The command description.
     */
    public static function getCommandDescription(): string
    {
        return 'Creates a new Symfony bundle with a predefined structure';
    }

    /**
     * Configures the command with available options and arguments.
     *
     * @param Command $command The command to configure.
     * @param InputConfiguration $inputConfig The input configuration.
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription($this->getCommandDescription())
            ->addOption(
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'Path where the bundle will be created',
                self::DEFAULT_PATH
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'Name of the bundle with namespace (e.g., Napse\\DemoBundle)',
            );

    }

    /**
     * Interacts with the user to gather missing options.
     *
     * @param InputInterface $input The input interface.
     * @param ConsoleStyle $io The console style.
     * @param Command $command The command being executed.
     */
    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        $questionHelper = new QuestionHelper();

        // Prompt for bundle name if not provided
        if (!$input->getOption('name')) {
            $question = new Question('Please enter the name of the bundle (with namespace, e.g., Napse\\DemoBundle): ');
            $bundleName = $questionHelper->ask($input, $io, $question);
            $input->setOption('name', $bundleName);
        }

        // Prompt for path if not provided
        if (!$input->getOption('path')) {
            $question = new Question('Please enter the path where the bundle should be created (Default: ../Bundles): ', self::DEFAULT_PATH);
            $bundlePath = $questionHelper->ask($input, $io, $question);
            $input->setOption('path', $bundlePath);
        }
    }

    /**
     * Generates the bundle structure and files.
     *
     * @param InputInterface $input The input interface.
     * @param ConsoleStyle $io The console style.
     * @param Generator $generator The generator instance.
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $fullBundleName = $input->getOption('name'); // e.g., "Napse\DemoBundle"
        $pathName = BundleNameHelper::generatePathName($fullBundleName); // e.g., "napse-demo-bundle"
        $path = rtrim($input->getOption('path'), '/') . '/' . $pathName; // e.g., "../Bundles/napse-demo-bundle"

        $filesystem = new Filesystem();

        // Check if the path already exists
        if ($filesystem->exists($path)) {
            throw new \RuntimeException(sprintf('The path "%s" already exists.', $path));
        }

        // Create directory structure
        foreach (self::DIRECTORY_NAMES as $dir) {
            $dirPath = $path . '/' . $dir;
            $filesystem->mkdir($dirPath);
            $filesystem->touch($dirPath . '/.gitkeep');
        }

        // Create .gitignore file
        $filesystem->dumpFile($path . '/.gitignore', self::GITIGNORE_CONTENT);

        // Generate composer package name
        $composerName = BundleNameHelper::generateComposerName($fullBundleName); // e.g., "napse/demo-bundle"

        // Parse namespace and class name
        [$namespace, $className] = BundleNameHelper::parseBundleName($fullBundleName); // ["Napse\DemoBundle", "DemoBundle"]

        // Create composer.json
        $composerData = [
            "name" => $composerName,
            "description" => "Symfony Bundle for " . $fullBundleName,
            "type" => "symfony-bundle",
            "require" => [
                "php" => ">=8.1",
                "symfony/framework-bundle" => "^6.4"
            ],
            "autoload" => [
                "psr-4" => [
                    "{$namespace}\\" => "src/"
                ]
            ],
            "extra" => [
                "symfony" => [
                    "bundle" => "{$namespace}\\{$className}"
                ]
            ]
        ];

        $filesystem->dumpFile($path . '/composer.json', json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Create Bundle class
        $bundleClassContent = <<<EOT
        <?php
        
        namespace {$namespace};
        
        use Symfony\Component\HttpKernel\Bundle\Bundle;
        use Symfony\Component\DependencyInjection\ContainerBuilder;
        
        /**
         * {$className} class.
         */
        class {$className} extends Bundle
        {
            /**
             * Builds the bundle by adding configurations to the container.
             *
             * @param ContainerBuilder \$container The container builder.
             */
            public function build(ContainerBuilder \$container): void
            {
                parent::build(\$container);
            }
        }
        EOT;

        $filesystem->dumpFile($path . "/src/{$className}.php", $bundleClassContent);

        $io->success(sprintf('The bundle "%s" was successfully created at "%s".', $fullBundleName, $path));
    }

    /**
     * Configures dependencies required by the command.
     *
     * @param DependencyBuilder $dependencies The dependency builder.
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // No additional dependencies required
    }
}
