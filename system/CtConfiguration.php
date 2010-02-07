<?php
/**
 * Command Task Configuration Interface Class
 *
 * Class for retrieving stored settings from main central
 * configuration file.
 *
 * @author Roger C.B. Johnsen
 */
class CtConfiguration {
    /**
     * Path to configuration file
     * @var String
     */
    protected $ini_file;
    /**
     * Loaded configuration
     * @var Array
     */
    protected $configuration;
    /**
     * Currently loaded configuration sections
     * @var Array
     */
    protected $sections;

    public function __construct() {
        $this->ini_file = __DIR__ . '/../etc/configuration.ini';
        $this->configuration = array();
        $this->sections = array();
    }

    /**
     * Load configuration from main ini file
     *
     * @return Array
     */
    public function loadConfiguration() {
        if(is_file($this->ini_file)) {
            $this->configuration = parse_ini_file($this->ini_file, true);
            $this->sections = array_keys($this->configuration);
            
            $status_smtp = $this->setIniSMTP($this->configuration['EMAIL']['smtp']);

            if(!$status_smtp) {
                $this->bail('INI: setting SMTP failed.');
            }

            $status_sendmail_from = $this->setIniSendmailFrom(
                                                $this->configuration['EMAIL']['email_name'],
                                                $this->configuration['EMAIL']['email_sending']
                                            );

            if(!$status_sendmail_from) {
                $this->bail('INI: setting sendmail_from failed.');
            }

            return $this->configuration;
        } else {
            throw new Exception('Configuration file not present');
        }
    }

    /**
     * Get current loaded configuration
     * @return Array
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Describe currently defined sections in configuration
     * @return Array
     */
    public function describeSections() {
        return $this->sections;
    }

    /**
     * Set INI setting for SMTP URL
     *
     * @param String $smtp smtp url
     * @return Boolean status
     */
    protected function setIniSMTP($smtp) {
        print "This is smtp: $smtp<br/>";
        if(!empty($smtp)) {
            ini_set('SMTP', $smtp);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set INI setting for sendmail_from
     *
     * @param String $email_name
     * @param String $email_sender_addr
     * @return Boolean status
     */
    protected function setIniSendmailFrom($email_name, $email_sender_addr) {
        if(empty($email_name) == false && empty($email_sender_addr) == false) {
            $string = sprintf("%s <%s>" , $email_name, $email_sender_addr);
            ini_set('sendmail_from', $string);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Throw exception
     *
     * @param String $error_message
     * @param Integer $code
     * @param Unknown $previous
     */
    protected function bail($error_message, $code=null, $previous=null) {
        if(!empty($error_message)) {
            throw new Exception($error_message, $code, $previous);
        } else {
            // Bail out on ourself
            $error_message = 'Provided error message is empty';
            throw new Exception($error_message, $code, $previous);
        }
    }
}