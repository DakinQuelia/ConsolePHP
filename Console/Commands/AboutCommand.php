<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier     : Console\Commands\AboutCommand.php     		            ||
||  Version     : 1.0.0.                                	            ||
||  Auteur(s)   : Fabien Potencer / Dakin Quelia						||
||  ------------------------------------------------------------------  ||
||  Copyright ©2022 Corona CMS - codé par Dakin Quelia                  ||
\*======================================================================*/
namespace Console\Commands;

use Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Descriptor\ApplicationDescription;

class AboutCommand extends Command
{
    /**
    *   Cette méthode permet de configurer la commande.
    **/
    protected function configure()
    {
        $this->ignoreValidationErrors();
        $this->setName("about");
		$this->setDescription("A propos de l'application");
		$this->setHelp("Cette commande vous informe sur l'application (CMS) et sur la console.");
    }

    /**
    *   Cette méthode définit une commande.
    **/
    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

    /**
	* 	Cette méthode est exécutée après la configuration de la commande pour initialiser des propriétés.
	**/
	protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // Parent
		parent::initialize($input, $output);	
    }

    /**
	* 	Cette méthode permet d'interagir avec la console.
	**/
	protected function interact(InputInterface $input, OutputInterface $output): void
    {

    }

    /**
	*	Cette méthode exécute la commande.
	**/
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
        $output->writeln('');
        $output->writeln("================ A PROPOS ================");
        $this->io->text([
            "Auteur(s)              :   Dakin Quelia",
            "Version Application    :   1.0.0",
            "Version Console        :   1.0.0"
        ]);
        $output->writeln("==========================================");

        return COMMAND::SUCCESS;
    }

    /**
	*	Cette méthode permet d'afficher une texte d'aide 
	**/
	private function getCommandHelp(): string
    {
        return '';
    }

    /**
    *   Cette méthode retourne l'auto-complétion de la commande.
    **/
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        
    }
}

?>