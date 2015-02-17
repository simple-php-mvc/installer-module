<?php

namespace InstallerModule\Generator;

use MVC\Module\Module;

/**
 * Description of PDOGenerator
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class PDOGenerator extends ModelGenerator
{
    
    /**
     *
     * @var type 
     */
    protected static $classTemplate = 
'<?php

<namespace>

use MVC\DataBase\Model,
    MVC\DataBase\PDO;

<modelAnnotation>
class <modelClassName> extends Model
{
<construct>
<modelBody>
}
';
    
    /**
     * @var string
     */
    protected static $constructorMethodTemplate =
'<spaces>/**
<spaces> * Constructor
<spaces> */
<spaces>public function __construct(PDO $pdo)
<spaces>{
<spaces><spaces>parent::__construct($pdo, \'<table>\');
<spaces>}
<spaces>';
    
    /**
     * Generate PDO Model
     * 
     * @param Module $module
     * @param string $modelName
     * @param array $arrayValues
     */
    public function generate(Module $module, $modelName, array $arrayValues)
    {
        $modelClass = $module->getNamespace() . '\\Model\\' . $modelName;
        $modelPath  = $module->getPath() . '/Model/' . str_replace('\\', '/', $modelName) . '.php';
        
        $modelCode  = $this->generateCode($module, $modelName, $arrayValues);
        
        if (file_exists($modelPath)) {
            throw new \RuntimeException(sprintf('Model "%s" already exists.', $modelClass));
        }
        
        $this->explorer->mkdir(dirname($modelPath));
        file_put_contents($modelPath, $modelCode);
    }
    
    /**
     * Generate model class code
     * 
     * @param Module $module
     * @param string $modelName
     * @param array $arrayValues
     * @return string
     */
    protected function generateCode(Module $module, $modelName, $arrayValues)
    {
        $replaces = array(
            '<namespace>'       => 'namespace ' . $module->getNamespace() . '\\Model;',
            '<modelAnnotation>' => $this->generateDocBlock($modelName),
            '<modelClassName>'  => $modelName,
            '<construct>'       => self::$constructorMethodTemplate,
            '<modelBody>'       => $this->generateBody($modelName, $arrayValues),
            '<spaces>'          => "    ",
            '<table>'           => strtolower($modelName)
        );
        $classTemplate = str_replace(array_keys($replaces), array_values($replaces), self::$classTemplate);
        return $classTemplate;
    }
    
}
