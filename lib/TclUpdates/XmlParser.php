<?php

namespace TclUpdates;

class XmlParser
{
    private $dom;

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
}
