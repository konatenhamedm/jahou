<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class ApprovisionnementRetourMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'rh';

    public function __construct(FactoryInterface $factory, Security $security)
    {
        $this->factory = $factory;
        $this->security = $security;
        $this->user = $security->getUser();
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setExtra('module', self::MODULE_NAME);
        if ($this->user->hasRoleOnModule(self::MODULE_NAME)) {
            $menu->addChild(self::MODULE_NAME, ['label' => 'Approvisionnements & retours']);
        }
        
        if (isset($menu[self::MODULE_NAME])) {
            $menu->addChild('approvisionnement', ['route' => 'app_stock_sortie_index', 'label' => 'Approvisionnements'])->setExtra('icon', 'bi bi-gear');
            $menu->addChild('retour', ['route' => 'app_stock_retour_fournisseur_index', 'label' => 'Retours fournisseurs'])->setExtra('icon', 'bi bi-gear');

        }
       
        return $menu;
    }
}