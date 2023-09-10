<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
  <style>
    @import 'https://fonts.googleapis.com/css?family=Noto+Sans';
    * {
      box-sizing: border-box;
    }

    body {
      background: skyblue;
      font: 12px/16px "Noto Sans", sans-serif;
    }

    .floating-chat {
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 80px;
      height: 80px;
      transform: translateY(70px);
      transition: all 250ms ease-out;
      border-radius: 50%;
      opacity: 0;
      background: -moz-linear-gradient(-45deg, #183850 0, #183850 25%, #192C46 50%, #22254C 75%, #22254C 100%);
      background: -webkit-linear-gradient(-45deg, #183850 0, #183850 25%, #192C46 50%, #22254C 75%, #22254C 100%);
      background-repeat: no-repeat;
      background-attachment: fixed;
    }
    .floating-chat.enter:hover {
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
      opacity: 1;
    }
    .floating-chat.enter {
      transform: translateY(0);
      opacity: 0.6;
      box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.12), 0px 1px 2px rgba(0, 0, 0, 0.14);
    }
    .floating-chat .image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center center;
      transition: 0.4s ease-out;
      border-radius: 50%;
      overflow: hidden;
      display: none;
    }
    .floating-chat.expand {
      width: 250px;
      max-height: 500px;
      min-height: 80px;
      height: 220px;
      border-radius: 5px;
      cursor: auto;
      opacity: 1;
    }
    .floating-chat :focus {
      outline: 0;
      box-shadow: 0 0 3pt 2pt rgba(14, 200, 121, 0.3);
    }
    .floating-chat .chat {
      display: flex;
      flex-direction: column;
      position: absolute;
      opacity: 0;
      width: 1px;
      height: 1px;
      border-radius: 50%;
      transition: all 250ms ease-out;
      margin: auto;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
    }
    .floating-chat .chat.enter {
      opacity: 1;
      border-radius: 0;
      margin: 0;
      width: auto;
      height: auto;
    }
    .floating-chat .chat .header {
      flex-shrink: 0;
      padding-bottom: 10px;
      display: flex;
      background: transparent;
      position: relative;
    }
    .floating-chat .chat .header button {
      border: 0;
      text-transform: uppercase;
      cursor: pointer;
      flex-shrink: 0;
      position: absolute;
      right: -20px;
      top: -20px;
      background: white;
      color: black;
      border-radius: 50%;
      aspect-ratio: 1;
    }
    .floating-chat .chat .header .image {
      width: 100%;
      max-height: 90px;
      object-fit: cover;
      object-position: center center;
      margin: 0;
      display: block;
      border-radius: 0;
      padding: 0;
    }
    .floating-chat .chat .body {
      padding: 0 12px 12px;
      font-size: 16px;
      line-height: 21px;
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .floating-chat .chat .body .btn {
      margin: 0;
      display: block;
      background: white;
      padding: 8px;
      text-align: center;
      margin-top: auto;
      width: 100%;
      text-decoration: none;
      color: black;
    }
  </style>

    <div class="floating-chat enter">
      <img src="http://via.placeholder.com/100" class="image">
      <div class="chat">
          <div class="header">
            <img src="http://via.placeholder.com/400" class="image">
            <button>
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
          </div>
          <div class="body">
            <div>Testing Text Just To Take Place</div>
            <a href="#" class="btn">Take Quiz</a>
          </div>
      </div>
    </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script>
      var element = $('.floating-chat');

      setTimeout(function() {
          openElement();
      }, 1000);

      element.click(openElement);

      function openElement() {
        element.find('>.image').hide()
        element.addClass('expand');
        element.find('.chat').addClass('enter');
        element.off('click', openElement);
        element.find('.header button').click(closeElement);
      }

      function closeElement() {
          element.find('.chat').removeClass('enter').hide();
          element.removeClass('expand');
          element.find('.header button').off('click', closeElement);
          setTimeout(function() {
              element.find('>.image').show()
              element.find('.chat').removeClass('enter').show()
              element.click(openElement);
          }, 250);
      }

  </script>
</body>
</html>
