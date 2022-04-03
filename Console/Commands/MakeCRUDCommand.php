<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier     : Console\Commands\MakeCRUDCommand.php        			||
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

class MakeCRUDCommand extends Command
{
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
		$this->setName("make:crud");
		$this->setDescription("Création d'un nouveau module (CRUD)");
		$this->setHelp("Cette commande permet de créer un nouveau module (CRUD).");

		$this->addArgument('module_name', InputArgument::REQUIRED, "Nom du module");
		$this->addArgument('author_name', InputArgument::REQUIRED, "Auteur du modèle");
		$this->addArgument('controller_name', InputArgument::REQUIRED, "Nom du contrôleur");
		$this->addArgument('type_view', InputArgument::REQUIRED, "Type de modèle");
	}

	/**
	* 	Cette méthode est exécutée après la configuration de la commande pour initialiser des propriétés.
	**/
	protected function initialize(InputInterface $input, OutputInterface $output): void
    {
		// Parent
		parent::initialize($input, $output);	

		// Global
		$domain_src = "Domain";
		$views_src = "Views";
		
		// Le dossier principal des modules
        $this->domain_dir = ROOT . DIRECTORY_SEPARATOR . $domain_src;
		$this->skeleton_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Skeleton' . DIRECTORY_SEPARATOR;
		$this->view_dir = ROOT . DIRECTORY_SEPARATOR . $views_src;

		// Fichiers modèles
		$this->controller_file = $this->skeleton_dir . DIRECTORY_SEPARATOR . 'CRUD' . DIRECTORY_SEPARATOR . 'controllercrud.tpl.php';
		$this->controller_view_file = $this->skeleton_dir . DIRECTORY_SEPARATOR . 'CRUD' . DIRECTORY_SEPARATOR . 'controller.html';

		// On vérifie si le fichier de création du contrôleur existe
		if (!file_exists($this->controller_file))
		{
			throw new RuntimeException("Le fichier n'existe pas et/ou le chemin vers le fichier n'est pas correct.");
		}
    }

	/**
	* 	Cette méthode permet d'interagir avec la console.
	**/
	protected function interact(InputInterface $input, OutputInterface $output): void
    {
		// Informations
		$this->io->title("Création d'un module (CRUD)");
        $this->io->text([
    	  "Si vous préférez utiliser la ligne de commande plutôt que l'installateur",
          "Voici la commande :",
          '',
          ' $ php bin/console make:crud <nom_module> <auteur_controleur> <nom_controleur> <type_vue>',
          ''
        ]);

		// Nom du module
		$module_name = $this->io->ask(sprintf("Indiquez le nom du module [Exemple :: %s]", "<comment>AcmeDemo</comment>"), null, function($value) 
		{
			if ($value === '' || $value === null) 
			{
				throw new RuntimeException("Le nom du module du contrôleur est OBLIGATOIRE. Par exemple : AcmeDemo");
			}

			return $value;
		});
		$input->setArgument('module_name', $module_name);

		// Nom de l'auteur
		$author_name = $this->io->ask(sprintf("Indiquez le nom de l'auteur de votre contrôleur [Exemple :: %s] ", "<comment>Auteur</comment>"), "CMS Corona");
        $input->setArgument('author_name', $author_name);

		// Nom du contrôleur
		$controller_name = $this->io->ask(sprintf("Indiquez le nom du contrôleur [Exemple :: %s] ", "<comment>MonController</comment>"), null, function($value) 
		{
			if (!preg_match('/^[a-zA-Z]+Controller$/', $value)) 
			{
				throw new RuntimeException("Le nom du contrôleur doit contenir « Controller ». Par exemple : DemoController");
			}

			return $value;
		});
		$input->setArgument('controller_name', $controller_name);

		// Type du fichier de la vue
		$type_view = $this->io->choice("Choisissez le type de template [Exemple :: Public] ", ['Public', 'Admin'], 0);
		$input->setArgument('type_view', $type_view);
	}

	/**
	*	Cette méthode exécute la commande.
	**/
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		// Tableau des données
		$data = [
			'module_name'		=> $input->getArgument('module_name'),
			'author_name'		=> $input->getArgument('author_name'),
			'controller_name'	=> $input->getArgument('controller_name'),
			'type_view'			=> $input->getArgument('type_view')


		];

		// Demande de confirmation
		$confirm = $this->confirm("Confirmez-vous la création du contrôleur ?");

		//
		if ($confirm)
		{
			// Génère le contrôleur
			$this->generateCRUD($data);

			// Nom du fichier
			$filename_end = $this->domain_dir . "\\" . $data['module_name'] . '\\Controllers\\'. $data['controller_name'] . ".php";

			// Message de validation
			$new_controller_view_dir =  $this->view_dir . DIRECTORY_SEPARATOR . $data['module_name'] . DIRECTORY_SEPARATOR;
			$controller_file = $filename_end;

			// Modèles
			$templates = [
				$new_controller_view_dir . "index.html",
				$new_controller_view_dir . "add.html",
				$new_controller_view_dir . "edit.html",
				$new_controller_view_dir . "delete.html"
			];

			// Message de validation
			$this->io->success("Le contrôleur « " . $data['controller_name'] . " » du module « " . $data['module_name'] . " » été créé avec succès.");

			// On affiche les fichiers
			$output->writeln(sprintf('  <fg=green>Créé :</> %s', $this->relativizePath($controller_file)));
			foreach($templates as $template)
			{
				$output->writeln(sprintf('  <fg=green>Créé :</> %s', $this->relativizePath($template)));
			}
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

	/**
	* 	Cette méthode permet de générer le contrôleur.
	*	
	*	@param array $data 			Informations sur le contrôleur
	**/
	public function generateCRUD(array $data): void
	{
		// On récupère le contenu du fichier modèle
		$controller_tpl = file_get_contents($this->controller_file);
		$controller_view = file_get_contents($this->controller_view_file);

		// Si le dossier des modules n'existe pas, on le crée.
		if (!file_exists($this->domain_dir))
		{
			@mkdir($this->domain_dir, 0700);
		}

		// On crée les dossiers relatifs au module/bundle
		$module_dir = $this->domain_dir . DIRECTORY_SEPARATOR . $data['module_name'];
		$module_view_dir = $this->view_dir . DIRECTORY_SEPARATOR . $data['module_name'];

		if (!file_exists($module_dir))
		{
			@mkdir($module_dir, 0777);
			@mkdir($module_view_dir, 0777);
		}

		$controller_dir = $module_dir . DIRECTORY_SEPARATOR . 'Controllers';

		if (!file_exists($controller_dir))
		{
			@mkdir($controller_dir, 0777);
		}

		// Création du nouveau fichier Contrôleur et sa vue
		$templates = ["index.html", "add.html", "edit.html", "delete.html"];
		$controller = $data['controller_name'];
		$filelayout = lcfirst($data['type_view']);
		$namespace_name = "App\\Domain\\" . $data['module_name'] . "\\Controllers";
		$template_dir = $data['type_view'] . DIRECTORY_SEPARATOR . $data['module_name']; 
	
		$controller_tpl = str_replace('{{author}}', $data['author_name'], $controller_tpl);
		$controller_tpl = str_replace('{{namespace}}', $namespace_name, $controller_tpl);
		$controller_tpl = str_replace('{{classname}}', $controller, $controller_tpl);
		$controller_tpl = str_replace('{{template}}', $template_dir, $controller_tpl);
		$controller_view = str_replace('{{layout}}', $filelayout, $controller_view);

		// Création des fichiers .html
		foreach($templates as $template)
		{	
			$file = $this->view_dir . DIRECTORY_SEPARATOR . $data['module_name'] . DIRECTORY_SEPARATOR . $template;

			$new_controller_view = file_put_contents($file, $controller_view);
		}
		
		$new_controller_tpl = file_put_contents($controller_dir . DIRECTORY_SEPARATOR . $controller . ".php", $controller_tpl);

		// Vérification de la création du contrôleur et/ou sa vue
		if ($new_controller_tpl === false)
		{
			throw new RuntimeException('La création du contrôleur a échoué.');
		}
		else if ($new_controller_view === false)
		{
			throw new RuntimeException('La création de la vue du contrôleur a échoué.');
		}
		else if (file_exists($new_controller_tpl)) 
		{
			throw new RuntimeException(sprintf('Le contrôleur « %s » existe déjà.', $new_controller_tpl));
		}
		else if (file_exists($new_controller_view))
		{
			throw new RuntimeException(sprintf('La vue « %s » du contrôleur « %s » existe déjà.', $new_controller_view, $data['controller_name']));
		}
	}
}

?>