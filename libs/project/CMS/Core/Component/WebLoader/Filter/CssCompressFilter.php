<?php
namespace CMS\Core\Component\WebLoader\Filter;

class CssCompressFilter
{
    /**
     * Invoke filter
     * @param string $code
     * @return string
     */
    public function __invoke($code)
    {
        $code = preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*/#', '', $code);
        $code = str_replace(' 0px', ' 0', $code);
        $code = str_replace(array("\r\n", "\r"), "\n", $code);
        $code = str_replace("\t", '', $code);
        $code = str_replace("\n   ", "\n", $code);
        $code = str_replace("\n  ", "\n", $code);
        $code = str_replace("\n ", "\n", $code);
        $code = str_replace("\n", ' ', $code);
        $code = str_replace('   ', ' ', $code);
        $code = str_replace('  ', ' ', $code);
        $code = str_replace(': ', ':', $code);
        $code = str_replace('; ', ';', $code);
        $code = str_replace(' { ', '{', $code);
        $code = str_replace(' } ', '}', $code);
        $code = str_replace('} .', '}.', $code);
        $code = str_replace(', .', ',.', $code);
        $code = str_replace(', ', ',', $code);
        $code = str_replace(';}', '}', $code);
        $code = str_replace('"', '\'', $code);
        $code = str_replace(' !imp', '!imp', $code);
        $code = str_replace(' > ', '>', $code);
        $code = str_replace('#000000', '#000', $code);
        $code = str_replace(array('#ffffff','#FFFFFF'), '#fff', $code);
        $code = str_replace('#FFF', '#fff', $code);

        return $code;
    }

}