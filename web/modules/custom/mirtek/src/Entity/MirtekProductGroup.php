<?php

namespace Drupal\mirtek\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\mirtek\MirtekProductGroupInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the mirtek product group entity class.
 *
 * @ContentEntityType(
 *   id = "mirtek_product_group",
 *   label = @Translation("Mirtek product group"),
 *   label_collection = @Translation("Mirtek product groups"),
 *   label_singular = @Translation("mirtek product group"),
 *   label_plural = @Translation("mirtek product groups"),
 *   label_count = @PluralTranslation(
 *     singular = "@count mirtek product groups",
 *     plural = "@count mirtek product groups",
 *   ),
 *   bundle_label = @Translation("Mirtek product group type"),
 *   handlers = {
 *     "list_builder" = "Drupal\mirtek\MirtekProductGroupListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\mirtek\Form\MirtekProductGroupForm",
 *       "edit" = "Drupal\mirtek\Form\MirtekProductGroupForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "access" = "Drupal\mirtek\MirtekProductGroupAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\mirtek\Routing\MirtekProductGroupHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "mirtek_product_group",
 *   data_table = "mirtek_product_group_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer mirtek product group types",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "bundle" = "bundle",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/mirtek-product-group",
 *     "add-form" = "/admin/mirtek/product-group/add/{mirtek_product_group_type}",
 *     "add-page" = "/admin/mirtek/product-group/add",
 *     "canonical" = "/mirtek-product-group/{mirtek_product_group}",
 *     "edit-form" = "/mirtek-product-group/{mirtek_product_group}",
 *     "delete-form" = "/mirtek-product-group/{mirtek_product_group}/delete",
 *   },
 *   bundle_entity_type = "mirtek_product_group_type",
 *   field_ui_base_route = "entity.mirtek_product_group_type.edit_form",
 * )
 */
class MirtekProductGroup extends ContentEntityBase implements MirtekProductGroupInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['mirtek_product'] = BaseFieldDefinition::create('entity_reference')
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setLabel(t('Product reference'))
      ->setDescription(t('The concept id to the Mirtek Concept Entity.'))
      ->setSetting('target_type', 'mirtek_product')
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setTranslatable(TRUE)
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the mirtek product group was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the mirtek product group was last edited.'));



    return $fields;
  }

}
