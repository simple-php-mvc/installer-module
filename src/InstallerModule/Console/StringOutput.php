<?php

namespace InstallerModule\Console;

use Symfony\Component\Console\Output\Output;

/**
 * Description of BufferedOutput
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class StringOutput extends Output
{
    
    /**
     * String buffer
     * 
     * @var string
     */
    protected $buffer = '';
    
    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline)
    {
        $this->buffer .= $message. ($newline? PHP_EOL: '');
    }

    /**
     * Get buffer
     * 
     * @return string
     */
    public function getBuffer()
    {
        return $this->buffer;
    }
}
