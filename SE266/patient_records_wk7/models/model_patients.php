<?php 
//Include the db.php which has our connection and PDO. 
include ('db.php');

//returns all records in patients table. 
function getPatients(){
    global $db; //telling the func to use db from db.php

    $results = [];//create and initialize an array for storing results. 

    //here, we prepare our SQL statement for getting the data, and store it in the $stmt var.
    $stmt = $db->prepare("SELECT id, patientFirstName, patientLastName, patientMarried, patientBirthDate FROM patients ORDER BY patientLastName");

    //Execute the statement, and if successful, store the results in our previous array. 
    if($stmt->execute() && $stmt->rowCount() > 0)
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return ($results); //i mean...should be obvious....
}

//Funcction for adding a patient. Takes in the parameters needed to fill a record in our patients table. 
function addPatient($fName, $lName, $m, $bd){
    global $db;

    //Similar to getPatients, except its a INSERT, and we use placeholders for now. 
    $stmt = $db->prepare("INSERT INTO patients SET patientFirstName = :patientFirstName, patientLastName = :patientLastName, patientMarried = :patientMarried, patientBirthDate = :patientBirthDate");

    //an array for binding our placeholders, using the passed parameters from the func call. 
    //This becomes the parameter passed into the execute function, telling the function what the values actually are. See below. 
    $binds = array(
        ":patientFirstName" => $fName,
        ":patientLastName" => $lName,
        ":patientMarried" => $m,
        ":patientBirthDate" => $bd
    );

    if($stmt->execute($binds) && $stmt->rowCount() > 0) //See?
        $results = 'Patient Added'; //If successful, We're just storing a string to results here. Makes it easy to echo out in HTML. 

    return ($results); //See?
}

//New Function updatePatient will take the same params as addPatient, plus an id. The statements are nearly identical, except we UPDATE instead of INSERT, 
// As well as adding an ID to our binds array. 
function updatePatient($id, $fName, $lName, $m, $bd){
    global $db;

    $stmt = $db->prepare("UPDATE patients SET patientFirstName = :patientFirstName, patientLastName = :patientLastName, patientMarried = :patientMarried, patientBirthDate = :patientBirthDate WHERE id = :id");
    $results = "";
    $binds = array(
        ":patientFirstName" => $fName,
        ":patientLastName" => $lName,
        ":patientMarried" => $m,
        ":patientBirthDate" => $bd,
        ":id" => $id
    );

    if($stmt->execute($binds) && $stmt->rowCount() > 0) //Executing the statement with binds as a param, and making sure we got a result. 
        $results = 'Patient Updated'; //If successful, We're just storing a string to results here. Makes it easy to echo out in HTML. 

    return ($results); 
}

//Easiest of the functions. Takes the id of the row selected, and used it to create a DELETE statement, returning the row that was affected. 
function deletePatient($id){
    global $db;

    $results="ERROR: Patient Not Deleted.";
    $stmt = $db->prepare("DELETE FROM patients WHERE id=:id");
    $stmt->bindValue(':id', $id);

    //Same as others. execute the statement, and if we get a row back (success), tell the user using Result. 
    if($stmt->execute() && $stmt->rowCount() > 0)
        $results = "Patient Deleted";

    return ($results);
}

//Similar to getPatients, except we are selecting only the record with the id we submit. 
function getOnePatient($id){
    global $db;

    $results = [];
    $stmt = $db->prepare("SELECT id, patientFirstName, patientLastName, patientMarried, patientBirthDate FROM patients WHERE id=:id");
    $stmt->bindValue(':id', $id);
    
    if($stmt->execute() && $stmt->rowCount() > 0)
        $results = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($results);
}

function searchPatients ($fName, $lName, $married) {
    global $db;
    
    $binds = array();
    $sql = "SELECT id, patientFirstName, patientLastName, patientMarried, patientBirthDate FROM patients WHERE 0=0 ";
    //if the parameters are not empty, we append the string to the SQL statement. 
    if ($fName != "") {
         $sql .= " AND patientFirstName LIKE :patientFirstName";
         $binds['patientFirstName'] = '%'.$fName.'%';
    }
   
    if ($lName != "") {
        $sql .= " AND patientLastName LIKE :patientLastName";
        $binds['patientLastName'] = '%'.$lName.'%';
    }
    if ($married != "") {
        $sql .= " AND patientMarried LIKE :patientMarried";
        $binds['patientMarried'] = '%'.$married.'%';
    }
    
    //run the search
    $stmt = $db->prepare($sql);
   
     $results = array();
     if ($stmt->execute($binds) && $stmt->rowCount() > 0) {
         $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
     }
     return ($results);
}

//Check the credentials entered on the login screen, returning true if they match, false otherwise. 
function checkLogin ($userName, $password) {
    global $db;
    $stmt = $db->prepare("SELECT id FROM users WHERE username =:userName AND userPassword = :password");

    $stmt->bindValue(':userName', $userName);
    $stmt->bindValue(':password', sha1("school-salt".$password)); //rehash the password before searching, otherwise it won't work. I left the school-salt from the example code, as it made sense to me. 
    
    $stmt->execute ();
   
    return( $stmt->rowCount() > 0);
    
}

?>