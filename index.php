<?php
  require_once('raxan/pdi/autostart.php');

  Raxan::config('autostart','EmployeeDir');

  class EmployeeDirPage extends RaxanWebPage {
    protected $db;
    protected $closeIcon;

    protected function _config(){
      $this->closeIcon = '<span class="close ui-icon ui-icon-close right click-cursor"></span>';
    }

    protected function _init(){

      $this->source('views/employees.html');

      try {
        $this->db = $this->Raxan->connect('default');
      }
      catch (Exception $e ){
        $this->halt('Error while connecting to Database Server.');
      }
    }

    protected function _prerender() {
      if ($this->db) { 
        $this->loadEmployees();
      }
      else {
        $this->halt('Unable to connect to Database Server.');
      }
    }

    protected function _load(){
      $this->employeeslist->delegate('button.deleteEmp', '#click', '.deleteEmployee');
      $this->employeeslist->delegate('button.editEmp', '#click', '.editEmployee');
    }

    protected function loadEmployees() {
      $sql = "SELECT * , 
              CONCAT_WS(' ', firstName, lastName) 
              AS empName
              FROM employees
              ORDER BY empNum";
      $rows = $this->db->query($sql);
      $this->employeeslist->bind($rows, array(
        'format' => array(
          'name'=>'capitalize',
          'birthDate'=>'date:d M, Y'
        )
      ));
    }

    protected function goToNewEmpForm($e){
      $this->redirectTo('addemployee.php');
    }

    protected function deleteEmployee($e){
      $selectedempNum = $e->intVal();
      $rows = $this->db->tableDelete('employees', 'empNum=?', $selectedempNum);
    }

    protected function editEmployee($e){      
      $selectedempNum = $e->intVal();
      $row = $this->db->table('employees','empNum = ?', $selectedempNum);

      $this->editEmpFormContainer->show();

      $this->editfirstname->val($row[0]['firstName']);
      $this->editlastname->val($row[0]['lastName']);
      $this->editemployeenum->val($row[0]['empNum']);
      $this->editgender->val($row[0]['gender']);
      $this->editbirthdate->val($row[0]['birthDate']);

    }

    protected function updateEmployee($e){

      $firstname = $this->post->textVal('editfirstname');
      $lastname = $this->post->textVal('editlastname');
      $employeenum = $this->post->intVal('editemployeenum');
      $gender = $this->post->textVal('editgender');
      $birthdate = $this->post->dateVal('editbirthdate');

      $empdata = array(
        'firstName' => $firstname,
        'lastName' => $lastname,
        'empNum' => $employeenum,
        'gender' => $gender,
        'birthdate' => $birthdate
      );
      
      if ($this->db) { 
        $rows = $this->db->tableUpdate('employees',$empdata,'empNum=?',$employeenum);

        $this->flashmsg($this->closeIcon.'Employee updated successfully','fade','rax-box success',null,array(
          'color'=>'#eee',
          'closeOnClick' => true,
          'closeOnEsc' => true
        ));
      }
      else {
        $this->halt('Unable to connect to Database Server.');
      }
    }

    protected function cancelupdateEmployee($e){
      $this->editEmpFormContainer->hide();
    }
    protected function consoleLog($data){
      $output = json_encode($data);

      echo "<script>console.log('{$output}' );</script>";
    }
  }
?>