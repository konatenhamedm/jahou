<?php

namespace App\Controller\Stock;

use App\Entity\LigneRetourClient;
use App\Entity\LigneRetourFournisseur;
use App\Entity\RetourClient;
use App\Entity\RetourFournisseur;
use App\Entity\Sortie;
use App\Entity\Vente;
use App\Form\RetourClientType;
use App\Form\SortieType;
use App\Form\VenteType;
use App\Repository\ArticleRepository;
use App\Repository\ClientRepository;
use App\Repository\FournisseurRepository;
use App\Repository\LigneRetourClientRepository;
use App\Repository\LigneRetourFournisseurRepository;
use App\Repository\RetourClientRepository;
use App\Repository\RetourFournisseurRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Registry;

#[Route('/admin/stock/retour/client')]
class RetourClientController extends AbstractController
{
    private $workflow;
    private $em;
    public function __construct(Registry $workflow,EntityManagerInterface $em)
    {
        $this->workflow = $workflow;
        $this->em = $em;
    }

    #[Route('/', name: 'app_stock_retour_client_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $etats = ['creation_facture' => 'Création du retour', 'retour_c_cree' => 'Retours crées','retour_c_valider' => 'Retour validés'];
        $tabs = [];
        foreach ($etats as $etat => $label) {
            $tabs[] =  [
                'name' => $etat,
                'label' => $label,
                'url' => $this->generateUrl('app_retour_client_ls', ['etat' => $etat])
            ];
        }


        return $this->render('stock/retour_client/index.html.twig', [
            'tabs' => $tabs,
            'titre'=>"rfff"
        ]);
    }

    #[Route('/{etat}/liste', name: 'app_retour_client_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request,string $etat, DataTableFactory $dataTableFactory): Response
    {
        $titre ="";
        if ($etat == 'creation_facture') {

            $titre ="Création du retour";
        } elseif($etat == 'retour_c_cree') {

            $titre ="Retours crées";
        }else{
            $titre ="Retours validés";
        }
        if ($etat == 'creation_facture') {

            $table = $dataTableFactory->create()
                ->add('reference', TextColumn::class, ['label' => 'Réference'])
                ->add('client', TextColumn::class, ['field' => 'client.fullName','label' => 'Fournisseur'])
                ->add('dateVente', DateTimeColumn::class, ['label' => 'Date vente','format' => 'd-m-Y'])
                ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Vente::class,

                    'query' => function(QueryBuilder $qb){
                        $qb->select('s, client')
                            ->from(Vente::class, 's')
                            ->join('s.client', 'client')
                            ->where("s.etat =:etat")
                            ->setParameter('etat', "creation_facture")
                        ;
                    }
                ])
                ->setName('dt_app_stock_retour_client');
        }else{
            $table = $dataTableFactory->create()
                ->add('reference', TextColumn::class, ['label' => 'Réference'])
                ->add('client', TextColumn::class, ['field' => 'client.fullName','label' => 'Fournisseur'])
                ->add('dateRetour', DateTimeColumn::class, ['label' => 'Date approvisionnement','format' => 'd-m-Y'])
                ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => RetourClient::class,

                    'query' => function(QueryBuilder $qb) use ($etat){
                        $qb->select('s, client')
                            ->from(RetourClient::class, 's')
                            ->join('s.client', 'client')

                        ;
                        if ($etat == 'retour_c_cree') {
                            $qb->andWhere("s.etat =:etat")
                                ->setParameter('etat', "retour_c_cree");
                        } elseif ($etat == 'retour_c_valider') {
                            $qb->andWhere("s.etat =:etat")
                                ->setParameter('etat', "retour_c_valider");
                        }
                    }
                ])
                ->setName('dt_app_stock_retour_client');

        }
        if ($etat == 'creation_facture') {
            $renders = [
                'creation_retour' => new ActionRender(fn() => $etat == 'creation_facture'),
                /* 'workflow_validation' =>  new ActionRender(fn() => $etat == 'passer_en_validation'),
                 'workflow_livraison' =>  new ActionRender(fn() => $etat == 'demande_valider'),*/
            ];
        }else{
            $renders = [
                'edit' => new ActionRender(fn() => $etat == 'retour_c_cree' || $etat == 'retour_c_valider'),
                /* 'workflow_validation' =>  new ActionRender(fn() => $etat == 'passer_en_validation'),
                 'workflow_livraison' =>  new ActionRender(fn() => $etat == 'demande_valider'),*/
            ];
        }
        /*   $renders = [
               'edit' =>  new ActionRender(function () {
                   return true;
               }),
               'delete' => new ActionRender(function () {
                   return true;
               }),
           ];*/


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }
        if($etat == 'creation_facture'){
            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions'
                    , 'orderable' => false
                    ,'globalSearchable' => false
                    ,'className' => 'grid_row_actions'
                    , 'render' => function ($value, Vente $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'creation_retour' => [
                                    'url' => $this->generateUrl('app_stock_creation_retour_client', ['id' => $value])
                                    , 'ajax' => true
                                    , 'icon' => '%icon% bi bi-plus'
                                    , 'attrs' => ['class' => 'btn-default']
                                    , 'render' => $renders['creation_retour']
                                ],

                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        } else{
            if ($hasActions) {

                $table->add('id', TextColumn::class, [
                    'label' => 'Actions'
                    , 'orderable' => false
                    ,'globalSearchable' => false
                    ,'className' => 'grid_row_actions'
                    , 'render' => function ($value, RetourClient $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_stock_retour_client_edit', ['id' => $value])
                                    , 'ajax' => true
                                    , 'icon' => '%icon% bi bi-pen'
                                    , 'attrs' => ['class' => 'btn-success']
                                    , 'render' => $renders['edit']
                                ],

                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('stock/retour_client/liste.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'titre'=>$titre
        ]);
    }


    #[Route('/{id}/creation/retour', name: 'app_stock_creation_retour_client', methods: ['GET', 'POST'])]
    public function creationRetour(Request $request, Vente $vente,RetourClientRepository $retourClientRepository,LigneRetourClientRepository $ligneRetourClientRepository,ClientRepository $clientRepository, ArticleRepository $articleRepository, FormError $formError,RetourFournisseurRepository $retourFournisseurRepository): Response
    {
        $form = $this->createForm(VenteType::class, $vente, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_creation_retour_client', [
                'id' => $vente->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_retour_client_index');
            $lignes = $form->get('ligneSorties')->getData();
            if ($form->isValid()) {
                $retour = new RetourClient();
                //dd();
                $retour->setDateRetour(new \DateTime())
                    ->setReference($this->numero())
                    ->setLibelle($form->get('libelle')->getData())
                    ->setClient($clientRepository->find($form->get('client')->getData()->getId()))
                    ->setType($form->get('type')->getData());
                    if($form->get('nom')->getData() !=''){
                        $retour->setNom($form->get('nom')->getData());
                    }
                if($form->get('prenom')->getData() !=''){
                    $retour->setPrenom($form->get('prenom')->getData());
                }
                if($form->get('telephone')->getData() !=''){
                    $retour->setTelephone($form->get('telephone')->getData());
                }
                    $retour->setEtat("retour_c_cree");

                $retourClientRepository->add($retour, true);

                foreach ($lignes as $ligne) {
                    $ligneRetour = new LigneRetourClient();
                    $ligneRetour->setArticle($articleRepository->find($ligne->getArticle()))
                        /* ->setQuantiteRetournee($ligne->getQuantiteDemande())*/
                        ->setQuantite($ligne->getQuantite())
                        ->setRemise($ligne->getRemise())
                        ->setRetourClient($retour);
                    $ligneRetourClientRepository->add($ligneRetour, true);
                }

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);

            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }

            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('stock/retour_client/creation_retour.html.twig', [
            'sortie' => $vente,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_stock_retour_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RetourClientRepository $retourClientRepository, FormError $formError): Response
    {
        $retourClient = new RetourClient();
        $form = $this->createForm(RetourClientType::class, $retourClient, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_retour_client_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_retour_client_index');




            if ($form->isValid()) {

                $retourClientRepository->save($retourClient, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);


            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }

            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }


        }

        return $this->renderForm('stock/retour_client/new.html.twig', [
            'retour_client' => $retourClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_stock_retour_client_show', methods: ['GET'])]
    public function show(RetourClient $retourClient): Response
    {
        return $this->render('stock/retour_client/show.html.twig', [
            'retour_client' => $retourClient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_retour_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RetourClient $retourClient, RetourClientRepository $retourClientRepository, FormError $formError): Response
    {

        $form = $this->createForm(RetourClientType::class, $retourClient, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_retour_client_edit', [
                    'id' =>  $retourClient->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_retour_client_index');


            if ($form->isValid()) {
                $workflow = $this->workflow->get($retourClient, 'retour_client');
                if ($form->isValid()) {
                    if ($form->get('transition')->isClicked()){

                        $data = $form['ligneRetourClients']->getData();
                        try {
                            if($data){
                                $retourClientRepository->save($retourClient, true);

                                if ($workflow->can($retourClient,'passer')){
                                    $workflow->apply($retourClient, 'passer');
                                    $this->em->flush();
                                    $retourClientRepository->save($retourClient, true);
                                }
                            }

                        } catch (LogicException $e) {

                            $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));
                        }
                        //$venteRepository->save($vente, true);
                    }else{
                        $retourClientRepository->save($retourClient, true);
                    };

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);


            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }

            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }
        }

        return $this->renderForm('stock/retour_client/edit.html.twig', [
            'retour_client' => $retourClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_retour_client_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, RetourClient $retourClient, RetourClientRepository $retourClientRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_stock_retour_client_delete'
                ,   [
                        'id' => $retourClient->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $retourClientRepository->remove($retourClient, true);

            $redirect = $this->generateUrl('app_stock_retour_client_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->renderForm('stock/retour_client/delete.html.twig', [
            'retour_client' => $retourClient,
            'form' => $form,
        ]);
    }

    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(RetourClient::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0){
            $nb = 1;
        }else{
            $nb =$nb + 1;
        }
        return (date("y").'APP'.date("m", strtotime("now")).str_pad($nb, 3, '0', STR_PAD_LEFT));

    }
}
