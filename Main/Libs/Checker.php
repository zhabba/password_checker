<?php

namespace Main\Libs;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Checker
{
    private $log;
    private $rules;
    private $errorMessage = "";

    /**
     * @param array $config
     */
    function __construct(array $config)
    {
        $this->log = new Logger("Checker");
        $this->log->pushHandler(new StreamHandler($config['inc']['logs'], Logger::DEBUG));
        $this->rules = $this->getRules($config['inc']['rules']);
    }


    /**
     * Check given password against set of rules
     *
     * @param string $password
     * @return bool
     */
    public function check($password)
    {
        $isValidPassword = true;
        foreach ($this->rules['rules'] as $rule) {
            if (!preg_match($rule['regexp'], $password)) {
                $isValidPassword = false;
                $this->errorMessage = $rule['err_message'];
                $this->log->addError(sprintf("Password '%s' didn't pass check with message '%s'.", $password, $this->getErrorMessage()));
                break;
            } else {
                $this->log->addInfo(sprintf("Password '%s' successfully passed check against rule: '%s'.", $password, $rule['description']));
            }
        }
        if ($isValidPassword) {
            $this->log->addInfo(sprintf("Password '%s' successfully passed all checks.", $password));
        }
        return $isValidPassword;
    }


    /**
     * Reads rules for checking
     *
     * @param string $rulesPath
     * @return array
     */
    private function getRules($rulesPath)
    {
        return \Spyc::YAMLLoad($rulesPath);
    }

    /**
     * Getter for $errorMessage
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}