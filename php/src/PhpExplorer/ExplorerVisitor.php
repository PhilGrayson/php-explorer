<?php
namespace PhpExplorer;

class ExplorerVisitor extends \PhpParser\NodeVisitorAbstract
{
    protected $nodes = array();
    protected $lineToFind;
    protected $thisClassNode;
    protected $className;
    protected $methodName;
    protected $methodOwner;
    protected $assignmentValue;

    public function setLineToFind($lineToFind)
    {
        $this->lineToFind = $lineToFind;
        return $this;
    }

    public function setNodeFind($node)
    {
        if ($node instanceof \PhpParser\Node\Expr\MethodCall) {
            $this->methodName    = $node->name;
            $this->methodOwner   = $this->getMethodOwner($node->var);
            $this->assignmentValue = $node->var->name;
        }

        if ($node instanceof \PhpParser\Node\Expr\ConstFetch) {
            $this->className = $node->name->toString();
        }

        return $this;
    }

    public function enterNode(\PhpParser\Node $node)
    {
        if ($node instanceof \PhpParser\Node\Stmt\Class_) {
            $this->thisClassNode = $node->namespacedName;
        }

        if (
            $node->getLine() == $this->lineToFind &&
            $node instanceof \PhpParser\Node\Stmt\UseUse
        ) {
            $this->nodes[] = $node->name;
        }

        if (
            $node->getLine() == $this->lineToFind &&
            $node instanceof \PhpParser\Node\Name\FullyQualified
        ) {
            $this->nodes[] = $node;
        }

        if (
            $node instanceof \PhpParser\Node\Expr\Assign &&
            $this->getMethodOwner($node->var) == $this->methodOwner
        ) {
            $this->assignmentValue = $node->expr->name;
        }
    }

    public function leaveNode(\PhpParser\Node $node)
    {
        if ($node instanceof \PhpParser\Node\Stmt\ClassMethod && isset($this->assignmentValue)) {
            foreach ($node->params as $paramNode) {
                if (
                    $paramNode->name == $this->assignmentValue &&
                    $paramNode->type instanceof \PhpParser\Node\Name\FullyQualified
                ) {
                    $this->nodes[] = $paramNode->type;
                }
            }
        }
    }

    public function getClassName()
    {
        if (1 === count($this->nodes)) {
            return $this->nodes[0]->toString();
        }

        foreach ($this->nodes as $node) {
            if ($node->getLast() === $this->className) {
                return $node->toString();
            }

            if (
                $node->hasAttribute('alias') &&
                $node->getAttribute('alias') === $this->className
            ) {
                return $node->toString();
            }
        }

        $names = array_unique(
            array_map(function($node) {
                return $node->toString();
            }, $this->nodes)
        );

        if (1 === count($names)) {
            return $names[0];
        }

        if (isset($this->thisClassNode)) {
            return $this->thisClassNode->toString();
        }
    }

    public function getMethodName()
    {
        return $this->methodName;
    }

    protected function getMethodOwner($node)
    {
        $varNodes = array();

        if ($node instanceof \PhpParser\Node\Expr\Variable) {
            $varNodes[] = $node;
        } elseif ($node instanceof \PhpParser\Node\Expr\PropertyFetch) {
            foreach ($node->var as $subnode) {
                if ($subnode instanceof \PhpParser\Node\Expr\Variable) {
                    $varNodes[] = $subnode;
                }
            }

            $varNodes[] = $node;
        }

        return array_map(function($varNode) {
            return $varNode->name;
        }, $varNodes);
    }
}
