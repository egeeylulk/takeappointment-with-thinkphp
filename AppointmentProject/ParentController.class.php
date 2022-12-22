<?php

namespace Home\Controller;

use Think\Controller;
use PhpMyAdmin\SqlParser\Components\Condition;

class ParentController extends Controller
{

    // This function is used to register the parent user.
    public function regParent()
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json);


        $name = $data->name;
        $surname = $data->surname;
        $ssn = $data->ssn;
        $birthdate = $data->birthdate;
        $email = $data->email;
        $password = $data->password;

        //CHECKS İF THE FIELDS ARE EMPTY
        if (
            is_null($name)
            ||
            is_null($surname)
            ||
            is_null($birthdate)
            ||
            is_null($ssn)
            ||
            is_null($email)
            ||
            is_null($password)
        ) {
            response(false, "Please do not leave empty", false);
        }
        //CHECKS IF THE AD AND SOYAD IN UPPERCASE
        if (strtoupper($name) != $name) {
            response(false, "This name cannot be used.", false);
        }
        if (strtoupper($surname) != $surname) {
            response(false, "This surname cannot be used.", false);
        }
        //CHECKS IF THE TC IS 11 DIGITS
        if (strlen((string)$ssn) != 11) {
            response(false, "SSN Number must be 11 digits.", false);
        }
        // CHECKS IF SOMEONE HAS ALREADY REGISTERED WITH THE SAME TC
        $check = M("apmtuser")
            ->where(["ssn" => $ssn])
            ->find();
        if (Count($check) > 0) {
            response(
                false,
                "This SSN Number Has Been Registered Before.",
                false
            );
        }
        //IF EVERYTHING IS OKAY, IT WILL ADD THE USER TO THE DATABASE
        else {
            $appointment = M("apmtuser");
            $dataList[] = [
                "name" => strtolower($name),
                "surname" => strtolower($surname),
                "birthdate" => $birthdate,
                "ssn" => $ssn,
                "email" => $email,
                "password" => $password,
                //"password" =>md5($password),
                "type" => "1",
            ];
            $appointment->addAll($dataList);
            response(true, "Registered.", true);
        }
    }

    // This function is used to login the parent user.

    public function login()
    {

        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $ssn = $data->ssn;
        $password = $data->password;
        $id = $data->id;

        $check = M("apmtuser")
            ->where([
                "ssn" => $ssn,
                "password" => $password,
            ])->select();

        if (count($check) == 0) {
            response(false, "SSN Number or Password Wrong", false);
        }

        Session("ID", $check[0]["id"]);
        var_dump(session("ID"));
        //die();

        response(true, "WELCOME.", true);
    }
    // This function is used to list the appointments.

    public function myAppointments()
    {

        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $listele = $data->listele;

        $check = M("appointment")
            ->field(["date", "teacher_id", "apmt_hour"])
            ->where(["parent_id" => $_SESSION["ID"]])
            ->select();

        if (count($check) == 0) {
            response(false, "You do not have an appointment.", false);
        } else {
            response($check, "You have appointments .", true);
        }
    }
     
    // This function is used to take the appointment.
    public function takeAppointment()
    {

        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $chooseteacher = $data->chooseteacher;
        $choosedate = $data-> choosedate;
        $choosehour = $data->choosehour;

        $appointmenthours = array();

        for ($i = 9; $i <= 17; $i++) {
            $appointmenthours[] = $i . ":00:00<br>";
        }
        for ($i = 0; $i < count($appointmenthours); $i++) {
            //echo $appointmenthours[$i];
            //die();
        }

        $$checkfull = M("appointment")
            ->field(["apmt_hour", "date"])
            ->where([
                "teacher_id" => $chooseteacher,
                "date" =>  $choosedate,
            ])
            ->select();
        //var_dump($checkfull);
        //die();

        foreach ($checkfull as $Key => $Element) {
            if (in_array($Element["apmt_hour"], $appointmenthours)) {
                $appointmenthours = array_diff($appointmenthours, [
                    $Element["apmt_hour"],
                ]);
            }
        }

        $randevu = M("appointment");
        $datalist[] = [
            "teacher_id" => $chooseteacher ,
            "date" => $choosedate,
            "apmt_hour" => $choosehour,
            "parent_id" => $_SESSION["ID"],
        ];
        $randevu->addAll($datalist);
        //var_dump($datalist);
        //die();
        if (count($datalist) == 0) {
            response(false, "ERROR", false);
        } else {
            response(true, "Your appointment has been successfully saved.", true);
        }
    }

    //This function is used to logut the parent user.

    public function logout()
    {
        session('ID', null);
        response(false, "You Exit", false);
    }
}
