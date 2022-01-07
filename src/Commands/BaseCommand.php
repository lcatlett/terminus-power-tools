<?php

/**
 * These commands use the `@authenticated`
 * attribute to signal Terminus to require an authenticated session to
 * use this command.
 */

namespace Pantheon\TerminusPowerTools\Commands;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Exceptions\TerminusException;
use Pantheon\TerminusLando\Model\Greeter;
use Consolidation\AnnotatedCommand\CommandData;

use Pantheon\TerminusBuildTools\Commands\ProjectCreateCommand;
use Pantheon\TerminusBuildTools\Commands\BuildToolsBase;
use Pantheon\Terminus\DataStore\FileStore;
use Pantheon\TerminusBuildTools\ServiceProviders\ProviderEnvironment;
use Symfony\Component\Console\Style\SymfonyStyle;
use Pantheon\Terminus\Models\Environment;
use Pantheon\Terminus\Models\Site;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\BuilderAwareInterface;
use Robo\TaskAccessor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Robo\LoadAllTasks;

/**
 * Provides commands to create and manage decoupled sites on the Pantheon platform.
 * Extends terminus-build-tools, provides recommended default configuration and project architecture,
 * CI/CD templates, generates local development environment.
 *
 */
class BaseCommand extends ProjectCreateCommand {

    /**
     * Provide default configuration to terminus-build-tools project create command.
     *
     * @hook pre-command build:project:create
     */
    public function adjustDefaultOptions(CommandData $commandData)
    {

        $ci_template = $commandData->input()->getOption('ci-template');

        if (str_contains($ci_template, 'pantheon-systems/tbt-ci-templates')) {
            $commandData->input()->setOption('ci-template', 'git@github.com:lcatlett/tbt-ci-templates.git');
        }

        $commandData->input()->setOption('keep', 'TRUE');
        $commandData->input()->setOption('visibility', 'private');
        $commandData->input()->setOption('stability', 'dev');

        $options_after = $commandData->input()->getOptions();
        //print_r($options_after);
    }


    /**
     * Configures Lando local development environment.
     * @authorize
     *
     * @hook post-process build:project:create
     */
    public function taskInit()
    {
        $this->landoSetup();
    }

    /**
     * Configures Lando local development environment.
     * @authorize
     *
     * @command build:lando:setup
     * @aliases lando:setup
     */

    public function landoSetup()
    {
        $this->log()->notice('Generating Lando local development environment configuration');

        // Get the project name
        if (empty($site_name)) {
            $site_name = basename(getcwd());
        }

        // If a template.lando.yml file exists in the template repository, copy it to a new .lando.yml.
        if (file_exists('template.lando.yml')) {
            $this->log()->notice('template.lando.yml exists, generating new .lando.yml');

            $result = $this->taskFilesystemStack()
                ->copy('template.lando.yml', '.lando.yml')
                ->stopOnFail()
                ->run();

            if (!$result->wasSuccessful()) {
                $this->log()->notice('Error: Unable to copy template.lando.yml to .lando.yml');
                return;
            }

            $result = $this->taskReplaceInFile('.lando.yml')
                ->from('%SITE_NAME%')
                ->to($site_name)
                ->run();

            if (!$result->wasSuccessful()) {
                $this->log()->notice('Error: Unable to generate config for .lando.yml');
                return;
            }

            $builder = $this->collectionBuilder();

            $builder
                ->taskGitStack()
                ->stopOnFail()
                ->add('.lando.yml')
                ->commit('Added .lando.yml local development environment configuration.');

            $builder->taskFilesystemStack()
                ->remove('template.lando.yml');

            return $builder->run();

            if (!$builder->wasSuccessful()) {
                $this->log()->notice('Error: Unable to commit .lando.yml to local repository.');
                return;
            }
            $this->log()->notice('Success! Lando configuration was generated and committed to the local repository. Start lando with "lando start"');
        }

        //$this->log()->notice('Success2! Lando configuration was generated.');
       // $this->log()->notice('Success3! Start lando with "lando start"');
    }

    /**
     * Enables Pantheon site plan add-ons.
     *
     * @command build:addons:enable
     * @param string $site_name Pantheon site name.
     *
     * @aliases addons:enable
     *
     */

    public function setupAddOns($site_name) {
        $this->log()->notice('Setting up Pantheon site plan add-ons');

        // Enable Redis.
        $this->log()->notice('Enabling Redis on Pantheon site.');
        passthru("terminus redis:enable $site_name --no-interaction");

        // Enable New Relic.
        $this->log()->notice('Enabling New Relic on Pantheon site.');
        passthru("terminus new-relic:enable $site_name --no-interaction");
    }

    /**
     * Drupal settings - redis, etc once enabled.
     */

    // Check if enabled - module and on Pantheon

    // Settings includes

    // Truncate cache tables on environments?


    /**
     * Drupal settings - redis, etc once enabled.
     */

    // Sync quicksilver hooks for drupal into codebase.

    // Add to pantheon.yml


    /**
     * Standardized git hooks
     */



    /**
     * Code quality checks/static analysis moved out of composer.json
     */

    // Lint

    // phpcs

    // phpcsfixer


    /**
     * Prepare for pantheon moved into robo commands
     */


    /**
     * Asset/frontend build scripts moved into robo commands.
     */



    /**
     * Drupal security checkes moved into robo commands.
     */






}
