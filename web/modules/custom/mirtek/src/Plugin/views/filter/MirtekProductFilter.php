<?php

namespace Drupal\mirtek\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\ManyToOne;
use Drupal\mirtek\Entity\MirtekProduct;

/**
 * Defines a filter for referenced entities.
 *
 * @ViewsFilter("mirtek_product_filter")
 */
class MirtekProductFilter extends ManyToOne {

    public function query() {
        $selected_ids = [];

        if (!empty($this->value) && is_array($this->value)) {
            foreach ($this->value as $key => $id) {
                if (!empty($id) && is_numeric($id)) {
                    $selected_ids[] = $id;
                }
            }
        }

        if (!empty($selected_ids)) {
            // Применяем фильтр только к выбранным продуктам.
            $this->query->addWhere($this->options['group'], "node__field_produkt.field_produkt_target_id", $selected_ids, 'IN');
        }
        else {
            \Drupal::logger('mirtek')->notice('No products were selected for filtering.');
        }
    }

    public function getValueOptions() {
        $this->valueOptions = [];
        $products = MirtekProduct::loadMultiple();

        foreach ($products as $product) {
            $this->valueOptions[$product->id()] = $product->label();
        }

        return $this->valueOptions;
    }

    public function acceptExposedInput($input) {
        // Проверяем наличие входных данных под ключом 'value'.
        if (isset($input['value']) && is_array($input['value'])) {
            // Если выбрано "Выбрать все".
            if (isset($input['value']['all']) && $input['value']['all'] == 'all') {
                $this->value = array_keys($this->getValueOptions());
            } else {
                // Фильтруем только выбранные значения.
                $this->value = array_filter($input['value'], function ($value) {
                    return !empty($value) && $value !== 'all';
                });
            }
        }
//        else {
//            $this->value = [];
//            \Drupal::logger('mirtek')->notice('No values were accepted.');
//        }

        return TRUE;
    }


    public function buildExposedForm(&$form, FormStateInterface $form_state) {
        // Добавляем поле поиска.
//        ['mirtek_product_filter']

        $form['product_filter'] = array(
            '#type' => 'details',
            '#title' => $this->t('Produkt'),
            '#open' => true,
        );

        $form['product_filter']['search'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Search'),
            '#attributes' => [
                'placeholder' => $this->t('Найти...'),
                'class' => ['mirtek-product-filter-search'],
            ],
        ];

        // Получаем опции продуктов и добавляем "Выбрать все".
        $options = $this->getValueOptions();
        $options = ['all' => $this->t('Select all')] + $options;

        // Добавляем чекбоксы с продуктами.
        $form['product_filter']['value'] = [
            '#type' => 'checkboxes',
            '#title' => $this->t(' '),
            '#options' => $options, // Используем измененную переменную $options
            '#default_value' => !empty($this->value) ? $this->value : [],
            '#attributes' => [
                'class' => ['mirtek-product-filter-options'],
            ],
            '#prefix' => '<div class="mirtek-product-filter-container">', // Контейнер с фиксированной высотой и скроллом.
            '#suffix' => '</div>',
        ];

        // Добавляем текстовый элемент "Показать ещё".
        $form['product_filter']['show_more'] = [
            '#markup' => '<span class="mirtek-product-filter-show-more">' . $this->t('Показать ещё') . '</span>',
        ];

        // Подключаем библиотеку для JavaScript фильтрации.
        $form['#attached']['library'][] = 'mirtek/mirtek_filter';

    }

}
