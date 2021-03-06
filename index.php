<?php
require 'vendor/autoload.php';
require_once('vendor/gabordemooij/redbean/rb.php');
require_once('lib/brewerydb.php');
require_once('lib/model.php');
$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Twig(),
	'cookies.encrypt' => true,
	'cookies.lifetime' => "2 days", // = 1 day
    'cookies.secret_key' => 'This is a $uper M@g!c K3y',
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC
));

$app->response->headers->set('Content-Type', 'text/html;charset=utf8');
R::setup('mysql:host=localhost;dbname=hopsex',
        'root','');
$app->hook('slim.before.dispatch', function() use ($app) { 
   $id = $app->getCookie('_hopsauth');
   if($id){
      $user = R::load( 'user', $id );
	  $app->view()->setData('user', $user);
   }
});
		
$app->group('/api','APIrequest',function() use($app){
        //this request will have full json responses
	$app->get('/', function () use($app){
		$app->render(200,array(
			'api' => "Welcome to the HopsEx API!"
		));		
	});
	$app->get('/kegs(/:id)', function ($id = -1) use($app){
		if($id==-1){
			$kegs = R::findAll( 'keg' );
			$kegs = R::exportAll( $kegs );
			$app->render(200,array("results" => $kegs));
		}else{
			$keg = R::load( 'keg', $id );
			if($keg->ID){
				$app->render(200,$keg->export());
			}else{
				$app->render(404,array(
					"error" => true,
					"msg" => "No keg exists"
				));
			}
		}
	});
	$app->get('/buy/keg/:id',function($id) use ($app){
		$uid = $app->getCookie('_hopsauth');
		$user = R::load( 'user', $uid );
		$keg = R::load( 'keg', $id );
		if(count($keg->ownShareList && $app->request->get("price"))){
			$share = array_shift($keg->ownShareList);
			print_r($keg->ownShareList);
			//$share->purchasePrice = $app->request->get("price");
			//$user->ownShareList[] = $share;
			R::store($keg);
			R::store($user);
			$app->render(200,array("msg"=>"Success"));
		}
		$app->render(500,array("msg"=>"Unable to complete transaction"));
	});
	$app->get('/brewsearch',function() use($app){
		$q = $app->request->get("q");
		$ret = array("results" => brewerydb_lookup('search',array("q"=>$q,"type"=>"beer")));
		$app->render(200,$ret);
	});
	$app->get('/styles',function() use($app){
		$app->render(200,array(brewerydb_lookup('styles')));
	});
	$app->post('/kegs(/:id)', function ($id = -1) use($app){
		$msg = "";
		$num = 200;
		$error = false;
		$vars = 'bdbid,price,size,pourSize,name,abv,description,style,label';

		if($id==-1){
			$keg = R::dispense( 'keg' );
			$keg->import($app->request->params(), $vars);
			$psize = $app->request->post("pourSize");
			$size = $app->request->post("size");
			$nshares = floor($size / $psize);
			$shares = R::dispense("share",$nshares);
			$keg->ownShareList = $shares;
			$id = R::store( $keg );
			$msg = "New keg created.";
		}else{
			$keg = R::load( 'keg', $id );
			if($keg->ID){
				//print_r($app->request->params());
				$keg->import($app->request->params(), $vars);
				$id = R::store( $keg );
				$msg = "Keg updated.";					
			}else{
				$error = true;
				$msg = "Sorry, keg was not found.";
				$num = 404;
			}
		}

		$app->render($num,array(
			'id' => $id,
			'error' => $error,
			'msg' => $msg
		));			
	});	
	
	$app->post('/tastings(/:id)', function ($id = -1) use($app){
		$msg = "";
		$num = 200;
		$error = false;
		$vars = 'host,style,location,date,guests';

		if($id==-1){
			$tasting = R::dispense( 'tasting' );
			$tasting->import($app->request->params(), $vars);
			$host = $app->request->post("host");
			$style = $app->request->post("style");
			$location = $app->request->post("location");
			$date = $app->request->post("date");
			$guests = $app->request->post("guests");
			$id = R::store( $tasting );
			$msg = "New tasting created.";
		}else{
			$tasting = R::load( 'tasting', $id );
			if($tasting->ID){
				$tasting->import($app->request->params(), $vars);
				$id = R::store( $tasting );
				$msg = "Tasting updated.";					
			}else{
				$error = true;
				$msg = "Sorry, tasting was not found.";
				$num = 404;
			}
		}

		$app->render($num,array(
			'id' => $id,
			'error' => $error,
			'msg' => $msg
		));			
	});	
	$app->get('/tastings(/:id)', function ($id = -1) use($app){
		if($id==-1){
			$tastings = R::findAll( 'tasting' );
			$tastings = R::exportAll( $tastings );
			$app->render(200,array("results" => $tastings));
		}else{
			$tasting = R::load( 'tasting', $id );
			if($tasting->ID){
				$app->render(200,$tasting->export());
			}else{
				$app->render(404,array(
					"error" => true,
					"msg" => "No tasting exists"
				));
			}
		}
	});
});

$app->get('/login',function() use($app){
	$id = $app->getCookie('_hopsauth');
	if(!$id){
		require '/lib/login.php';		
	}else{
		$app->redirect("/");		
	}
});
$app->get('/logout',function() use($app){
	require '/lib/logout.php';
});
$app->get('/', function () {
	makePage();
});
$app->get('/:id',function($id){
	makePage($id . ".twig");
});
$app->get('/beers/:id',function($id){
	makePage($id . ".twig");
});
function makePage($page = "base.twig",$vars = array()){
	$app = \Slim\Slim::getInstance();
	$app->view->appendData(array("style"=>"/styles.php/style.scss"));	
	$app->render($page,$vars);	
}

function APIrequest(){
	$app = \Slim\Slim::getInstance();
	$uid = $app->getCookie('_hopsauth');
	$error = true;
	$num = 401;
	$app->view(new \JsonApiView());
	$app->add(new \JsonApiMiddleware());
	
	if($uid){
		$user = R::load( 'user', $uid );
		$error = false;
	}
	if($error){
		$app->render($num,array(
			'error' => $error,
			'msg' => "Unauthorized"
		));		
	}
}

$app->run();
?>