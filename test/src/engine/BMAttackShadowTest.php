<?php

class BMAttackShadowTest extends PHPUnit_Framework_TestCase {
    /**
     * @var BMAttackShadow
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = BMAttackShadow::get_instance();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers BMAttackShadow::validate_attack
     */
    public function testValidate_attack()
    {
        $game = new BMGame;

        $die1 = new BMDie;
        $die1->add_skill('Shadow');
        $die1->init(6);
        $die1->value = 6;

        $die2 = new BMDie;
        $die2->add_skill('Shadow');
        $die2->init(6);
        $die2->value = 1;

        // Basic error handling
        $this->assertFalse($this->object->validate_attack($game, array(), array()));
        $this->assertFalse($this->object->validate_attack($game, array($die1), array()));
        $this->assertFalse($this->object->validate_attack($game, array(), array($die1)));

        // Basic attacks

        // 1 < 6
        $this->assertTrue($this->object->validate_attack($game, array($die2), array($die1)));

        // 6 ! <= 1
        $this->assertFalse($this->object->validate_attack($game, array($die1), array($die2)));

        // 6 == 6
        $this->assertTrue($this->object->validate_attack($game, array($die1), array($die1)));

        // 1 == 1
        $this->assertTrue($this->object->validate_attack($game, array($die2), array($die2)));

        $die3 = new BMDie;
        $die3->add_skill('Shadow');
        $die3->init(5);
        $die3->value = 1;

        // nSides (5) < value (6)
        $this->assertFalse($this->object->validate_attack($game, array($die3), array($die1)));

        // james: still need a test targeting a non-valid target, e.g., a stealth die
    }
}
