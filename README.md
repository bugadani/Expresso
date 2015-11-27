Todo:
- range in array elements - [1..], [1..3]
- generators - Haskell-ish {x^2 | x <- [2, 4, 6]}  ----> [4, 16, 36] ... handle infinite lists, guard expressions(using callback filters)

 { expression (| generator)* } - filterrel ütközik így (kell egyáltalán a filter?)

- lambdas: \x -> y, \(x, y) -> z --> \ argument list -> func body (\ is needed) (tetszőleges expression lehet)
- constant stack size implementation