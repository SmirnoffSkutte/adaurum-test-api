<?php

class CompaniesService {

    public function getPublicCompanyByInn(string $companyInn){
        $db=new Database();
        $query=$db->query("SELECT * FROM companies
        WHERE companyInn=$companyInn");
        if (count($query)===0){
            http_response_code(404);
            exit('Не найдено ни одной компании по данному инн');
        }
        return json_encode($query);
    }

    public function getAllPublicCompanies(){
        $db=new Database();
        $query=json_encode($db->query("SELECT * FROM companies"));
        return $query;
    }

    //User companies

    public function getUserCompanyByInn(int $userId,string $companyInn){
        $db=new Database();
        $data=$db->query("SELECT * FROM user_companies WHERE userId=$userId AND companyInn=$companyInn ");
        if(count($data)===0){
            http_response_code(404);
            $err=['message'=>'У данного пользователя нет компании с таким инн'];
            exit(json_encode($err));
        }
        return json_encode($data);
    }

    public function getAllUserCompanies(int $userId){
        $db=new Database();
        $query=json_encode($db->query("SELECT * FROM user_companies
        WHERE userId=$userId"));
        return $query;
    }

    public function addUserCompany(int $userId,array $body){
        $db=new Database();
        $companyInn=$body['companyInn'];
        $oldCompany=$db->query("SELECT * FROM user_companies WHERE companyInn=$companyInn AND userId=$userId");
        if(count($oldCompany)>0){
            http_response_code(406);
            exit('У данного пользователя уже есть компания с таким инн');
        }
        $oldPublicCompany=$db->query("SELECT * FROM companies WHERE companyInn=$companyInn");
        if(count($oldPublicCompany)>0){
            http_response_code(409);
            exit('Компания с таким инн уже есть в общей базе данных');
        }
        $companyName=$body['companyName'];
        $info=$body['info'];
        $genDirector=$body['genDirector'];
        $address=$body['address'];
        $phone=$body['phone'];
        $insert=$db->query("INSERT INTO user_companies (userId,companyInn,companyName,info,genDirector,address,phone) VALUES (
        :userId,:companyInn,:companyName,:info,:genDirector,:address,:phone)",[
            'userId'=>$userId,
            'companyInn'=>$companyInn,
            'companyName'=>$companyName,
            'info'=>$info,
            'genDirector'=>$genDirector,
            'address'=>$address,
            'phone'=>$phone
        ]);
        return $insert;
    }

    public function deleteUserCompanyByInn(int $userId,string $companyInn){
        $db=new Database();
        $rows=$db->query("DELETE FROM user_companies
        WHERE userId=$userId AND companyInn=$companyInn");
        //Deleting comments
        $db->query("DELETE FROM comments WHERE userId=$userId AND companyInn=$companyInn");

        if ($rows===0){
            http_response_code(404);
            exit('Компания с таким инн не найдена');
        }
        return $rows;
    }

    public function hidePublicCompanyByInnForUser(int $userId,string $companyInn){
        $db=new Database();
        $insert=$db->query("INSERT INTO user_hidden_companies (userId,companyInn) VALUES (
        :userId,:companyInn)",[
            'userId'=>$userId,
            'companyInn'=>$companyInn,
        ]);
        return $insert;
    }

    public function unhidePublicCompanyByInnForUser(int $userId,string $companyInn){
        $db=new Database();
        $rows=$db->query("DELETE FROM user_hidden_companies
        WHERE userId=$userId AND companyInn=$companyInn");
        if ($rows===0){
            http_response_code(404);
            exit('Компания с таким инн не найдена');
        }
        return $rows;
    }

    public function getAllUnhiddenCompaniesForUser(int $userId){
        $db=new Database();
        $data1=$db->query("SELECT * FROM companies
        WHERE NOT EXISTS (SELECT * FROM user_hidden_companies
        WHERE companies.companyInn = user_hidden_companies.companyInn
        AND user_hidden_companies.userId=$userId)");

        $data2=$db->query("SELECT * FROM user_companies
        WHERE userId=$userId");

        $fullData=array_merge($data1,$data2);
        return json_encode($fullData);
    }

    public function getAllHiddenCompaniesForUser(int $userId){
        $db=new Database();
        $query=$db->query("SELECT * FROM companies
        WHERE EXISTS (SELECT * FROM user_hidden_companies
        WHERE user_hidden_companies.userId=$userId
        AND companies.companyInn = user_hidden_companies.companyInn)");
        return json_encode($query);
    }

}