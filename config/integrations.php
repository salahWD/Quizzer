<?php

return [

  "INTEGRATIONS" => [

    /* Calanly */
    1 => [
      "ID" => 1,
      "INTEGRATION" => "calendly",
      "NAME" => "Calendly",
      "DESC" => "Calendly for arranging meetings",
      "IS_USABLE" => FALSE,
      "LOGO" => "https://placehold.co/48",
      "FIELDS" => [
        [
          "NAME" => "API Key",
          "INPUT" => ["textarea", "enter api key", "key"],
        ],
      ],
    ],

    /* Meta Pixle */
    2 => [
      "ID" => 2,
      "INTEGRATION" => "facebook",
      "NAME" => "Meta Pixle",
      "DESC" => "track and enhance your compaigns",
      "IS_USABLE" => TRUE,
      "LOGO" => "https://placehold.co/48",
      "FIELDS" => [
        [
          "NAME" => "Facebook Pixle Code",
          "INPUT" => ["textarea", "paste pixle code here", "key"],
        ],
      ],
    ],

    /* Active Compaign */
    3 => [
      "ID" => 3,
      "INTEGRATION" => "activeCompaign",
      "NAME" => "Active Compaign",
      "DESC" => "Active Compaign for traking compaigns",
      "IS_USABLE" => TRUE,
      "LOGO" => "https://placehold.co/48",
      "FIELDS" => [
        [
          "NAME" => "URL",
          "INPUT" => ["url", "Enter activeCompaign url", "url"],
        ],
        [
          "NAME" => "API Key",
          "INPUT" => ["textarea", "api key", "key"],
        ],
      ],
      "USABLE_FIELDS" => [
        "ac_lists" => 'list_id',
      ],
    ],

  ],

];
