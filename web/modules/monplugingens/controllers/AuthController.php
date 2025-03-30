<?php
namespace modules\monplugingens\controllers;

use Craft;
use craft\web\Controller;
use modules\monplugingens\Module as gensmodule; // Importer la classe du Module
use yii\web\Response;

class AuthController extends Controller
{
    protected array|int|bool $allowAnonymous = true;

    public function actionLogin(): ?Response
    {
        $request = Craft::$app->getRequest();
        if ($request->getIsPost()) {
            // ... récupérer loginIdentifier et password ...

            // ***** CHANGEMENT : Récupérer l'instance du MODULE *****
            /** @var GensModule|null $gensModule */
            $gensModule = Craft::$app->getModule('monplugingens'); // Utiliser le handle défini dans app.php

            if ($gensModule) {
                $loggedInGens = $gensModule->loginGens(
                    $loginIdentifier,
                    $password,
                    'mail', // Adapter si besoin
                    'pass'  // Adapter si besoin
                );

                if ($loggedInGens) {
                    // Connexion réussie ...
                    // ... (logique de session, redirection) ...
                     Craft::$app->getSession()->set('loggedInGensId', $loggedInGens->id);
                     return $this->redirectTo('/espace-membre');
                } else {
                    // Échec ...
                    // ... (message d'erreur, redirection) ...
                     Craft::$app->getSession()->setError("Identifiant ou mot de passe incorrect.");
                     return $this->redirectTo('/connexion');
                }
            } else {
                 Craft::$app->getSession()->setError("Erreur interne du module.");
                 Craft::error('Module monplugingens non trouvé ou inactif.');
                 return $this->redirectTo('/connexion');
            }
        }
        // ... afficher le formulaire ...
         return $this->renderTemplate('chemin/vers/template/loginForm');
    }

     public function actionLogout(): Response
     {
         Craft::$app->getSession()->remove('loggedInGensId');
         return $this->redirectTo('/');
     }
}