<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait PersonAdvancedContainerTrait
{
    use PersonBasicContainerTrait;
    use EmailConatinerTrait;
    use PhoneContainerTrait;
    use UrlContainerTrait;
    use AddressContainerTrait;
}
