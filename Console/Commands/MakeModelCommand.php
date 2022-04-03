<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier     : Console\Commands\MakeModelCommand.php     		    ||
||  Version     : 1.0.0.                                	            ||
||  Auteur(s)   : Dakin Quelia						                    ||
||  ------------------------------------------------------------------  ||
||  Copyright ©2022 Corona CMS - codé par Dakin Quelia                  ||
\*======================================================================*/
namespace Console\Commands;

use Exception;
use Console\Command;
use RuntimeException;
use InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModelCommand extends Command
{
	protected string $domain_dir;
	protected string $skeleton_dir;
	protected string $model_file;
	protected array $properties;
	protected array $methods;

	/**
	*	Cette méthode permet de configurer la commande.
	**/
	protected function configure(): void
	{
		$this->setName("make:model");
		$this->setDescription("Création d'un nouveau modèle");
		$this->setHelp("Cette commande permet de créer un modèle.");

		$this->addArgument('module_name', InputArgument::REQUIRED, "Nom du module");
		$this->addArgument('model_name', InputArgument::REQUIRED, "Nom du modèle");
		$this->addArgument('author_name', InputArgument::REQUIRED, "Auteur du modèle");
		$this->addArgument('table_name', InputArgument::REQUIRED, "Nom de la table");
	}

	/**
	* 	Cette méthode est exécutée après la configuration de la commande pour initialiser des propriétés.
	**/
	protected function initialize(InputInterface $input, OutputInterface $output): void
    {
		// Parent
		parent::initialize($input, $output);	

		// Le dossier principal des modules
        $this->domain_dir = ROOT . DIRECTORY_SEPARATOR . 'Domain';
		$this->skeleton_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Skeleton' . DIRECTORY_SEPARATOR;

		// Le fichier modèle
		$this->model_file = $this->skeleton_dir . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'model.tpl.php';

		// Tableau des propriétés
		$this->properties = [];
    }

	/**
	* 	Cette méthode permet d'interagir avec la console.
	**/
	protected function interact(InputInterface $input, OutputInterface $output): void
    {
		if ($input->getArgument('module_name') !== null || $input->getArgument('model_name') !== null || $input->getArgument('author_name') !== null ||  $input->getArgument('table_name') !== null)
		{
			return;
		}

		// Informations
		$this->io->title("Création d'un modèle");
        $this->io->text([
    	  "Si vous préférez utiliser la ligne de commande plutôt que le script d'installation",
          "Voici la commande :",
          '',
          ' $ php bin/console <make:model> <nom_module> <nom_modele> <auteur> <nom_table>',
          ''
        ]);

		// Nom du module
		$module_name = $this->io->ask(sprintf("Indiquez le nom du module [Exemple :: %s]", "<comment>AcmeDemo</comment>"), 'AcmeDemo');
        $input->setArgument('module_name', $module_name);

		// Nom du modèle
		$model_name = $this->io->ask(sprintf("Donnez un nom à votre modèle [Exemple :: %s] ", "<comment>Post</comment>"), 'Post');
        $input->setArgument('model_name', $model_name);

		// Nom de l'auteur
		$author_name = $this->io->ask(sprintf("Indiquez le nom de l'auteur de votre modèle [Exemple :: %s] ", "<comment>Auteur</comment>"), 'Auteur');
        $input->setArgument('author_name', $author_name);

		// Nom de la table 
		$table_name = $this->io->ask(sprintf("Donnez un nom à la table de votre modèle [Exemple :: %s] ", "<comment>website_posts</comment>"), null, function($table) use($model_name)
		{
			if ($table === null || $table === "") 
			{
				return lcfirst($model_name . 's');
			}

			return $table;
		});
        $input->setArgument('table_name', $table_name);
		
		// Les propriétés
		$this->io->writeln("=== Début de l'ajout des propriétés ===");

		// Les propriétés
		$is_first_field = true;

		while (true) 
		{
			$new_field = $this->askForNextField($this->properties, $is_first_field);
			$is_first_field = false;

			if (null === $new_field) 
			{
                break;
            }

			if (is_array($new_field)) 
			{
				$this->properties[] = [
					'field_name' => $new_field['field_name'], 
					'field_type' => $new_field['field_type']
				];
            }
			else 
			{
                throw new Exception('La valeur est invalide.');
            }
		}

		$this->io->writeln("=== Fin de l'ajout des propriétés ===");
	}	

	/**
	*	Cette méthode exécute la commande.
	**/
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		// On récupère les informations
		$data = [
			'module_name'		=> $input->getArgument('module_name'), 
			'model_name'		=> $input->getArgument('model_name'),
			'author_name'		=> $input->getArgument('author_name'),
			'table_name'		=> $input->getArgument('table_name')
		];

		// Demande de confirmation
		$confirm = $this->confirm("Confirmez-vous la création du modèle ?");	

		// Si on confirme
		if ($confirm)
		{
			$this->generateModel($data);

			$filename_end = $this->domain_dir . "\\" . $data['module_name'] . '\\Models\\'. $data['model_name'] . ".php";

			$this->io->success(sprintf("Le modèle « %s » du module « %s » été créé avec succès.", $data['model_name'], $data['module_name']));
			$output->writeln(sprintf('  <fg=green>Créé :</> %s', $this->relativizePath($filename_end)));
		}

		$this->io->newLine();

		$table = new Table($output);
		$table->setHeaders(['Propriété', 'Type']);
		$rows = [];

		if (is_array($this->properties) && $this->properties !== null)
		{
			foreach($this->properties as $property)
			{	
				$rows[] = [$property['field_name'], $property['field_type']];
			}

			$table->setRows($rows);
			$table->render();
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
	* 	Cette méthode permet de générer le modèle.
	*	
	*	@param array $data 			Informations sur le modèle
	**/
	public function generateModel(array $data): void
	{
		// Le répertoire du module
     	$module_dir = $this->domain_dir . DIRECTORY_SEPARATOR . $data['module_name'];

		// Le namespace
		$namespacename = "App\\Domain\\" . $data['module_name'] . "\\Models";

		// On récupère le contenu du fichier modèle
		$model_tpl = file_get_contents($this->model_file);

		// Si le dossier des modules n'existe pas, on le crée.
		if (!file_exists($this->domain_dir))
		{
			@mkdir($this->domain_dir, 0777);
		}

		if (!file_exists($module_dir))
		{
			@mkdir($module_dir, 0777);
		}

		// Dossier des modèles du module
		$module_model_dir = $this->domain_dir . DIRECTORY_SEPARATOR . $data['module_name'] . "\\Models";

		// On crée le dossier des modèles dans le module
		@mkdir($module_model_dir, 0700);

		$model_tpl = str_replace('{{namespace}}', $namespacename, $model_tpl);
		$model_tpl = str_replace('{{classname}}', $data['model_name'], $model_tpl);
		$model_tpl = str_replace('{{author}}', $data['author_name'], $model_tpl);
		$model_tpl = str_replace('{{table}}', $data['table_name'], $model_tpl);
		$model_tpl = str_replace('{{properties}}', $this->makeProperties($this->properties), $model_tpl);
		$model_tpl = str_replace('{{methods}}', $this->makeMethods($this->properties), $model_tpl);

		// Nom du fichier
		$filename = $module_model_dir . DIRECTORY_SEPARATOR . $data['model_name'];

		// Nouveau fichier
		$new_model_tpl = file_put_contents($filename . ".php", $model_tpl);

		// On vérifie qu'il a bien créé le contrôleur et/ou sa vue
		if ($new_model_tpl === false)
		{
			throw new RuntimeException('La création du modèle a échoué.');
		}
	}

	/**
	* 	Cette méthode permet de créer une "méthode" dans le fichier du modèle.
	*	
	*	@param array $properties				Propriétés du modèle
	*	
	* 	@return string
	**/
	public function makeMethods(array $properties): string
	{		
		$code = "";

		if (!is_array($properties))
		{
			return $this->io->error("Cette méthode doit recevoir un tableau en paramètre.");
		}

		foreach ($properties as $property)
		{
			$field_name = $property['field_name'];
			$field_type = $property['field_type'];
			$method_name = ucfirst($field_name);

			$code .= "/**\n\t";
			$code .= "*   Cette méthode permet de récupérer la valeur de " . $field_name . "\n\t";
			$code .= "*   \n\t";
			$code .= "*   @return " . $field_type . "\n\t";
			$code .= "**/\n\t";
			$code .= "public function Get" . $this->propertyToMethod($method_name) . "(): " . $field_type . "\n\t";
			$code .= "{\n\t";
			$code .= "	return $" . "this->" . $field_name . ";\n\t";
			$code .= "}\n\n\t";
		}

		return $code;
	}

	/**
	* 	Cette méthode retourne la liste des propriétés
	*
	*	@param array $properties				Propriétés du modèle
	*
	* 	@return string
	**/
	public function makeProperties(array $properties): string
	{
		$data = "";

		if (!is_array($properties))
		{
			return $this->io->error("Cette méthode doit recevoir un tableau en paramètre.");
		}

		foreach($properties as $property)
		{
			$data .= 'protected ' . $property['field_type'] . ' $' . $property['field_name'] . ";		// Propriété :: " . $property['field_name'] . "\n\t";
		}

		return $data;
	}

	/**
    *   Cette méthode permet de retourner la question suivante.
	*
	*	@param array $fields					Champs
	*	@param bool $is_first_field				Si c'est le 1er champ
	*	
	*	@return array
    **/
    public function askForNextField(array $fields, bool $is_first_field)
    {
        if ($is_first_field) 
        {
            $question_text = "Nouvelle propriété : Donnez-lui un nom (appuyez <comment><retour></comment> pour arrêter d'ajouter des champs)";
        } 
        else 
        {
            $question_text = "Ajouter une autre propriété ? Tapez le nom de la propriété  (ou appuyez <comment><retour></comment> pour arrêter d'ajouter des champs)";
        }

		$field_name = $this->io->ask($question_text, null, function($name) use ($fields) 
		{
			if (!$name) 
			{
				return $name;
			}

			if (in_array($name, $fields)) 
			{
				throw new InvalidArgumentException(sprintf("La propriété « %s » existe déjà.", $name));
			}

			return $name;
		});

		if (!$field_name) 
		{
            return null;
        }

        $default_type = 'string';

		if ('_at' === $suffix = substr($field_name, -3) || '_AT' === $suffix = substr($field_name, -3)) 
		{
            $default_type = 'DateTime';
        } 
		elseif ('_id' === $suffix) 
		{
            $default_type = 'int';
        } 
		elseif (0 === strpos($field_name, 'is_')) 
		{
            $default_type = 'bool';
        } 
		elseif (0 === strpos($field_name, 'has_')) 
		{
            $default_type = 'bool';
        } 

		$type = null;

		// Tous les types
		$all_valid_types = ['string', 'int', 'bool', 'array', 'float', 'DateTime'];

		while (null === $type) 
		{
			$question = new Question("Type de champ (tapez <comment>?</comment> pour voir tous les types)", $default_type);
            $question->setAutocompleterValues($all_valid_types);
            $type = $this->io->askQuestion($question);

			if ($type === '?') 
			{
				$this->io->block($all_valid_types);	
                $this->io->writeln('');

                $type = null;
            } 
			elseif (!in_array($type, $all_valid_types)) 
			{
                $this->io->error(sprintf('Le type « %s » est invalide.', $type));
                $this->io->writeln('');

                $type = null;
            }
		}

		$data = ['field_name' => $field_name, 'field_type' => $type];

        return $data;
    }
}

?>