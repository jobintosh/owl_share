<?php

declare(strict_types=1);

namespace Rector\Nette\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use Rector\Nette\Contract\ValueObject\ParameterArrayInterface;

final class MagicTemplatePropertyCalls implements ParameterArrayInterface
{
    /**
     * @var Node[]
     */
    private $nodesToRemove = [];

    /**
     * @var array<string, Expr>
     */
    private $templateVariables = [];

    /**
     * @var array<string, Assign[]>
     */
    private $conditionalAssigns = [];

    /**
     * @param array<string, Expr> $templateVariables
     * @param Node[] $nodesToRemove
     * @param array<string, Assign[]> $conditionalAssigns
     */
    public function __construct(array $templateVariables, array $nodesToRemove, array $conditionalAssigns)
    {
        $this->templateVariables = $templateVariables;
        $this->nodesToRemove = $nodesToRemove;
        $this->conditionalAssigns = $conditionalAssigns;
    }

    /**
     * @return array<string, Expr>
     */
    public function getTemplateVariables(): array
    {
        return $this->templateVariables;
    }

    /**
     * @return array<string, Assign[]>
     */
    public function getConditionalAssigns(): array
    {
        return $this->conditionalAssigns;
    }

    /**
     * @return string[]
     */
    public function getConditionalVariableNames(): array
    {
        return array_keys($this->conditionalAssigns);
    }

    /**
     * @return Node[]
     */
    public function getNodesToRemove(): array
    {
        return $this->nodesToRemove;
    }
}
