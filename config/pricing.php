<?php

return [

  "FREE_TRIAL_DAYES" => 14,

  "CURRENCY" => "$",
  "CURRENCY_NAME" => "USD",

  "PRICING_PACKAGES" => [

    /* Basic Package */
    1 => [
      "ID" => 1,
      "NAME" => "Basic",
      "PRICE" => 37,
      "WEBSITES" => 1,
      "RESPONSES" => 2500,
      "TRANSLATABLE" => FALSE,
      "CUSTOM_DOMAIN" => FALSE,
    ],

    /* Pro Package */
    2 => [
      "ID" => 2,
      "NAME" => "Pro",
      "PRICE" => 74,
      "WEBSITES" => 5,
      "RESPONSES" => 5000,
      "TRANSLATABLE" => FALSE,
      "CUSTOM_DOMAIN" => TRUE,
    ],

    /* Premium Package */
    3 => [
      "ID" => 3,
      "NAME" => "Premium",
      "PRICE" => 186,
      "WEBSITES" => 15,
      "RESPONSES" => 15000,
      "TRANSLATABLE" => TRUE,
      "CUSTOM_DOMAIN" => TRUE,
    ],

  ],

];
