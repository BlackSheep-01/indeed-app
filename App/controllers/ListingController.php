<?php
namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;


class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath("config/db.php");
        $this->db = new Database($config);
    }

    /**
     * view the All Jobs page
     * @return void
     */
    public function index(){
        $listings = $this->db->query("SELECT * FROM listings ORDER BY created_at DESC")->fetchAll();
        loadView("listings/index", [
            'listings' => $listings
        ]);
    }

    /**
     * view create listing form
     * @return void
     */
    public function create(){
        loadView("listings/create");
    }


    /**
     * view a listing details
     * @param array $params
     * @return void
     */
    public function show($params){
        $id = $params["id"] ?? "";
        $queryParams = [
            "id" => $id
        ];
        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $queryParams)->fetch();
        if( !$listing){
            ErrorController::notFound("Listing not found");
            return;
        }
        loadView("listings/show", [
            'listing' => $listing
        ]);
    }


    /**
     * push user input data in database
     * @return void
     */
    public function store(){
        $allowedFields = ["title", "description", "salary", "tags", "company", "address", "city", "state", "phone", "email", "requirements", "benefits"];
        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));          // traverse through $_POST and keep the keys which are common in $allowedFields. $newListingData = (common keys of $_POST & $allowedFields, values of $_POST)
        // [ 0=>"title", 1=>"description", 2=>"salary", ... ] -> array_flip -> [ "title"=>0, "description"=>1, "salary"=>2, ...]
        $newListingData["user_id"] = Session::get("user")["id"];
        $newListingData = array_map("sanitize", $newListingData);                       //run sanitize() on each element of $newListingData
        $requiredFields = ["title", "description", "salary", "city", "state", "email"];
        $errors = [];
        foreach($requiredFields as $field){
            if(empty($newListingData[$field])  or  !Validation::string($newListingData[$field])){     // if user input($_POST) doesn't have the required fields or it doesnt pass validation checks
                $errors[$field] = ucfirst($field) . " is required";                       // eg: $errors["city" => "City is required"]
            }
        }
        if( !empty($errors)){          //error
            loadView("listings/create", [
                "errors" => $errors, 
                "listing" => $newListingData
            ]);
        }
        else{
            $fields = [];
            foreach($newListingData as $field => $value){
                $fields[] = $field;
            }
            $fields = implode(", ", $fields);      //convert array to string
            $values = [];
            foreach($newListingData as $field => $value){
                if($value === ""){               //convert empty values (of fields) to null
                    $newListingData[$field] = null;
                }
                $values[] = ":" . $field;
            }
            $values = implode(", ", $values);

            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
            $this->db->query($query, $newListingData);

            Session::setFlashMessage("success_message", "Listing created successfully");
            redirect("/listings");
        }
        /* eg:
        $fields = "title, description, salary, tags, company...." 
        $values = ":title, :description, :salary, :tags, :company..."
        */   
    }


    /**
     * delete a listing
     * @param array $params
     * @return void
     */
    public function destroy($params){
        $id = $params["id"];
        $queryParams = [
            "id" => $id
        ];
        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $queryParams)->fetch();
        if( !$listing){   
            ErrorController::notFound("Listing not found");
            return;
        }
        if(Session::get("user")["id"] !== $listing["user_id"]){     // if listing doesnt belong to loggedin user
            Session::setFlashMessage("error_message", "You are not authorized to delete this listing");
            return redirect("/listings/" . $listing["id"]);
        }

        $this->db->query("DELETE FROM listings WHERE id = :id", $queryParams);

        Session::setFlashMessage("success_message", "Listing deleted successfully");
        redirect("/listings");
    }


    /**
     * view edit form for a listing
     * @param array $params
     * @return void
     */
    public function edit($params){ 
        $id = $params["id"] ?? "";
        $queryParams = [
            "id" => $id
        ];
        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $queryParams)->fetch();
        if (!$listing) {
            ErrorController::notFound("Listing not found");
            return;
        }
        // inspectAndDie(Session::get("user")["id"]);
        if (Session::get("user")["id"] !== $listing["user_id"]) {     // if listing doesnt belong to loggedin user
            Session::setFlashMessage("error_message", "You are not authorized to edit this listing");
            return redirect("/listings/" . $listing["id"]);
        }
        loadView("listings/edit", [
            'listing' => $listing,
        ]);
    }


    /**
     * update listing data in database
     * @param array $params
     * @return void
     */
    public function update($params){
        //check if a listing with the given id exists in database or not
        $id = $params["id"] ?? "";
        $queryParams = [
            "id" => $id
        ];
        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $queryParams)->fetch();
        if ( !$listing) {
            ErrorController::notFound("Listing not found");
            return;
        }
        if (Session::get("user")["id"] !== $listing["user_id"]) {     // if listing doesnt belong to loggedin user
            Session::setFlashMessage("error_message", "You are not authorized to edit this listing");
            return redirect("/listings/" . $listing["id"]);
        }
        //if all okay ~
        $allowedFields = ["title", "description", "salary", "tags", "company", "address", "city", "state", "phone", "email", "requirements", "benefits"];
        $updateData = array_intersect_key($_POST, array_flip($allowedFields));          
        $updateData = array_map("sanitize", $updateData);
        $requiredFields = ["title", "description", "salary", "city", "state", "email"];
        $errors = [];
        foreach($requiredFields as $field){
            if(empty($updateData[$field])  or  !Validation::string($updateData[$field])){
                $errors[$field] = ucfirst($field) . " is required";
            }
        }
        if( !empty($errors)){
            loadView("listings/edit", [
                "errors" => $errors,
                "listing" => $listing
            ]);
        }
        else{
            $updateFields = [];
            foreach(array_keys($updateData) as $field){
                $updateFields[] = "{$field} = :{$field}";
            }
            $updateFields = implode(", ", $updateFields);
            $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";
            $updateData["id"] = $id;
            $this->db->query($updateQuery, $updateData);

            Session::setFlashMessage("success_message", "Listing updated successfully");
            redirect("/listings/" . $id);
        }
    }
    /*eg: $updateFields = "title = :title, description = :description, salary = :salary, ..." */


    /**
     * search listing by keyword/location in home page
     * @return void
     */
    public function search(){
        $keywords = isset($_GET["keywords"]) ? trim($_GET["keywords"]) : '';
        $location = isset($_GET["location"]) ? trim($_GET["location"]) : '';
        $query = "
        SELECT * FROM listings 
        WHERE (title LIKE :keywords OR description LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords)
        AND (city LIKE :location OR state LIKE :location)
        ";
        $params = [
            "keywords" => "%{$keywords}%",
            "location" => "%{$location}%"
        ];
        $listings = $this->db->query($query, $params)->fetchAll();
        loadView("listings/index", [
            "listings" => $listings,
            "keywords" => $keywords,
            "location" => $location
        ]);
    }

}
