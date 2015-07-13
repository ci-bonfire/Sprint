<?php

use Zend\Escaper\Escaper;

if (! function_exists('esc'))
{
    /**
     * Escapes strings to make them safe for use
     * within HTML templates. Used by the auto-escaping
     * functionality in setVar() and available to
     * use within your views.
     *
     * Uses ZendFramework's Escaper to handle the actual escaping,
     * based on context. Valid contexts are:
     *      - html
     *      - htmlAttr
     *      - js
     *      - css
     *      - url
     *
     * References:
     *  - https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet
     *  - http://framework.zend.com/manual/current/en/modules/zend.escaper.introduction.html
     *
     * @param $data
     * @param $context
     * @param escaper   // An instance of ZF's Escaper to avoid repeated class instantiation.
     *
     * @return string
     */
    function esc($data, $context='html', $escaper=null)
    {
        if (is_array($data))
        {
            foreach ($data as $key => &$value)
            {
                $value = esc($value, $context);
            }
        }

        $context = strtolower($context);

        if (! is_object($escaper))
        {
            $escaper = new Escaper(config_item('charset'));
        }

        // Valid context?
        if (! in_array($context, ['html', 'htmlattr', 'js', 'css', 'url']))
        {
            throw new \InvalidArgumentException('Invalid Context type: '. $context);
        }

        if (! is_string($data))
        {
            return $data;
        }

        switch ($context)
        {
            case 'html':
                $data = $escaper->escapeHtml($data);
                break;
            case 'htmlattr':
                $data = $escaper->escapeHtmlAttr($data);
                break;
            case 'js':
                $data = $escaper->escapeJs($data);
                break;
            case 'css':
                $data = $escaper->escapeCss($data);
                break;
            case 'url':
                $data = $escaper->escapeUrl($data);
                break;
            default:
                break;
        }

        return $data;
    }
}