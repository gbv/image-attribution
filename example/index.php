<!DOCTYPE html>
<html ng-app="app">
  <head>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
    <meta charset="UTF-8">
    <title>random images from Wikimedia Commons with attribution</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="app.js"></script>
  </head>
  <body ng-controller="ImageAttributionContoller"
        style="background: url({{image.src}}) no-repeat center center fixed">
    <div id="content">
      <div id="player">
        <button ng-click="startStop()" ng-class="pause ? 'start' : 'stop'" style="float:right"></button>
        <a href="https://github.com/gbv/image-attribution">image-attribution demo</a>
      </div>
      <div image-attribution="image">
        <p>
          <a href="{{image.url}}">
            <span ng-if="image.name">{{image.name}}</span>
            <span ng-if="!image.name">Image from Wikimedia Commons</span>
          </a>
          <span ng-if="apiurl">
            (<a href="{{apiurl}}">API</a>)
          </span>
        </p>
        <p>
          <div ng-class="image.attribution ? 'credit-required' : 'credit'">
            <div ng-if="image.license">
              <span ng-if="image.creator">
                {{image.creator}} /
              </span>
              <a href="{{image.license}}">{{image.license_name ? image.license_name : image.license}}</a>
              <span ng-if="image.sharealike"> &#x21ba;</a>
            </div>
            <div ng-if="!image.license">
              {{image.credit}}
              <span ng-if="image.sharealike"> &#x21ba;</a>
            </div>
          </div>
        </p>
        <p>
          <span class="date" ng-if="image.date">{{image.date}} </span>
          ({{image.width}}x{{image.height}})
          <!-- TODO: mime and size -->
        </p>
        <p class="restrictions" ng-if="image.restrictions">{{image.restrictions}}</p>
        <p class="description" ng-if="image.description">{{image.description}}</p>
      </div>
    </div>
  </body>
</html>
