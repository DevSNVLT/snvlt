<?php

namespace App\Controller\Carto;

use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\Foret;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Pages\PagecpRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectionsController extends AbstractController
{
    #[Route('/carto/projections', name: 'app_carto_projections')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry,
        Connection $connection
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $sql = "SELECT * FROM pef";
                $stmt = $connection->prepare($sql);
                $resultSet = $stmt->executeQuery();
                $results = $resultSet->fetchAllAssociative();

                return $this->render('spacial_data_layer/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'data' => $results
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
    #[Route('api/spacial-data-layer/polygon', name: 'app_spacial_data_layer_polygon')]
    public function polygon(Connection $connection): JsonResponse
    {


        $sql = "SELECT gid,zone_, numero_pef, ST_AsGeoJSON(geom) AS geom FROM pef";
        $stmt = $connection->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $results = $resultSet->fetchAllAssociative();

        // Si aucun résultat n'est trouvé
        if (empty($results)) {
            return $this->json(['success'=>false, 'message' => 'Aucun résultat trouvé pour ce code.'], 404);
        }

        // Retourner les résultats au format JSON
        return $this->json([
            'success'=>true,
            'layer' => 'Périmètre trouvé avec succès !',
            'data' => $results,
        ]);
    }

    #[Route('api/spacial-data-layer/getPointBrh', name: 'app_spacial_data_layer_getbille')]
    public function layer_getbille(Request $request, Connection $connection): JsonResponse
    {
        $code = $request->query->get('code');


        if (!$code) {
            return $this->json(['success'=>false, 'error' => 'Le paramètre "code" est requis.'], 400);
        }


        $sql = "SELECT id, numero_lignepagebrh, lettre_lignepagebrh, longeur_lignepagebrh, diametre_lignepagebrh, cubage_lignepagebrh, ST_AsGeoJSON(ST_Transform(ST_SetSRID(ST_MakePoint(lignepagebrh.x_lignepagebrh, lignepagebrh.y_lignepagebrh), 32630), 4326 )) AS geom FROM metier.lignepagebrh WHERE code_pagebrh_id =" . $code;
        $stmt = $connection->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $results = $resultSet->fetchAllAssociative();

        // Si aucun résultat n'est trouvé
        if (empty($results)) {
            return $this->json(['success'=>false, 'message' => 'Aucun résultat trouvé pour ce code.'], 404);
        }

        // Retourner les résultats au format JSON
        return $this->json([
            'success'=>true,
            'layer' => 'Billes trouvé avec succès !',
            'data' => $results,
        ]);
    }
    #[Route('snvlt/page/pef/{id_foret}', name: 'search_brh_page')]
    public function search_brh_page(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_foret,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN')) {
                $foret = $registry->getRepository(Foret::class)->find($id_foret);
                $info_pef = array();
                if ($foret){
                    if ($foret->getAttributions()->get(0)->getCodeExploitant()->getSigle()){
                        $exp = $foret->getAttributions()->get(0)->getCodeExploitant()->getSigle();
                    } else {
                        $exp =   $foret->getAttributions()->get(0)->getCodeExploitant()->getRaisonSocialeExploitant();
                    }
                    $info_pef[] = array(
                        'exploitant'=>$exp
                    );
                }
                return  new JsonResponse(json_encode($info_pef));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/chargements/lst/{id_foret}', name: 'search_chargements')]
    public function search_chargements(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_foret,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {

            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN')) {
                $foret = $registry->getRepository(Foret::class)->find($id_foret);
                $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);

                $mes_pages = array();

                foreach ($attributions as $attribution){
                    $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);

                    foreach ($reprises as $reprise){
                        $doc_brhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);

                        foreach ($doc_brhs as $doc_brh){
                            $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc_brh, 'fini'=>true]);

                            foreach ($pages as $page){
                                $mes_pages[] = array(
                                    'pageid' => $page->getId(),
                                    'numero_page' => $page->getNumeroPagebrh()
                                );
                            }
                        }
                    }
                }
                //dd($mes_pages);
                return  new JsonResponse(json_encode($mes_pages));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/chargement_info/{id_page}', name: 'search_chargement_billes')]
    public function search_chargement_billes(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_page,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {

            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN')) {

                $page_brh = $registry->getRepository(Pagebrh::class)->find($id_page);
                $mes_billes = array();
                if ($page_brh){
                    $lignes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page_brh]);
                    foreach ($lignes as $ligne){

                            $mes_billes[] = array(
                                'id_bille' => $ligne->getId(),
                                'numero_bille' => $ligne->getNumeroLignepagebrh(). $ligne->getLettreLignepagebrh(),
                                'x'=>$ligne->getXLignepagebrh(),
                                'y'=>$ligne->getYLignepagebrh(),
                                'zh'=>$ligne->getZhLignepagebrh()->getZone(),
                                'essence'=>$ligne->getNomEssencebrh()->getNomVernaculaire(),
                                'lng'=>$ligne->getLongeurLignepagebrh(),
                                'dm'=>$ligne->getDiametreLignepagebrh(),
                                'vol'=>$ligne->getCubageLignepagebrh()
                            );

                    }
                }
                return  new JsonResponse(json_encode($mes_billes));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/billes_recherche/{numero_bille}', name: 'recherche_bille_carto')]
    public function recherche_bille_carto(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $numero_bille,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {

            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN')) {

                $numero = substr(strtoupper($numero_bille), 0, -1) ;
                $lettre = substr(strtoupper($numero_bille), -1);

                $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['numero_lignepagebrh'=>$numero, 'lettre_lignepagebrh'=>$lettre]);
                $mes_forets = array();

                    foreach ($billes as $bille){

                        $mes_forets[] = array(
                            'id_foret'=>$bille->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getId(),
                            'foret'=>$bille->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getNumeroForet()
                        );

                    }

                return  new JsonResponse(json_encode($mes_forets));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
