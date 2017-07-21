<?php

namespace Enterface\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EnterfaceUserBundle extends Bundle {

    function getParent() {
        return "FOSUserBundle";
    }

}
