<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier     : Console\Command.php     		                        ||
||  Version     : 1.0.0.                                	            ||
||  Auteur(s)   : Dakin Quelia						                    ||
||  ------------------------------------------------------------------  ||
||  Copyright ©2022 Corona CMS - codé par Dakin Quelia                  ||
\*======================================================================*/
namespace Console;    

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
    
class Command extends SymfonyCommand 
{
    protected SymfonyStyle $io;

    /**
    *   Constructeur de la commande 
    **/
    public function __construct()
    {
        parent::__construct();
    }

    /**
	* 	Cette méthode est exécutée après la configuration de la commande pour initialiser des propriétés.
    *
    *   @param InputInterface $input        Entrée de l'utilisateur
    *   @param OutputInterface $output      Sortie en console
    *
    *   @return void
	**/
	protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
    *   Cette méthode permet de retourner une confirmation : Oui / Non
    *   
    *   @param string $question             Question de confirmation  
    *
    *   @return bool
    **/
    public function confirm(string $question): bool
    {
        $confirm = $this->io->ask($question . " (Oui/Non)", "Non");
		$confirm = preg_match('/^(y|yes|oui|o)$/i', $confirm);

        return $confirm;
    }

    /**
    *   Cette méthode permet de nettoyer le chemin relatif du fichier.
    *   
    *   @param string $absolutepath         Chemin du fichier
    *
    *   @return string
    **/
    public function relativizePath($absolutepath): string
    {
        $relativepath = str_replace(getcwd(), '.', $absolutepath);

        return is_dir($absolutepath) ? rtrim($relativepath, '/') . '/' : $relativepath;
    }

    /**
    *   Cette méthode permet de supprimer un dossier.
    *
    *   @param string $dir                  Nom du dossier
    *
    *   @return bool
    **/
    public function deleteDir(string $dir): bool
    {
        return rmdir($dir); 
    }

    /**
    *   Cette méthode permet de vider un dossier.
    *
    *   @param string $dir                  Nom du dossier
    *
    *   @return bool
    **/
    public function clearDir(string $dir, string $ext = null): bool
    {
        if ($ext !== null)
        {
            return array_map('unlink', glob("$dir/*.$ext"));
        }

        $files = array_diff(scandir($dir), array('.', '..')); 

        foreach ($files as $file) 
        { 
            unlink("$dir/$file"); 
        } 
    }

    /**
    *   Cette méthode permet de valider la valuer d'un champ.
    **/
    public function fieldValidator(): void
    {
        // A FAIRE : méthode à créer
    }

    /**
    *   Cette méthode permet transformer un nom de champ en nom de méthode.
    *
    *   @param string $field                Nom du champ
    *
    *   @return string
    **/
    public function propertyToMethod(string $field): string
    {
        $field_explode = explode('_', $field);
        $value = '';

        foreach($field_explode as $f)
        {
            $value .= ucfirst($f);
        }

        unset($f);
        
        return $value;
    }
}

?>