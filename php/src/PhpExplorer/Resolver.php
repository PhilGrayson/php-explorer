<?php
namespace PhpExplorer;

class Resolver
{
    /**
     * Side-effects: Adds a function to the autoload queue
     */
    public function resolveUnderCursor($currentDirectory, $fileContents, $lineNumber, $word)
    {
        $this->discoverAutoload($currentDirectory);

        $parser    = new \PhpParser\Parser(new \PhpParser\Lexer\Emulative);
        $traverser = new \PhpParser\NodeTraverser;

        try {
            $parsedWord = $parser->parse("<?php $word;");
        } catch (PhpParser\Error $e) {
            return false;
        }

        $visitor = new ExplorerVisitor;
        $visitor->setLineToFind($lineNumber)
                ->setNodeFind($parsedWord[0]);

        $traverser->addVisitor(new NameResolverVisitor);
        $traverser->addVisitor($visitor);

        try {
            $fileParse = $parser->parse($fileContents);
            $fileParse = $traverser->traverse($fileParse);
        } catch (PhpParser\Error $e) {
            return false;
        }

        $className = $visitor->getClassName();
        if (empty($className)) {
            return false;
        }

        $reflectionClass = new \ReflectionClass($className);

        $methodName = $visitor->getMethodName();
        if ($reflectionClass->hasMethod($methodName)) {
            $reflectionFunction = $reflectionClass->getMethod($methodName);
            return array(
                'fileName' => $reflectionFunction->getFileName(),
                'line'     => $reflectionFunction->getStartLine(),
            );
        }

        return array(
            'fileName' => $reflectionClass->getFileName(),
            'line'     => $reflectionClass->getStartLine(),
        );
    }

    public static function parseCursorWord($cursorWord)
    {
        if ($pos = strpos($cursorWord, '(')) {
            // $this->field->function($argment, -> $this->field->function()
            $cursorWord = substr($cursorWord, 0, $pos + 1) . ')';
        }

        if (0 === strpos($cursorWord, '\\')) {
            $cursorWord = substr($cursorWord, 1);
        }

        return $cursorWord;
    }

    protected function discoverAutoload($directory)
    {
        $found = false;
        while (!$found && $directory != pathinfo($directory, PATHINFO_DIRNAME)) {
            $directory = pathinfo($directory, PATHINFO_DIRNAME);
            if (is_file($directory . '/vendor/autoload.php')) {
                @require $directory . '/vendor/autoload.php';
                return true;
            }
        }
        return $found;
    }
}
