<?php
namespace PhpExplorerTest;

use PhpExplorer\Resolver;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $resolver;

    public function setUp()
    {
        $this->resolver = new Resolver;
        $this->testAppPath = pathinfo(__DIR__, PATHINFO_DIRNAME) . '/application/';
    }

    /**
     * @dataProvider typehintDataProvider
     */
    public function testResolvesFromTypehint($lineNumber, $word, $answerFile, $answerLine = null)
    {
        $found = $this->resolver->resolveUnderCursor(
            'tests/application/src/TestApplication/ClassA.php',
            file_get_contents('tests/application/src/TestApplication/ClassA.php'),
            $lineNumber,
            $word
        );

        $this->assertInternalType('array', $found);
        $this->assertArrayHasKey('fileName', $found);
        $this->assertArrayHasKey('line', $found);
        $this->assertSame($this->testAppPath . $answerFile, $found['fileName']);

        if (isset($answerLine)) {
            $this->assertSame($answerLine, $found['line']);
        }
    }

    public function typehintDataProvider()
    {
        /**
         * test name -> [line of cursor, word under cursor, file of class, line of declaration]
         */
        return array(
            'class from Use statement' => array(
                4, 'TestApplication\ClassB', 'src/TestApplication/ClassB.php', 4,
            ),
            'class from Use statement alias' => array(
                4, 'AliasB', 'src/TestApplication/ClassB.php', 4,
            ),
            'class from Typehint with multiple method parameters' => array(
                17, 'ClassA', 'src/TestApplication/ClassA.php', 6,
            ),
            'class from Typehint with multiple method parameters' => array(
                17, 'ClassB', 'src/TestApplication/ClassB.php', 4,
            ),
            'class from Aliased Typehint with multiple method parameters' => array(
                17, 'AliasB', 'src/TestApplication/ClassB.php', 4,
            ),
            'class and method line from local variable method call' => array(
                19, '$classA->methodA();', 'src/TestApplication/ClassA.php', 17,
            ),
            'class and method line from local variable method call' => array(
                20, '$classB->methodB();', 'src/TestApplication/ClassB.php', 6,
            ),
            'class and method line from aliased local variable method call' => array(
                21, '$aliasB->methodB();', 'src/TestApplication/ClassB.php', 6,
            ),
            'class and method line from field method call' => array(
                23, '$this->fieldA->methodA();', 'src/TestApplication/ClassA.php', 17,
            ),
            'local method' => array(
                23, '$this->methodA2();', 'src/TestApplication/ClassA.php', 28,
            ),
            'method from inheritence' => array(
                24, '$this->methodB();', 'src/TestApplication/ClassB.php', 6,
            ),
            'overriden method' => array(
                25, '$this->methodB2();', 'src/TestApplication/ClassA.php', 32,
            ),
        );
    }
}
