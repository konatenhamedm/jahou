<?php

namespace App\Controller\Stock;

use App\Entity\Sortie;
use App\Entity\Vente;
use App\Form\SortieType;
use App\Repository\SortieRepository;
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

#[Route('/stock/approvisionnement')]
class SortieController extends AbstractController
{


    private $workflow;
    private $em;
    public function __construct(Registry $workflow,EntityManagerInterface $em)
    {
        $this->workflow = $workflow;
        $this->em = $em;
    }


    #[Route('/', name: 'app_stock_sortie_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
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
                ;
            }
        ])
        ->setName('dt_app_stock_sortie');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

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
                            'edit' => [
                            'url' => $this->generateUrl('app_stock_sortie_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-pen'
                            , 'attrs' => ['class' => 'btn-default']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#exampleModalSizeNormal',
                            'url' => $this->generateUrl('app_stock_sortie_delete', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-trash'
                            , 'attrs' => ['class' => 'btn-main']
                            ,  'render' => $renders['delete']
                        ]
                    ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('stock/sortie/index.html.twig', [
            'datatable' => $table
        ]);
    }

    #[Route('/new', name: 'app_stock_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SortieRepository $sortieRepository, FormError $formError): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_sortie_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_sortie_index');




            if ($form->isValid()) {
                $sortie->setEtat("initie");
               $sortie->setReference($this->numero());
                $sortieRepository->save($sortie, true);
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

        return $this->renderForm('stock/sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_stock_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('stock/sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository, FormError $formError): Response
    {

        $form = $this->createForm(SortieType::class, $sortie, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_sortie_edit', [
                    'id' =>  $sortie->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_sortie_index');


            if ($form->isValid()) {

                $workflow = $this->workflow->get($sortie, 'appro');
                if ($form->isValid()) {
                    if ($form->get('transition')->isClicked()){

                        $data = $form['ligneEntrees']->getData();
                        try {
                            if($data){
                                $sortieRepository->save($sortie, true);

                                if ($workflow->can($sortie,'passer')){
                                    $workflow->apply($sortie, 'passer');
                                    $this->em->flush();
                                    $sortieRepository->save($sortie, true);
                                }
                            }

                        } catch (LogicException $e) {

                            $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));
                        }
                        //$venteRepository->save($vente, true);
                    }else{
                        $sortieRepository->save(sortie, true);
                    }
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
            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('stock/sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_sortie_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_stock_sortie_delete'
                ,   [
                        'id' => $sortie->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $sortieRepository->remove($sortie, true);

            $redirect = $this->generateUrl('app_stock_sortie_index');

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

        return $this->renderForm('stock/sortie/delete.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Sortie::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0){
            $nb = 1;
        }else{
            $nb =$nb + 1;
        }
        return (date("y").'APP'.date("m", strtotime("now")).str_pad($nb, 3, '0', STR_PAD_LEFT));

    }
}
