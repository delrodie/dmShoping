<?php

namespace App\Utilities;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class GestionLog
{

    private $logger;
    private $kernel;
    private $request;

    public function __construct(LoggerInterface $logger, KernelInterface $kernel, RequestStack $request)
    {

        $this->logger = $logger;
        $this->kernel = $kernel;
        $this->request = $request;
    }

    /**
     * Anregistrement du log dans le fichier du jour
     *
     * @param $str
     * @return bool
     */
    public function addLogger($str)
    {
        $log = $str." ".$this->request->getCurrentRequest()->getClientIp();
        $this->logger->info($log);

        return true;
    }

    public function monitoring($date)
    {
        // Recuperer la date puis affecter l'extension
        // Recuperer l'environnement encours puis chercher le chemin du repertoire
        $extension = $date.'.txt';
        $env = $this->kernel->getEnvironment();
        $racine = $this->kernel->getProjectDir().'/var/log/'.$env.'.monitoring-'.$extension;

        // Si le fichier n'existe pas alors retourner false
        // Sinon renvoyer le fichier ouvert
        if (!file_exists($racine))return false;
        else{
            $fichier = file($racine);

            return $fichier;
        }
    }
}