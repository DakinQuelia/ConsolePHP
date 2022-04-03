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

use App\Controllers\Controller as Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class {{classname}} extends Controller
{
    /**
	*	On affiche la page
	*	
	*	@param Request $request		Requête
	*	@param Response $response 	Réponse
	*	@return View $view			
	**/
    public function Index(Request $request, Response $response)
    {
        return $this->Render($response, '{{templatename}}', [
            'controller_name' => '{{classname}}',
        ]);
    }
}

?>