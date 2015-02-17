<?php

namespace InstallerModule\Command;

use InstallerModule\MVCStore;
use InstallerModule\Generator\DBALGenerator;
use InstallerModule\Generator\ORMGenerator;
use InstallerModule\Generator\PDOGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Description of CreateModelCommand
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class GenerateModelCommand extends Command
{
    
    static $types = array(
        'integer', 'string', 'array', 'boolean', 'text', 'datetime'
    );
    
    static $defaultFields = array(
        'firstname', 'lastname', 'email', 'created_at', 'updated_at', 'gender',
        'id', 'title', 'description', 'date', 'author_id', 'author', 'category_id',
        'category'
    );


    public function __construct()
    {
        parent::__construct('app:generate:model');
    }
    
    protected function configure()
    {
        $this
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'The model/entity name to generate the class (shortcut notation)')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Use the format for configuration files (php, xml, yml, or annotation)', 'annotation')
            ->addOption('fields', null, InputOption::VALUE_REQUIRED, 'The fields to create with the new entity')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Model type (dbal, orm, or pdo)', 'pdo')
            ->addOption('with-repository', null, InputOption::VALUE_NONE, 'Whether to generate the entity repository or not')
            ->setAliases(array('app:generate:model'))
            ->setDescription('Create a model database.')
            ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # static mvc
        $mvc = MVCStore::retrieve('mvc');
        # get helper set
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            #$question = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm generation', 'yes', '?'), true);
            $question = new ConfirmationQuestion('Do you confirm generation? (yes or no): yes ', true);
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }
        
        $modelName = Validators::validateEntityName($input->getOption('name'));
        list($module, $modelName) = $this->parseShortcutNotation($modelName);
        $format = Validators::validateFormat($input->getOption('format'));
        $fields = $this->parseFields($input->getOption('fields'));
        
        $output->writeln(array(
            '',
            $this->getHelperSet()->get('formatter')->formatBlock('Entity generation', 'bg=blue;fg=white', true),
            '',
        ));
        
        $module = $mvc->getModule($module);
        
        if ('dbal' == strtolower($input->getOption('type'))) {
            $generator = new DBALGenerator();
            $generator->generate($module, $modelName, array($fields));
        } else if ('orm' == strtolower($input->getOption('type'))) {
            $generator = new ORMGenerator();
        } else {
            $generator = new PDOGenerator();
            $generator->generate($module, $modelName, array($fields));
        }
        
        $output->writeln('Generating the entity code: <info>OK</info>');
        
        $output->writeln(array(
            '',
            $this->getHelperSet()->get('formatter')->formatBlock('You can now start using the generated code!', 'bg=blue;fg=white', true),
            '',
        ));
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        # static mvc
        $mvc = MVCStore::retrieve('mvc');
        # get helper set
        $questionHelper = $this->getQuestionHelper();
        
        $output->writeln(array(
            '',
            'This command helps you generate your App models.',
            '',
            'First, you need to give the model type you want to generate',
            '',
            'Second, you need to give the model/entity name you want to generate.',
            'You must use the shortcut notation like <comment>AppModule:Post</comment>.',
            '',
        ));
        
        $modulesNames = array_keys($mvc->getModules());
        $modelTypes = array('dbal', 'orm', 'pdo');
        
        $question = new Question(sprintf('The Model type: <info>[%s]</info> ', $input->getOption('type')), $input->getOption('type'));
        $question->setAutocompleterValues($modelTypes);
        $modelType = $questionHelper->ask($input, $output, $question);
        # Sets type
        $input->setOption('type', $modelType);
        
        while (true) {
            $question = new Question(sprintf('The Model/Entity shortcut name: <info>[%s]</info> ', $input->getOption('name')), $input->getOption('name'));
            $question->setValidator(array('InstallerModule\Command\Validators', 'validateEntityName'));
            $question->setAutocompleterValues($modulesNames);
            $modelName = $questionHelper->ask($input, $output, $question);

            list($module, $modelName) = $this->parseShortcutNotation($modelName);

            // check reserved words
            /*if ($this->getGenerator()->isReservedKeyword($modelName)) {
                $output->writeln(sprintf('<bg=red> "%s" is a reserved word</>.', $modelName));
                continue;
            }*/

            try {
                $m = $mvc->getModule($module);

                // validate model type
                if ('orm' == strtolower($input->getOption('name')) && !file_exists($m->getPath() . '/Entity/' . str_replace('\\', '/', $modelName) . '.php')) {
                    break;
                } else if (!file_exists($m->getPath() . '/Model/' . str_replace('\\', '/', $modelName) . '.php')) {
                    break;
                }

                $output->writeln(sprintf('<bg=red>Model/Entity "%s:%s" already exists</>.', $module, $modelName));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Module "%s" does not exist.</>', $module));
            }
        }
        
        $input->setOption('name', $module . ':' . $modelName);
        
        if ('orm' == $modelType) {
            // format
            $output->writeln(array(
                '',
                'Determine the format to use for the mapping information.',
                '',
            ));

            $formats = array('yml', 'xml', 'php', 'annotation');

            $question = new Question(sprintf('Configuration format (yml, xml, php, or annotation): <info>[%s]</info>', $input->getOption('format')), $input->getOption('format'));
            $question->setValidator(array('InstallerModule\Command\Validators', 'validateFormat'));
            $question->setAutocompleterValues($formats);
            $format = $questionHelper->ask($input, $output, $question);
            $input->setOption('format', $format);
        }
        
        # fields
        $input->setOption('fields', $this->addFields($input, $output, $questionHelper));
        
        # repository?
        if ('orm' == $modelType) {
            $output->writeln('');
            $question = new ConfirmationQuestion('Do you want to generate an empty repository class? (yes or no): [yes] ', true);
            $withRepository = $questionHelper->ask($input, $output, $question);
            $input->setOption('with-repository', $withRepository);
        }
        
        # summary messages
        $sumaryMessages = array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf("You are going to generate a \"<info>%s:%s</info>\" %s model/entity", $module, $modelName, strtoupper($modelType))
        );
        # Format to model type orm
        if ('orm' == $modelType) {
            $sumaryMessages[] = sprintf("using the \"<info>%s</info>\" format.", $format);
        }
        $sumaryMessages[] = '';
        
        # Summary
        $output->writeln($sumaryMessages);
    }
    
    private function addFields(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $fields = $this->parseFields($input->getOption('fields'));
        $output->writeln(array(
            '',
            'Instead of starting with a blank entity, you can add some fields now.',
            'Note that the primary key will be added automatically (named <comment>id</comment>).',
            '',
        ));
        $output->write('<info>Available types:</info> ');

        $types = self::$types;
        $count = 20;
        foreach ($types as $i => $type) {
            if ($count > 50) {
                $count = 0;
                $output->writeln('');
            }
            $count += strlen($type);
            $output->write(sprintf('<comment>%s</comment>', $type));
            if (count($types) != $i + 1) {
                $output->write(', ');
            } else {
                $output->write('.');
            }
        }
        $output->writeln('');

        $lengthValidator = function ($length) {
            if (!$length) {
                return $length;
            }

            $result = filter_var($length, FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1),
            ));

            if (false === $result) {
                throw new \InvalidArgumentException(sprintf('Invalid length "%s".', $length));
            }

            return $length;
        };
        
        while (true) {
            $output->writeln('');
            $question = new Question(sprintf('New field name (press <return> to stop adding fields): ', null), null);
            $question->setAutocompleterValues(self::$defaultFields);
            $columnName = $questionHelper->ask($input, $output, $question);
            if (!$columnName) {
                break;
            }

            $defaultType = 'string';

            // try to guess the type by the column name prefix/suffix
            if (substr($columnName, -3) == '_at') {
                $defaultType = 'datetime';
            } elseif (substr($columnName, -3) == '_id') {
                $defaultType = 'integer';
            } elseif (substr($columnName, 0, 2) == 'id') {
                $defaultType = 'integer';
            } elseif (substr($columnName, 0, 3) == 'is_') {
                $defaultType = 'boolean';
            } elseif (substr($columnName, 0, 4) == 'has_') {
                $defaultType = 'boolean';
            }

            $question = new Question(sprintf('Field type: <info>[%s]</info> ', $defaultType), $defaultType);
            $question->setAutocompleterValues($types);
            $type = $questionHelper->ask($input, $output, $question);

            $data = array('columnName' => $columnName, 'fieldName' => lcfirst($this->camelize($columnName)), 'type' => $type);

            if ($type == 'string') {
                $question = new Question(sprintf('Field length: <info>[%d]</info> ', 255), 255);
                $question->setValidator($lengthValidator);
                $data['length'] = $questionHelper->ask($input, $output, $question);
            }

            $fields[$columnName] = $data;
        }

        return $fields;
    }
    
    /**
     * Camelizes a string.
     *
     * @param string $id A string to camelize
     *
     * @return string The camelized string
     */
    public function camelize($id)
    {
        return strtr(ucwords(strtr($id, array('_' => ' ', '.' => '_ ', '\\' => '_ '))), array(' ' => ''));
    }
    
    /**
     * 
     * @return QuestionHelper
     */
    protected function getQuestionHelper()
    {
        return $this->getHelperSet()->get('question');
    }
    
    private function parseFields($input)
    {
        if (is_array($input)) {
            return $input;
        }

        $fields = array();
        foreach (explode(' ', $input) as $value) {
            $elements = explode(':', $value);
            $name = $elements[0];
            if (strlen($name)) {
                $type = isset($elements[1]) ? $elements[1] : 'string';
                preg_match_all('/(.*)\((.*)\)/', $type, $matches);
                $type = isset($matches[1][0]) ? $matches[1][0] : $type;
                $length = isset($matches[2][0]) ? $matches[2][0] : null;

                $fields[$name] = array('fieldName' => $name, 'type' => $type, 'length' => $length);
            }
        }

        return $fields;
    }
    
    protected function parseShortcutNotation($shortcut)
    {
        $modelName = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($modelName, ':')) {
            throw new \InvalidArgumentException(sprintf('The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $modelName));
        }

        return array(substr($modelName, 0, $pos), substr($modelName, $pos + 1));
    }
}
