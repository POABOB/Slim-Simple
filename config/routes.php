<?php

declare(strict_types=1);

use Slim\Http\Response;
// 發現使用Slim\Http\Request常常會報錯，所以使用官方的Request當作請求
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use Slim\App;
use Medoo\Medoo;

return function(App $app, Medoo $db) {
  $app->group("/api", function (RouteCollectorProxy $app) use ($db) {

    $app->get("/home[/]", function(Request $req, Response $res, array $args) :Response { 
      return $res->withJson("GET HOME", 200, JSON_UNESCAPED_UNICODE);
    });

    $app->get("/home/{name}[/]", function(Request $req, Response $res, array $args) :Response { 
      return $res->withJson("GET Hello {$args['name']}!", 200, JSON_UNESCAPED_UNICODE);
    });

    $app->get("/user[/]", function(Request $req, Response $res, array $args) use($db) :Response { 
      $data = $db->select("Users", ["id", "name"], 1);
      return $res->withJson($data, 200, JSON_UNESCAPED_UNICODE);
    });
    $app->post("/user[/]", function(Request $req, Response $res, array $args) use($db) :Response { 
      try {
        $data = (array)$req->getParsedBody();
        $db->insert("Users", $data);
        $id = $db->id();
        return $res->withJson($id, 200, JSON_UNESCAPED_UNICODE);
      } catch (\PDOException $e) {
        return $res->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE);
      }
    });

    $app->patch("/user[/]", function(Request $req, Response $res, array $args) use($db) :Response { 
      try {
        $data = (array)$req->getParsedBody();
        $db->update("Users", ["name" => $data["name"]], ["id" => $data["id"]]);
        return $res->withJson("Success", 200, JSON_UNESCAPED_UNICODE);
      } catch (\PDOException $e) {
        return $res->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE);
      }
    });

    $app->delete("/user[/]", function(Request $req, Response $res, array $args) use($db) :Response { 
      try {
        $data = (array)$req->getParsedBody();
        $db->delete("Users", ["id" => $data["id"]]);
        return $res->withJson("Success", 200, JSON_UNESCAPED_UNICODE);
      } catch (\PDOException $e) {
        return $res->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE);
      }
    });
 });
};
