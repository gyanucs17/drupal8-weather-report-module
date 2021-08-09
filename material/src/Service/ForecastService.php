<?php

/**
 * @file
 * Contains \Drupal\material\Controller\WeatherController.
 */

namespace Drupal\material\Service;

use \GuzzleHttp\ClientInterface;

class ForecastService {

  protected $client;

  protected $api_key;

  /**
   * ForecastService constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   */
  function __construct(ClientInterface $client) {
    $this->client = $client;
    $this->api_key = "36053740d0bd118115d83723d2f599eb";
  }

  /**
   * {@inheritdoc}
   */
  public function getApiKey() {
    $config = \Drupal::config('local_weather_forecast.settings');
    return $config->get('api_key');
  }

  /**
   *
   * @param string $city
   *
   * @param int $cnt
   *
   * @return array $element
   */
  public function getForecast($city, $cnt) {
    // get data via http://openweathermap.org/api
    /* @var \GuzzleHttp\Message\Response $result */
    $request = $this->client->get(
      'https://api.openweathermap.org/data/2.5/forecast',
      [
        'query' => [
          'q' => $city,
          'appid' => $this->api_key,
          'cnt' => $cnt,
        ],
      ]
    );
    try {
      if (200 == $request->getStatusCode()) {
        $forecast = json_decode($request->getBody());
      }
      $hourly_forecast = $forecast->list;
     $timezone = timezone_name_from_abbr('', $forecast->city->timezone, 0);
      // use our theme function to render twig template
      $element = [
        '#theme' => 'weather_forecast',
        '#city' => $forecast->city->name,
        '#timezone' => $timezone,
        '#forecast' => $hourly_forecast,
      ];
      $element['#cache']['max-age'] = 0;
      return $element;
    } catch (\Exception $e) {
      drupal_set_message(t("Could not get a hourly forecast for $city, please try again later:" . $e->getMessage()), 'error');
    }
  }

  public function getWeather($city) {
    // get data via http://openweathermap.org/api
    /* @var \GuzzleHttp\Message\Response $result */
    $request = $this->client->get(
      'https://openweathermap.org/data/2.5/weather',
      [
        'query' => [
          'q' => $city,
          'appid' => "439d4b804bc8187953eb36d2a8c26a02",
        ],
      ]
    );
    try {
      if (200 == $request->getStatusCode()) {
        $weather = json_decode($request->getBody());
      }
      $timezone = timezone_name_from_abbr('', $weather->timezone, 0);
      // use our theme function to render twig template
      $element = [
        '#theme' => 'weather_report',
        '#city' => $weather->name,
        '#timezone' => $timezone,
        '#icon' => $weather->weather[0]->icon,
        '#humidity' => $weather->main->humidity,
        '#wind' => $weather->wind->speed,
        '#date' => $weather->dt,
        '#temp' => $weather->main->temp,
      ];
      $element['#cache']['max-age'] = 0;
      return $element;
    } catch (\Exception $e) {
      drupal_set_message(t("Could not get a weather details for $city, please try again later:" . $e->getMessage()), 'error');
    }
  }
}