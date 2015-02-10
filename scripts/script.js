$app = angular.module('app', ['ui.bootstrap'])
$app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('//');
    $interpolateProvider.endSymbol('//');
});
$app.controller('BreweryDBSearch', function($scope, $http) {
  $scope.asyncSelected = undefined;  
  $scope.getKegs = function(val) {
	return $http.get('/api/brewsearch?q=' + val, {

		}).then(function(response){
		return response.data.results.data;
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
	$scope.onSelect = function ($item, $model, $label) {
		$scope.newKeg.description = $item.description || "";
		if($item.labels){
			$scope.newKeg.label = $item.labels.large;
		}else{
			$scope.newKeg.label = "";
		}
		$scope.newKeg.abv = $item.abv;
	};
	$scope.postKegs = function(val){
		var k = $scope.newKeg;
		return $http({
			method: 'post',
			url: '/api/kegs',
			transformRequest: function(obj) {
				var str = [];
				for(var p in obj)
				str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				return str.join("&");
			},			
			data: {
				bdbid: k.bdb.id,
				abv: k.abv,
				label: k.label,
				style: k.bdb.style.name,
				name: k.bdb.name,
				description: k.description,
				price: k.price,
				size: k.size,
				pourSize: k.pourSize
			},
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
$app.controller('ModalMsgInstance',function($scope,$modalInstance,$sce,response){
	$scope.title = response.title || "Success";
	$scope.msg = $sce.trustAsHtml(response.msg);
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
$app.controller('BuyShare',function($scope,$http,$modal){
    $scope.buy = function(){
		var modalInstance = $modal.open({
			templateUrl: 'ModalContent.html',
			controller: 'ModalMsgInstance',
			resolve: {
				response: function(){
					return {
						msg: "<input type='text' class='form-control'></input>",
						title: 'Buy a Share'
					}
				}
			}
		});	
	}
});