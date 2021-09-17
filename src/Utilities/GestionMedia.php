<?php


namespace App\Utilities;


use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GestionMedia
{
    private $mediaArtiste;
    private $mediaAlbum;

    public function __construct($artisteDirectory, $albumDirectory)
    {
        $this->mediaArtiste = $artisteDirectory;
        $this->mediaAlbum = $albumDirectory;
    }

    /**
     * Enregistrement du fichier dans le repertoire approprié
     *
     * @param UploadedFile $file
     * @param null $media
     * @return string
     */
    public function upload(UploadedFile $file, $media = null)
    {
        // Initialisation du slug
        $slugify = new Slugify(); //dd($file);

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugify->slugify($originalFileName);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension(); //dd($this->mediaActivite);

        // Deplacement du fichier dans le repertoire dedié
        try {
            if ($media === 'artiste') $file->move($this->mediaArtiste, $newFilename);
            elseif ($media === 'album') $file->move($this->mediaAlbum, $newFilename);
            else $file->move($this->mediaArtiste, $newFilename);
        }catch (FileException $e){

        }

        return $newFilename;
    }

    /**
     * Suppression de l'ancien media sur le server
     *
     * @param $ancienMedia
     * @param null $media
     * @return bool
     */
    public function removeUpload($ancienMedia, $media = null)
    {
        if ($media === 'artiste') unlink($this->mediaArtiste.'/'.$ancienMedia);
        elseif ($media === 'album') unlink($this->mediaAlbum.'/'.$ancienMedia);
        else return false;

        return true;
    }
}