<?php

namespace Drupal\mirtek\Plugin\search_api\processor;

use Drupal\search_api\Processor\FieldsProcessorPluginBase;
use Drupal\search_api\Query\QueryInterface;

/**
 * Processor that handles Russian letters transformation for indexing and querying.
 *
 * @SearchApiProcessor(
 *   id = "mirtek_processor",
 *   label = @Translation("e_yo_normalize"),
 *   description = @Translation("Allows you to define stopwords which will be ignored in searches."),
 *   stages = {
 *     "preprocess_index" = -5,
 *     "preprocess_query" = -2,
 *   }
 * )
 */
class RussianProcessor extends FieldsProcessorPluginBase {

    /**
     * Applies the required transformations for both indexing and querying.
     */
    protected function process(&$value) {
        // Применение всех преобразований
        $value = preg_replace(['/ё/', '/Ё/'], ['е', 'Е'], $value);
        $value = preg_replace(['/й/', '/Й/'], ['и', 'И'], $value);
        $value = preg_replace(['/ъ/', '/Ъ/'], ['', ''], $value);
        $value = preg_replace(['/ь/', '/Ь/'], ['', ''], $value);
        $value = preg_replace(['/э/', '/Э/'], ['е', 'Е'], $value);
    }

    /**
     * {@inheritdoc}
     */
    public function preprocessIndex(array &$fields, QueryInterface $query) {
        foreach ($fields as &$field) {
            if (is_string($field['value'])) {
                // Применяем обработку к значению для индексации
                $this->process($field['value']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preprocessQuery(array &$query_values) {
        foreach ($query_values as &$value) {
            if (is_string($value)) {
                // Применяем обработку к запросу пользователя
                $this->process($value);
            }
        }
    }
}
