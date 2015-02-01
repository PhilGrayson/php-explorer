<?php
namespace PhpExplorer;

use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class NameResolverVisitor extends NameResolver
{
    /**
     * @extends
     */
    protected function resolveClassName(Name $name)
    {
        // don't resolve special class names
        if (in_array(strtolower($name), array('self', 'parent', 'static'))) {
            if (!$name->isUnqualified()) {
                throw new Error(
                    sprintf("'\\%s' is an invalid class name", $name->toString()),
                    $name->getLine()
                );
            }
            return $name;
        }
        // fully qualified names are already resolved
        if ($name->isFullyQualified()) {
            return $name;
        }
        $aliasName = strtolower($name->getFirst());
        if (!$name->isRelative() && isset($this->aliases[Stmt\Use_::TYPE_NORMAL][$aliasName])) {
            // Custom functionality: Save original alias
            $name->setAttribute('alias', $name->getFirst());
            // resolve aliases (for non-relative names)
            $name->setFirst($this->aliases[Stmt\Use_::TYPE_NORMAL][$aliasName]);
        } elseif (null !== $this->namespace) {
            // if no alias exists prepend current namespace
            $name->prepend($this->namespace);
        }

        return new Name\FullyQualified($name->parts, $name->getAttributes());
    }
}
