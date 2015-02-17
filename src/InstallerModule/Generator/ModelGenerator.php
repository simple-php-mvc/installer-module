<?php

namespace InstallerModule\Generator;

/**
 * Description of ModelGenerator
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
abstract class ModelGenerator extends AbstractGenerator
{
    
    /**
     * @var string
     */
    protected static $getMethodTemplate =
'<spaces>/**
<spaces> * <description>
<spaces> *
<spaces> * @return <variableType>
<spaces> */
<spaces>public function <methodName>()
<spaces>{
<spaces><spaces>return $this-><fieldName>;
<spaces>}';

    /**
     * @var string
     */
    protected static $setMethodTemplate =
'<spaces>/**
<spaces> * <description>
<spaces> *
<spaces> * @param <variableType> $<variableName>
<spaces> * @return <modelClass>
<spaces> */
<spaces>public function <methodName>($<variableName>)
<spaces>{
<spaces><spaces>$this-><fieldName> = $<variableName>;

<spaces><spaces>return $this;
<spaces>}';
    
    /**
     * Generate array properties, getters and setters
     * 
     * @param string $modelName
     * @param array $properties
     * @return string
     */
    protected function generateBody($modelName, array $properties)
    {
        $linesProperties = array("");
        $linesMethods    = array("");
        
        foreach ($properties as $field) {
            foreach ($field as $property) {
                $linesProperties[] = "    /**";
                $linesProperties[] = "     * " . $property['columnName'];
                $linesProperties[] = "     * ";
                $linesProperties[] = "     * @var $property[type] $$property[fieldName]";
                $linesProperties[] = "     */";
                $linesProperties[] = "    protected $$property[fieldName];";
                $linesProperties[] = "";

                # Getter Property
                $linesMethods[] = str_replace(array(
                    '<description>', 
                    '<fieldName>', 
                    '<variableType>', 
                    '<methodName>',
                    '<spaces>'
                ), array(
                    'Get ' . $property['columnName'], 
                    $property['fieldName'], 
                    $property['type'], 
                    'get' . ucwords($property['fieldName']),
                    "    "
                ), self::$getMethodTemplate);
                $linesMethods[] = "";
                # Setter Property
                $linesMethods[] = str_replace(array(
                    '<description>',
                    '<fieldName>',
                    '<variableType>',
                    '<variableName>', 
                    '<modelClass>',
                    '<methodName>',
                    '<spaces>'
                ), array(
                    'Set ' . $property['columnName'], 
                    $property['fieldName'], 
                    $property['type'],
                    $property['fieldName'],
                    $modelName,
                    'set' . ucwords($property['fieldName']),
                    "    "
                ), self::$setMethodTemplate);
                $linesMethods[] = "";
            }
        }
        
        return implode("\n", $linesProperties) . implode("\n", $linesMethods);
    }
    
    /**
     * Genate doc block class
     * 
     * @param string $modelName
     * @return string
     */
    protected function generateDocBlock($modelName)
    {
        $lines = array();
        $lines[] = '/**';
        $lines[] = ' * ' . $modelName;
        $lines[] = ' */';

        return implode("\n", $lines);
    }
}
