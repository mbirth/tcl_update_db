<?php

namespace TclUpdates;

class XmlParser
{
    private $dom;
    private $xp;
    private $attr_map = array(
        'update_desc' => '//UPDATE_DESC',
        'encoding_error' => '//ENCODING_ERROR',
        'curef' => '//CUREF',
        'type' => '//VERSION/TYPE',
        'fv' => '//VERSION/FV',
        'tv' => '//VERSION/TV',
        'svn' => '//VERSION/SVN',
        'publisher' => '//VERSION/RELEASE_INFO/publisher',
        'fw_id' => '//FIRMWARE/FW_ID',
        'fileset_count' => '//FIRMWARE/FILESET_COUNT',
        'filename' => '//FILESET/FILE[0]/FILENAME',
        'file_id' => '//FILESET/FILE[0]/FILE_ID',
        'file_size' => '//FILESET/FILE[0]/SIZE',
        'file_chksum' => '//FILESET/FILE[0]/CHECKSUM',
        'file_version' => '//FILESET/FILE[0]/FILE_VERSION',
        'description_en' => '//DESCRIPTION/en',
        'description_ja' => '//DESCRIPTION/ja',
        'description_zh' => '//DESCRIPTION/zh',
    );

    public function __construct()
    {
        $this->dom = new \DOMDocument();
    }

    public function loadXMLFromString($xml)
    {
        $xml_ok = $this->dom->loadXML($xml, LIBXML_NOENT);
        return $xml_ok;
    }

    public function validateGOTU()
    {
        if ($this->dom->childNodes->length < 1) {
            return false;
        }
        $root_node = $this->dom->childNodes->item(0);
        if ($root_node->nodeName != 'GOTU') {
            return false;
        }
        return true;
    }

    public function getAttr($attr)
    {
        if (!isset($this->attr_map[$attr])) {
            return false;
        }
        $xpath = $this->attr_map[$attr];
        $node = $this->getXPathValue($xpath);
        return $node;
    }

    public function getReleaseTime()
    {
        $yr = $this->getXPathValue('//VERSION/RELEASE_INFO/year');
        $mo = $this->getXPathValue('//VERSION/RELEASE_INFO/month');
        $dy = $this->getXPathValue('//VERSION/RELEASE_INFO/day');
        $hr = $this->getXPathValue('//VERSION/RELEASE_INFO/hour');
        $mn = $this->getXPathValue('//VERSION/RELEASE_INFO/minute');
        $se = $this->getXPathValue('//VERSION/RELEASE_INFO/second');
        $tz = $this->getXPathValue('//VERSION/RELEASE_INFO/timezone');

        $tz = intval(str_replace('GMT', '', $tz));   // returns hours from GMT (e.g. "8" or "-8")
        $stamp = sprintf('%04u-%02u-%02uT%02u:%02u:%02u%+03d:00', $yr, $mo, $dy, $hr, $mn, $se, $tz);
        //$unix  = strtotime($stamp);
        return $stamp;
    }

    private function getXPath($path, $context = null)
    {
        if (is_null($this->xp)) {
            $this->xp = new \DOMXPath($this->dom);
        }
        $result = $this->xp->query($path, $context);
        //var_dump($result);
        if ($result->length == 0) {
            return null;
        }
        return $result;
    }

    private function getXPathValue($path, $context = null)
    {
        $node = $this->getXPath($path, $context);
        if (is_null($node)) {
            return null;
        }
        if ($node->length == 1) {
            return $node->item(0)->nodeValue;
        }
        return $node;
    }
}
