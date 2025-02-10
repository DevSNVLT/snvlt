<?php

namespace App\Controller;

use App\Controller\Services\AdministrationService;
use App\Entity\Admin\Exercice;
use App\Repository\Autorisations\RepriseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RepriseController extends AbstractController
{
    public function __construct()
    {

    }

    #[Route('/reprises', name: 'liste_reprises')]
    public function index(RepriseRepository $pef_autorises, ManagerRegistry $registry): Response
    {

        $array_reprises = array();
        $exo = $registry->getRepository(Exercice::class)->findOneBy(['cloture'=>false],['id'=>'DESC']);
        $liste_reprises = $pef_autorises->findBy(['exercice'=>$exo],['date_autorisation'=>'DESC']);

        foreach ($liste_reprises as $reprise){
            $rs = "-";
            $code = "-";
            $marteau = "-";
            if ($reprise->getCodeAttribution()->getCodeExploitant()){
                if ($reprise->getCodeAttribution()->getCodeExploitant()->getSigle()){
                    $rs = $reprise->getCodeAttribution()->getCodeExploitant()->getSigle();
                } else {
                    $rs = $reprise->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant();
                }
                $code = $reprise->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant();
                $marteau = $reprise->getCodeAttribution()->getCodeExploitant()->getMarteauExploitant();
            }

            $array_reprises[] = array(
                'rs'=>$rs,
                'code'=>$code,
                'marteau'=>$marteau,
                'autorisation'=>$reprise->getNumeroAutorisation(). " du ". $reprise->getDateAutorisation()->format('d/m/Y'),
                'pef'=>$reprise->getCodeAttribution()->getCodeForet()->getDenomination()
            );
        }

        return $this->render('reprises/index.html.twig', [
            'reprises' => $array_reprises,
            'exo'=>$exo->getAnnee()
        ]);
    }
}
