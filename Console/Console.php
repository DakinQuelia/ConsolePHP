<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier     : Console\Console.php     		                        ||
||  Version     : 1.0.0.                                	            ||
||  Auteur(s)   : Dakin Quelia						                    ||
||  ------------------------------------------------------------------  ||
||  Copyright ©2022 Corona CMS - codé par Dakin Quelia                  ||
\*======================================================================*/
namespace Console;

use Console\Commands\HelpCommand;
use Console\Commands\ListCommand;
use Console\Commands\AboutCommand;
use Console\Commands\MakeCRUDCommand;
use Console\Commands\MakeModelCommand;
use Console\Commands\ClearCacheCommand;
use Console\Commands\MakeControllerCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application as BaseApplication;

class Console extends BaseApplication 
{
    protected string $root;
    protected string $name = "Console";
    protected string $version = "1.0.0";
    protected array $console_options = [];
    
    // Logo
    protected static $logo = <<<LOGO
     _____                                    _____ ___  ___ _____ 
    /  __ \                                  /  __ \|  \/  |/  ___|
    | /  \/  ___   _ __  ___   _ __    __ _  | /  \/| .  . |\ `--. 
    | |     / _ \ | '__|/ _ \ | '_ \  / _` | | |    | |\/| | `--. \
    | \__/\| (_) || |  | (_) || | | || (_| | | \__/\| |  | |/\__/ /
     \____/ \___/ |_|   \___/ |_| |_| \__,_|  \____/\_|  |_/\____/
LOGO;

    /**
    *   Constructeur
    **/
    public function __construct(string $root)
    {
        //
        parent::__construct('', '');

        $this->root = $root;
        $this->defaultcommand = 'list';
        $this->console_options = [];
    }

    /**
    *   Cette méthode permet d'excuter la console. 
    **/
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // Parent
        parent::doRun($input, $output);

        // Renvoie les informations "à propos"
        if (true === $input->hasParameterOption(['--about', '-a'], true)) 
        {
            $output->writeln('');
            $output->writeln("================ A PROPOS ================");
            $output->writeln($this->about());
            $output->writeln("==========================================");

            return 0;
        }
    }

    /**
    *   Cette méthode retourne l'affichage de l'aide.
    **/
    public function getHelp(): string
	{
        return static::$logo . parent::getHelp() . "\n\n" . "Copyright (c) 2022 by Dakin Quelia <http://monsite.com>" . "\n" . sprintf("<info>%s</info>", $this->name) . " - Version " . sprintf("<comment>%s</comment>", $this->version) . " 30/03/2022 02:43";
	}

    /**
    *   Les définitiations par défaut
    **/
    protected function getDefaultInputDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, "La commande à exécuter"),
            new InputOption('--about', '-a', InputOption::VALUE_NONE, "Affiche les informations de la console"),
            new InputOption('--help', '-h', InputOption::VALUE_NONE, "Affichage de l'aide de la commande spécifiée. Si aucune commande n'est spécifiée, cela affichera l'aide pour la commande  <info>" . $this->defaultcommand . "</info>"),
            new InputOption('--quiet', '-q', InputOption::VALUE_NONE, "Mode silencieux"),
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, "Indiquez le niveau de verbosité des messages : <comment>1</comment> correspond à l'affichage par défaut, <comment>2</comment> à un affichage plus verbeux et <comment>3</comment> pour le débogage"),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, "Affichage la version de cette application"),
            new InputOption('--ansi', '', InputOption::VALUE_NEGATABLE, "Forcer (ou désactiver --no-ansi) la sortie ANSI", null),
            new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, "Aucune interaction"),
        ]);
    }

    /**
    *   Liste des commandes de base
    **/
    protected function getDefaultCommands(): array
    {
        $commands = [];
        $commands[] = new AboutCommand();
        $commands[] = new HelpCommand();
        $commands[] = new ListCommand();
        $commands[] = new ClearCacheCommand();
        $commands[] = new MakeControllerCommand();
        $commands[] = new MakeModelCommand();
        $commands[] = new MakeCRUDCommand();

        return $commands;
    }

    /**
    *   Cette méthode permet de définir les options par défaut de la console. 
    *   
    *   @param InputInterface $input                Informations tapées dans la console
    **/
    protected function getDefaultOptions(InputInterface $input): int
    {   
        foreach($this->console_options as $arg)
        {
            if (true === $input->hasParameterOption([$arg['command'], $arg['shortcut']], true)) 
            {
                $arg['callback'];

                return 0;
            }
        }
    }

    /**
    *   Cette méthode retourne les informations de l'application/console.
    **/
    protected function about(): array
    {
        return [
            "Auteur(s)              :   Dakin Quelia",
            "Version Application    :   1.0.0",
            "Version Console        :   1.0.0"
        ];
    }
}

?>