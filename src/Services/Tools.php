<?php
namespace App\Services;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class Tools
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;

    }

    /**
     * Undocumented function
     *
     * @return User
     */
    public function getUser(){
       return $this->security->getUser();

    }

    /**
     * méthode qui renvoie l'adresse complette en form de string
     *
     * @return string
     */
    public function getEttiquet() :string
    {
        $user = $this->getUser();
        $adresse = $user->getAdresse() . ' ' . $user->getVille() . ' ' . $user->getCpost();
        return $adresse;
    }

}
