<?php

namespace Drupal\video_embed_vk\Plugin\video_embed_field\Provider;

use Drupal\video_embed_field\ProviderPluginBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * This module provides Vk handler for Video Embed Field.
 *
 * @VideoEmbedProvider(
 *   id = "vk",
 *   title = @Translation("Vk")
 * )
 */
class Vk extends ProviderPluginBase {

    public const PROVIDER_ID = 'vk';

    /**
     * {@inheritdoc}
     */
    public function renderEmbedCode($width, $height, $autoplay) {
        $ownerId = $this->getOwnerId();
        $videoId = $this->getVideoId();

        // Создаем iframe URL
        $iframe = [
            '#type' => 'video_embed_iframe',
            '#provider' => self::PROVIDER_ID,
            '#url' => sprintf('https://vk.com/video_ext.php?oid=%s&id=%s&hd=2', $ownerId, $videoId),
            '#attributes' => [
                'width' => $width,
                'height' => $height,
                'frameborder' => '0',
                'webkitAllowFullScreen' => 'webkitAllowFullScreen',
                'mozallowfullscreen' => 'mozallowfullscreen',
                'allowfullscreen' => 'allowfullscreen',
            ],
        ];

        $allowed = ['clipboard-write'];
        if ($autoplay) {
            $allowed[] = 'autoplay';
        }
        $iframe['#attributes']['allow'] = implode('; ', $allowed);

        if ($timeIndex = self::getTimeIndex($this->input)) {
            $iframe['#query']['t'] = $timeIndex;
        }

        return $iframe;
    }

    /**
     * {@inheritdoc}
     */
    public static function getIdFromInput($input) {
        $matches = [];
        // Извлекаем oid и id из ссылки на видео
        preg_match('/https?:\/\/vk\.com\/video-(?<oid>-?\d+)_(?<id>\d+)/', $input, $matches);
        return $matches['id'] ?? FALSE;
    }

    /**
     * Get the owner ID from the URL.
     *
     * @return string|false
     *   Owner ID or FALSE if not found.
     */
    public function getOwnerId() {
        $matches = [];
        // Извлекаем oid из ссылки на видео и добавляем минус перед группой, если это группа
        preg_match('/https?:\/\/vk\.com\/video-(?<oid>-?\d+)_(?<id>\d+)/', $this->input, $matches);
        $owner_id = $matches['oid'] ?? FALSE;
        return $owner_id ? '-' . ltrim($owner_id, '-') : FALSE;
    }

    /**
     * Get the time index from the URL.
     *
     * @return string|false
     *   A time index parameter to pass to the frame or FALSE if none is found.
     */
    public static function getTimeIndex($input) {
        $matches = [];
        preg_match('/t=(?<time_index>(\d+))/', $input, $matches);
        return $matches['time_index'] ?? FALSE;
    }

}
