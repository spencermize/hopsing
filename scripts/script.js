$app = angular.module('app', ['ui.bootstrap'])
$app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('//');
    $interpolateProvider.endSymbol('//');
});
$app.controller('TypeaheadCtrl', function($scope, $http) {
  $scope.asyncSelected = undefined;  
  $scope.getKegs = function(val) {
	return $http.get('/api/kegs', {

		}).then(function(response){
		return response.data.results;
    });
  }
})
$app.controller('KegCreate',function($scope,$http,$modal) {
	$scope.postKegs = function(val){
		return $http({
			method: 'post',
			url: '/api/kegs',
			data: "name="+$scope.newKeg.name+"&abv="+$scope.newKeg.abv+"&type=" +$scope.newKeg.type,
			headers: {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(response){
			$scope.response = response;
			$scope.open();
		});
	}
  $scope.open = function () {
	  
    var modalInstance = $modal.open({
      templateUrl: 'ModalContent.html',
      controller: 'ModalMsgInstance',
      resolve: {
        response: function () {
          return $scope.response;
        }
      }
    });
  };	
});
$app.controller('ModalMsgInstance',function($scope,$modalInstance,response){
	$scope.title = "Success";
	$scope.msg = response.msg;
	$scope.ok = function () {
		$modalInstance.close();
	};
})