<?php

$lang = array(
"LIBRARY_RECORDS" =>"LIBRARY RECORDS",
"Library_Records" =>"Library Records",
"LS" => "LS",
"Toggle_Nav" => "Toggle Nav",
"Login_to_reach_panel" => "Login to reach admin panel",
"Login" => "Login",
"lang" => "Language",
"Homepage" => "Homepage",
"Control_Panel" => "Control Panel",
"Total_Books" => "Total Books",
"Total_Keys" => "Total Keys",
"Total_Computers" => "Total Computers",
"Total_Personnel" => "Total Personnel",
"Books" => "Books",
"Computers" => "Computers",
"Keys" => "Keys",
"Personnel" => "Personnel",
"Loans" => "Loans",
"Login_Title" => "Login to LibraryRecords",
"Add_New_Personnel" => "Add New Personnel",
"Name_Surname" => "Name Surname",
"Username" => "Username",
"eMail" => "e-Mail",
"Phone" => "Phone",
"Operations" => "Operations",
"Are_you_sure_to_delete" => "Are you sure to delete? This operation cannot be reverted!",
"Enter_Name_Surname" => "Enter Name and Surname",
"Enter_Phone" => "Enter Phone Number",
"Enter_eMail" => "Enter e-mail",
"Is_Admin" => "Is this person an admin?",
"Add_the_Personnel" => "Add the personnel",
"Add_a_Key" => "Add a New Key",
"Number" => "Number",
"Content" => "Content",
"Status" => "Status",
"Enter_Key_Number" => "Enter Key Number",
"Enter_Key_Content" => "Enter Key Content",
"Key_Number" => "Key Number",
"Key_Content" => "Key Content",
"Key" => "Key",
"Add_the_Key" => "Add the Key",
"Add_New_Computer" => "Add New Computer",
"Manufacturer" => "Manufacturer",
"Model" => "Model",
"Serial_Number" => "Serial Number",
"Edit_Computer" => "Edit_Computer",
"Enter_Serial_Number" => "Enter Serial Number",
"Enter_Model" => "Enter Model",
"In_Inventory" => "In Inventory",
"Lent" => "Lent",
"Enter_Manufacturer" => "Enter Manufacturer",
"Add_the_Computer" => "Add the Computer",
"Add_New_Book" => "Add New Book",
"Book_Name" => "Book Name",
"Writer" => "Writer",
"Book_Writer" => "Book Writer",
"Enter_Book_Writer" => "Enter Book Writer",
"Book" => "Book",
"Enter_Book_Name" => "Enter Book Name",
"Enter_Book_Writer" => "Enter Book Writer",
"Add_the_Book" => "Add the Book",
"Edit" => "Edit",
"Cancel" => "Cancel",
"Save" => "Save",
"Process_Successful" => "Process Successful",
"Process_Failed" => "Process Failed",
"Lent_Information" => "Lent Information",
"New_Loan_Entry" => "New Loan Entry",
"Please_Select_a_Book_From_List" => "Please Select a Book From List",
"Please_Select_a_Computer_From_List" => "Please Select a Computer From List",
"Please_Select_a_Key_From_List" => "Please Select a Key From List",
"Computer" => "Computer",
"Loaner" => "Loaner",
"Lender" => "Lender",
"Loan_Date" => "Loan Date",
"Return_Date" => "Return Date",
"Return_Accepter" => "Return Accepter",
"Please_check_once_more" => "Please check once more",
"Select_Personnel" => "Select Personnel",
"Type" => "Type",
"Name" => "Name",
"Not_Returned_Yet" => "Not Returned Yet",
"Date_and_Repossesser_will_be_assigned_automatically" => "Date and Repossesser will be assigned automatically",
"This_loan_information_for_this_product_will_be_deleted_from_the_system" => "This loan information for this product will be deleted from the system",
"Settings" => "Settings",
"Personnel_Settings_Screen" => "Personnel Settings Screen",
"Enter_Username" => "Enter Username",
"Enter_Password" => "Enter Password",
"Enter_Phone" => "Enter Phone",
"PageTitle" => "Library Records Admin Panel",
"Record_Deleted" => "Record Deleted",
"Record_couldnt_Deleted" => "Record couldnt deleted",
"" => "",
"" => "",

);
function getTypeName ($type)
{
	$returnString="";
	switch($type)
	{
		case 0:
		{
			$returnString = $lang["Book"];
			break;
		}
		case 1:
		{
			$returnString = $lang["Computer"];
			break;
		}
		case 2:
		{
			$returnString = $lang["Key"];
			break;
		}
		default:
		{
			$returnString = "UNDEFINED";
		}
	}
	return $returnString;
}
function returnKeyStatus()
{
	$returnString="";
	$returnString += 'This '.getTypeName($pulledLoan['type']).', has been lent by ';
	
	$pullPersonnel=$connection->query("select * from personnel where personnelId=".$pulledLoan['loanerId']."")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pullPersonnel as $pulledPersonnel)
	{
		$returnString += $pulledPersonnel['nameSurname'];
	}
	$returnString +=  ' to ';
	$pullPersonnel2=$connection->query("select * from personnel where personnelId=".$pulledLoan['lenderId']."")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pullPersonnel2 as $pulledPersonnel2)
	{
		$returnString += $pulledPersonnel2['nameSurname'];
	}

	$returnString +=  ' at '.$pulledLoan['loanDate'];

	if ($pulledLoan['returnDate']!="0000-00-00")
	{
		$returnString += ' and was taken back by ';
	
		$pullPersonnel3=$connection->query("select * from personnel where personnelId=".$pulledLoan['returnAccepterId']."")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($pullPersonnel3 as $pulledPersonnel3)
		{
			$returnString += $pulledPersonnel3['nameSurname'];
		}
	
		$returnString += ' at '.$pulledLoan['returnDate'];
	}
	else 
	{
		$returnString += 'This key hasn\'t been returned yet.';
	}
	
	return $returnString;
}
?>