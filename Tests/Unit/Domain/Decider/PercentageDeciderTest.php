<?php

namespace Wysiwyg\ABTesting\Tests\Unit\Domain\Decider;

use PHPUnit\Framework\Assert;
use Wysiwyg\ABTesting\Domain\Comparator\FixedValueComparator;
use Wysiwyg\ABTesting\Domain\Comparator\RandomComparator;
use Wysiwyg\ABTesting\Domain\Decider\PercentageDecider;

class PercentageDeciderTest extends AbstractPercentageDeciderTest
{

    /**
     * Scenario: A/B - 100 - 0
     *
     * Test if the decider decides for 'a', when 'a' has 100% configured.
     * This test will cover configured decisions for tests where only a and b are configured.
     *
     * @dataProvider hundredPercentAProvider
     * @test
     */
    public function deciderDecidesForAOnHundredPercent($a, $b)
    {
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), new RandomComparator());

        Assert::assertSame('a', $decision);
    }

    /**
     * Scenario: A/B - 30 - 70
     *
     * Test if the decider decides for 'a', when 'a' has 100% configured.
     * This test will cover configured decisions for tests where only a and b are configured.
     *
     * @dataProvider thirtyASeventyBProvider
     * @test
     */
    public function deciderDecidesForAOnThirtyPercent($a, $b)
    {
        $fixedValueComparator = new FixedValueComparator($a - 1);
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), $fixedValueComparator);

        Assert::assertSame('a', $decision);
    }

    /**
     * Scenario: A/B - 0 - 100
     *
     * Test if the decider decides for 'b', when 'b' has 100% configured.
     * This test will cover configured decisions for tests where only a and b are configured.
     *
     * @dataProvider hundredPercentBProvider
     * @test
     */
    public function deciderDecidesForBOnHundredPercent($a, $b)
    {
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), new RandomComparator());

        Assert::assertSame('b', $decision);
    }

    /**
     * Scenario: A/B/C 0 - 0 - 100
     *
     * Test if the decider decides for 'c', when 'c' has 100% configured.
     * This test will cover configured decisions for tests where a, b and c are configured.
     *
     * @dataProvider hundredPercentCProvider
     * @test
     */
    public function deciderDecidesForCOnHundredPercent($a, $b, $c)
    {
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), new RandomComparator());

        Assert::assertSame('c', $decision);
    }

    /**
     * Scenario: A/B/C 0 - 50 - 50
     *
     * Test if the decider decides for 'b' or 'c', when 'a' has 0 percent configured.
     *
     * @dataProvider zeroAFiftyBFiftyCProvider
     * @test
     */
    public function deciderAlwaysDecidesForBorC($a, $b, $c)
    {
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), new RandomComparator());

        Assert::assertTrue(in_array($decision, ['b', 'c']));
        Assert::assertNotSame('a', $decision);
    }

    /**
     * Scenario: A/B/C 50 - 0 - 50
     *
     * Test if the decider decides for 'a' or 'c', when 'b' has 0 percent configured.
     *
     * @dataProvider fiftyAZeroBFiftyCProvider
     * @test
     */
    public function deciderCanOnlyDecideForAorC($a, $b, $c)
    {
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), new RandomComparator());

        Assert::assertTrue(in_array($decision, ['a', 'c']));
        Assert::assertNotSame('b', $decision);
    }

    /**
     * Scenario: A/B/C 33 - 33 - 33
     *
     * Test if the decider decides for 'a', 'b' or 'c'.
     *
     * @dataProvider balancedAbcProvider
     * @test
     */
    public function deciderCanOnlyDecideForAorBorC($a, $b, $c)
    {
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), new RandomComparator());

        Assert::assertTrue(in_array($decision, ['a', 'b', 'c']));
    }

    /**
     * Scenario: A/B 30 - 70, randomNumber $a -1
     *
     * Test if the decider only chooses 'a', when the random number is in range of a.
     *
     * @dataProvider thirtyASeventyBProvider
     * @test
     */
    public function deciderDecidesOnlyForAWhenRandomNumberIsLowerThanB($a, $b)
    {
        $fixedValueComparator = new FixedValueComparator($a - 1);
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), $fixedValueComparator);
        Assert::assertSame('a', $decision);
    }

    /**
     * Scenario: A/B 30 - 70, randomNumber $a + 1
     *
     * Test if the decider only chooses 'b', when the random number is in range of b.
     *
     * @dataProvider thirtyASeventyBProvider
     * @test
     */
    public function deciderDecidesOnlyForBWhenRandomNumberIsHigherThanB($a, $b)
    {
        $fixedValueComparator = new FixedValueComparator($a + 1);
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), $fixedValueComparator);
        Assert::assertSame('b', $decision);
    }


    /**
     * Scenario: A/B/C 33 - 33 - 33, randomNumber $a + 1
     *
     * Test if the decider only chooses 'c', when the random number is in range of b.
     *
     * @dataProvider balancedAbcProvider
     * @test
     */
    public function deciderDecidesOnlyForBWhenRandomNumberIsHigherThanAAndLowerThanC($a, $b, $c)
    {
        $fixedValueComparator = new FixedValueComparator($a + 1);
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), $fixedValueComparator);
        Assert::assertSame('b', $decision);
    }

    /**
     * Scenario: A/B/C 33 - 33 - 33, randomNumber $a + $b - 1
     *
     * @dataProvider balancedAbcProvider
     * @test
     */
    public function deciderDecidesOnlyForBWhenRandomNumberIsLowerThanC($a, $b, $c)
    {
        $fixedValueComparator = new FixedValueComparator($a + $b - 1);
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), $fixedValueComparator);
        Assert::assertSame('b', $decision);
    }

    /**
     * Scenario: A/B/C 33 - 33 - 33, randomNumber $a +$b + 1
     *
     * Test if the decider only chooses 'c', when the random number is in range of c.
     *
     * @dataProvider balancedAbcProvider
     * @test
     */
    public function deciderDecidesOnlyForCWhenRandomNumberIsHigherThanAAndB($a, $b, $c)
    {
        $fixedValueComparator = new FixedValueComparator($a + $b + 1);
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), $fixedValueComparator);
        Assert::assertSame('c', $decision);
    }

    /**
     * Scenario: several unbalanced A/B/C, randomNumber $a - 1
     *
     * @dataProvider unbalancedAbcProviderForA
     * @test
     */
    public function deciderDecidesOnlyForAWhenRandomNumberIsLowerThanA($a, $b, $c)
    {
        $fixedValueComparator = new FixedValueComparator($a - 1);
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), $fixedValueComparator);
        Assert::assertSame('a', $decision);
    }

    /**
     * Scenario: several unbalanced A/B/C, randomNumber $a + 1
     *
     * @dataProvider unbalancedAbcProviderForB
     * @test
     */
    public function deciderDecidesForBWhenRandomNumberIsHigherThanA($a, $b, $c)
    {
        $fixedValueComparator = new FixedValueComparator($a + 1);
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), $fixedValueComparator);
        Assert::assertSame('b', $decision);
    }

    /**
     * Scenario: several unbalanced A/B/C, randomNumber $a + $b + 1
     *
     * @dataProvider unbalancedAbcProviderForC
     * @test
     */
    public function deciderDecidesForCWhenRandomNumberIsHigherThanAndB($a, $b, $c)
    {
        $fixedValueComparator = new FixedValueComparator($a + $b + 1);
        $percentageDecider = new PercentageDecider();
        $decision = $percentageDecider->decide($this->getProvidedData(), $fixedValueComparator);
        Assert::assertSame('c', $decision);
    }
}
