<?php
namespace Main;

use Main\DAL\MySQL;
use Main\Exceptions\CheckerConfigException;
use Main\Libs\Checker;
use Main\Models\PasswordsModel;

class Main
{
    private $config;
    private $checker;
    private $db;

    /**
     * Main app Constructor
     *
     * @param string $configPath
     * @throws CheckerConfigException
     */
    public function __construct($configPath = 'config/checker.ini')
    {
        $this->config = $this->readConfig($configPath);
        $this->checker = new Checker($this->config);
        $adapter = new MySQL($this->config['db']);
        $this->db = $adapter->getDb();
    }

    /**
     * Runs main test
     */
    public function run()
    {
        $table = new PasswordsModel($this->db, $this->config['db']['table']);
        $records = $table->getAllRecords();
        foreach ($records as $record) {
            $password = $record['password'];
            $id = $record['id'];
            $isPasswordValid = $this->checker->check($password);
            if (!$isPasswordValid) {
                $message = sprintf("id %s - Password '%s' didn't pass check with message '%s'.\n", $id, $password, $this->checker->getErrorMessage());
            } else {
                $message = sprintf("id %s - Password '%s' successfully passed check and DB record updated.\n", $id, $password);
            }
            $table->updatePasswordValidity($id, $isPasswordValid);

            // cli vs. apache
            if (php_sapi_name() == 'cli') {
                echo $message;
            } else {
                echo "<p>" . $message . "</p>";
                ob_implicit_flush(true);
            }
        }
    }

    /**
     * Reads configuration file
     *
     * @param string $configPath
     * @return array
     * @throws CheckerConfigException
     */
    public function readConfig($configPath)
    {
        $config = parse_ini_file($configPath, true);
        if (!is_array($config) ||
            !array_key_exists('inc', $config) ||
            !array_key_exists('rules', $config['inc']) ||
            !array_key_exists('logs', $config['inc'])
        ) {
            throw new CheckerConfigException("Read ini-file error. Please check .ini file.");
        }
        return $config;
    }
}