<?php

namespace Myth\Auth;

define('DICTIONARY_PATH', dirname(__FILE__) .'/dictionary.txt');

/**
 * Class Password
 *
 * Provides methods to determine how secure a password is. Uses code from
 * Barebones CMS's SSO - licensed MIT.
 * See their download page at http://barebonescms.com/documentation/sso/
 * for details on the licensing.
 *
 * For information and background on the algorithm they implemented,
 * see the following blog posts:
 *
 *      Part 1 - http://cubicspot.blogspot.com/2011/11/how-to-calculate-password-strength.html
 *      Part 2 - http://cubicspot.blogspot.com/2012/01/how-to-calculate-password-strength-part.html
 *      Part 3 - http://cubicspot.blogspot.com/2012/06/how-to-calculate-password-strength-part.html
 *
 * @package Myth\Auth
 */
class Password {

    /**
     * A standardized method for hasing a password before storing
     * in the database.
     *
     * @param $password
     * @return bool|mixed|string
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    //--------------------------------------------------------------------

    /**
     * Determines the number of entropy bits a password has based on
     *
     *
     * @param $password
     * @param bool $repeatcalc
     * @return int
     */
    public static function getNISTNumBits($password, $repeatcalc = false)
    {
        $y = strlen($password);
        if ($repeatcalc)
        {
            // Variant on NIST rules to reduce long sequences of repeated characters.
            $result = 0;
            $charmult = array_fill(0, 256, 1);
            for ($x = 0; $x < $y; $x++)
            {
                $tempchr = ord(substr($password, $x, 1));
                if ($x > 19)  $result += $charmult[$tempchr];
                else if ($x > 7)  $result += $charmult[$tempchr] * 1.5;
                else if ($x > 0)  $result += $charmult[$tempchr] * 2;
                else  $result += 4;

                $charmult[$tempchr] *= 0.75;
            }

            return $result;
        }
        else
        {
            if ($y > 20)  return 4 + (7 * 2) + (12 * 1.5) + $y - 20;
            if ($y > 8)  return 4 + (7 * 2) + (($y - 8) * 1.5);
            if ($y > 1)  return 4 + (($y - 1) * 2);

            return ($y == 1 ? 4 : 0);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Determines whether a password is strong enough to use. You should check
     * the password against this method and reject it if the password is not
     * strong enough.
     *
     * The following guidelines come from the author's tests against 10.4 million actual passwords
     * ( see post: http://cubicspot.blogspot.com/2012/01/how-to-calculate-password-strength-part.html )
     * and represents the suggested minimum entropy bits for different types of sites:
     *
     *      - 18 bits of entropy = minimum for ANY website.
     *      - 25 bits of entropy = minimum for a general purpose web service used relatively widely (e.g. Hotmail).
     *      - 30 bits of entropy = minimum for a web service with business critical applications (e.g. SAAS).
     *      - 40 bits of entropy = minimum for a bank or other financial service.
     *
     * The algorithm is based upon a modified version of the NIST rules which suggest the following:
     *
     *      - The first byte counts as 4 bits.
     *      - The next 7 bytes count as 2 bits each.
     *      - The next 12 bytes count as 1.5 bits each.
     *      - Anything beyond that counts as 1 bit each.
     *      - Mixed case + non-alphanumeric = up to 6 extra bits.
     *
     * @param string    $password   - The password to check
     * @param int       $minbits    - Minimum "entropy bits" that is allowed
     * @param bool      $usedict    - Should we check the password against a 300,000 word English dictionary?
     * @param int       $minwordlen -
     * @return bool
     */
    public static function isStrongPassword($password, $minbits = 18, $usedict = false, $minwordlen = 4)
    {
        // NIST password strength rules allow up to 6 extra bits for mixed case and non-alphabetic.
        $upper = false;
        $lower = false;
        $numeric = false;
        $other = false;
        $space = false;
        $y = strlen($password);
        for ($x = 0; $x < $y; $x++)
        {
            $tempchr = ord(substr($password, $x, 1));
            if ($tempchr >= ord("A") && $tempchr <= ord("Z"))  $upper = true;
            else if ($tempchr >= ord("a") && $tempchr <= ord("z"))  $lower = true;
            else if ($tempchr >= ord("0") && $tempchr <= ord("9"))  $numeric = true;
            else if ($tempchr == ord(" "))  $space = true;
            else  $other = true;
        }
        $extrabits = ($upper && $lower && $other ? ($numeric ? 6 : 5) : ($numeric && !$upper && !$lower ? ($other ? -2 : -6) : 0));
        if (!$space)  $extrabits -= 2;
        else if (count(explode(" ", preg_replace('/\s+/', " ", $password))) > 3)  $extrabits++;
        $result = self::getNISTNumBits($password, true) + $extrabits;

        $password = strtolower($password);
        $revpassword = strrev($password);
        $numbits = self::getNISTNumBits($password) + $extrabits;
        if ($result > $numbits)  $result = $numbits;

        // Remove QWERTY strings.
        $qwertystrs = array(
            "1234567890-qwertyuiopasdfghjkl;zxcvbnm,./",
            "1qaz2wsx3edc4rfv5tgb6yhn7ujm8ik,9ol.0p;/-['=]:?_{\"+}",
            "1qaz2wsx3edc4rfv5tgb6yhn7ujm8ik9ol0p",
            "qazwsxedcrfvtgbyhnujmik,ol.p;/-['=]:?_{\"+}",
            "qazwsxedcrfvtgbyhnujmikolp",
            "]\"/=[;.-pl,0okm9ijn8uhb7ygv6tfc5rdx4esz3wa2q1",
            "pl0okm9ijn8uhb7ygv6tfc5rdx4esz3wa2q1",
            "]\"/[;.pl,okmijnuhbygvtfcrdxeszwaq",
            "plokmijnuhbygvtfcrdxeszwaq",
            "014725836914702583697894561230258/369*+-*/",
            "abcdefghijklmnopqrstuvwxyz"
        );
        foreach ($qwertystrs as $qwertystr)
        {
            $qpassword = $password;
            $qrevpassword = $revpassword;
            $z = 6;
            do
            {
                $y = strlen($qwertystr) - $z;
                for ($x = 0; $x < $y; $x++)
                {
                    $str = substr($qwertystr, $x, $z);
                    $qpassword = str_replace($str, "*", $qpassword);
                    $qrevpassword = str_replace($str, "*", $qrevpassword);
                }

                $z--;
            } while ($z > 2);

            $numbits = self::getNISTNumBits($qpassword) + $extrabits;
            if ($result > $numbits)  $result = $numbits;
            $numbits = self::getNISTNumBits($qrevpassword) + $extrabits;
            if ($result > $numbits)  $result = $numbits;

            if ($result < $minbits)  return false;
        }

        if ($usedict && $result >= $minbits)
        {
            $passwords = array();

            // Add keyboard shifting password variants.
            $keyboardmap_down_noshift = array(
                "z" => "", "x" => "", "c" => "", "v" => "", "b" => "", "n" => "", "m" => "", "," => "", "." => "", "/" => "", "<" => "", ">" => "", "?" => ""
            );
            if ($password == str_replace(array_keys($keyboardmap_down_noshift), array_values($keyboardmap_down_noshift), $password))
            {
                $keyboardmap_downright = array(
                    "a" => "z",
                    "q" => "a",
                    "1" => "q",
                    "s" => "x",
                    "w" => "s",
                    "2" => "w",
                    "d" => "c",
                    "e" => "d",
                    "3" => "e",
                    "f" => "v",
                    "r" => "f",
                    "4" => "r",
                    "g" => "b",
                    "t" => "g",
                    "5" => "t",
                    "h" => "n",
                    "y" => "h",
                    "6" => "y",
                    "j" => "m",
                    "u" => "j",
                    "7" => "u",
                    "i" => "k",
                    "8" => "i",
                    "o" => "l",
                    "9" => "o",
                    "0" => "p",
                );

                $keyboardmap_downleft = array(
                    "2" => "q",
                    "w" => "a",
                    "3" => "w",
                    "s" => "z",
                    "e" => "s",
                    "4" => "e",
                    "d" => "x",
                    "r" => "d",
                    "5" => "r",
                    "f" => "c",
                    "t" => "f",
                    "6" => "t",
                    "g" => "v",
                    "y" => "g",
                    "7" => "y",
                    "h" => "b",
                    "u" => "h",
                    "8" => "u",
                    "j" => "n",
                    "i" => "j",
                    "9" => "i",
                    "k" => "m",
                    "o" => "k",
                    "0" => "o",
                    "p" => "l",
                    "-" => "p",
                );

                $password2 = str_replace(array_keys($keyboardmap_downright), array_values($keyboardmap_downright), $password);
                $passwords[] = $password2;
                $passwords[] = strrev($password2);

                $password2 = str_replace(array_keys($keyboardmap_downleft), array_values($keyboardmap_downleft), $password);
                $passwords[] = $password2;
                $passwords[] = strrev($password2);
            }

            // Deal with LEET-Speak substitutions.
            $leetspeakmap = array(
                "@" => "a",
                "!" => "i",
                "$" => "s",
                "1" => "i",
                "2" => "z",
                "3" => "e",
                "4" => "a",
                "5" => "s",
                "6" => "g",
                "7" => "t",
                "8" => "b",
                "9" => "g",
                "0" => "o"
            );

            $password2 = str_replace(array_keys($leetspeakmap), array_values($leetspeakmap), $password);
            $passwords[] = $password2;
            $passwords[] = strrev($password2);

            $leetspeakmap["1"] = "l";
            $password3 = str_replace(array_keys($leetspeakmap), array_values($leetspeakmap), $password);
            if ($password3 != $password2)
            {
                $passwords[] = $password3;
                $passwords[] = strrev($password3);
            }

            // Process the password, while looking for words in the dictionary.
            $a = ord("a");
            $z = ord("z");
            $data = file_get_contents(DICTIONARY_PATH);
            foreach ($passwords as $num => $password)
            {
                $y = strlen($password);
                for ($x = 0; $x < $y; $x++)
                {
                    $tempchr = ord(substr($password, $x, 1));
                    if ($tempchr >= $a && $tempchr <= $z)
                    {
                        for ($x2 = $x + 1; $x2 < $y; $x2++)
                        {
                            $tempchr = ord(substr($password, $x2, 1));
                            if ($tempchr < $a || $tempchr > $z)  break;
                        }

                        $found = false;
                        while (!$found && $x2 - $x >= $minwordlen)
                        {
                            $word = "/\\n" . substr($password, $x, $minwordlen);
                            for ($x3 = $x + $minwordlen; $x3 < $x2; $x3++)  $word .= "(" . $password{$x3};
                            for ($x3 = $x + $minwordlen; $x3 < $x2; $x3++)  $word .= ")?";
                            $word .= "\\n/";

                            preg_match_all($word, $data, $matches);
                            if (!count($matches[0]))
                            {
                                $password{$x} = "*";
                                $x++;
                                $numbits = self::getNISTNumBits(substr($password, 0, $x)) + $extrabits;
                                if ($numbits >= $minbits)  $found = true;
                            }
                            else
                            {
                                foreach ($matches[0] as $match)
                                {
                                    $password2 = str_replace(trim($match), "*", $password);
                                    $numbits = self::getNISTNumBits($password2) + $extrabits;
                                    if ($result > $numbits)  $result = $numbits;

                                    if ($result < $minbits)  return false;
                                }

                                $found = true;
                            }
                        }

                        if ($found)  break;

                        $x = $x2 - 1;
                    }
                }
            }
        }

        return $result >= $minbits;
    }
}