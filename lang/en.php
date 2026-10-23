<?php
/**
 * English interface strings.
 */

return [
    // Application
    'App_Name'            => 'Library Records',
    'App_Name_Upper'      => 'LIBRARY RECORDS',
    'App_Short_Name'      => 'LR',
    'Page_Title'          => 'Library Records Admin Panel',
    'Version'             => 'Version',
    'All_Rights_Reserved' => 'All rights reserved.',
    'Toggle_Navigation'   => 'Toggle navigation',

    // Sign in
    'Login_Title'                 => 'Sign in to Library Records',
    'Login_Prompt'                => 'Sign in to reach the admin panel',
    'Login'                       => 'Sign In',
    'Sign_Out'                    => 'Sign Out',
    'Enter_Username_And_Password' => 'Please enter your username and password.',
    'Invalid_Credentials'         => 'The username or password is incorrect.',
    'No_Panel_Access'             => 'Your account does not have access to the admin panel.',
    'Too_Many_Login_Attempts'     => 'Too many failed attempts. Please try again in 15 minutes.',

    // Navigation and dashboard
    'Homepage'        => 'Homepage',
    'Control_Panel'   => 'Control Panel',
    'Books'           => 'Books',
    'Computers'       => 'Computers',
    'Keys'            => 'Keys',
    'Personnel'       => 'Personnel',
    'Loans'           => 'Loans',
    'Settings'        => 'Settings',
    'Total_Books'     => 'Total Books',
    'Total_Keys'      => 'Total Keys',
    'Total_Computers' => 'Total Computers',
    'Total_Personnel' => 'Total Personnel',
    'Open_Loans'      => 'Items currently on loan',
    'More_Info'       => 'More info',

    // Common
    'Add'        => 'Add',
    'Edit'       => 'Edit',
    'Delete'     => 'Delete',
    'Save'       => 'Save',
    'Cancel'     => 'Cancel',
    'Close'      => 'Close',
    'Status'     => 'Status',
    'Type'       => 'Type',
    'Item'       => 'Item',
    'Operations' => 'Operations',
    'Unknown'    => 'Unknown',

    // Messages
    'Process_Successful'         => 'The operation was successful.',
    'Process_Failed'             => 'The operation failed.',
    'Record_Deleted'             => 'The record was deleted.',
    'Fill_All_Fields'            => 'Please fill in all required fields.',
    'Invalid_Request'            => 'Invalid or expired request. Please go back, reload the page and try again.',
    'Confirm_Delete'             => 'Are you sure you want to delete this? This cannot be undone!',
    'Item_On_Loan_Cannot_Delete' => 'This item is on loan. Please mark it as returned before deleting it.',

    // Item status
    'In_Inventory'      => 'In inventory',
    'Lent'              => 'Lent',
    'Lent_Description'  => 'Lent to %1$s on %2$s by %3$s.',
    'Deleted_Personnel' => '(deleted personnel)',
    'Deleted_Item'      => '(deleted item)',

    // Books
    'Book'              => 'Book',
    'Book_Name'         => 'Book Name',
    'Author'            => 'Author',
    'Add_New_Book'      => 'Add New Book',
    'Add_Book'          => 'Add Book',
    'Edit_Book'         => 'Edit Book',
    'Enter_Book_Name'   => 'Enter book name',
    'Enter_Book_Author' => 'Enter author',

    // Computers
    'Computer'            => 'Computer',
    'Manufacturer'        => 'Manufacturer',
    'Model'               => 'Model',
    'Serial_Number'       => 'Serial Number',
    'Add_New_Computer'    => 'Add New Computer',
    'Add_Computer'        => 'Add Computer',
    'Edit_Computer'       => 'Edit Computer',
    'Enter_Manufacturer'  => 'Enter manufacturer',
    'Enter_Model'         => 'Enter model',
    'Enter_Serial_Number' => 'Enter serial number',

    // Keys
    'Key'                   => 'Key',
    'Key_Number'            => 'Key Number',
    'Description'           => 'Description',
    'Add_New_Key'           => 'Add New Key',
    'Add_Key'               => 'Add Key',
    'Edit_Key'              => 'Edit Key',
    'Enter_Key_Number'      => 'Enter key number',
    'Enter_Key_Description' => 'Enter what the key opens',

    // Loans
    'New_Loan'             => 'New Loan',
    'Select_Book'          => 'Select a book',
    'Select_Computer'      => 'Select a computer',
    'Select_Key'           => 'Select a key',
    'Select_Personnel'     => 'Select personnel',
    'Borrower'             => 'Borrower',
    'Lender'               => 'Lender',
    'Loan_Date'            => 'Loan Date',
    'Return_Date'          => 'Return Date',
    'Received_By'          => 'Received By',
    'Not_Returned_Yet'     => 'Not returned yet',
    'Mark_Returned'        => 'Mark as returned',
    'Confirm_Return'       => 'Mark this item as returned? Today\'s date and your name will be recorded.',
    'Confirm_Delete_Loan'  => 'This loan record will be deleted from the system. Continue?',
    'Item_Already_Lent'    => 'This item is already on loan.',
    'Already_Returned'     => 'This item has already been returned.',
    'Return_Before_Delete' => 'A loan that has not been returned cannot be deleted. Please mark it as returned first.',

    // Personnel
    'Full_Name'                    => 'Full Name',
    'Username'                     => 'Username',
    'Email'                        => 'Email',
    'Phone'                        => 'Phone',
    'Password'                     => 'Password',
    'Admin'                        => 'Admin',
    'Is_Admin'                     => 'Administrator (can sign in to this panel)',
    'Add_New_Personnel'            => 'Add New Personnel',
    'Edit_Personnel'               => 'Edit Personnel',
    'Enter_Full_Name'              => 'Enter full name',
    'Enter_Phone'                  => 'Enter phone number',
    'Enter_Email'                  => 'Enter email',
    'Enter_Username'               => 'Enter username',
    'Enter_Password'               => 'Enter password',
    'Leave_Blank_To_Keep_Password' => 'Leave blank to keep the current password',
    'Invalid_Email'                => 'Please enter a valid email address.',
    'Password_Too_Short'           => 'The password must be at least 8 characters long.',
    'Username_Taken'               => 'This username is already in use.',
    'Cannot_Delete_Self'           => 'You cannot delete your own account.',
    'Personnel_Has_Open_Loans'     => 'This person still has items on loan. Please return them first.',

    // Settings
    'Account_Settings'          => 'Account Settings',
    'New_Password'              => 'New Password',
    'Current_Password'          => 'Current Password',
    'Current_Password_Required' => 'Required to save changes',
    'Current_Password_Wrong'    => 'Your current password is incorrect.',
];
