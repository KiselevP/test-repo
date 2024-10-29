<?php

namespace Drupal\mirtek\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the mirtek product group entity edit forms.
 */
class MirtekProductGroupForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);
    $entity = $this->getEntity();
    $products = $entity->get('mirtek_product')->referencedEntities();
    foreach ($products as $product) {
        $product->set('field_group_products', $entity->id());
        $product->save();
    }

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New mirtek product group %label has been created.', $message_arguments));
        $this->logger('mirtek')->notice('Created new mirtek product group %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The mirtek product group %label has been updated.', $message_arguments));
        $this->logger('mirtek')->notice('Updated mirtek product group %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.mirtek_product_group.collection');

    return $result;
  }

}
