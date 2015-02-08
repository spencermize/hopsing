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
	$scope.sizeOptions = [
		{ label: '8oz', value: 8 },
		{ label: '10oz', value: 10 },
		{ label: '12oz', value: 12 },
		{ label: '16oz', value: 16 },
		{ label: '20oz', value: 20 }
	];
	$scope.kegSizes = [
		{ label: 'Half Barrel', value: 1984 },
		{ label: 'Quarter Barrel', value: 992 },
		{ label: 'Sixth Barrel', value: 661 },
		{ label: 'Corny', value: 640 },
		{ label: 'Mini', value: 169 }
	];

	$scope.postKegs = function(val){
		return $http({
			method: 'post',
			url: '/api/kegs',
			data: "name="+$scope.newKeg.name + "&price=" + $scope.newKeg.price + "&size=" + $scope.newKeg.size + "&pourSize=" + $scope.newKeg.pourSize,
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
$app.controller('BeerStyleList',function($scope,$http){
	$http({
		method: 'get',
		url: '/api/styles'
	}).success(function(response){
		$scope.styles = response[0].data;
	});
});
$app.controller('KegList',function($scope,$http){
	$http({
		method: 'get',
		url: '/api/kegs'
	}).success(function(response){
		$scope.kegs = response.results;
	});
});