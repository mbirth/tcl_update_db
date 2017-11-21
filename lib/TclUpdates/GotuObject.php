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
        $g->attrs['type'] = $xp->getAttr('type');
        $g->attrs['fv']   = $xp->getAttr('fv');
        $g->attrs['tv']   = $xp->getAttr('tv');
        $g->attrs['time'] = $xp->getReleaseTime();
        return $g;
    }
}
