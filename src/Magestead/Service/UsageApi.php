<?php

namespace Magestead\Service;

/**
 * Class UsageApi.
 */
class UsageApi
{
    /**
     * @var string
     */
    protected $_apiUrl = 'http://api.magestead.com/v1/usage';
//    protected $_apiUrl = "http://magestead-api.app/v1/usage";

    /**
     * @var array
     */
    protected $_params = [];

    /**
     * UsageApi constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->_params['os_type'] = urlencode($data['os']);
        $this->_params['server_type'] = urlencode($data['server']);
        $this->_params['php_version'] = urlencode($data['phpver']);
        $this->_params['application_version'] = urlencode($data['app']);
        $this->_params['vm_memory_limit'] = urlencode($data['memory_limit']);
        $this->_params['vm_cpu_count'] = urlencode($data['cpus']);
        $this->_params['ip_address'] = urlencode($data['ip_address']);
        $this->_params['box'] = urlencode($data['box']);
        $this->_params['locale'] = urlencode($data['locale']);
        $this->_params['default_currency'] = urlencode($data['default_currency']);
    }

    public function send()
    {
        $fields_string = $this->getFieldsString();

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_apiUrl);
        curl_setopt($ch, CURLOPT_POST, count($this->_params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        curl_close($ch);
    }

    /**
     * @return string
     */
    protected function getFieldsString()
    {
        $fields_string = '';
        foreach ($this->_params as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');

        return $fields_string;
    }
}
