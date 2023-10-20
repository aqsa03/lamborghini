<?php
/**
 *  "php akamai_token_v2.php [options]\n";
                 "ie.\n";
                 "php akamai_token_v2.php --start:now --window:86400\n";
                 "\n";
                 "-i IP_ADDRESS, --ip=IP_ADDRESS     IP Address to restrict this token to.\n";
                 "-s START_TIME, --start-time=START_TIME       What is the start time. (Use now for the current time)\n";
                 "-w WINDOW_SECONDS, --window=WINDOW_SECONDS\n";
                 "                    How long is this token valid for?\n";
                 "-u URL, --url=URL           URL path. [Used for:URL]\n";
                 "-a ACCESS_LIST, --acl=ACCESS_LIST   Access control list delimited by ! [ie. /*]\n";
                 "-k KEY, --key=KEY           Secret required to generate the token.\n";
                 "-p PAYLOAD, --payload=PAYLOAD   Additional text added to the calculated digest.\n";
                 "-A ALGORITHM, --algo=ALGORITHM    Algorithm to use to generate the token. (sha1, sha256,\n";
                 "                    or md5) [Default:sha256]\n";
                 "-S SALT, --salt=SALT         Additional data validated by the token but NOT\n";
                 "                    included in the token body.\n";
                 "-I SESSION_ID, --session_id=SESSION_ID\n";
                 "                    The session identifier for single use tokens or other\n";
                 "                    advanced cases.\n";
                 "-d FIELD_DELIMITER, --field_delimiter=FIELD_DELIMITER\n";
		 "                    Character used to delimit token body fields.\n";
		 "                    [Default:~]\n";
		 "-D ACL_DELIMITER, --acl_delimiter=ACL_DELIMITER\n";
		 "                    Character used to delimit acl fields. [Default:!]\n";
                 "-x, --escape_early      Causes strings to be url encoded before being used.\n";
                 "                    (legacy 2.0 behavior)\n";
 */

namespace App\Services\Akamai\Token;
                 
class TokenGenerator {
    private $key = '';

    public function __construct($key) {
        $this->key = $key;
    }
    /**
     * Token generator function
     * The only really required field is 'key'
     * @param {Array} $opts
     *  array(
     *    'window' => ,
     *    'start-time' => ,
     *    'ip' => ,
     *    'acl' => ,
     *    'session-id' => ,
     *    'payload' => ,
     *    'url' => ,
     *    'salt' => ,
     *    'field-delimiter' => ,
     *    'algo => ,
     *    'key' => ,
     *    'debug' => ,
     * )
     */
    public function generate(array $opts = array()) {
        $c = new EdgeAuth_Config();
		$g = new EdgeAuth_Generate();
        
        $c->set_key($this->key);
        
        if (!empty($opts['window'])) {
            $c->set_window($opts['window']);
        }
        if (!empty($opts['start-time'])) {
            $c->set_start_time($opts['start-time']);
        }
        if (!empty($opts['ip'])) {
            $c->set_ip($opts['ip']);
        }
        if (!empty($opts['acl'])) {
            $c->set_acl($opts['acl']);
        }
        if (!empty($opts['session-id'])) {
            $c->set_session_id($opts['session-id']);
        }
        if (!empty($opts['payload'])) {
            $c->set_data($opts['payload']);
        }
        if (!empty($opts['url'])) {
            $c->set_url($opts['url']);
        }
        if (!empty($opts['salt'])) {
            $c->set_salt($opts['salt']);
        }
        if (!empty($opts['field-delimiter'])) {
            $c->set_field_delimiter($opts['field-delimiter']);
        }
        if (!empty($opts['algo'])) {
            $c->set_algo($opts['algo']);
        }
        /*if (!empty($opts['key'])) {
            $c->set_key($opts['key']);
        }*/
        if (!empty($opts['escape-early'])) {
            $c->set_early_url_encoding($opts['escape-early']);
        }
        return $g->generate_token($c);
    }
}