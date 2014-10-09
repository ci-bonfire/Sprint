<?php

use Myth\Auth\Password as Password;

/**
 * Class PasswordTest
 *
 * NOTE: These tests are just to verify the validity of the 3rd party code and how it works.
 * They are not meant to be an exhaustive test suite.
 */
class PasswordTest extends CodeIgniterTestCase {

    public function _before()
    {

    }

    //--------------------------------------------------------------------

    public function _after()
    {

    }

    //--------------------------------------------------------------------

    public function testNistScoresHigherForMixedWords()
    {
        $password = 'correcthorsebatterystaple';

        $score1 = Password::getNISTNumBits('correcthorsebatterystaple', true);
        $score2 = Password::getNISTNumBits('CorrectHorseBatteryStaple', true);
        $score3 = Password::getNISTNumBits('CorrectHorseBatteryStaple!', true);
        $score4 = Password::getNISTNumBits('CorrectHorseBatteryStaple!34', true);

        $this->assertTrue($score2 > $score1);
        $this->assertTrue($score3 > $score2);
        $this->assertTrue($score4 > $score3);
    }

    //--------------------------------------------------------------------

    public function testNistScoresLowerForRepeatedLetters()
    {
        $score1 = Password::getNISTNumBits('correcthorsebatterystaple', false);
        $score2 = Password::getNISTNumBits('correcthorsebatterystaple', true);

        $this->assertTrue($score2 < $score1);
    }

    //--------------------------------------------------------------------

    public function testNistScoreIsPatheticForSingleCharacterString()
    {
        $score1 = Password::getNISTNumBits('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', true);
        $score2 = Password::getNISTNumBits('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaAAAAAAAaaaaaaaaaaaaaaaaaa', true);

        $this->assertTrue($score1 < 18);
        $this->assertTrue($score2 < 18);
    }

    //--------------------------------------------------------------------

    public function testIsStrongWithoutDictionary()
    {
        $result1 = Password::isStrongPassword('asinine');
        $result2 = Password::isStrongPassword('aaaaaaa');
        $result3 = Password::isStrongPassword('Aalesund');  // Dictionary word
        $result4 = Password::isStrongPassword('correcthorse');  // bits = 21.5
        $result5 = Password::isStrongPassword('correcthorse', 25);  // bits = 21.5
        $result6 = Password::isStrongPassword('CorrectHorse123!*!');  // bits = 30.7

//        $score2 = Password::getNISTNumBits('CorrectHorse123!*!', true);
//        die(var_dump($score2));

        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);
        $this->assertTrue($result4);
        $this->assertFalse($result5);
        $this->assertTrue($result6);
    }

    //--------------------------------------------------------------------

    public function testIsStrongWithDictionary()
    {
        $result1 = Password::isStrongPassword('asinine', 18, true);
        $result2 = Password::isStrongPassword('aaaaaaa', 18, true);
        $result3 = Password::isStrongPassword('Aalesund', 18, true);  // Dictionary word
        $result4 = Password::isStrongPassword('correcthorse', 18, true);  // bits = 21.5
        $result5 = Password::isStrongPassword('correcthorse', 25, true);  // bits = 21.5
        $result6 = Password::isStrongPassword('CorrectHorse123!*!', 25, true);  // bits = 30.7

//        $score2 = Password::getNISTNumBits('correcthorse', true);
//        die(var_dump($score2));

        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);
        $this->assertFalse($result4);
        $this->assertFalse($result5);
        $this->assertTrue($result6);
    }

    //--------------------------------------------------------------------


}