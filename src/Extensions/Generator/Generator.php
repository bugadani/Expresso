<?php

namespace Expresso\Extensions\Generator;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Parser\OperatorParser;
use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Extension;
use Expresso\Extensions\Core\Core;
use Expresso\Extensions\Generator\Nodes\FunctionDefinitionNode;
use Expresso\Extensions\Generator\Nodes\GeneratorArgumentNode;
use Expresso\Extensions\Generator\Nodes\GeneratorBranchNode;
use Expresso\Extensions\Generator\Nodes\GeneratorNode;
use Expresso\Extensions\Lambda\Lambda;

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
    public function getSymbols()
    {
        return ['<-', '{', '}', ',', ';'];
    }

    /**
     * @inheritdoc
     */
    public function addParsers(OperatorParser $parser, CompilerConfiguration $configuration)
    {
        $parserContainer = $parser->getParserContainer();

        $operandParser = $parserContainer->get('operand');
        $expression    = $parserContainer->get('expression');

        $openingBraces = TokenParser::create(Token::SYMBOL, '{');
        $closingBraces = TokenParser::create(Token::SYMBOL, '}');
        $elementOf     = TokenParser::create(Token::SYMBOL, '<-');
        $comma         = TokenParser::create(Token::SYMBOL, ',');
        $separator     = TokenParser::create(Token::SYMBOL, ';');
        $identifier    = TokenParser::create(Token::IDENTIFIER);
        $keyword       = function ($keyword) {
            return TokenParser::create(Token::IDENTIFIER, $keyword);
        };

        $pattern = $identifier;

        $elementOfExpression = $pattern
            ->followedBy($elementOf)
            ->followedBy($expression);

        $filterExpression = $keyword('where')
            ->followedBy($expression);

        $returnArgument = function ($index) {
            return function (array $children) use ($index) {
                return $children[ $index ];
            };
        };

        $generatorSource = $separator
            ->followedBy(
                $filterExpression
                    ->orA($elementOfExpression)
                    ->repeatSeparatedBy($comma)
            )
            ->process($returnArgument(1));

        $generatorExpression = $openingBraces
            ->followedBy($expression)
            ->followedBy(
                $generatorSource
                    ->repeated()
            )
            ->followedBy($closingBraces)
            ->process(
                function (array $children) {
                    list(, $funcBody, $generatorBranches,) = $children;

                    $node = new GeneratorNode(new FunctionDefinitionNode($funcBody));

                    foreach ($generatorBranches as $argumentOrFilterList) {
                        $branch = new GeneratorBranchNode();
                        foreach ($argumentOrFilterList as $argumentOrFilter) {

                            $isFilter = $argumentOrFilter[0] instanceof Token
                                        && $argumentOrFilter[0]->test(Token::IDENTIFIER, 'where');

                            if ($isFilter) {

                                $branch->addFilter($argumentOrFilter[1]);

                            } else {
                                //generator def
                                //second element is the arrow symbol
                                list($argument, , $source) = $argumentOrFilter;
                                $argumentDefinition = new GeneratorArgumentNode($argument, $source);
                                $branch->addArgument($argumentDefinition);
                            }

                        }

                        $node->addBranch($branch);
                    }

                    return $node;
                }
            );

        $parserContainer->set('operand', $operandParser->orA($generatorExpression));
    }

    /**
     * @inheritdoc
     */
    public function getDependencies()
    {
        return [
            Core::class,
            Lambda::class
        ];
    }
}
