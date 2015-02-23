# Password Strength

Sprint contains a very powerful password strength checking library. This library was developed by [Thomas Hruska](http://cubicspot.blogspot.com/) and is part of the [Barebones CMS SSO Server/Client package](http://barebonescms.com/documentation/sso/) released under the MIT license.

## Why So Special?
The package implements a strength checker based on the [NIST Special Publication 800-63](http://en.wikipedia.org/wiki/Password_strength#NIST_Special_Publication_800-63) that presents a proposal for calculating the "entropy bits" of human-generated passwords.

What makes this proposal so exciting is that it provides a strong base for calculating password strength that does NOT enforce any specific rules. In other words, no more telling your users that they have to use 1 capital letter, 1 number and 1 symbol, and it must be a certain length long. That only creates hard to remember passwords for the users. Instead, this method allows, and rewards password length, so using different words together, like "[correcthorsebatterystaple](http://cubicspot.blogspot.com/2011/11/how-to-calculate-password-strength.html)", can create a decent password. Doing any of the previous items (mix of cases, numerals and symbols) will create stronger passwords and the users are encouraged to do so, but they are not forced to.

In addition to the core NIST proposal, this library makes things even tougher, but helps the user to create safer passwords, with the following checks:

- Any repeating letters get treated at 0.75% of the NIST proposed value, becuase 'aaaaaaaaaa' is NOT a strong password. :)
- Check for keyboard layout passwords and "tricks" (shift by one). For example, 'qwertyuiop' is not a strong password because it uses aspects of the keyboard's layout to come up with a password.
- Can checks the password (and keyboard-sliding/LeetSpeak variations) against a 300,000 word English dictionary.

Combined, these make for a very powerful way to enforce a strict password requirement.

## Checking Password Strength
The user signup routine that comes with Sprint already implements and enforces this password strength check, but if you need to implement it in other areas of the site, you can find it in the `Myth\Auth\Password` class. This has only one static method that you need to worry about: `isStrongPassword`.

The first parameter is the password itself, and is the only required value. The defaults are all reasonable, though Sprint modifies them a bit for stronger security out of the box.

	use \Myth\Auth\Password as Password;

	if (! Password::isStrongPassword($password) )
	{
		...
	}

The method will simply return either `true` or `false`.

The second password is the number of bits of entropy the password must calculate to. This is a bit confusing but the following guidelines should help you determine which to use:

- 18 bits of entropy = minimum for ANY website.
- 25 bits of entropy = minimum for a general purpose web service used relatively widely (e.g. Hotmail).
- 30 bits of entropy = minimum for a web service with business critical applications (e.g. SAAS).
- 40 bits of entropy = minimum for a bank or other financial service.

Sprint defaults to 18. Feel free to raise it if you need to by editing the value in the `application/config/auth.php` config file.

	Password::isStrongPassword($password, 25);

The third parameter determines whether the password should be compared to English language words. For English based websites, this is highly encouraged, and is the default value in Sprint. The system ships with a 300,000 English language dictionary that the words will get compared to and have it's entropy values reduced depending on the types of match found.

	Password::isStrongPassword($password, 25, true);

## Using With Form Validation
We have included a small method that can be used as one of the validation rules in the Form Validation library to automatically enforce a strong password. This is in the `auth/password_helper` file, and would be loaded like any other helper in the system.

	$this->load->helper('auth/password');

Then, in your rules for the password field, be sure to include the function `isStrongPassword` as one of the rules.

	$this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean|isStrongPassword);

## References

- [wikipedia: Password_strength](http://en.wikipedia.org/wiki/Password_strength#NIST_Special_Publication_800-63)
- Thomas' Blog posts, [Part 1](http://cubicspot.blogspot.com/2011/11/how-to-calculate-password-strength.html), [Part 2](http://cubicspot.blogspot.com/2012/01/how-to-calculate-password-strength-part.html), and [Part 3](http://cubicspot.blogspot.com/2012/06/how-to-calculate-password-strength-part.html)
