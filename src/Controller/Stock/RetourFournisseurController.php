<?php

namespace App\Controller\Stock;

use App\Entity\LigneRetourFournisseur;
use App\Entity\RetourFournisseur;
use App\Entity\Sortie;
use App\Form\RetourFournisseurType;
use App\Form\SortieType;
use App\Repository\ArticleRepository;
use App\Repository\FournisseurRepository;
use App\Repository\LigneRetourFournisseurRepository;
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

#[Route('/stock/retour/fournisseur')]
class RetourFournisseurController extends AbstractController
{

    private $workflow;
    private $em;
    public function __construct(Registry $workflow,EntityManagerInterface $em)
    {
        $this->workflow = $workflow;
        $this->em = $em;
    }

    #[Route('/', name: 'app_stock_retour_fournisseur_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $etats = ['valider' => 'Création du retour', 'retour_f_cree' => 'Retours crées','retour_f_valider' => 'Retour validés'];
        $tabs = [];
        foreach ($etats as $etat => $label) {
            $tabs[] =  [
                'name' => $etat,
                'label' => $label,
                'url' => $this->generateUrl('app_retour_fournisseur_ls', ['etat' => $etat])
            ];
        }


        return $this->render('stock/retour_fournisseur/index.html.twig', [
            'tabs' => $tabs,
            'titre'=>"rfff"
        ]);
    }

    #[Route('/{etat}/liste', name: 'app_retour_fournisseur_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request,string $etat, DataTableFactory $dataTableFactory): Response
    {
        $titre ="";
        if ($etat == 'valider') {

            $titre ="Création du retour";
        } elseif($etat == 'retour_f_cree') {

            $titre ="Retours crées";
        }else{
            $titre ="Retours validés";
        }
        if ($etat == 'valider') {

            $table = $dataTableFactory->create()
                ->add('reference', TextColumn::class, ['label' => 'Réference'])
                ->add('fournisseur', TextColumn::class, ['field' => 'fournisseur.denominationSocial','label' => 'Fournisseur'])
                ->add('dateSortie', DateTimeColumn::class, ['label' => 'Date approvisionnement','format' => 'd-m-Y'])
                ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Sortie::class,

                    'query' => function(QueryBuilder $qb){
                        $qb->select('s, fournisseur')
                            ->from(Sortie::class, 's')
                            ->join('s.fournisseur', 'fournisseur')
                            ->where("s.etat =:etat")
                            ->setParameter('etat', "valider")
                        ;
                    }
                ])
                ->setName('dt_app_stock_retour_fournisseur');
        }else{
            $table = $dataTableFactory->create()
                ->add('reference', TextColumn::class, ['label' => 'Réference'])
                ->add('fournisseur', TextColumn::class, ['field' => 'fournisseur.denominationSocial','label' => 'Fournisseur'])
                ->add('dateRetour', DateTimeColumn::class, ['label' => 'Date approvisionnement','format' => 'd-m-Y'])
                ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => RetourFournisseur::class,

                    'query' => function(QueryBuilder $qb) use ($etat){
                        $qb->select('s, fournisseur')
                            ->from(RetourFournisseur::class, 's')
                            ->join('s.fournisseur', 'fournisseur')

                        ;
                        if ($etat == 'retour_f_cree') {
                            $qb->andWhere("s.etat =:etat")
                                ->setParameter('etat', "retour_f_cree");
                        } elseif ($etat == 'retour_f_valider') {
                            $qb->andWhere("s.etat =:etat")
                                ->setParameter('etat', "retour_f_valider");
                        }
                    }
                ])
                ->setName('dt_app_stock_retour_fournisseur');

        }
        if ($etat == 'valider') {
            $renders = [
                'creation_retour' => new ActionRender(fn() => $etat == 'valider'),
                /* 'workflow_validation' =>  new ActionRender(fn() => $etat == 'passer_en_validation'),
                 'workflow_livraison' =>  new ActionRender(fn() => $etat == 'demande_valider'),*/
            ];
        }else{
            $renders = [
                'edit' => new ActionRender(fn() => $etat == 'retour_f_cree' || $etat == 'retour_f_valider'),
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
if($etat == 'valider'){
    if ($hasActions) {
        $table->add('id', TextColumn::class, [
            'label' => 'Actions'
            , 'orderable' => false
            ,'globalSearchable' => false
            ,'className' => 'grid_row_actions'
            , 'render' => function ($value, Sortie $context) use ($renders) {
                $options = [
                    'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                    'target' => '#exampleModalSizeLg2',

                    'actions' => [
                        'creation_retour' => [
                            'url' => $this->generateUrl('app_stock_creation_retour_fournisseur', ['id' => $value])
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
            , 'render' => function ($value, RetourFournisseur $context) use ($renders) {
                $options = [
                    'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                    'target' => '#exampleModalSizeLg2',

                    'actions' => [
                        'edit' => [
                            'url' => $this->generateUrl('app_stock_retour_fournisseur_edit', ['id' => $value])
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


        return $this->render('stock/retour_fournisseur/liste.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'titre'=>$titre
        ]);
    }


    #[Route('/{id}/creation/retour', name: 'app_stock_creation_retour_fournisseur', methods: ['GET', 'POST'])]
    public function creationRetour(Request $request, Sortie $sortie,LigneRetourFournisseurRepository $ligneRetourFournisseurRepository,FournisseurRepository $fournisseurRepository, ArticleRepository $articleRepository, FormError $formError,RetourFournisseurRepository $retourFournisseurRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_creation_retour_fournisseur', [
                'id' => $sortie->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_retour_fournisseur_index');
            $lignes = $form->get('ligneEntrees')->getData();
            if ($form->isValid()) {
                $retour = new RetourFournisseur();
                //dd();
                $retour->setDateRetour(new \DateTime())
                    ->setReference($this->numero())
                    ->setLibelle($form->get('libelle')->getData())
                    ->setFournisseur($fournisseurRepository->find($form->get('fournisseur')->getData()->getId()))
                    ->setEtat("retour_f_cree");
                $retourFournisseurRepository->add($retour, true);

                foreach ($lignes as $ligne) {
                    $ligneRetour = new LigneRetourFournisseur();
                    $ligneRetour->setArticle($articleRepository->find($ligne->getArticle()))
                        /* ->setQuantiteRetournee($ligne->getQuantiteDemande())*/
                        ->setQuantite($ligne->getQuantite())
                        ->setRetourFournisseur($retour);
                    $ligneRetourFournisseurRepository->add($ligneRetour, true);
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

        return $this->renderForm('stock/retour_fournisseur/creation_retour.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_stock_retour_fournisseur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RetourFournisseurRepository $retourFournisseurRepository, FormError $formError): Response
    {
        $retourFournisseur = new RetourFournisseur();
        $form = $this->createForm(RetourFournisseurType::class, $retourFournisseur, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_retour_fournisseur_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_retour_fournisseur_index');




            if ($form->isValid()) {

                $retourFournisseurRepository->save($retourFournisseur, true);
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

        return $this->renderForm('stock/retour_fournisseur/new.html.twig', [
            'retour_fournisseur' => $retourFournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_stock_retour_fournisseur_show', methods: ['GET'])]
    public function show(RetourFournisseur $retourFournisseur): Response
    {
        return $this->render('stock/retour_fournisseur/show.html.twig', [
            'retour_fournisseur' => $retourFournisseur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_retour_fournisseur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RetourFournisseur $retourFournisseur, RetourFournisseurRepository $retourFournisseurRepository, FormError $formError): Response
    {

        $form = $this->createForm(RetourFournisseurType::class, $retourFournisseur, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_retour_fournisseur_edit', [
                    'id' =>  $retourFournisseur->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_retour_fournisseur_index');


            if ($form->isValid()) {
                $workflow = $this->workflow->get($retourFournisseur, 'retour_fournisseur');
                if ($form->isValid()) {
                    if ($form->get('transition')->isClicked()){

                        $data = $form['ligneRetourFournisseurs']->getData();
                        try {
                            if($data){
                                $retourFournisseurRepository->save($retourFournisseur, true);

                                if ($workflow->can($retourFournisseur,'passer')){
                                    $workflow->apply($retourFournisseur, 'passer');
                                    $this->em->flush();
                                    $retourFournisseurRepository->save($retourFournisseur, true);
                                }
                            }

                        } catch (LogicException $e) {

                            $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));
                        }
                        //$venteRepository->save($vente, true);
                    }else{
                        $retourFournisseurRepository->save($retourFournisseur, true);
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

        return $this->renderForm('stock/retour_fournisseur/edit.html.twig', [
            'retour_fournisseur' => $retourFournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_retour_fournisseur_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, RetourFournisseur $retourFournisseur, RetourFournisseurRepository $retourFournisseurRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_stock_retour_fournisseur_delete'
                ,   [
                        'id' => $retourFournisseur->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $retourFournisseurRepository->remove($retourFournisseur, true);

            $redirect = $this->generateUrl('app_stock_retour_fournisseur_index');

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

        return $this->renderForm('stock/retour_fournisseur/delete.html.twig', [
            'retour_fournisseur' => $retourFournisseur,
            'form' => $form,
        ]);
    }

    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(RetourFournisseur::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0){
            $nb = 1;
        }else{
            $nb =$nb + 1;
        }
        return (date("y").'REF'.date("m", strtotime("now")).str_pad($nb, 3, '0', STR_PAD_LEFT));

    }
}
