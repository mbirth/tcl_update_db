<?php

namespace TclUpdates;

class GotuObject
{
    private $attrs = array();

    public function __construct()
    {

    }

    public function __isset($attr)
    {
        return array_key_exists($attr, $this->attrs);
    }

    public function __get($attr)
    {
        if (!$this->__isset($attr)) {
            return null;
        }
        return $this->attrs[$attr];
    }

    public function getAttrs()
    {
        return $this->attrs;
    }
    
    public static function fromXmlParser(XmlParser $xp)
    {
        if (!$xp->validateGOTU()) {
            return null;
        }
        $g = new self();
        $g->attrs = $xp->getAttrs();
        return $g;
    }
}
