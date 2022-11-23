<?php

namespace App\Controller\Stock;

use App\Entity\MouvementSortie;
use App\Form\MouvementSortieType;
use App\Repository\MouvementSortieRepository;
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

#[Route('/admin/stock/mouvement/sortie')]
class MouvementSortieController extends AbstractController
{
    #[Route('/', name: 'app_stock_mouvement_sortie_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->createAdapter(ORMAdapter::class, [
            'entity' => MouvementSortie::class,
        ])
        ->setName('dt_app_stock_mouvement_sortie');

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
                , 'render' => function ($value, MouvementSortie $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('app_stock_mouvement_sortie_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-pen'
                            , 'attrs' => ['class' => 'btn-default']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#exampleModalSizeNormal',
                            'url' => $this->generateUrl('app_stock_mouvement_sortie_delete', ['id' => $value])
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


        return $this->render('stock/mouvement_sortie/index.html.twig', [
            'datatable' => $table
        ]);
    }

    #[Route('/new', name: 'app_stock_mouvement_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MouvementSortieRepository $mouvementSortieRepository, FormError $formError): Response
    {
        $mouvementSortie = new MouvementSortie();
        $form = $this->createForm(MouvementSortieType::class, $mouvementSortie, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_mouvement_sortie_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_mouvement_sortie_index');




            if ($form->isValid()) {

                $mouvementSortieRepository->save($mouvementSortie, true);
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

        return $this->renderForm('stock/mouvement_sortie/new.html.twig', [
            'mouvement_sortie' => $mouvementSortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_stock_mouvement_sortie_show', methods: ['GET'])]
    public function show(MouvementSortie $mouvementSortie): Response
    {
        return $this->render('stock/mouvement_sortie/show.html.twig', [
            'mouvement_sortie' => $mouvementSortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_mouvement_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MouvementSortie $mouvementSortie, MouvementSortieRepository $mouvementSortieRepository, FormError $formError): Response
    {

        $form = $this->createForm(MouvementSortieType::class, $mouvementSortie, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_mouvement_sortie_edit', [
                    'id' =>  $mouvementSortie->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_mouvement_sortie_index');


            if ($form->isValid()) {

                $mouvementSortieRepository->save($mouvementSortie, true);
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

        return $this->renderForm('stock/mouvement_sortie/edit.html.twig', [
            'mouvement_sortie' => $mouvementSortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_mouvement_sortie_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, MouvementSortie $mouvementSortie, MouvementSortieRepository $mouvementSortieRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_stock_mouvement_sortie_delete'
                ,   [
                        'id' => $mouvementSortie->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $mouvementSortieRepository->remove($mouvementSortie, true);

            $redirect = $this->generateUrl('app_stock_mouvement_sortie_index');

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

        return $this->renderForm('stock/mouvement_sortie/delete.html.twig', [
            'mouvement_sortie' => $mouvementSortie,
            'form' => $form,
        ]);
    }
}
