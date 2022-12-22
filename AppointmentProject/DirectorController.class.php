<?php
namespace Home\Controller;
use Think\Controller;
use PhpMyAdmin\SqlParser\Components\Condition;

class DirectorController extends Controller{


     //Director register
    public function register(){

        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $email = $data->email;
        $password = $data->password;

        if(is_null($email) ||
        is_null($password)){
            response(false,"Please do not leave empty",false);
        }

        $check = M("apmtuser")
            ->where(["email" => $email])
            ->find();
        if (Count($check) > 0) {
            response(false, "This email has already been registered.", false);
        }

        $director = M("apmtuser");
        $dataList[] = [
            "email" => $email,
            "password" => $password,
            "type" => 3,
        ];
        $director->addAll($dataList);
        response(true, "Your Registration to Director Panel has been done successfully.", true);
    }

     //Director login
    public function login()
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $email = $data->email;
        $password = $data->password;
        
        $checkdirector = M("apmtuser")
            ->where([
              'email' => $email,
              'password' => $password,
            ])
            ->select();
          
        if (count($checkdirector) ==0) {

            response(false, "Email or Password Wrong", false);

        } else {
            Session("ID", $checkdirector[0]["id"]);
            //var_dump(session("ID"));

            response( $_SESSION["ID"], "Welcome",true);
        }
    }
      // Director adds teacher
    public function addteacher()
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $email = $data->email;
        $password = $data->password;
        $name = $data->name;
        $surname = $data->surname;
        $course_name = $data->course_name;

        if (
            is_null($name) ||
            is_null($surname) ||
            is_null($email) ||
            is_null($password)||
            is_null($course_name)
        ) {
            response(false, "Please do not leave empty", false);
        }
        $addteacher = M("apmtuser")
            ->where(["email" => $email])
            ->find();
        if (Count($addteacher) > 0) {
            response(false, "This email has already been registered.", false);
        }

        $director = M("apmtuser");
        $dataList[] = [
            "name" => $name,
            "surname" => $surname,
            "email" => $email,
            "password" => $password,
            "type" => "2",
            "course_name" => $course_name,
        ];
        $director->addAll($dataList);
        response(true, "Successfully Registered to the Teacher Panel.", true);
    }

    //Lists all the teachers in the database

    public function listteacher(){

        $json = file_get_contents("php://input");
        $data = json_decode($json);
    
        $list=$data->list; 
    
        $check = M("apmtuser")
        ->field(["name","surname","email","password","course_name"])
        ->where
                (["type"=>2])
        ->select();
    
        if (count($check) == 0) {
            response(false, "There isn't any teacher to list", false);
        } else {
            response($check, "Below is the list of teachers", true);
        }
    }
 
    //Teaher logs out
    public function logout(){

        //session('');
        //session(''); 
        session('ID', null);
        response(false, "You exit", false);
    
    }
    


    }






?>