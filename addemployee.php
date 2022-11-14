<?php
  require_once('raxan/pdi/autostart.php');

  Raxan::config('autostart','AddEmployee');

  class AddEmployee extends RaxanWebPage {
    protected $db;
    protected $closeIcon;

    protected function _init(){
      $this->source('views/addemployee.html');
      try {
        $this->db = $this->Raxan->connect('default');
      }
      catch (Exception $e ){
        $this->halt('Error while connecting to Database Server.');
      }
    }

    protected function _config() {
      $this->closeIcon = '<span class="close ui-icon ui-icon-close right click-cursor"></span>';
    }

    protected function returnDir($e){
      $this->redirectTo('index.php');
    }

    protected function addNewEmployee($e){  
      $firstname = $this->post->textVal('firstname');
      $lastname = $this->post->textVal('lastname');
      $employeenum = $this->post->intVal('employeenum');
      $gender = $this->post->textVal('gender');
      $birthdate = $this->post->dateVal('birthdate');

      $empdata = array(
        'firstName' => $firstname,
        'lastName' => $lastname,
        'empNum' => $employeenum,
        'gender' => $gender,
        'birthdate' => $birthdate
      );

      $checkEmp = $this->db->table('employees','empNum = ?', $employeenum);
      
      if ($this->db) {
        if($checkEmp){
          $this->flashmsg($this->closeIcon.'Employee Number already exists','fade','rax-box error',null,array(
            'color'=>'#eee',
            'closeOnClick' => true,
            'closeOnEsc' => true
          ));
        }
        else{
          $row = $this->db->tableInsert('employees', $empdata);
          $this->redirectTo('index.php');
        }
      }
      else {
        $this->halt('Unable to connect to Database Server.');
      }     
    }
  }
?>