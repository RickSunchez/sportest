<?php
namespace Delorius\Routing;


class CompiledRoute
{
    private $variables;
    private $tokens;
    private $staticPrefix;
    private $regex;
    private $pathVariables;
    private $hostName;

    /**
     * Constructor.
     *
     * @param string      $staticPrefix       The static prefix of the compiled route
     * @param string      $regex              The regular expression to use to match this route
     * @param array       $tokens             An array of tokens to use to generate URL for this route
     * @param array       $pathVariables      An array of path variables
     * @param string|null $hostRegex          Host regex
     * @param array       $hostTokens         Host tokens
     * @param array       $hostVariables      An array of host variables
     * @param array       $variables          An array of variables (variables defined in the path and in the host patterns)
     */
    public function __construct($staticPrefix, $regex, array $tokens, array $pathVariables, $hostName = null, array $variables = array())
    {
        $this->staticPrefix = (string) $staticPrefix;
        $this->regex = $regex;
        $this->tokens = $tokens;
        $this->pathVariables = $pathVariables;
        $this->hostName = $hostName;
        $this->variables = $variables;
    }

    /**
     * Returns the static prefix.
     *
     * @return string The static prefix
     */
    public function getStaticPrefix()
    {
        return $this->staticPrefix;
    }

    /**
     * Returns the regex.
     *
     * @return string The regex
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Returns the tokens.
     *
     * @return array The tokens
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Returns the variables.
     *
     * @return array The variables
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Returns the path variables.
     *
     * @return array The variables
     */
    public function getPathVariables()
    {
        return $this->pathVariables;
    }

    /**
     * Returns the host variables.
     *
     * @return array The variables
     */
    public function getHostName()
    {
        return $this->hostName;
    }

}
