<?php

namespace Drupal\video_embed_rutube\Plugin\video_embed_field\Provider;

use Drupal\video_embed_field\ProviderPluginBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * This module provides Rutube handler for Video Embed Field.
 *
 * @VideoEmbedProvider(
 *   id = "rutube",
 *   title = @Translation("Rutube")
 * )
 */
class Rutube extends ProviderPluginBase {

  public const PROVIDER_ID = 'rutube';

  /**
   * {@inheritdoc}
   */
  public function renderEmbedCode($width, $height, $autoplay) {
    $iframe = [
      '#type' => 'video_embed_iframe',
      '#provider' => self::PROVIDER_ID,
      '#url' => sprintf('https://rutube.ru/play/embed/%s', $this->getVideoId()),
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
  public function getRemoteThumbnailUrl() {
    return $this->oEmbedData('thumbnail_url');
  }

  /**
   * Get the oEmbed data for this video.
   *
   * @param string|bool $key
   *   An optional key to retrieve.
   *
   * @return array|string|int
   *   An oEmbed data.
   */
  protected function oEmbedData($key = FALSE) {
    $videoId = $this->getVideoId();

    $data = [];
    try {
      $uri = "https://rutube.ru/api/oembed/?url=https://rutube.ru/video/$videoId/&format=json";
      $response = $this->httpClient->request(Request::METHOD_GET, $uri);
      $data = json_decode($response->getBody(), TRUE);
    } catch (\Throwable $exception) {}

    return $key ? $data[$key] : $data;
  }

  /**
   * {@inheritdoc}
   */
  public static function getIdFromInput($input) {
    $matches = [];
    preg_match('/https?:\/\/(www\.)?rutube.ru\/video\/(?<id>[a-z0-9]*)(.*?)/', $input, $matches);
    return $matches['id'] ?? FALSE;
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
