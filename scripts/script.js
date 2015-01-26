$app = angular.module('app', ['ui.bootstrap'])
$app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('//');
    $interpolateProvider.endSymbol('//');
});
$app.controller('TypeaheadCtrl', function($scope, $http) {

  $scope.selected = undefined;
  
  $scope.getLocation = function(val) {
    return $http.get('http://localhost/api/kegs', {

    }).then(function(response){

      return response.data.map(function(item){
		  console.log("!");
        return item.name;
      });
    });
  };
});