<?php

declare(strict_types=1);

namespace Rector\CodingStyle\Rector\ClassMethod;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\CodingStyle\ValueObject\ObjectMagicMethods;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @sponsor Thanks https://twitter.com/afilina & Zenika (CAN) for sponsoring this rule - visit them on https://zenika.ca/en/en
 *
 * @see \Rector\CodingStyle\Tests\Rector\ClassMethod\RemoveDoubleUnderscoreInMethodNameRector\RemoveDoubleUnderscoreInMethodNameRectorTest
 */
final class RemoveDoubleUnderscoreInMethodNameRector extends AbstractRector
{
    /**
     * @var string
     * @see https://regex101.com/r/oRrhDJ/3
     */
    private const DOUBLE_UNDERSCORE_START_REGEX = '#^__(.+)#';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Non-magic PHP object methods cannot start with "__"',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class SomeClass
{
    public function __getName($anotherObject)
    {
        $anotherObject->__getSurname();
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class SomeClass
{
    public function getName($anotherObject)
    {
        $anotherObject->getSurname();
    }
}
CODE_SAMPLE
                ),

            ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, MethodCall::class, StaticCall::class];
    }

    /**
     * @param ClassMethod|MethodCall|StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        $methodName = $this->getName($node->name);
        if ($methodName === null) {
            return null;
        }

        if (in_array($methodName, ObjectMagicMethods::METHOD_NAMES, true)) {
            return null;
        }

        if (! Strings::match($methodName, self::DOUBLE_UNDERSCORE_START_REGEX)) {
            return null;
        }

        $newName = Strings::substring($methodName, 2);

        if (is_numeric($newName[0])) {
            return null;
        }

        $node->name = new Identifier($newName);

        return $node;
    }
}
