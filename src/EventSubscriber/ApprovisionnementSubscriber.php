<?php

namespace App\EventSubscriber;

use App\Entity\LigneEntree;
use App\Entity\LigneMouvement;
use App\Entity\LigneSortie;
use App\Entity\Mouvement;
use App\Entity\MouvementEntre;
use App\Entity\MouvementSortie;
use App\Entity\Sens;
use App\Repository\SensRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;
use function Symfony\Component\Config\Definition\Builder\find;

class ApprovisionnementSubscriber implements EventSubscriberInterface
{
    private $em;
    private $workflow;
    private $sensRepository;



    public function __construct(EntityManagerInterface $em, \Symfony\Component\Workflow\Registry $workflow,SensRepository $sensRepository)
    {

        $this->em = $em;
        $this->workflow = $workflow;
        $this->sensRepository = $sensRepository;

    }

    public function handleValidation(TransitionEvent $event): void
    {

        $transition_name = $event->getTransition()->getName();
        $entity = $event->getSubject();
       // dd($entity);
        //$entity->setDateLivraison(new \DateTime());
        $this->em->flush();

        $lignes = $entity->getLigneEntrees()->filter(function (LigneEntree $ligne) {
            return $ligne->getQuantite() != 0 ;
        })->toArray();

        $mouvement = new MouvementEntre();
        $mouvement->setReference($entity->getReference());
        $mouvement->setLibelle($entity->getLibelle());
        $mouvement->setDateMouvement(new \DateTime());
        $mouvement->setSens($this->sensRepository->findOneBy(array('libelle'=>'Entree')));
        $mouvement->setEntree($entity);
        $this->em->persist($mouvement);
        $this->em->flush();
        foreach ($lignes as $ligne) {
            $ligne_mouvement = new LigneMouvement();
            $ligne_mouvement->setQuantite($ligne->getQuantite());
            $ligne_mouvement->setArticle($ligne->getArticle());
            $ligne_mouvement->setMouvement($mouvement);
            $this->em->persist($ligne_mouvement);
            $this->em->flush();
        }

    }



    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.appro.transition.passer' => 'handleValidation',
           // 'workflow.demande.transition.cloture' => 'handleCloture',
        ];
    }
}
