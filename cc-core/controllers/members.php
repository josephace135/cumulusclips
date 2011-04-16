<?php

### Created on March 5, 2009
### Created by Miguel A. Hurtado
### This script allows users to browse through channels


// Include required files
include ('../config/bootstrap.php');
App::LoadClass ('User');
App::LoadClass ('Pagination');
App::LoadClass ('Picture');
View::InitView();


// Establish page variables, objects, arrays, etc
View::$vars->logged_in = User::LoginCheck();
if (View::$vars->logged_in) View::$vars->user = new User (View::$vars->logged_in);
View::$vars->page_title = 'Cumulus - Browse Memberss';
$records_per_page = 12;
$url = HOST . '/members';



// Retrieve total count
$query = "SELECT user_id FROM users WHERE account_status = 'Active'";
$result_count = $db->Query ($query);
$total = $db->Count ($result_count);

// Initialize pagination
View::$vars->pagination = new Pagination ($url, $total, $records_per_page);
$start_record = View::$vars->pagination->GetStartRecord();

// Retrieve limited results
$query .= " LIMIT $start_record, $records_per_page";
View::$vars->result = $db->Query ($query);


// Output Page
View::SetLayout ('full.layout.tpl');
View::Render ('members.tpl');

?>