<?php
require_once 'router/router.php';
require_once 'auth/AuthController.php';
require_once 'auth/AuthService.php';
require_once 'companies/CompaniesController.php';
require_once 'companies/CompaniesService.php';
require_once 'comments/CommentsController.php';
require_once 'comments/CommentsService.php';

header('Content-Type: application/json; charset=utf-8');

header("Access-Control-Allow-Origin: http://adaurum");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Authorization");
//X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding
$router=new Router();

//Auth routes

$router->post('/registration',function (){
    $authController=new AuthController();
    $user=$authController->registration();
    print_r($user);
});

$router->post('/login',function (){
    $authController=new AuthController();
    $user=$authController->login();
    print_r($user);
});

$router->post('/refresh',function (){
    $authController=new AuthController();
    $newTokens=$authController->refreshTokens();
    print_r($newTokens);
});

//Public companies routes

$router->get('/companies/public',function (){
    $companiesController=new CompaniesController();
    $data=$companiesController->getAllPublicCompanies();
    print_r($data);
});

$router->get('/companies/public/{inn}',function ($inn){
    $companiesController=new CompaniesController();
    $data=$companiesController->getPublicCompanyByInn($inn);
    print_r($data);
});

//User companies routes

$router->post('/companies/user/personal/add',function (){
    $companiesController=new CompaniesController();
    $insert=$companiesController->addUserCompany();
    print_r($insert);
});

$router->delete('/companies/user/personal/delete/{inn}',function ($inn){
    $companiesController=new CompaniesController();
    $insert=$companiesController->deleteUserCompanyByInn($inn);
    print_r($insert);
});

$router->get('/companies/user/personal/{inn}',function ($inn){
    $companiesController=new CompaniesController();
    $data=$companiesController->getUserCompanyByInn($inn);
    print_r($data);
});

$router->get('/companies/user/personal',function (){
    $companiesController=new CompaniesController();
    $data=$companiesController->getAllUserCompanies();
    print_r($data);
});

//User hidden unhidden companies routes

$router->post('/companies/user/hide/{inn}',function ($inn){
    $companiesController=new CompaniesController();
    $insert=$companiesController->hidePublicCompanyByInnForUser($inn);
    print_r($insert);
});

$router->delete('/companies/user/unhide/{inn}',function ($inn){
    $companiesController=new CompaniesController();
    $rows=$companiesController->unhidePublicCompanyByInnForUser($inn);
    print_r($rows);
});

$router->get('/companies/user/public/unhidden',function (){
    $companiesController=new CompaniesController();
    $data=$companiesController->getAllUserUnhiddenCompanies();
    print_r($data);
});

$router->get('/companies/user/public/hidden',function (){
    $companiesController=new CompaniesController();
    $data=$companiesController->getAllUserHiddenCompanies();
    print_r($data);
});

//Comments routes

$router->get('/companies/user/comments/{inn}',function (int $companyInn){
    $commentsController=new CommentsController();
    $data=$commentsController->getAllCompanyComments($companyInn);
    print_r($data);
});

$router->post('/companies/user/addcomment',function (){
    $commentsController=new CommentsController();
    $insert=$commentsController->addCommentForCompany();
    print_r($insert);
});

$router->delete('/companies/user/deletecomment/{id}',function ($id){
    $commentsController=new CommentsController();
    $rows=$commentsController->deleteCommentFromCompany($id);
    print_r($rows);
});

$router->run();