<?php
namespace Home\Controller;
use Think\Controller;
use PhpMyAdmin\SqlParser\Components\Condition;

class TeacherController extends Controller{


    //This function is used to login the teacher user.
    public function login(){
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $email = $data->email;
        $password = $data->password;

        if (is_null($email) || is_null($password)) {
            response(false, "Please do not leave empty", false);
        }

        $checkteacher = M("apmtuser")
            ->where([
                "email" => $email,
                "password" => $password,
            ])
            ->select();

        if (count($checkteacher) == 0) {
            response(false, "Email or Şifre Wrong", false);
        } else {
            Session("ID", $checkteacher[0]["id"]);
            var_dump(session("ID"));

            response(true, "Welcome", true);
        }

    }
      
    //This function is for lising the Appointments for teachers

    public function seeAppointments(){

        $json = file_get_contents("php://input");
        $data = json_decode($json);



        $seeappointments=$data->seeappointments;

        $checkappointment = M("appointment")
            ->field(["date","apmt_hour","parent_id","statement"])
            ->where([
              "teacher_id"=>$_SESSION["ID"]
            ])
            ->select();

        if (count($checkappointment) == 0) {
            response(false, "You do not have an appointment.", false);
        } else {
            response($checkappointment, true,true);
        } 
    }

    //This function is for teacher to add statement to appointment

    public function addstatement()
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $chooseparent=$data->chooseparent;
        $statement=$data->statement;
       
        $appointment= M("appointment"); 
        $appointment->where("parent_id=$chooseparent")->setField('statement', $statement );
    
        if (count($statement) == 1) {
            response( $statement,"Statement added successfully.",true);
        } 
    
    }
    //This function is used to logout for teachers

    public function logout(){

        //session('');
        session('ID', null);
        response(false, "You Exit", false);

    
    }

}


?>