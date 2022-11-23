<?php

namespace App\Controller\Stock;

use App\Entity\Article;
use App\Entity\LigneSortie;
use App\Entity\Vente;
use App\Form\VenteType;
use App\Repository\ArticleRepository;
use App\Repository\VenteRepository;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Registry;

#[Route('/stock/vente')]
class VenteController extends AbstractController
{


    private $workflow;
    private $em;
    private $articleRepository;
    public function __construct(Registry $workflow,EntityManagerInterface $em,ArticleRepository $articleRepository)
    {
        $this->workflow = $workflow;
        $this->em = $em;
        $this->articleRepository = $articleRepository;
    }


    #[Route('/', name: 'app_stock_vente_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
       //
        $table = $dataTableFactory->create()
            ->add('reference', TextColumn::class, ['label' => 'Réference'])
            ->add('client', TextColumn::class, ['field' => 'client.fullName','label' => 'Client'])
            ->add('dateVente', DateTimeColumn::class, ['label' => 'Date vente','format' => 'd-m-Y'])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
        ->createAdapter(ORMAdapter::class, [
            'entity' => Vente::class,
            'query' => function(QueryBuilder $qb) {
                $qb->select('v, client')
                    ->from(Vente::class, 'v')
                    ->join('v.client', 'client')
                ;

            }
        ])
        ->setName('dt_app_stock_vente');
        //dd($this->articleRepository->getMontant(1));
        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'show' => new ActionRender(function () {
                return true;
            }),
            'imprimer' => new ActionRender(function () {
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
                , 'render' => function ($value, Vente $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('app_stock_vente_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-pen'
                            , 'attrs' => ['class' => 'btn-default']
                            , 'render' => $renders['edit']
                        ],
                         'imprimer' => [
                                'url' => $this->generateUrl('imprimer', ['id' => $value])
                                , 'ajax' => false
                                , 'target' => '_blank'
                                , 'icon' => '%icon% bi bi-download'
                                , 'attrs' => ['class' => 'btn-success', 'title' => 'Imprimer document', 'target' => '_blank']
                                , 'render' =>$renders['imprimer']

                            ],
                        'show' => [
                            'target' => '#exampleModalSizeNormal',
                            'url' => $this->generateUrl('app_stock_vente_show', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-eye'
                            , 'attrs' => ['class' => 'btn-main']
                            ,  'render' => $renders['show']
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


        return $this->render('stock/vente/index.html.twig', [
            'datatable' => $table
        ]);
    }

    #[Route('/new', name: 'app_stock_vente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VenteRepository $venteRepository, FormError $formError): Response
    {
        $vente = new Vente();
        $ligneSortie = new LigneSortie();
        $ligneSortie->setQuantite(1)
            ->setRemise(0);
        $vente->addLigneSorty($ligneSortie);
        $form = $this->createForm(VenteType::class, $vente, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_vente_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();



        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_vente_index');
            $workflow = $this->workflow->get($vente, 'vente');
            if ($form->isValid()) {

                $vente->setEtat("creation_minute");
                $vente->setReference($this->numero());
                $venteRepository->save($vente, true);
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

        return $this->renderForm('stock/vente/new.html.twig', [
            'vente' => $vente,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_stock_vente_show', methods: ['GET'])]
    public function show(Vente $vente): Response
    {
        return $this->render('stock/vente/show.html.twig', [
            'vente' => $vente,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_vente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vente $vente, VenteRepository $venteRepository, FormError $formError): Response
    {

        $form = $this->createForm(VenteType::class, $vente, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_stock_vente_edit', [
                    'id' =>  $vente->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_stock_vente_index');

            $workflow = $this->workflow->get($vente, 'vente');
            if ($form->isValid()) {
                if ($form->get('transition')->isClicked()){

                    $data = $form['ligneSorties']->getData();
                    try {
                      if($data){
                          $venteRepository->save($vente, true);

                          if ($workflow->can($vente,'passer')){
                              $workflow->apply($vente, 'passer');
                              $this->em->flush();
                              $venteRepository->save($vente, true);
                          }
                      }

                    } catch (LogicException $e) {

                        $this->addFlash('danger', sprintf('No, that did not work: %s', $e->getMessage()));
                    }
                    //$venteRepository->save($vente, true);
                }else{
                    $venteRepository->save($vente, true);
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


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('stock/vente/edit.html.twig', [
            'vente' => $vente,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_vente_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Vente $vente, VenteRepository $venteRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_stock_vente_delete'
                ,   [
                        'id' => $vente->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $venteRepository->remove($vente, true);

            $redirect = $this->generateUrl('app_stock_vente_index');

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

        return $this->renderForm('stock/vente/delete.html.twig', [
            'vente' => $vente,
            'form' => $form,
        ]);
    }

    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Vente::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0){
            $nb = 1;
        }else{
            $nb =$nb + 1;
        }
        return (date("y").'FAC'.date("m", strtotime("now")).str_pad($nb, 3, '0', STR_PAD_LEFT));

    }


    #[Route('/montant', name: 'montant', methods: ['GET', 'POST'])]
    public function existe(ArticleRepository $repository,Request $request): Response
    {
        $response = new Response();
        $format="";



        if ($request->isXmlHttpRequest()) {
            $id = "";
            $id = $request->get("id");
            $data  = $repository->getMontant($id);
            $arrayCollection[] = array(
                'montant' =>  $data,

                // ... Same for each property you want
            );
            $data = json_encode($data); // formater le résultat de la requête en json
            //dd($data);
            $response->headers->set('Content-type', 'application/json');
            $response->setContent($data);
        }
        return $response;

    }

    #[Route('/{id}/imprimer', name: 'imprimer', methods: ['GET', 'POST'])]
    public function imprimer($id, Request $request)
    {



        $html = $this->renderView('stock/vente/imprimer.html.twig', [
           // 'info'=>$scolariteRepository->getInfoEleve($id),
            //'versement' => $versementRepository->getAllVersementBySolarite($id),
        ]);


        //}
        $mpdf = new \Mpdf\Mpdf([

            'mode' => 'utf-8', 'format' => 'A5'
        ]);
        $mpdf->PageNumSubstitutions[] = [
            'from' => 1,
            'reset' => 0,
            'type' => 'I',
            'suppress' => 'on'
        ];

        $mpdf->WriteHTML($html);
        $mpdf->SetFontSize(6);
        $mpdf->Output();


    }
}
