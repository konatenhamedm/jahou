<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class VenteRetourMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'stock';

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
            $menu->addChild(self::MODULE_NAME, ['label' => 'Ventes & retours']);
        }
        
        if (isset($menu[self::MODULE_NAME])) {
            $menu->addChild('vente', ['route' => 'app_stock_vente_index', 'label' => 'Ventes'])->setExtra('icon', 'bi bi-gear');
            $menu->addChild('retour', ['route' => 'app_stock_retour_client_index', 'label' => 'Retours ventes'])->setExtra('icon', 'bi bi-gear');

        }
       
        return $menu;
    }
}