<?php

namespace TclUpdates;

class GotuObject
{
    private $attrs = array();

    public function __construct()
    {

    }

    public static function fromXmlParser(XmlParser $xp)
    {
        if (!$xp->validateGOTU()) {
            return false;
        }
        $g = new self();
        $g->attrs = $xp->getAttrs();
        return $g;
    }
}
