<?php

namespace Drupal\mirtek\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Mirtek product type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "mirtek_product_type",
 *   label = @Translation("Mirtek product type"),
 *   label_collection = @Translation("Mirtek product types"),
 *   label_singular = @Translation("mirtek product type"),
 *   label_plural = @Translation("mirtek products types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count mirtek products type",
 *     plural = "@count mirtek products types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\mirtek\Form\MirtekProductTypeForm",
 *       "edit" = "Drupal\mirtek\Form\MirtekProductTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\mirtek\MirtekProductTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   admin_permission = "administer mirtek product types",
 *   bundle_of = "mirtek_product",
 *   config_prefix = "mirtek_product_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/mirtek_product_types/add",
 *     "edit-form" = "/admin/structure/mirtek_product_types/manage/{mirtek_product_type}",
 *     "delete-form" = "/admin/structure/mirtek_product_types/manage/{mirtek_product_type}/delete",
 *     "collection" = "/admin/structure/mirtek_product_types"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *   }
 * )
 */
class MirtekProductType extends ConfigEntityBundleBase {

  /**
   * The machine name of this mirtek product type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the mirtek product type.
   *
   * @var string
   */
  protected $label;

}
