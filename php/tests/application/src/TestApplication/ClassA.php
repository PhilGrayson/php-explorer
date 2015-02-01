<?php
namespace TestApplication;

use TestApplication\ClassB as AliasB;

class ClassA extends ClassB
{
    protected $fieldA;
    protected $fieldB;

    public function __construct(ClassA $fieldA, ClassB $fieldB)
    {
        $this->fieldA = $fieldA;
        $this->fieldB = $fieldB;
    }

    public function methodA(ClassA $classA, ClassB $classB, AliasB $aliasB)
    {
        $classA->methodA();
        $classB->methodB();
        $aliasB->methodB();
        $this->fieldA->methodA();
        $this->methodA2();
        $this->methodB();
        $this->methodB2();
    }

    public function methodA2()
    {
    }

    public function methodB2()
    {
    }
}
