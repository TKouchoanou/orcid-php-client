<?php

namespace Orcid\Work\Data;

 use Orcid\Work\Loader;

 abstract class Common extends \Orcid\Work\Common implements Loader
 {
     /**
      * return true when the required data of Common are set and is valid.
      * Exemple: the PublicationDate will be valid if a valid year are set
      * The Citation will be valid if the value (citation text are set)
      * The ExternaliId if valid ExternalId type and value are set
      * @return bool
      */
     abstract public function isValid();
 }
