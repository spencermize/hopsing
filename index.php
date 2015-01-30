<?php
require 'vendor/autoload.php';
require_once('vendor/gabordemooij/redbean/rb.php');
require_once('lib/auth.php');

use JeremyKendall\Password\PasswordValidator;
use JeremyKendall\Slim\Auth\Adapter\Db\PdoAdapter;
use JeremyKendall\Slim\Auth\Bootstrap;

$db = new \PDO('mysql:host=localhost;dbname=hopsex','root','');
$adapter = new PdoAdapter(
    $db, 
    'users', 
    'username', 
    'password', 
    new PasswordValidator()
);

$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Twig(),
	'cookies.encrypt' => true,
	'cookies.secret_key' => 'Th!s is @n @maz!ngly L0ng 3ncrypti0n k3Y!',
));

$app->response->headers->set('Content-Type', 'text/html;charset=utf8');
R::setup('mysql:host=localhost;dbname=hopsex',
        'root','');
$app->get('/', function () {
	makePage();
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
	$app->post('/kegs(/:id)', function ($id = -1) use($app){
		$vars = 'name,type,abv';
		$num = 200;
		$error = false;
		if($id==-1){
			$keg = R::dispense( 'keg' );
			$keg->import($app->request->params(), $vars);
			$id = R::store( $keg );
			$msg = "New keg created.";
		}else{
			$keg = R::load( 'keg', $id );
			if($keg->ID){
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
});

function makePage(){
	$app = \Slim\Slim::getInstance();
	$app->view->appendData(array("style"=>"styles.php/style.scss"));	
	$app->render("base.twig");	
}

function APIrequest(){
	$app = \Slim\Slim::getInstance();
	$app->view(new \JsonApiView());
	$app->add(new \JsonApiMiddleware());
}

$app->run();
?>