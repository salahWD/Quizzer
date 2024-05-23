<?php

return [

  "INTEGRATIONS" => [

    /* Calanly */
    1 => [
      "ID" => 1,
      "INTEGRATION" => "calendly",
      "NAME" => "Calendly",
      "DESC" => "To book appointments and meetings",
      "IS_USABLE" => FALSE,
      "LOGO" => env('APP_URL') . "/images/cl-logo.png",
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
      "DESC" => "To track people on Facebook and Instagram and retarget them",
      "IS_USABLE" => TRUE,
      "LOGO" => env('APP_URL') . "/images/fb-logo.png",
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
      "DESC" => "To create a custom mailing list and custom automation for it",
      "IS_USABLE" => TRUE,
      "LOGO" => env('APP_URL') . "/images/ac-logo.png",
      "FIELDS" => [
        [
          "NAME" => "URL",
          "INPUT" => ["url", "Enter activeCompaign url", "value"],
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

    /* GetResponse */
    4 => [
      "ID" => 4,
      "INTEGRATION" => "getResponse",
      "NAME" => "GetResponse",
      "DESC" => "To create a custom mailing list and custom automation for it",
      "IS_USABLE" => TRUE,
      "LOGO" => env('APP_URL') . "/images/gr-logo.png",
      "FIELDS" => [
        [
          "NAME" => "API Key",
          "INPUT" => ["textarea", "api key", "key"],
        ],
      ],
      // "BTN_URL" => "https://app.getresponse.com/oauth2_authorize.html?response_type=code&client_id=" . config("getresponse.client") . "&state=quizzer_key",
      // "BTN_TEXT" => "Connect",
    ],

    /* Convert Kit */
    5 => [
      "ID" => 5,
      "INTEGRATION" => "convertKit",
      "NAME" => "convertKit",
      "DESC" => "To create a custom mailing list and custom automation for it",
      "IS_USABLE" => TRUE,
      "LOGO" => env('APP_URL') . "/images/ck-logo.png",
      "FIELDS" => [
        [
          "NAME" => "API Key",
          "INPUT" => ["text", "api key", "value"],
        ],
        [
          "NAME" => "Secret Key",
          "INPUT" => ["text", "secret key", "key"],
        ],
      ],
      // "BTN_URL" => "https://app.getresponse.com/oauth2_authorize.html?response_type=code&client_id=" . config("getresponse.client") . "&state=quizzer_key",
      // "BTN_TEXT" => "Connect",
    ],

    /* Google Ads */
    6 => [
      "ID" => 6,
      "INTEGRATION" => "googleAds",
      "NAME" => "Google Ads",
      "DESC" => "To track the audience and retarget them on Google services at any time",
      "IS_USABLE" => TRUE,
      "LOGO" => env('APP_URL') . "/images/ga-logo.png",
      "FIELDS" => [
        [
          "NAME" => "Google Tag",
          "INPUT" => ["textarea", "Tag Code", "key"],
        ],
      ],
    ],



    /* Google Tag Manager */
    7 => [
      "ID" => 7,
      "INTEGRATION" => "tagManager",
      "NAME" => "Google Tag Manager",
      "DESC" => "You can connect your smart sales associate with more than 2,000 services and locations",
      "IS_USABLE" => TRUE,
      "LOGO" => env('APP_URL') . "/images/gt-logo.png",
      "FIELDS" => [
        [
          "NAME" => "Head tag snippet",
          "INPUT" => ["textarea", "head tag", "key"],
        ],
        [
          "NAME" => "Body tag snippet",
          "INPUT" => ["textarea", "body tag", "value"],
        ],
      ],
    ],

    /* Tiktok Pixle */
    8 => [
      "ID" => 8,
      "INTEGRATION" => "tiktok",
      "NAME" => "Tiktok",
      "DESC" => "You can link your tracking code and track and retarget your audience on the TikTok platform",
      "IS_USABLE" => TRUE,
      "LOGO" => env('APP_URL') . "/images/tk-logo.png",
      "FIELDS" => [
        [
          "NAME" => "Tiktok Pixle Code",
          "INPUT" => ["textarea", "paste pixle code here", "key"],
        ],
      ],
    ],

    /* Snapchat Pixle */
    9 => [
      "ID" => 9,
      "INTEGRATION" => "snapchat",
      "NAME" => "Snapchat",
      "DESC" => "You can link Snapchat's pixel code and track the audience on it at any time",
      "IS_USABLE" => TRUE,
      "LOGO" => env('APP_URL') . "/images/sc-logo.png",
      "FIELDS" => [
        [
          "NAME" => "Snapchat Pixle Code",
          "INPUT" => ["textarea", "paste pixle code here", "key"],
        ],
      ],
    ],

    /* Twitter Pixle */
    10 => [
      "ID" => 10,
      "INTEGRATION" => "twitter",
      "NAME" => "Twitter",
      "DESC" => "You can link your Twitter pixel code, create events, and track your audience",
      "IS_USABLE" => TRUE,
      "LOGO" => env('APP_URL') . "/images/tw-logo.png",
      "FIELDS" => [
        [
          "NAME" => "Twitter Pixle Code",
          "INPUT" => ["textarea", "paste pixle code here", "key"],
        ],
      ],
    ],

  ],


];
