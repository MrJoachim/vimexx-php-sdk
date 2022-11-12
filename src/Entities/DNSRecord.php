<?php

namespace MrJoachim\VimexxPhpSdk\Entities;

class DNSRecord
{
    /**
     * @var string
     */
    protected $hostname;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var int
     */
    protected $prio;

    /**
     * Parse from api request.
     * 
     * @var array $data
     * 
     * @return DNSRecord
     */
    static function fromApi($data)
    {
        $dnsRecord = new DNSRecord();
        $dnsRecord->hostname = $data['name'];
        $dnsRecord->type = $data['type'];
        $dnsRecord->content = $data['content'];
        $dnsRecord->prio = $data['prio'];

        return $dnsRecord;
    }

    /**
     * Returns the hostname.
     * 
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Returns the type.
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the content.
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns the prio.
     * 
     * @return int
     */
    public function getPrio()
    {
        return $this->prio;
    }

    /**
     * Create a new DNSRecord entity.
     * 
     * @param string $type
     * @param string $hostname
     * @param string $content
     * @param string $prio
     * 
     * @return DNSRecord
     */
    public static function createRecord($type, $hostname, $content, $prio)
    {
        $dnsRecord = new DNSRecord();
        $dnsRecord->hostname = $hostname;
        $dnsRecord->type = $type;
        $dnsRecord->content = $content;
        $dnsRecord->prio = $prio;

        return $dnsRecord;
    }

    /**
     * Create a new A record.
     * 
     * @param string $hostname
     * @param string $content
     * 
     * @return DNSRecord
     */
    public static function createARecord($hostname, $content)
    {
        return self::createRecord("A", $hostname, $content, 0);
    }

    /**
     * Create a new AAAA record.
     * 
     * @param string $hostname
     * @param string $content
     * 
     * @return DNSRecord
     */
    public static function createAAAARecord($hostname, $content)
    {
        return self::createRecord("AAAA", $hostname, $content, 0);
    }

    /**
     * Create a new CName record.
     * 
     * @param string $hostname
     * @param string $content
     * 
     * @return DNSRecord
     */
    public static function createCNameRecord($hostname, $content)
    {
        return self::createRecord("CNAME", $hostname, $content, 0);
    }

    /**
     * Create a new MX record.
     * 
     * @param string $hostname
     * @param int $prio
     * @param string $content
     * 
     * @return DNSRecord
     */
    public static function createMXRecord($hostname, $prio, $content)
    {
        return self::createRecord("CNAME", $hostname, $content, $prio);
    }

    /**
     * Create a new TXT record.
     * 
     * @param string $hostname
     * @param string $content
     * 
     * @return DNSRecord
     */
    public static function createTXTRecord($hostname, $content)
    {
        return self::createRecord("TXT", $hostname, $content, 0);
    }

    /**
     * Returns a valid format for the api.
     * 
     * @return array
     */
    public function getForApi()
    {
        return [
            'type'      => $this->type,
            'name'      => $this->hostname,
            'content'   => $this->content,
            'prio'      => $this->prio,
            'ttl'       => 3600
        ];
    }
}
