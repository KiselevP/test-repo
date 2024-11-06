<?php

namespace Drupal\mirtek\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the mirtek product entity edit forms.
 */
class MirtekProductForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New mirtek product %label has been created.', $message_arguments));
        $this->logger('mirtek')->notice('Created new mirtek product %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The mirtek product %label has been updated.', $message_arguments));
        $this->logger('mirtek')->notice('Updated mirtek product %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.mirtek_product.canonical', ['mirtek_product' => $entity->id()]);

    return $result;
  }

}
