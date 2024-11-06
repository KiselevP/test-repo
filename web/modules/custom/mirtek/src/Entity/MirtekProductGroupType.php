<?php

namespace Drupal\mirtek\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Mirtek product group type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "mirtek_product_group_type",
 *   label = @Translation("Mirtek product group type"),
 *   label_collection = @Translation("Mirtek product group types"),
 *   label_singular = @Translation("mirtek product group type"),
 *   label_plural = @Translation("mirtek product groups types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count mirtek product groups type",
 *     plural = "@count mirtek product groups types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\mirtek\Form\MirtekProductGroupTypeForm",
 *       "edit" = "Drupal\mirtek\Form\MirtekProductGroupTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\mirtek\MirtekProductGroupTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   admin_permission = "administer mirtek product group types",
 *   bundle_of = "mirtek_product_group",
 *   config_prefix = "mirtek_product_group_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/mirtek_product_group_types/add",
 *     "edit-form" = "/admin/structure/mirtek_product_group_types/manage/{mirtek_product_group_type}",
 *     "delete-form" = "/admin/structure/mirtek_product_group_types/manage/{mirtek_product_group_type}/delete",
 *     "collection" = "/admin/structure/mirtek_product_group_types"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *   }
 * )
 */
class MirtekProductGroupType extends ConfigEntityBundleBase {

  /**
   * The machine name of this mirtek product group type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the mirtek product group type.
   *
   * @var string
   */
  protected $label;

}
