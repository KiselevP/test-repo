<?php
namespace Drupal\mirtek\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\mirtek\MirtekProductGroupInterface;

class MirtekCloneController extends ControllerBase {

    /**
     * The entity type manager.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    public function __construct(EntityTypeManagerInterface $entityTypeManager) {
        $this->entityTypeManager = $entityTypeManager;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('entity_type.manager')
        );
    }

    public function cloneEntity(MirtekProductGroupInterface $mirtek_product_group) {

            $cloned_entity = $mirtek_product_group->createDuplicate();

            // Клонирование продуктов
            $products = $mirtek_product_group->get('mirtek_product')->referencedEntities();
            $cloned_products = [];
            foreach ($products as $product) {
                $cloned_product = $product->createDuplicate();
                $cloned_product->save();
                $cloned_products[] = ['target_id' => $cloned_product->id()];
            }

            $cloned_entity->set('mirtek_product', $cloned_products);
            $cloned_entity->save();

            $url = Url::fromRoute('entity.mirtek_product_group.canonical', ['mirtek_product_group' => $cloned_entity->id()]);
            return new RedirectResponse($url->toString());

    }



}
