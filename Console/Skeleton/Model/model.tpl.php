<?php
/*======================================================================*\
||  Corona CMS						                                    ||
||  Fichier : {{namespace}}\{{classname}}.php                           ||
||  Version : 1.0.0.                                	                ||
||  Auteur  : {{author}}                                                ||
||  ------------------------------------------------------------------  ||
||  Copyright ©2021 Corona CMS                                          ||
\*======================================================================*/
namespace {{namespace}};

use DateTime;
use App\Helpers\CustomDate;
use App\Core\Database\Model;

class {{classname}} extends Model 
{
    protected string $table = "{{table}}";                      // Le nom de la table
    protected string $pk = "id";                               // Clé primaire       
    protected int $id;                                        // L'ID
    {{properties}}
    protected string $createdAt;                            // La date de création
    protected string $updatedAt;                           // La date de mise à jour

    /**
    *   Cette méthode renvoie l'ID de l'objet.
    *   
    *   @return int 
    **/
    public function GetID(): int
    {
        return $this->id;
    }

    /**
    *   Cette méthode renvoie la date de création.
    *
    *   @return string
    **/
    public function GetCreatedAt(): string
    {
        return new CustomDate($this->createdAt);
    }

    /**
    *   Cette méthode renvoie la date de mise à jour.
    *
    *   @return string
    **/
    public function GetUpdatedAt(): string
    {
        return new CustomDate($this->updatedAt);
    }

    {{methods}}
}

?>