<?php

namespace InstallerModule\Generator;

/**
 * Description of ORMGenerator
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class ORMGenerator extends ModelGenerator
{
    
    /**
     *
     * @var type 
     */
    protected static $classTemplate = 
'<?php

<namespace>

use Doctrine\ORM\Mapping as ORM;

<entityAnnotation>
<entityClassName>
{
<entityBody>
}
';
    
    /**
     * @var string
     */
    protected static $addMethodTemplate =
'/**
 * <description>
 *
 * @param <variableType>$<variableName>
 * @return <entity>
 */
public function <methodName>(<methodTypeHint>$<variableName>)
{
<spaces>$this-><fieldName>[] = $<variableName>;

<spaces>return $this;
}';

    /**
     * @var string
     */
    protected static $removeMethodTemplate =
'/**
 * <description>
 *
 * @param <variableType>$<variableName>
 */
public function <methodName>(<methodTypeHint>$<variableName>)
{
<spaces>$this-><fieldName>->removeElement($<variableName>);
}';

    /**
     * @var string
     */
    protected static $lifecycleCallbackMethodTemplate =
'/**
 * @<name>
 */
public function <methodName>()
{
<spaces>// Add your code here
}';

    /**
     * @var string
     */
    protected static $constructorMethodTemplate =
'/**
 * Constructor
 */
public function __construct()
{
<spaces><collections>
}
';
}
