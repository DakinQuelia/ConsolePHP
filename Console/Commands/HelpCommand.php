<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier     : Console\Commands\HelpCommand.php     		            ||
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

class HelpCommand extends Command
{
    /**
    *   Cette méthode permet de configurer la commande.
    **/
    protected function configure()
    {
        $this->ignoreValidationErrors();
        $this->setName('help');
        $this->setDefinition([
            new InputArgument('command_name', InputArgument::OPTIONAL, "Le nom de la commande", 'help'),
            new InputOption('format', null, InputOption::VALUE_REQUIRED, "Le format de sortie (txt, xml, json, ou md)", 'txt'),
            new InputOption('raw', null, InputOption::VALUE_NONE, "Pour afficher l'aide de la commande"),
        ]);
        $this->setDescription("Affiche l'aide d'une commande");
        $this->setHelp(<<<'EOF'
La commande <info>%command.name%</info> affiche l'aide pour une commande spécifique :

  <info>%command.full_name% list</info>

Vous pouvez aussi exporter l'aide dans un autre format  en utilisant l'option <comment>--format</comment> :

  <info>%command.full_name% --format=xml list</info>

Pour afficher la liste des commandes disponibles, veuillez utiliser la commande <info>list</info>.
EOF
        );
    }

    /**
    *   Cette méthode définit une commande.
    **/
    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

    /**
    *   Cette méthode permet d'exécuter la commande.
    **/
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->command) 
        {
            $this->command = $this->getApplication()->find($input->getArgument('command_name'));
        }

        $helper = new DescriptorHelper();
        $helper->describe($output, $this->command, [
            'format' => $input->getOption('format'),
            'raw_text' => $input->getOption('raw'),
        ]);

        $this->command = null;

        return 0;
    }

    /**
    *   Cette méthode permet d'auto-compléter la commande.
    **/
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('command_name')) 
        {
            $descriptor = new ApplicationDescription($this->getApplication());
            $suggestions->suggestValues(array_keys($descriptor->getCommands()));

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