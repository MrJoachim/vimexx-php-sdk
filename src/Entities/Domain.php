<?php

namespace MrJoachim\VimexxPhpSdk\Entities;

use MrJoachim\VimexxPhpSdk\VimexxSDK;

class Domain
{

    /**
     * The instance to send requests to.
     * 
     * @var VimexxSDK
     */
    protected $sdk;

    /**
     * The second-level of the domain.
     * 
     * @var string
     */
    protected $sld = "";

    /**
     * The top-level of the domain.
     * 
     * @var string
     */
    protected $tld = "";

    /**
     * The expiration date of the domain.
     * 
     * @var string
     */
    protected $expirationDate = "";

    /**
     * The domain has dns management via vimexx.
     * 
     * @var bool
     */
    protected $hasDNSManagement = false;

    /**
     * The domain has been transferred away from vimexx.
     * 
     * @var bool
     */
    protected $transferredAway = false;


    /**
     * The domain has auto renew enabled.
     * 
     * @var bool
     */
    protected $autoRenewEnabled = false;

    /**
     * Get the expiration date of the domain as a string.
     * 
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Returns true if the domain has DNS management via vimexx.
     * 
     * @return string
     */
    public function hasDNSManagement()
    {
        return $this->hasDNSManagement;
    }

    /**
     * Returns true if the domain has been transferred away from vimexx.
     * 
     * @return string
     */
    public function isTransferredAway()
    {
        return $this->transferredAway;
    }

    /**
     * Returns true if the domain has auto renew enabled.
     * 
     * @return string
     */
    public function hasAutoRenewEnabled()
    {
        return $this->autoRenewEnabled;
    }

    /**
     * Parse from api request.
     * 
     * @var VimexxSDK $sdk
     * @var array $data
     * 
     * @return Domain
     */
    public static function fromApi($sdk, $data)
    {
        $domain = new Domain();
        $domain->sdk = $sdk;
        $domain->sld = $data["sld"];
        $domain->tld = $data["tld"];
        $domain->expirationDate = $data["expireDate"];
        $domain->hasDNSManagement = $data["dnsManagement"];
        $domain->transferredAway = $data["transferredAway"];
        $domain->autoRenewEnabled = isset($data["autoExtend"]) && $data["autoExtend"] != "0";

        return $domain;
    }

    /**
     * Returns the full domain name.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->sld . "." . $this->tld;
    }

    /**
     * Get the dns records of the domain as an array.
     * 
     * @return array<string>
     */
    public function getDNS()
    {
        $response = $this->sdk->request(VimexxSDK::METHOD_POST, "/whmcs/domain/dns", [
            'sld' => $this->sld,
            'tld' => $this->tld
        ]);

        $records = [];
        foreach ($response["data"]["dns_records"] as $record) {
            $records[] = DNSRecord::fromApi($record);
        }

        return $records;
    }

    /**
     * Set the DNS Records of the domain.
     * Returns true on success.
     * 
     * @param array<DNSRecord> $records
     * 
     * @return bool
     */
    public function setDNS($records)
    {
        $records_formatted = [];
        foreach($records as $record){
            $records_formatted[] = $record->getForApi();
        }

        return $this->sdk->request(VimexxSDK::METHOD_PUT, '/whmcs/domain/dns', [
            'sld' => $this->sld,
            'tld' => $this->tld,
            'dns_records'   => $records_formatted,
        ])["result"] ? true : false;
    }

    /**
     * Get the nameservers of the domain as an array.
     * 
     * @return array<string>
     */
    public function getNameservers()
    {
        $response = $this->sdk->request(VimexxSDK::METHOD_POST, "/whmcs/domain/nameservers", [
            'sld' => $this->sld,
            'tld' => $this->tld
        ]);

        return [
            'ns1' => (isset($response['data']['ns1'])) ? $response['data']['ns1'] : '',
            'ns2' => (isset($response['data']['ns2'])) ? $response['data']['ns2'] : '',
            'ns3' => (isset($response['data']['ns3'])) ? $response['data']['ns3'] : '',
            'ns4' => (isset($response['data']['ns4'])) ? $response['data']['ns4'] : '',
            'ns5' => (isset($response['data']['ns5'])) ? $response['data']['ns5'] : '',
        ];
    }

    /**
     * Set the nameservers of the domain.
     * Returns true on success.
     * 
     * @param string $ns1
     * @param string $ns2
     * @param string $ns3
     * @param string $ns4
     * @param string $ns5
     * 
     * @return bool
     */
    public function setNameservers($ns1, $ns2, $ns3 = null, $ns4 = null, $ns5 = null)
    {

        $ns = [["ns" => $ns1], ["ns" => $ns2]];

        if ($ns3) $ns[] = ["ns" => $ns3];
        if ($ns4) $ns[] = ["ns" => $ns4];
        if ($ns5) $ns[] = ["ns" => $ns5];

        return $this->sdk->request(VimexxSDK::METHOD_PUT, '/whmcs/domain/nameservers', [
            'sld' => $this->sld,
            'tld' => $this->tld,
            'nameservers'   => $ns,
            'name'          => 'whmcs-' . $this->sld . '.' . $this->tld
        ])["result"] ? true : false;
    }
}
