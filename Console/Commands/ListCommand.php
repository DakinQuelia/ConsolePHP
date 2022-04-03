<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier     : Console\Commands\ListCommand.php     		            ||
||  Version     : 1.0.0.                                	            ||
||  Auteur(s)   : Fabien Potencer / Dakin Quelia						||
||  ------------------------------------------------------------------  ||
||  Copyright ©2022 Corona CMS - codé par Dakin Quelia                  ||
\*======================================================================*/
namespace Console\Commands;

use Console\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Descriptor\ApplicationDescription;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    /**
    *   Cette méthode permet de configurer la commande.
    **/
    protected function configure()
    {
        $this->setName('list');
        $this->setDefinition([
            new InputArgument('namespace', InputArgument::OPTIONAL, "Le nom du namespace"),
            new InputOption('raw', null, InputOption::VALUE_NONE, "Pour générer une liste de commandes"),
            new InputOption('format', null, InputOption::VALUE_REQUIRED, "Le format de sortie (txt, xml, json, ou md)", 'txt'),
            new InputOption('short', null, InputOption::VALUE_NONE, "Pour ignorer la description des arguments des commandes"),
        ]);
        $this->setDescription('Liste des commandes');
        $this->setHelp(<<<'EOF'
La commande <info>%command.name%</info> permet de lister toutes les commandes :

  <info>%command.full_name%</info>

Vous pouvez aussi afficher les commandes pour un namespace spécifique :

  <info>%command.full_name% test</info>

Vous pouvez aussi exporter les informations dans un autre format en utilisant l'option <comment>--format</comment> :

  <info>%command.full_name% --format=xml</info>

Il est aussi possible d'obtenir la liste brute de toutes les commandes (très utile pour intéger dans l'exécuteur de commande) :

  <info>%command.full_name% --raw</info>
EOF
        );
    }

    /**
    *   Cette méthode permet d'exécuter la commande.
    **/
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = new DescriptorHelper();
        $helper->describe($output, $this->getApplication(), [
            'format' => $input->getOption('format'),
            'raw_text' => $input->getOption('raw'),
            'namespace' => $input->getArgument('namespace'),
            'short' => $input->getOption('short'),
        ]);

        return 0;
    }

    /**
    *   Cette méthode permet d'auto-compléter la commande.
    **/
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('namespace')) 
        {
            $descriptor = new ApplicationDescription($this->getApplication());
            $suggestions->suggestValues(array_keys($descriptor->getNamespaces()));

            return;
        }

        if ($input->mustSuggestOptionValuesFor('format')) 
        {
            $helper = new DescriptorHelper();
            $suggestions->suggestValues($helper->getFormats());
        }
    }
}

?>