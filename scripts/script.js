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
$app.controller('KegCreate',function($scope,$http) {
	$scope.postKegs = function(val){
		return $http({
			method: 'post',
			url: '/api/kegs',
			data: "name="+$scope.newKeg.name+"&abv="+$scope.newKeg.abv+"&type=" +$scope.newKeg.type,
			headers: {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).then(function(response){
			
		});
		
	}
	
});