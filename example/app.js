angular.module('app', [])

.controller('ImageAttributionContoller', ['$scope', '$http', function($scope, $http) {
  $scope.image = {};
  $scope.speed = 5000; // ms
  $scope.pause = false;

  $scope.startStop = function() {
    $scope.pause = !$scope.pause;
  };

  function nextImage() {
    if (!$scope.pause) {
      setTimeout(updateImage, $scope.speed);
    }
  }

  function updateImage() {
    $http.get('api.php').then(
      function(response) {
        if ($scope.pause) return;
        $scope.image = response.data[0];
        $scope.apiurl = $scope.image.name ? 'api.php?image=' + $scope.image.name : null;
        nextImage();
      },
      function(response) {
        nextImage();
      }
    );
  }

  updateImage();
}])

.directive('imageAttribution', function() {
  return {
    restrict: 'AE',
    scope: { 
      image: '=imageAttribution',
    },
/*
    link: function link(scope, element, attr) {
        scope.$watch('image',function(image) {
        angular.forEach([
          'src', 'page', 'license', 'attribution', 'sharealike', 'creator', 
          'credit', 'description', 'date', 'restrictions'
        ], function(field) {
          scope[field] = image ? image[field] : null;
        });
      },true);
    }
*/
  };
});
