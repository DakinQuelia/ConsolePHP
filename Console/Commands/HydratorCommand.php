<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier     : Console\Commands\Hydrator.php                     	||
||  Version     : 1.0.0.                                	            ||
||  Auteur(s)   : Dakin Quelia						                    ||
||  ------------------------------------------------------------------  ||
||  Copyright ©2022 Corona CMS - codé par Dakin Quelia                  ||
\*======================================================================*/
namespace Console\Commands;

use RuntimeException;
use Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HydratorCommand extends Command
{
	private SymfonyStyle $io;
	private string $domain_dir;
	private string $skeleton_dir;
	private string $model_file;
	private array $properties;

	/**
	*	Cette méthode permet de configurer la commande.
	**/
	protected function configure(): void
	{
		$this->setName("hydrator");
		$this->setDescription("Hydrater la base de données");
		$this->setHelp("Cette commande permet d'ajouter des données de tests dans la base de données.");

	
	}

	/**
	* 	Cette méthode est exécutée après la configuration de la commande pour initialiser des propriétés.
	**/
	protected function initialize(InputInterface $input, OutputInterface $output): void
    {
		// Style de la console
        $this->io = new SymfonyStyle($input, $output);

		
    }

	/**
	* 	Cette méthode permet d'interagir avec la console.
	**/
	protected function interact(InputInterface $input, OutputInterface $output): void
    {
		
		$this->io->title('Hydrater la base de données');
        $this->io->text([
    	  "Si vous préférez utiliser la ligne de commance plutôt que l'installateur",
          "Voici la commande :",
          '',
          ' $ php bin/console hydrator:model nom_model',
          ''
        ]);

		// Nom du modèle
		$model_name = $this->io->ask(sprintf("Indiquez le nom du modèle [Exemple :: %s]", "<comment>Post</comment>"), null);
        $input->setArgument('model_name', $model_name);
	}	

    /**
	*	Cette méthode exécute la commande.
	**/
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		// On récupère les informations
		$model_name = $input->getArgument('model_name');

		// Demande de confirmation
		$confirm = $this->io->confirm("Confirmez-vous l'insertion de données dans la base de données ?", false);

		//
		if ($confirm)
		{
		}
			
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