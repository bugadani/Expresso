<?php

namespace Expresso\Compiler\Tokenizer;

use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;

class Tokenizer
{
    private static $punctuation = [
        ','  => ',',
        '['  => '[',
        ']'  => ']',
        '('  => '(',
        ')'  => ')',
        '{'  => '{',
        '}'  => '}',
        ':'  => ':',
        '?'  => '?',
        '\\' => '\\',
        '=>' => '=>'
    ];

    private $operators;
    private $expressionPartsPattern;

    public function __construct(array $operatorSymbols)
    {
        $this->operators = array_combine($operatorSymbols, $operatorSymbols);

        $this->expressionPartsPattern = $this->getExpressionPartsPattern();
    }

    private function getExpressionPartsPattern()
    {
        $signs    = ' ';
        $patterns = [
            ':[a-zA-Z_\-0-9]+'      => 16, //:short-string
            '"(?:\\\\.|[^"\\\\])*"' => 21, //double quoted string
            "'(?:\\\\.|[^'\\\\])*'" => 21, //single quoted string
            '(?<!\w)\d+(?:\.\d+)?'  => 20 //number
        ];

        $iterator = new \AppendIterator();
        $iterator->append(
            new \CallbackFilterIterator( //filter out word operators so their names are not reserved
                new \ArrayIterator($this->operators),
                function ($operator) {
                    return !ctype_alpha($operator);
                }
            )
        );
        $iterator->append(new \ArrayIterator(self::$punctuation));

        foreach ($iterator as $symbol) {
            $length = strlen($symbol);
            if ($length === 1) {
                $signs .= $symbol;
            } else {
                if (strpos($symbol, ' ') !== false) {
                    $symbol = "(?<=^|\\W){$symbol}(?=[\\s()\\[\\]]|$)";
                } else {
                    $symbol = preg_quote($symbol, '/');
                }
                $patterns[ $symbol ] = $length;
            }
        }
        arsort($patterns);
        $patterns = implode('|', array_keys($patterns));

        $signs = preg_quote($signs, '/');

        return "/({$patterns}|[{$signs}])/i";
    }

    /**
     * @param $expression
     *
     * @return TokenStream
     */
    public function tokenize($expression)
    {
        return new TokenStream($this->tokenizeExpression($expression));
    }

    /**
     * @param $expression
     *
     * @return \Generator
     */
    private function tokenizeExpression($expression)
    {
        $flags  = PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY;
        $line   = 1;
        $offset = 0;

        $expression = strtr($expression, ["\r" => '']);
        $tokens     = preg_split($this->expressionPartsPattern, $expression, 0, $flags);

        foreach ($tokens as $token) {
            if (!ctype_space($token)) {
                $tokenObject = $this->createToken($token);

                $tokenObject->setPosition($line, $offset);

                yield $tokenObject;
            }

            $lines     = explode("\n", $token);
            $lineCount = count($lines) - 1;
            if ($lineCount === 0) {
                $offset += strlen($lines[0]);
            } else {
                $line += $lineCount;
                $offset = strlen($lines[ $lineCount ]);
            }
        }

        $endToken = new Token(Token::EOF);

        $endToken->setPosition($line, $offset);

        yield $endToken;
    }

    public function createToken($part)
    {
        if (isset(self::$punctuation[ $part ])) {
            $token = new Token(Token::PUNCTUATION, $part);
        } else if (isset($this->operators[ $part ])) {
            $token = new Token(Token::OPERATOR, $part);
        } else if (is_numeric($part)) {
            $number = (float)$part;
            //check whether the number can be represented as an integer
            if (ctype_digit($part) && $number <= PHP_INT_MAX) {
                $number = (int)$part;
            }
            $token = new Token(Token::CONSTANT, $number);
        } else {
            switch ($part[0]) {
                case '"':
                case "'":
                    //strip backslashes from double-slashes and escaped string delimiters
                    $part  = strtr($part, ['\\' . $part[0] => $part[0], '\\\\' => '\\']);
                    $token = new Token(Token::STRING, substr($part, 1, -1));
                    break;

                case ':':
                    $token = new Token(Token::STRING, substr($part, 1));
                    break;

                default:
                    $part = trim($part);
                    switch (strtolower($part)) {
                        case 'null':
                            $token = new Token(Token::CONSTANT, null);
                            break;

                        case 'true':
                            $token = new Token(Token::CONSTANT, true);
                            break;

                        case 'false':
                            $token = new Token(Token::CONSTANT, false);
                            break;

                        default:
                            $token = new Token(Token::IDENTIFIER, $part);
                            break;
                    }
                    break;
            }
        }

        return $token;
    }
}
