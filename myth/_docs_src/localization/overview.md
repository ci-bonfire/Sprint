# Localization Overview
The localization tools in CodeIgniter were never meant to provide a full solution to all of your internationalization and localization needs. They were simply there to provide a way to translate the UI of your application into other languages. Sprint aims to fix that. 

While the tools that Sprint provides do not cover every aspect of internationalization and localization (that's a huge, complex topic with many needs to properly address), it does aim to provide you a number of tools to help you create more international sites.

## Internationalization vs Localization
Before we can can go too much farther we need to ensure we are talking the same language when we refer to these terms. Please refer to the [W3C Q&A](http://www.w3.org/International/questions/qa-i18n.en) about the same topic for more details, but here's the difference in a nutshell: 

Localization (i10n) is the actual adapation of your application. This includes translating your UI, use of currency, changing images and content based upon locale, etc. 

Internationalization (i18n) enables you to easily localize your application.

Since Sprint provides you the tools to help you localize your application, this section should more appropriately be titled 'Internationalization', but has been called Localization since many people confuse the topic, or think of them as a single item. 

## Turning Localization On
Before you can use any of the Localization tools, you must enable them in the `application/config/application.php` config file. By default, they are turned off.

	$config['i18n'] = TRUE;

## URI Language Selection
A common method for selecting the langauge that the system should use is through the first segment of the URI. Sprint will automatically respond to these whenever  the Localization tools are enabled. 

The first step is to configure the available languages that your site supports. This is stored in `application/config/application.php` under `i18n.languages`. The key of each element of the array is the code that you want the language selection to respond to. While this can be anything, we recommend using the two- or three-character [ISO 639-2 Codes](http://www.loc.gov/standards/iso639-2/php/code_list.php). The 'value' of each element of the array must match the name of the language folder within CodeIgniter.

	$config['i18n.languages'] = [
	    'en' => 'english'
	];

With the default setting, it would detect that it should use the `english` translation when the URI looks something like `http://example.com/en/my/page`. 

Note that any [URI methods](http://www.codeigniter.com/userguide3/libraries/uri.html) will work as if the language code is NOT there.  In our example, `$this->uri->segment(2)` would return `page`, since segment 1 would be the first segment AFTER the language code. 

### CURRENT_LANGUAGE
This will also define a constant that is available throughout your application that defines the current language, appropriately titled `CURRENT_LANGUAGE`. This is the translation name, not the short code. For our example, this would be `english`. You should include this when loading your own language files.

	$this->lang->load('error_messages', CURRENT_LANGUAGE);

This value is also availble through the `config` class.

	config_item('language');
