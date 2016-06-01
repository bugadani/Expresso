<?php

namespace Expresso\Extensions\Generator;

use Expresso\Compiler\Parser\GrammarParser;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Parser\AbstractParser;

use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Extension;
use Expresso\Extensions\Core\Core;

use Expresso\Extensions\Generator\Nodes\FunctionDefinitionNode;
use Expresso\Extensions\Generator\Nodes\GeneratorBranchNode;
use Expresso\Extensions\Generator\Nodes\GeneratorNode;

/**
 * Class Generator defines an extension that implements list comprehension expressions.
 *
 * Current EBNF:
 * lce          = '{' , expression , {';' , branch} '}'
 * branch       = element , {',' , element},
 * element      = filter | elementOf
 * filter       = 'where' , expression
 * elementOf    = pattern , '->' , expression
 * pattern      = identifier
 *
 * @package Expresso\Extensions\Generator
 */
class Generator extends Extension
{
    /**
     * @inheritdoc
     */
    public function getSymbols() : array
    {
        return ['<-', ',', ';'];
    }

    /**
     * @inheritdoc
     */
    public function addParsers(GrammarParser $parser, CompilerConfiguration $configuration)
    {
        $parserContainer = $parser->getContainer();

        $expression   = $parserContainer->get('expression');
        $listSubtypes = $parserContainer->get('listSubtypes');

        $elementOf       = TokenParser::create(Token::SYMBOL, '<-');
        $comma           = TokenParser::create(Token::SYMBOL, ',');
        $branchSeparator = TokenParser::create(Token::SYMBOL, ';');
        $identifier      = TokenParser::create(Token::IDENTIFIER);
        $keyword         = function ($keyword) {
            return TokenParser::create(Token::IDENTIFIER, $keyword);
        };

        $pattern = $identifier;

        $elementOfExpression = $pattern
            ->followedBy($elementOf)
            ->followedBy($expression)
            ->process(function (array $children) {
                //discard the arrow
                return [$children[0]->getValue(), $children[2]];
            });

        /** @var AbstractParser $filterExpression */
        $filterExpression = $keyword('where')
            ->followedBy($expression);

        $generatorSource = $filterExpression
            ->orA($elementOfExpression)
            ->repeatSeparatedBy($comma);

        $newListParser = $listSubtypes->orA(
            $keyword('for')
                ->followedBy(
                    $generatorSource->repeatSeparatedBy($branchSeparator)
                )
                ->process(function (array $children, AbstractParser $parent) {
                    $parent->getParent()
                           ->tempProcess(function (array $children) {
                               list($funcBody, $generatorBranches) = $children;
                               $node = new GeneratorNode(new FunctionDefinitionNode($funcBody));

                               foreach ($generatorBranches as $generatorBranch) {
                                   $branch = new GeneratorBranchNode();
                                   foreach ($generatorBranch as $argumentOrFilter) {

                                       $isFilter = $argumentOrFilter[0] instanceof Token
                                           && $argumentOrFilter[0]->test(Token::IDENTIFIER, 'where');

                                       if ($isFilter) {
                                           $branch->addFilter($argumentOrFilter[1]);
                                       } else {
                                           //generator def
                                           list($argument, $source) = $argumentOrFilter;
                                           $branch->addArgument($argument, $source);
                                       }

                                   }

                                   $node->addBranch($branch);
                               }

                               return $node;
                           });

                    return $children[1];
                })
        );
        $parserContainer->set('listSubtypes', $newListParser);
    }

    /**
     * @inheritdoc
     */
    public function getDependencies() : array
    {
        return [
            Core::class
        ];
    }
}
