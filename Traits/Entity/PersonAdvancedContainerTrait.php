<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait PersonAdvancedContainerTrait
{
    use PersonBasicContainerTrait;
    use EmailContainerTrait;
    use PhoneContainerTrait;
    use UrlContainerTrait;
    use AddressContainerTrait;
}
