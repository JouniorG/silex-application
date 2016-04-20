<?php
	namespace WaveLearning\Rest\Controllers;

	use Silex\Application;
	use Silex\ControllerProviderInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\RedirectResponse;

	class TokenControllers implements ControllerProviderInterface {
		/*
			@{Override}
		*/
		public function connect(Application $app){
			$controllers = $app["controllers_factory"];
			
			$controllers->post("/forgot-password", function(Request $req) use($app){
				$email = $req->get("email");
				$result = $app["pdo.mysql"]->getSQLMatrix("select id from users where email='$email'");

				if($result){
					$id = $result[0];
					$id = $id[0];

					/*
						Work on this part
					*/

					return new JsonResponse($result);
				}	

				return new JsonResponse(array("token" => "non-generated"));
			})
				->before(function(Request $req){
					if(!preg_match($app["regex.rules"]->getRegexRule("email"), $req->get("email")))
						return new JsonResponse(array("token" => "non-generated"));
				});


			$controllers->get("/enrroll/{course_token}", function($course_token) use($app){
				$result = $app["pdo.mysql"]->getSQLMatrix("select id from class where token='$course_token'");

				//check if the course exists
				if($result){
					$cid = $result[0];
					$cid = $cid["id"];
					$uid = $app["session"]->get("user.object")->getID();
					$result = $app["pdo.mysql"]->getSQLMatrix("select uid where uid=$uid and cid=$cid");

					//if the student is not enrroled
					if(!$result){
						$app["pdo.mysql"]->exec("insert into refclassuser(cid, uid) values($cid, $uid)");
						return new JsonResponse(array("acepted" => "Action [enrroled], msg [the student was enrroled successfuly]"));
					}

					return new JsonResponse(array("error" => "Action [enrroll], msg [the student is enrroled]"), 403);
				}

				return new JsonResponse(array("error" => "Action [enrroll],  msg [the course don't exists]"), 404);
			})
				->assert("course_token", $app["regex.rules"]->getAssertRule("hash"))
				->assert("uid", $app["regex.rules"]->getAssertRule("number"));

			/*
				Check the uploads
			*/

			$controllers->post("/upload-activity", function(Request $req) use($app){
				$aid = $req->get("aid");
				$uid = $app["session"]->get("user.object")->getID();
				$content = $req->get("content");

			})
				->before(function(Request $req) use($app){
					$aid = $req->get("aid");
					
					if(!preg_match($app["pdo.mysql"], $aid))
						return new JsonResponse(array("error" => "Action [upload activity], msg [invalid http post request]"),403);
				});

			//get activities by course token
			$controllers->get("/activities/{ctoken}", function($ctoken) use($app){

			})
				->assert("ctoken", $app["regex.rules"]->getAssertRule("hash"));

			//get the activity content and user information uploaded by user throw course token
			$controllers->get("/activity-content/{ctoken}", function($ctoken) use($app){

			})
				->assert("ctoken", $app["regex.rules"]->getAssertRule("hash"));


			$controllers->post("/edit-course", function(Request $req) use($app){
				$id = $req->get("id");
				$token = $req->get("token");
				$name = $req->get("name");
				$desc = $req->get("desc");

				$result = $app["pdo.mysql"]->exec("update class set name='$name', description='$desc' where id=$id and token='$token'");
				
				if($result)	return new JsonResponse(array("acepted" => "Action [edit course], msg [course edited successfuly]"), 201);

				return new JsonResponse(array("error" => "Action [edit course], msg [the course wasn't edited]"), 401);
			})
				->before(function(Request $req) use($app){
					$id = $req->get("id");
					$token = $req->get("token");
					$name = $req->get("name");

					if(!preg_match($app["regex.rules"]->getRegexRule("number"), $id)
						|| !preg_match($app["regex.rules"]->getRegexRule("hash"), $token)
						|| !preg_match($app["regex.rules"]->getRegexRule("name"), $name))
						return new JsonResponse(array("error" => "Action [edit course], msg [invalid fields]"), 403);
				});

			$controllers->get("/del-course/{token}/{id}", function($token, $id) use($app){
				$result =  $app["pdo.mysql"]->exec("delete from class where id=$id and token='$token'");
				if($result)
					return new JsonResponse(array("acepted" => "Action [del course], msg [course deleted]"), 200);
				return new JsonResponse(array("error" => "Action [del course], msg [the course wasn't deleted]"), 401);
			})
				->assert("token", $app["regex.rules"]->getAssertRule("hash"))
				->assert("id", $app["regex.rules"]->getAssertRule("number"));


			$controllers->post("/edit-student", function(Request $req) use($app){
				$email = $req->get("email");
				$pass = $req->get("password-ver");
				$token = $req->get("token");

				if(empty($pass)) $pass = $token;
				else $pass = md5($pass);

				$id = $req->get("id");
				$about = $req->get("desc");
				$result = $app["pdo.mysql"]->exec("update users set email='$email', password='$pass', about='$about' where id=$id and password='$token'");

				if($result)
					return new JsonResponse(array("acepted" => "Action [edit student], msg[student edited]"), 201);

				return new JsonResponse(array("error" => "Action [edit student], msg [the student wasn't updated $result $pass]"), 401);
			})
				->before(function(Request $req) use($app){
					$email = $req->get("email");
					$pass = $req->get("password");
					$passver = $req->get("password-ver");
					$token = $req->get("token");

					if($pass !== $passver
						|| !preg_match($app["regex.rules"]->getRegexRule("email"), $email)
						|| !preg_match($app["regex.rules"]->getRegexRule("hash"), $token))
						return new JsonResponse(array("error"=>"Action [edit student], msg [the student edit request is not valid]"), 403);
				});

			$controllers->get("/del-student/{pass}/{id}", function($pass, $id) use($app){
				$result = $app["pdo.mysql"]->exec("delete from users where id=$id and password='$pass' and usertype=0");
				if($result)
					return new JsonResponse(array("acepted" => "Action [del student], msg [student deleted]"), 200);
				return new JsonResponse(array("error" => "Action [del course], msg [the student wasn't deleted]"), 401);
			})
				->assert("pass", $app["regex.rules"]->getAssertRule("hash"))
				->assert("id", $app["regex.rules"]->getAssertRule("number"));

			$controllers->post("/edit-profile", function(Request $req) use($app){
				$email = $req->get("email");
				$pass = $req->get("password-ver");
				$about = $req->get("desc");
				$id = $app["session"]->get("user.object")->getID();

				if(empty($pass))
					$pass = $app["session"]->get("user.object")->getPassword();
				else 
					$pass = md5($pass);

				$result = $app["pdo.mysql"]->exec("update users set email='$email', password='$pass', about='$about' where id=$id");

				if($result){
					//update user object
					$app["session"]->get("user.object")->setPassword($pass);
					$app["session"]->get("user.object")->setEmail($email);
					$app["session"]->set("userabout", $about);
					return new JsonResponse(array("acepted" => "Action [edit profile], msg [profile edited]"), 201);
				}
				
				return new JsonResponse(array("error" => "Action [edit profile], msg [the user profile wasn't edited]"), 401);
			}) 
				->before(function(Request $req) use($app){
					if(!$app["session"]->get("isAuth"))
						return $app->redirect($app->path("logout"));
					else{
						$email = $req->get("email");
						$pass = $req->get("password");
						$passver = $req->get("password-ver");

						if($pass !== $passver
							|| !preg_match($app["regex.rules"]->getRegexRule("email"), $email))
							return new JsonResponse(array("error" => "Action [edit profile], msg [invalid post request]"), 403);
					}
				});

			$controllers->get("/activities/{token}/{cid}", function($token, $cid) use($app){
				$activities = $app["pdo.mysql"]->getSQLMatrix("select activity.id, activity.adate, activity.name, activity.description from activity, class where activity.cid = $cid and activity.cid = class.id and class.token = '$token'");

				if($activities){
					$activities = $app["pdo.mysql"]->filterSQLMatrix($activities, array("id", "adate", "name", "description"));
					return $app->json($activities);
				}

				return $app->abort(404);
			})
				->assert("token", $app["regex.rules"]->getAssertRule("hash"))
				->assert("cid", $app["regex.rules"]->getAssertRule("number"));

			$controllers->post("/add-activity", function(Request $req) use($app){
				$name = $req->get("name");
				$date = $req->get("adate");
				$cid = $req->get("cid");
				$desc = $req->get("description");

				$result = $app["pdo.mysql"]->exec("insert into activity(cid,adate,name,description) values($cid,'$date','$name','$desc')");

				if($result)
					return new JsonResponse(array("acepted" => "Action [add-activity], msg [activity added]"), 201);
			
				return new JsonResponse(array("error" => "Action [add-activity], msg [activity don't added]"), 401);
			})
				->before(function(Request $req) use($app){
					$name = $req->get("name");
					$date = $req->get("adate");
					$cid = $req->get("cid");
						
					if(!preg_match($app["regex.rules"]->getRegexRule("name"), $name)
						|| !preg_match($app["regex.rules"]->getRegexRule("datestandar"), $date)
						|| !preg_match($app["regex.rules"]->getRegexRule("number"), $cid))
						return new JsonResponse(array("error" => "Action [add-activity], msg [invalid http post request]"), 403);
				});


			$controllers->post("/edit-activity", function(Request $req) use($app){
				$cid = $req->get("cid");
				$aid = $req->get("aid");
				$token = $req->get("ctoken");

				$result = $app["pdo.mysql"]->getSQLMatrix("select activity.id from activity, class where activity.id=$aid and activity.cid=$cid and class.token = '$token' and activity.cid = class.id");

				//check if the activity doesn't exist
				if($result){
					$name = $req->get("name");
					$date = $req->get("adate");
					$desc = $req->get("description");

					$result = $app["pdo.mysql"]->exec("update activity set name='$name', description='$desc', adate='$date' where cid=$cid and id=$aid");

					if($result)
						return new JsonResponse(array("acepted" => "Action [add-activity], msg [activity edited]"), 201);
					
					return new JsonResponse(array("error" => "Action [add-activity], msg [undefined error to edit the activity]"), 401);
				}
			
				return new JsonResponse(array("error" => "Action [add-activity], msg [the activity not exists]"), 401);
			})
				->before(function(Request $req) use($app){
					$name = $req->get("name");
					$date = $req->get("adate");
					$token = $req->get("ctoken");
					$cid = $req->get("cid");
					$aid = $req->get("aid");
						
					if(!preg_match($app["regex.rules"]->getRegexRule("name"), $name)
						|| !preg_match($app["regex.rules"]->getRegexRule("datestandar"), $date)
						|| !preg_match($app["regex.rules"]->getRegexRule("hash"), $token)
						|| !preg_match($app["regex.rules"]->getRegexRule("number"), $cid)
						|| !preg_match($app["regex.rules"]->getRegexRule("number"), $aid))
						return new JsonResponse(array("error" => "Action [add-activity], msg [invalid http post request]"), 403);
				});

			$controllers->get("/del-activity/{ctoken}/{aid}/{cid}", function($ctoken, $aid, $cid) use($app){
				$result = $app["pdo.mysql"]->getSQLMatrix("select activity.id from activity, class where activity.cid = class.id and activity.cid = $cid and activity.id = $aid and class.token = '$ctoken'");

				//if activity exists
				if($result){
					$result = $app["pdo.mysql"]->exec("delete from activity where id=$aid and cid=$cid");

					if($result)
						return $app->json(array("acepted" => "Action [delete activity], msg [activity deleted]"));

					return $app->abort(401);
				}
				return $app->abort(401);
			})
				->assert("ctoken", $app["regex.rules"]->getAssertRule("hash"))
				->assert("aid", $app["regex.rules"]->getAssertRule("number"))
				->assert("cid", $app["regex.rules"]->getAssertRule("number"));

			return $controllers;
		}
	}