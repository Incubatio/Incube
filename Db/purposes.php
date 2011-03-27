<?php
// datamodel as driver
$dataModel = Incube_Db_Adapter::factory("MySQL");
$dataModel->query("Select * FROM employees");
$dataModel->selectDb("MyDb");
$dataModel->from('employees')->select('*');

// datamodel as adapter
$dataModel = Incube_Db_Adapter::factory("MySQL");
$dataModel->query("Select * FROM employees");
$dataModel->from('mydb.employees')->retreive('all');

// datamodel as mapper
$dataModel = new DynamicMapper("MySQL");
$employee = $dataModel->from("employees")->get(array("id" => 4))->getNot ...;
$employee->id = 3;
$dataModel->update($employee);
