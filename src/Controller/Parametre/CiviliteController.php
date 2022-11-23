<?php

namespace App\Controller\Parametre;

use App\Entity\Civilite;
use App\Form\CiviliteType;
use App\Repository\CiviliteRepository;
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

#[Route('/admin/parametre/civilite')]
class CiviliteController extends AbstractController
{
    #[Route('/', name: 'app_parametre_civilite_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('code', TextColumn::class, ['label' => 'Code'])
        ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
        ->createAdapter(ORMAdapter::class, [
            'entity' => Civilite::class,
        ])
        ->setName('dt_app_parametre_civilite');

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
                , 'render' => function ($value, Civilite $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',
                            
                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('app_parametre_civilite_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-pen'
                            , 'attrs' => ['class' => 'btn-default']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#exampleModalSizeNormal',
                            'url' => $this->generateUrl('app_parametre_civilite_delete', ['id' => $value])
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


        return $this->render('parametre/civilite/index.html.twig', [
            'datatable' => $table
        ]);
    }

    #[Route('/new', name: 'app_parametre_civilite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CiviliteRepository $civiliteRepository, FormError $formError): Response
    {
        $civilite = new Civilite();
        $form = $this->createForm(CiviliteType::class, $civilite, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_civilite_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_civilite_index');

           


            if ($form->isValid()) {
                
                $civiliteRepository->add($civilite, true);
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

        return $this->renderForm('parametre/civilite/new.html.twig', [
            'civilite' => $civilite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_civilite_show', methods: ['GET'])]
    public function show(Civilite $civilite): Response
    {
        return $this->render('parametre/civilite/show.html.twig', [
            'civilite' => $civilite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_civilite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Civilite $civilite, CiviliteRepository $civiliteRepository, FormError $formError): Response
    {
        
        $form = $this->createForm(CiviliteType::class, $civilite, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_civilite_edit', [
                    'id' =>  $civilite->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_civilite_index');

           
            if ($form->isValid()) {
                
                $civiliteRepository->add($civilite, true);
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

        return $this->renderForm('parametre/civilite/edit.html.twig', [
            'civilite' => $civilite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_civilite_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Civilite $civilite, CiviliteRepository $civiliteRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_parametre_civilite_delete'
                ,   [
                        'id' => $civilite->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $civiliteRepository->remove($civilite, true);

            $redirect = $this->generateUrl('app_parametre_civilite_index');

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

        return $this->renderForm('parametre/civilite/delete.html.twig', [
            'civilite' => $civilite,
            'form' => $form,
        ]);
    }
}
