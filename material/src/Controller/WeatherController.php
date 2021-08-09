<?php

namespace Drupal\material\Controller;
Use Drupal\Core\Controller\ControllerBase;

class WeatherController extends ControllerBase{
  public function weather() {
    

     $forecaster = \Drupal::service('material.forecast');
     $forcost = $forecaster->getForecast("bangalore,india", "5");
     $weather = $forecaster->getWeather("bangalore,india");
     return array(
      '#markup' => drupal_render($weather).drupal_render($forcost)
     );
  }
}