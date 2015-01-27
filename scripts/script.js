$app = angular.module('app', ['ui.bootstrap'])
$app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('//');
    $interpolateProvider.endSymbol('//');
});
$app.controller('TypeaheadCtrl', function($scope, $http) {
  $scope.asyncSelected = undefined;  
  $scope.getKegs = function(val) {
	return $http.get('http://localhost/api/kegs', {

		}).then(function(response){
		return response.data.results;
    });
  }
})