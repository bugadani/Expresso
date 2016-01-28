Expresso
==============

Expresso is an expression evaluation and compiler engine written in PHP. It's main goal is to enable evaluating
both simple and complicated expressions, given as strings.

Expresso is also an extensible language framework.

Usage
-------------

    $expresso = new Expresso();
    $expresso->addExtension(new Core());
    $expresso->addExtension(new Lambda());

    $expresso->execute('a ^ 5', ['a' => 2]);     // returns 32

    $compiled = $expresso->compile('a ^ 5');
    $compiled(6);                                // 64

The Core extension
-------------
Core implements the base language used by Expresso. It contains the basic syntax, like operators, function calls, collection
definitions and defines some functions that can be used in expressions.

### Available operators
 - Arithmetic operators
   - `x + y`
   - `x - y`
   - `x * y`
   - `x / y`
   - `-x`
   - `x div y`: integer divison, divides `x` by `y` and rounds towards 0.
   - `x ^ y`: exponential operator, computes `x` to the `y`-th power
   - `x % y`: returns the remainder of `x` / `y`, where the result has the sign of `x` (i.e. `-2 % 5 = -2`)
   - `x mod y`: returns the  remainder of `x` / `y`, where the result is always positive (i.e. `-2 mod 5 = 3`)
 - Bitwise operators
   - `x b-and y`
   - `x b-or y`
   - `x b-xor y`
   - `x << y`: shifts `x` to the left by `y` bits
   - `x >> y`: shifts `x` to the right by `y` bits, while keeping the sign of `x`
   - `~x`: inverts `x`
 - Comparison operators
   - `x == y`
   - `x != y`
   - `x === y`
   - `x !== y`
   - `x < y`
   - `x <= y`
   - `x > y`
   - `x >= y`
 - Logical operators
   - `x && y`
   - `x || y`
   - `x xor y`
   - `!x`
 - Test operators
   - `x is divisible by y`
   - `x is not divisible by y`
   - `x is even`
   - `x is odd`
   - `x is set`
   - `x is not set`
 - Other operators
   - `x ~`: Concatenates two strings
   - `x..y`: Creates a collection of numbers between `x` and `y`, including both `x` and `y`
   - `x...`: Creates an infinite collection of numbers starting with `x`
   - `x()`
   - `x|y`
   - `x|y()`
   - `x.y`
   - `x?.y`
   - `x.y()`
   - `x?.y()`
   - `x[y]`
   - `x ?: y`
   - `x ? y : z`

### Available functions
 - `count(c)`
 - `join(c, g)`
 - `skip(c, n)`
 - `popcount(n)`
 - `replace(s, x, y)`
 - `replace(s, c)`
 - `reverse(s)`
 - `take(c, n)`

The Lambda extension
-------------
The Lambda extension adds the ability to define lambda expressions and adds some higher order functions.

A lambda expression is defined with the following syntax: `\<arguments> -> <expression>`

    $expresso->evaluate('[1..5]|map(\x -> 2*x)|join(", ")');    //returns "2, 4, 6, 8, 10"

### Available functions
 - `all`: returns true if all members of a collection match the given predicate
 - `any`: returns true if any member of a collection match the given predicate
 - `filter`: filters elements of a collection using a callback function
 - `first`: takes the first element of a collection
 - `fold`: reduces a collection to a single value using a callback function
 - `map`: maps every element of a collection to the return value of a function

TODO
-------------
 - documentation
 - compilation cache
