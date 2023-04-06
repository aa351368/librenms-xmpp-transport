<?php

namespace LibreNMS\Alert\Transport;

use LibreNMS\Alert\Transport;

class Sendxmpp extends Transport
{
    public function deliverAlert($obj, $opts)
    {
        $sendxmppOpts = [
            'path'  => escapeshellarg($this->config['path']),
            'recipient' => escapeshellarg($this->config['recipient']),
        ];

        return $this->contactSendxmpp($obj, $sendxmppOpts);
    }

    public function contactSendxmpp($obj, $opts)
    {
        $cmd = sprintf("%s %s %s", escapeshellcmd($opts['path']), $opts['recipient'], escapeshellarg($obj['title'] . PHP_EOL . strip_tags($obj['msg'])));
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout
            2 => array("pipe", "w")   // stderr
        );
        $process = proc_open($cmd, $descriptorspec, $pipes);

        if (is_resource($process)) {
            fclose($pipes[0]); // Close stdin

            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]); // Close stdout

            $error_output = stream_get_contents($pipes[2]);
            fclose($pipes[2]); // Close stderr

            $return_value = proc_close($process);

            if ($return_value == 0) {
                error_log("Sendxmpp transport successful.");
                error_log("Output: " . $output);
                return true;
            } else {
                error_log("Sendxmpp transport failed.");
                error_log("Command: " . $cmd);
                error_log("Output: " . $output);
                error_log("Error Output: " . $error_output);
                return false;
            }
        } else {
            error_log("Sendxmpp transport failed to open process.");
            return false;
        }
    }

    public static function configTemplate()
    {
        return [
            'validation' => [],
            'config' => [
                [
                    'title' => 'Path',
                    'name' => 'path',
                    'descr' => 'Local Path to sendxmpp_api.py script',
                    'type' => 'text',
                ],
                [
                    'title' => 'Recipient',
                    'name' => 'recipient',
                    'descr' => 'Jabber ID of recipient',
                    'type' => 'text',
                ],
            ],
        ];
    }
}
