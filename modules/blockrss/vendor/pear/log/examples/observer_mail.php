<?php

require_once 'Log/observer.php';

class Log_observer_mail extends Log_observer
{
    var $_to = '';
    var $_subject = '';
    var $_pattern = '';

    function Log_observer_mail($priority, $conf)
    {
        /* Call the base class constructor. */
        $this->Log_observer($priority);

        /* Configure the observer. */
        $this->_to = $conf['to'];
        $this->_subject = $conf['subject'];
        $this->_pattern = $conf['pattern'];
    }

    function notify($event)
    {
        if (preg_match($this->_pattern, $event['message']) != 0) {
            mail($this->_to, $this->_subject, $event['message']);
        }
    }
}

?>
