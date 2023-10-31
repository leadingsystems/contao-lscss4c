<?php


namespace LeadingSystems\LSCSS4CBundle\EventListener;


use LeadingSystems\LSCSS4CBundle\Compiler\Compiler;

class GeneratePageListener
{
    private string $projectDir;
    private Compiler $compiler;

    public function __construct(string $projectDir, Compiler $compiler)
    {
        $this->projectDir = $projectDir;
        $this->compiler = $compiler;

}
    public function insertLscss(): void
    {
        $str_pathToOutputFile = $this->compiler->compile();

        if ($str_pathToOutputFile)
        {
            $GLOBALS['TL_CSS'][] = $str_pathToOutputFile;
        }
    }
}