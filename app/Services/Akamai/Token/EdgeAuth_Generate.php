<?php

namespace App\Services\Akamai\Token;

class EdgeAuth_Generate {

	protected function h2b($str) {
    	$bin = "";
    	$i = 0;
    	do {
        	$bin .= chr(hexdec($str[$i].$str[($i + 1)]));
        	$i += 2;
    	} while ($i < strlen($str));
    	return $bin;
	}

	public function generate_token($config) {
		// ASSUMES:($algo='sha256', $ip='', $start_time=null, $window=300, $acl=null, $acl_url="", $session_id="", $payload="", $salt="", $key="000000000000", $field_delimiter="~")
		$m_token = $config->get_ip_field();
		$m_token .= $config->get_start_time_field();
		$m_token .= $config->get_expr_field();
		$m_token .= $config->get_acl_field();
		$m_token .= $config->get_session_id_field();
		$m_token .= $config->get_data_field();
		$m_token_digest = (string)$m_token;
		$m_token_digest .= $config->get_url_field();
		$m_token_digest .= $config->get_salt_field();

		// produce the signature and append to the tokenized string
		$signature = hash_hmac($config->get_algo(), rtrim($m_token_digest, $config->get_field_delimiter()), $this->h2b($config->get_key()));
		return $m_token.'hmac='.$signature;
	}
}