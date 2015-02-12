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
$app.controller('ModalMsgInstance',function($scope,$modalInstance,$sce,$http,response,keg){
	$scope.title = response.title || "Success";
	$scope.msg = $sce.trustAsHtml(response.msg);
	$scope.cancel = function (){
		$modalInstance.close();
	}
	$scope.ok = function () {	
		$modalInstance.close();
	};
	$scope.buyKeg = function (){
		$http({
			method: 'get',
			url: '/api/buy/keg/' + keg.id + "?price=" + $scope.newKeg.price
		}).success(function(response){
			$modalInstance.close();			
		});
	}
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
$app.controller('BuyShare',function($scope,$http,$modal,$attrs){
    $scope.buy = function(){
		var modalInstance = $modal.open({
			templateUrl: 'ModalContent.html',
			controller: 'ModalMsgInstance',
			resolve: {
				response: function(){
					return {
						title: 'Buy a Share'
					}
				},
				keg: function(){
					return {
						id: $attrs.kid
					}
				}
			}
		});	
	}
});
$app.controller('TastingCreate',function($scope,$http,$modal) {
	$scope.beerSytles = [
		{ label: 'Ale', value: "Ale" },
		{ label: 'Lager', value: "Lager" }
	];
	// $scope.onSelect = function ($item, $model, $label) {
		// $scope.newTasting.description = $item.description || "";
		// if($item.labels){
			// $scope.newTasting.label = $item.labels.large;
		// }else{
			// $scope.newTasting.label = "";
		// }
		// $scope.newTasting.open = "open";
	// };
	$scope.postTastings = function(val){
		var t = $scope.newTasting;
		return $http({
			method: 'post',
			url: '/api/tastings',
			transformRequest: function(obj) {
				var str = [];
				for(var p in obj)
				str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				return str.join("&");
			},			
			data: {
				host: t.host,
				style: t.style,
				location: t.location,
				date: t.date,
				guests: t.guests
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
$app.controller('TastingList',function($scope,$http){
	$http({
		method: 'get',
		url: '/api/tastings'
	}).success(function(response){
		$scope.tastings = response.results;
	});
});
$app.controller('AddGuests',function($scope,$http,$modal,$attrs){
    $scope.add = function(){
		var modalInstance = $modal.open({
			templateUrl: 'ModalContent.html',
			controller: 'ModalMsgInstance',
			resolve: {
				response: function(){
					return {
						title: 'Add Guests'
					}
				},
				keg: function(){
					return {
						id: $attrs.kid
					}
				}
			}
		});	
	}
});
$app.controller('JoinTasting',function($scope,$http,$modal,$attrs){
    $scope.join = function(){
		var modalInstance = $modal.open({
			templateUrl: 'ModalContent.html',
			controller: 'ModalMsgInstance',
			resolve: {
				response: function(){
					return {
						title: 'Join Tasting'
					}
				},
				keg: function(){
					return {
						id: $attrs.kid
					}
				}
			}
		});	
	}
});