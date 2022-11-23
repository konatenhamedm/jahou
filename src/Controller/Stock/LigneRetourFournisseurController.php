<?php

namespace App\Controller\Stock;

use App\Entity\LigneRetourFournisseur;
use App\Form\LigneRetourFournisseurType;
use App\Repository\LigneRetourFournisseurRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/ligne/retour/fournisseur')]
class LigneRetourFournisseurController extends AbstractController
{
    #[Route('/', name: 'app_stock_ligne_retour_fournisseur_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->createAdapter(ORMAdapter::class, [
            'entity' => LigneRetourFournisseur::class,
        ])
        ->setName('dt_app_stock_ligne_retour_fournisseur');

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
                , 'render' => function ($value, LigneRetourFournisseur $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('app_stock_ligne_retour_fournisseur_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-pen'
                            , 'attrs' => ['class' => 'btn-default']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#exampleModalSizeNormal',
                            'url' => $this->generateUrl('app_stock_ligne_retour_fournisseur_delete', ['id' => $value])
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


        return $this->render('stock/ligne_retour_fournisseur/index.html.twig', [
            'datatable' => $table
        ]);
    }

    #[Route('/new', name: 'app_stock_ligne_retour_fournisseur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LigneRetourFournisseurRepository $ligneRetourFournisseurRepository, FormError $formError): Response
    {
        $ligneRetourFournisseur = new LigneRetourFournisseur();
        $form = $this->createForm(LigneRetourFournisseurType::class, $ligneRetourFournisseur, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_ligne_retour_fournisseur_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_ligne_retour_fournisseur_index');




            if ($form->isValid()) {

                $ligneRetourFournisseurRepository->save($ligneRetourFournisseur, true);
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

        return $this->renderForm('stock/ligne_retour_fournisseur/new.html.twig', [
            'ligne_retour_fournisseur' => $ligneRetourFournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_stock_ligne_retour_fournisseur_show', methods: ['GET'])]
    public function show(LigneRetourFournisseur $ligneRetourFournisseur): Response
    {
        return $this->render('stock/ligne_retour_fournisseur/show.html.twig', [
            'ligne_retour_fournisseur' => $ligneRetourFournisseur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_ligne_retour_fournisseur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LigneRetourFournisseur $ligneRetourFournisseur, LigneRetourFournisseurRepository $ligneRetourFournisseurRepository, FormError $formError): Response
    {

        $form = $this->createForm(LigneRetourFournisseurType::class, $ligneRetourFournisseur, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_ligne_retour_fournisseur_edit', [
                    'id' =>  $ligneRetourFournisseur->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_ligne_retour_fournisseur_index');


            if ($form->isValid()) {

                $ligneRetourFournisseurRepository->save($ligneRetourFournisseur, true);
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

        return $this->renderForm('stock/ligne_retour_fournisseur/edit.html.twig', [
            'ligne_retour_fournisseur' => $ligneRetourFournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_ligne_retour_fournisseur_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, LigneRetourFournisseur $ligneRetourFournisseur, LigneRetourFournisseurRepository $ligneRetourFournisseurRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_stock_ligne_retour_fournisseur_delete'
                ,   [
                        'id' => $ligneRetourFournisseur->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $ligneRetourFournisseurRepository->remove($ligneRetourFournisseur, true);

            $redirect = $this->generateUrl('app_stock_ligne_retour_fournisseur_index');

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

        return $this->renderForm('stock/ligne_retour_fournisseur/delete.html.twig', [
            'ligne_retour_fournisseur' => $ligneRetourFournisseur,
            'form' => $form,
        ]);
    }
}
