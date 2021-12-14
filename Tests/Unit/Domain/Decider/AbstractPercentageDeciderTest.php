<?php

namespace Wysiwyg\ABTesting\Tests\Unit\Domain\Decider;

use Neos\Flow\Tests\UnitTestCase;

class AbstractPercentageDeciderTest extends UnitTestCase
{

    /**
     * DataProvider: A/B: 100 - 0
     *
     * @return array
     */
    public function hundredPercentAProvider()
    {
        return [
            [
                'a' => 100,
                'b' => 0
            ]
        ];
    }

    /**
     * DataProvider: A/B: 0 - 100
     *
     * @return array
     */
    public function hundredPercentBProvider()
    {
        return [
            [
                'a' => 0,
                'b' => 100
            ]
        ];
    }

    /**
     * DataProvider: A/B: 30 - 70
     *
     * @return array
     */
    public function thirtyASeventyBProvider()
    {
        return [
            [
                'a' => 30,
                'b' => 70
            ]
        ];
    }

    /**
     * DataProvider: A/B/C: 0 - 0 - 100
     *
     * @return array
     */
    public function hundredPercentCProvider()
    {
        return [
            [
                'a' => 0,
                'b' => 0,
                'c' => 100
            ]
        ];
    }

    /**
     * DataProvider: A/B/C: 0 - 50 - 50
     *
     * @return array
     */
    public function zeroAFiftyBFiftyCProvider()
    {
        return [
            [
                'a' => 0,
                'b' => 50,
                'c' => 50
            ]
        ];
    }

    /**
     * DataProvider: A/B/C: 50 - 0 - 50
     *
     * @return array
     */
    public function fiftyAZeroBFiftyCProvider()
    {
        return [
            [
                'a' => 50,
                'b' => 0,
                'c' => 50
            ]
        ];
    }

    /**
     * DataProvider: A/B/C: 33 - 33 - 33
     *
     * @return array
     */
    public function balancedAbcProvider()
    {
        return [
            [
                'a' => 33,
                'b' => 33,
                'c' => 33
            ]
        ];
    }

    /**
     * DataProvider, where a is never 0%
     *
     * Provided Test cases:
     *  a < b, c = 0
     *  a < b, a > c
     *  a > b, a < c
     *  a > b, a > c
     *  a = b, c = 0
     *  a > b, c = 0
     *  a > c, b = 0
     *  a < c, b = 0
     *  a = c, b = 0
     *  a = 100, b = 0, c = 0
     *
     * @return array
     */
    public function unbalancedAbcProviderForA()
    {
        return [
            // a < b, c = 0
            [
                'a' => 5,
                'b' => 95,
                'c' => 0
            ],
            // a < b, a > c
            [
                'a' => 20,
                'b' => 70,
                'c' => 10
            ],
            // a > b, a < c
            [
                'a' => 20,
                'b' => 10,
                'c' => 70
            ],
            // a > b, a > c
            [
                'a' => 50,
                'b' => 10,
                'c' => 40
            ],
            // a = b, c = 0
            [
                'a' => 50,
                'b' => 50,
                'c' => 0
            ],
            // a > b, c = 0
            [
                'a' => 70,
                'b' => 30,
                'c' => 0
            ],
            // a > c, b = 0
            [
                'a' => 70,
                'b' => 0,
                'c' => 30
            ],
            // a < c, b = 0
            [
                'a' => 30,
                'b' => 0,
                'c' => 70
            ],
            // a = c, b = 0
            [
                'a' => 50,
                'b' => 0,
                'c' => 50
            ],
            // a = 100, b = 0, c = 0
            [
                'a' => 100,
                'b' => 0,
                'c' => 0
            ]
        ];
    }


    /**
     * DataProvider, where b is never 0%
     *
     * Provided Test cases:
     *  b > a, b > c
     *  b < a, b > c
     *  b > a, b < c
     *  b < a, b < c
     *  b > a, c = 0
     *  b < a, c = 0
     *  b = a, c = 0
     *  b = c, a = 0
     *  b > c, a = 0
     *  b < c, a = 0
     *  b = 100, a = 0, c = 0
     *
     * @return array
     */
    public function unbalancedAbcProviderForB()
    {
        return [
            //  b > a, b > c
            [
                'a' => 10,
                'b' => 70,
                'c' => 20
            ],
            // b < a, b > c
            [

                'a' => 10,
                'b' => 50,
                'c' => 40
            ],
            // b > a, b < c
            [
                'a' => 10,
                'b' => 20,
                'c' => 70
            ],
            // b < a, b < c
            [
                'a' => 20,
                'b' => 30,
                'c' => 50
            ],
            // b > a, c = 0
            [
                'a' => 30,
                'b' => 70,
                'c' => 0
            ],
            // b < a, c = 0
            [
                'a' => 70,
                'b' => 30,
                'c' => 0
            ],
            // b > c, a = 0
            [
                'a' => 0,
                'b' => 60,
                'c' => 40
            ],
            // b < c, a = 0
            [
                'a' => 0,
                'b' => 40,
                'c' => 60
            ],
            // b = a, c = 0
            [
                'a' => 50,
                'b' => 50,
                'c' => 0
            ],
            // b = c, a = 0
            [
                'a' => 0,
                'b' => 50,
                'c' => 50
            ],
            // b = 100, a = 0, c = 0
            [
                'a' => 0,
                'b' => 100,
                'c' => 0
            ]
        ];
    }

    /**
     * DataProvider, where c is never 0%
     *
     * Provided data for Test cases:
     *  c < a, b < a
     *  c > a, b < a
     *  c < a, b > a
     *  c > a, b > a
     *  c > a, b = 0
     *  c < a, b = 0
     *  c < b, a = 0
     *  c > b, a = 0
     *  c = a, b = 0
     *  c = b, a = 0
     *  c = 100, a = 0, b = 0
     *
     * @return array
     */
    public function unbalancedAbcProviderForC()
    {
        return [
            // c < a, b < a
            [
                'a' => 60,
                'b' => 10,
                'c' => 30
            ],
            // c > a, b < a
            [
                'a' => 30,
                'b' => 10,
                'c' => 60
            ],
            // c < a, b > a
            [
                'a' => 30,
                'b' => 50,
                'c' => 20
            ],
            // c > a, b > a
            [
                'a' => 10,
                'b' => 70,
                'c' => 20
            ],
            // c > a, b = 0
            [
                'a' => 20,
                'b' => 0,
                'c' => 80
            ],
            // c < a, b = 0
            [
                'a' => 30,
                'b' => 0,
                'c' => 70
            ],
            // c < b, a = 0
            [
                'a' => 0,
                'b' => 70,
                'c' => 30
            ],
            // c > b, a = 0
            [
                'a' => 0,
                'b' => 30,
                'c' => 70
            ],
            // c = a, b = 0
            [
                'a' => 50,
                'b' => 0,
                'c' => 50
            ],
            // c = b, a = 0
            [
                'a' => 0,
                'b' => 50,
                'c' => 50
            ],
            // c = 100, a = 0, b = 0
            [
                'a' => 0,
                'b' => 0,
                'c' => 100
            ]
        ];
    }

}
