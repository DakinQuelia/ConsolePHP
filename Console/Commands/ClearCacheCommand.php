<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier     : Console\Commands\ClearCacheCommand.php     		    ||
||  Version     : 1.0.0.                                	            ||
||  Auteur(s)   : Dakin Quelia						                    ||
||  ------------------------------------------------------------------  ||
||  Copyright ©2022 Corona CMS - codé par Dakin Quelia                  ||
\*======================================================================*/
namespace Console\Commands;

use Console\Command;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends Command
{
    protected string $cache_dir;
	protected string $domain_dir;
	protected string $skeleton_dir;
	protected string $view_dir;
	protected string $controller_file;
	protected string $controller_view_file;

	/**
	*	Cette méthode permet de configurer la commande.
	**/
	protected function configure(): void
	{
		$this->setName("clear:cache");
		$this->setDescription("Vidage du cache");
		$this->setHelp("Cette commande permet de vider le cache.");
	}

	/**
	* 	Cette méthode est exécutée après la configuration de la commande pour initialiser des propriétés.
	**/
	protected function initialize(InputInterface $input, OutputInterface $output): void
    {
		// Parent
		parent::initialize($input, $output);	

        // Dossier du cache 
        $this->cache_dir = ROOT . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Cache';
    }

	/**
	* 	Cette méthode permet d'interagir avec la console.
	**/
	protected function interact(InputInterface $input, OutputInterface $output): void
    {
		//
	}

	/**
	*	Cette méthode exécute la commande.
	**/
	protected function execute(InputInterface $input, OutputInterface $output): int
	{	
		// Si le dossier n'est pas en écriture
		if (!is_writable($this->cache_dir))
		{
			throw new RuntimeException("Il est imopossible d'écrire dans le dossier « Cache » !");
		}

        // Demande de confirmation
		$confirm = $this->confirm("Confirmez-vous le vidage du cache ?");

		//
		if ($confirm)
		{
            // Vidage du Cache
            $this->clearDir($this->cache_dir);
        }

		// Message de validation
        $this->io->success("Le dossier « Cache »  été vidé avec succès.");

		return COMMAND::SUCCESS;
	}

	/**
	*	Cette méthode permet d'afficher une texte d'aide 
	**/
	private function getCommandHelp(): string
    {
        return '';
    }
}

?>