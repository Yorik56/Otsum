<?php

namespace App\Service;

use App\Entity\ContactRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Utils
{
    /** KernelInterface $appKernel */
    private KernelInterface $appKernel;
    private EntityManagerInterface $entityManager;

    private const FILENAMES =  [
        7  => 'sept_lettres.txt',
        8  => 'huit_lettres.txt',
        9  => 'neuf_lettres.txt',
        10 => 'dix_lettres.txt',
    ];

    private const VERIFICATION_API_URL = "https://frenchwordsapi.herokuapp.com/api/WordDefinition?idOrName=";
    private const VERIFICATION_API_VALID_RESPONSE = "WordID";

    public function __construct(EntityManagerInterface $entityManager, KernelInterface $appKernel)
    {
        $this->appKernel     = $appKernel;
        $this->entityManager = $entityManager;
    }

    /**
     * Generates a list of words
     *
     * @return void
     */
    function generatesValidList(){
        $projectDir = $this->appKernel->getProjectDir();
        $file = $projectDir . '/public/liste_francais2.txt';
        $file_arr = file($file);
        $num_lines = count($file_arr);
        $last_arr_index = $num_lines - 1;
        $rand_index = rand(0, $last_arr_index);
        $random_word = $file_arr[$rand_index];
        $nombre_de_mots = 0;
        setlocale(LC_CTYPE, 'fr_FR','fra'); //Fournit les informations de localisation
        foreach ($file_arr as $index => $word){
            $word_length = mb_strlen(trim( $word, " \n\r\t\v\x00"));
            if (($word_length > 6) && ($word_length < 11)  ){
                if (preg_match('#^[a-z]*$#',trim( $word, " \n\r\t\v\x00"))){
                    $nombre_de_mots++;
                    /*écriture du mot dans le fichier approprié*/
                    $random_word = fopen($projectDir.'/public/'.Utils::FILENAMES[$word_length], "a") or die("Unable to open file!");
                    fwrite($random_word, $word);
                    fclose($random_word);
                }
            }
        }
        /*écriture du nombre de mots dans un fichier séparé*/
        $compteur = fopen($projectDir.'/public/compteur.txt', "a") or die("Unable to open file!");
        fwrite($compteur, $nombre_de_mots);
        fclose($compteur);

    }

    /**
     * Searching for the word in local library,
     * then if not found searching for the word from an API
     *
     * @param string $word
     * @return bool
     */
    public function checkWordExistence(string $word): bool
    {
        $wordExists = false;

        // Count number of letters
        $numberOfLetters = strlen($word);
        // Get the words that have this number of letters
        $fileName = $this->appKernel->getProjectDir() . '\\public\\' .  Utils::FILENAMES[$numberOfLetters];



        // Is this entry a file or directory?
        if (is_file($fileName))
        {
            // Its a file, yay! Lets get the file's contents
            $data = file_get_contents($fileName);
            // Is the str in the local data (case-insensitive search)
            if(stripos($data, $word)){
                $wordExists = true;
            } else {
                // Is the str in the api
                $url = Utils::VERIFICATION_API_URL. $word;
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                //for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $resp = curl_exec($curl);
                curl_close($curl);
                if(isset(json_decode($resp, true)[Utils::VERIFICATION_API_VALID_RESPONSE])){
                    $wordExists = true;
                }
            }
        }
        return $wordExists;
    }


    /**
     * Return the contact liste of the current user
     *
     * @param $idUser
     * @return array
     */
    public function getContacts($idUser): array
    {
        $contacts = $this->entityManager->getRepository(entityName: ContactRequest::class)
            ->mesContacts(userId: $idUser);
        $usersContact = [];
        foreach ($contacts as $index => $contact) {
            $user = $this->entityManager->getRepository(entityName: User::class)
                ->findOneBy([
                    'id' => $contact['contact']
                ]);
            if($user){
                $usersContact[] = $user;
            }
        }

        return $usersContact;
    }

    /**
     * Return the contact liste of the current user
     *
     * @param $idUser
     * @return array
     */
    public function getContactsOnline($idUser): array
    {
        $contacts = $this->entityManager->getRepository(entityName: ContactRequest::class)
            ->mesContacts(userId: $idUser);
        $usersContact = [];
        foreach ($contacts as $index => $contact) {
            $user = $this->entityManager->getRepository(entityName: User::class)
                ->findOneBy([
                    'id' => $contact['contact'],
                    'connected' => 1
                ]);
            if($user){
                $usersContact[] = $user;
            }
        }

        return $usersContact;
    }



    public function getRandomWord($parametre_longueur_mot){
        // Parameters to get a word
        $nomsFichiers = [
            7  => 'sept_lettres.txt',
            8  => 'huit_lettres.txt',
            9  => 'neuf_lettres.txt',
            10 => 'dix_lettres.txt',
        ];
        //--- Get a random word
        $projectDir = $this->appKernel->getProjectDir();
        $file = $projectDir . '/public/'.$nomsFichiers[$parametre_longueur_mot];
        $file_arr = file($file);
        $num_lines = count($file_arr);
        $last_arr_index = $num_lines - 1;
        $rand_index = rand(0, $last_arr_index);

        return $file_arr[$rand_index];
    }
}
