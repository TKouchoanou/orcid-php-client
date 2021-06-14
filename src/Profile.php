<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr | theophane_malo@yahoo.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid;

/**
 * ORCID profile API class
 **/
class Profile
{
    /**
     * The oauth object
     *
     * @var  object
     **/
    private $oauth = null;

    /**
     * The raw orcid profile
     *
     * @var  object
     **/
    private $raw = null;

    /**
     * Constructs object instance
     *
     * @param   object  $oauth  the oauth object used for making calls to orcid
     * @return  void
     **/
    public function __construct($oauth = null)
    {
        $this->oauth = $oauth;
    }

    /**
     * Grabs the ORCID iD
     *
     * @return  string
     **/
    public function id()
    {
        return $this->oauth->getOrcid();
    }

    /**
     * Grabs the orcid profile (oauth client must have requested this level or access)
     *
     * @return  object
     **/
    public function raw()
    {
        if (!isset($this->raw)) {
            $this->raw = $this->oauth->getProfile($this->id());
        }

        return $this->raw;
    }

    /**
     * Grabs the ORCID person
     *
     * @return  object
     **/
    public function person()
    {
        $this->raw();

        return $this->raw->person;
    }

    /**
     * Grabs the users email if it's set and available
     *
     * @return  string|null
     **/
    public function email()
    {
        $this->raw();

        $email = null;
        $person = $this->person();

        if (isset($person->emails)) {
            if (isset($person->emails->email)) {
                if (is_array($person->emails->email) && isset($person->emails->email[0])) {
                    $email = $person->emails->email[0]->value;
                }
            }
        }

        return $email;
    }

    /**
     * Grabs the raw name elements to create fullname
     *
     * @return  string
     **/
    public function fullName()
    {
        $this->raw();
        $details = $this->person()->name;

        // "given-names" is a required field on ORCID profiles.
        // "family-name", however, may or may not be available.
        // https://members.orcid.org/api/tutorial/reading-xml#names
        return $details->{'given-names'}->value . ($details->{'family-name'} ? ' ' . $details->{'family-name'}->value : '');
    }
}
