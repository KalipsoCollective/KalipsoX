<?php

/**
 *  KalipsoX - Localization File
 *  English(en)
 **/

return [
  'lang' => [
    'code' => 'en',
    'iso_code' => 'en_US',
    'dir' => 'ltr',
    'timezone' => 'Europe/London',
    'currency' => 'usd',
    'plural_suffix' => 's',
  ],
  'langs' => [
    'tr' => 'Turkish',
    'en' => 'English',
    'mk' => 'Macedonian',
    'ar' => 'Arabic',
  ],
  'err' => 'Error',
  'error' => [
    'page_not_found' => 'Page not found!',
    'method_not_allowed' => 'Method not allowed!',
    'controller_not_defined' => 'Controller is not defined!',
    'unauthorized' => 'You are not authorized.',
    'view_definition_not_found' => 'Controller did not send view parameter!',
    'csrf_token_mismatch' => 'CSRF key mismatch.',
    'csrf_token_incorrect' => 'CSRF key invalid.',
    'username_is_already_used' => 'Username is already used.',
    'notification_hook_file_not_found' => 'Notification hook file not found!',
    'a_problem_occurred' => 'A problem occurred!',
    'endpoint_file_is_not_found' => 'Authority endpoint file not found!',
    'ip_blocked' => 'Your IP address has been blocked!',
    'module_not_found' => 'Module not found!',
    'missing_or_incorrect_parameter' => 'Missing or incorrect parameter',
  ],
  'notification' => [
    'registration_email_title' => 'Your Account Has Been Created!',
    'registration_email_body' => 'Hi [USER], <br>Your account has been created. You can verify your email address with the link below. <br>[VERIFY_LINK]',
    'recovery_request_email_title' => 'Account Recovery',
    'recovery_request_email_body' => 'Hi [USER], <br>We received your account recovery request. You can set your new password with the link below. <br>[RECOVERY_LINK]',
    'account_recovered_email_title' => 'Your Account Has Been Recovered!',
    'account_recovered_email_body' => 'Hi [USER], <br>Your account has been recovered. If you did not do this, please contact us.',
    'email_change_email_title' => 'Your Email Address Has Been Updated!',
    'email_change_email_body' => 'Hi [USER], <br>Your email address has been updated. You can verify with the link below. <br>[VERIFY_LINK] <br>[CHANGES]',
  ],
  'auth' => [
    'auth' => 'Profile',
    'auth_action' => 'Profile - Sub Pages',
    'auth_logout' => 'Logout',
    'management' => 'Management',
    'management_users' => 'Management - Users',
    'management_users_list' => 'Management - Users - List',
    'management_users_add' => 'Management - Users - Add',
    'management_users_detail' => 'Management - Users - Detail',
    'management_users_update' => 'Management - Users - Edit',
    'management_users_delete' => 'Management - Users - Delete',
    'management_roles' => 'Management - Roles',
    'management_roles_list' => 'Management - Roles - List',
    'management_roles_add' => 'Management - Roles - Add',
    'management_roles_detail' => 'Management - Roles - Detail',
    'management_roles_update' => 'Management - Roles - Edit',
    'management_roles_delete' => 'Management - Roles - Delete',
    'management_sessions' => 'Management - Sessions',
    'management_sessions_list' => 'Management - Sessions - List',
    'management_sessions_delete' => 'Management - Sessions - Delete',
    'management_logs' => 'Management - Logs',
    'management_logs_list' => 'Management - Logs - List',
    'management_logs_ip_block' => 'Management - Logs - IP Block',
    'management_settings' => 'Management - Settings',
    'management_settings_update' => 'Management - Settings - Edit',
    'management_icon_picker' => 'Management - Icon Picker',
    'management_contents' => 'Management - Contents',
    'management_contents_list' => 'Management - Contents - List',
    'management_contents_add' => 'Management - Contents - Add',
    'management_contents_detail' => 'Management - Contents - Detail',
    'management_contents_update' => 'Management - Contents - Edit',
    'management_contents_delete' => 'Management - Contents - Delete',
    'management_contents_slug' => 'Management - Contents - Slug Inquiry',
    'management_contents_autocomplete' => 'Management - Contents - Auto Complete Inquiry',
    'management_content_upload_file' => 'Management - Contents - Editor File Upload',
    'management_forms' => 'Management - Forms',
    'management_forms_list' => 'Management - Forms - List',
    'management_forms_detail' => 'Management - Forms - Detail',
    'management_forms_update' => 'Management - Forms - Update',
    'management_forms_delete' => 'Management - Forms - Delete',
    'management_media' =>  'Management - Media',
    'management_media_list' =>  'Management - Media - List',
    'management_media_add' =>  'Management - Media - Add',
    'management_media_detail' =>  'Management - Media - Detail',
    'management_media_update' =>  'Management - Media - Edit',
    'management_media_delete' =>  'Management - Media - Delete',
    'management_menu' =>  'Management - Menus',
    'management_menu_list' =>  'Management - Menus - List',
    'management_menu_add' =>  'Management - Menus - Add',
    'management_menu_detail' =>  'Management - Menus - Detail',
    'management_menu_update' =>  'Management - Menus - Edit',
    'management_menu_delete' =>  'Management - Menus - Delete',
    'management_menu_get_params' => 'Management - Menus - Get Menu Details',
  ],
  'settings' => [
    'basic_settings' => 'Basic Settings',
    'secure_settings' => 'Security Settings',
    'email_settings' => 'Email Settings',
    'optimization_settings' => 'Optimization Settings',
    'name' => 'Site Name',
    'name_info' => 'This value appears in page headers and meta.',
    'description' => 'Site Description',
    'description_info' => 'This value appears in the page headers, it is based on when there is no meta description.',
    'contact_email' => 'Contact Email',
    'contact_email_info' => 'It is used on the interface side, this information is shown as the sender when sending an e-mail.',
    'separator' => 'Seperator',
    'separator_info' => 'It represents the character inserted in the page title when the site name is displayed after the relevant page title.',
    'language' => 'Default Language',
    'language_info' => 'Default language definition based on initial visits.',
    'default_user_role' => 'Default User Role',
    'default_user_role_info' => 'This value is used when assigning a user role to new records.',
    'ssl' => 'SSL Mode',
    'ssl_info' => 'If there is an SSL certificate installed on the web page, this setting is enabled and the addresses are made accordingly. <strong class="text-danger">If you don\'t know, don\'t change!</strong>',
    'log' => 'Log Record',
    'log_info' => 'When active, it logs all transactions. It is recommended to keep it closed in heavily visited sites. In off state it only logs failed accesses.',
    'mail_send_type' => 'Email Send Type',
    'mail_send_type_info' => 'If the mail() function is active on the server, direct e-mail can be sent with the server setting. SMTP sending provides slow but secure transmission.',
    'smtp_address' => 'SMTP Address',
    'smtp_address_info' => 'In SMTP sending, it refers to the SMTP server address.',
    'smtp_port' => 'SMTP Port',
    'smtp_port_info' => 'Indicates the port number to be used for connection to the SMTP server in SMTP sending.',
    'smtp_email_address' => 'SMTP Email Address',
    'smtp_email_address_info' => 'In SMTP sending, it refers to the e-mail address of the account to which the transmission will be provided.',
    'smtp_email_pass' => 'SMTP Email Password',
    'smtp_email_pass_info' => 'In SMTP sending, it refers to the password of the account to which the transmission will be provided.',
    'smtp_secure' => 'SMTP Security',
    'smtp_secure_info' => 'Represents the protocol that will be used when connecting to the SMTP server in SMTP sending.',
    'mail_queue' => 'Email Queue',
    'mail_queue_info' => 'As with SMTP sending, when there is a slow email sending situation, it queues the sending to prevent users from waiting. Cron jobs must be active. Otherwise, no delivery will be made.',
    'view_cache' => 'View Caching',
    'view_cache_info' => 'It can be used to reduce response times on sites with a lot of static pages, not recommended in projects with heavy dynamic pages.',
    'db_cache' => 'Database Caching',
    'db_cache_info' => 'It improves performance by making queries run on database queries respond faster.',
    'route_cache' => 'Route Caching',
    'route_cache_info' => 'Caching in the route mechanism, which performs operations according to incoming requests, prevents the same checks from being made over and over again.',
    'maintenance_mode' => 'Maintenance Mode',
    'maintenance_mode_info' => 'It allows you to close the site to visits. Everyone is greeted with this screen, except the logged-in administrators.',
    'maintenance_mode_desc' => 'Maintenance Mode Message',
    'maintenance_mode_desc_info' => 'It is the message that will be shown to the visitors on the maintenance mode welcome page.',
    'map_embed_url' => 'Map Embedding Code',
    'map_embed_url_info' => 'The map embed code must have the src content inside.',
    'map_url' => 'Map Link',
    'map_url_info' => 'It is the map link that will be opened when clicked on the contact page.',
    'clarification_text' => 'Clarification Text Page',
    'clarification_text_info' => 'This is the page where the ornament clarification connection checkbox in forms is located.',
    'address' => 'Address',
    'address_info' => 'It is the address section that will appear in the contact sections.',
    'phone' => 'Telephone',
    'phone_info' => 'It is the phone section that will appear in the contact sections.',
    'facebook' => 'Facebook URL',
    'facebook_info' => 'It is the Facebook address that will appear in the contact sections.',
    'twitter' => 'Twitter URL',
    'twitter_info' => 'It is the Twitter address that will appear in the contact sections.',
    'linkedin' => 'LinkedIn URL',
    'linkedin_info' => 'It is the LinkedIn address that will appear in the contact sections.',
    'instagram' => 'Instagram URL',
    'instagram_info' => 'It is the Instagram address that will appear in the contact sections.',
    'cookie_policy' => 'Cookie Policy',
    'cookie_policy_info' => 'This is the page to be used for the cookie policy link.',
    'map_lat_lon' => 'Map Latitude Longitude',
    'map_lat_lon_info' => 'It is used for the map to be opened from the contact page. It is entered as "latitude, longitude".',
  ],
  'base' => [
    'byte' => 'Byte',
    'sandbox' => 'Sandbox',
    'sandbox_message' => 'You can access all the tools that will help you in the development process from this screen.',
    'clear_storage' => 'Clear Storage',
    'clear_storage_message' => 'Allows you to delete files inside the storage folder.',
    'session' => 'Session',
    'session_message' => 'Shows the data within the session.',
    'php_info' => 'PHP Info',
    'php_info_message' => 'Shows server PHP information.',
    'db_init' => 'Prepare DB',
    'db_init_message' => 'Prepares database tables according to the schema.',
    'db_init_success' => 'Database has been prepared successfully.',
    'db_init_problem' => 'There was a problem while preparing the database. -> [ERROR]',
    'db_seed' => 'Seed DB',
    'db_seed_message' => 'Inserts data into tables within the schema.',
    'column' => 'Column',
    'table' => 'Table',
    'data' => 'Data',
    'type' => 'Type',
    'auto_inc' => 'Auto Increment',
    'attribute' => 'Attribute',
    'default' => 'Default',
    'index' => 'Index',
    'yes' => 'yes',
    'no' => 'no',
    'charset' => 'Charset',
    'collate' => 'Collate',
    'engine' => 'Engine',
    'db_name' => 'Database Name',
    'db_charset' => 'Database Charset',
    'db_collate' => 'Database Collate',
    'db_engine' => 'Database Engine',
    'db_init_alert' => 'If there is no database named [DB_NAME], add it with the [COLLATION] collation.',
    'db_init_start' => 'Good, Prepare!',
    'db_seed_success' => 'Database has been seeded successfully.',
    'db_seed_problem' => 'There was a problem while seeding the database. -> [ERROR]',
    'db_seed_start' => 'Good, Seed!',
    'clear_storage_success' => 'Storage folder is cleared.',
    'folder' => 'Folder',
    'delete' => 'Delete',
    'folder_not_found' => 'Folder not found!',
    'change_language' => 'Change Language',
    'seeding' => 'Seeding...',
    'go_to_home' => 'Go to Home',
    'home' => 'Home',
    'welcome' => 'Welcome!',
    'welcome_message' => 'It is the start page of KalipsoNext.',
    'login' => 'Login',
    'login_message' => 'It is the sample login page.',
    'register' => 'Register',
    'register_message' => 'It is the sample register page.',
    'logout' => 'Logout',
    'account' => 'Account',
    'account_message' => 'Welcome!',
    'email_or_username' => 'Email or Username',
    'password' => 'Password',
    'recovery_account' => 'Recovery Account',
    'recovery_account_message' => 'From this page, you can get a password reset link by entering your e-mail address.',
    'email' => 'Email Address',
    'username' => 'Username',
    'name' => 'Name',
    'surname' => 'Surname',
    'form_cannot_empty' => 'The form cannot be empty!',
    'email_is_already_used' => 'Email address is already in use.',
    'username_is_already_used' => 'Username is already in use.',
    'registration_problem' => 'There was a problem during registration.',
    'registration_successful' => 'Registration successful!',
    'verify_email' => 'Verify Email Address',
    'verify_email_not_found' => 'Email verification link is invalid!',
    'verify_email_problem' => 'There was a problem verifying the email!',
    'verify_email_success' => 'Email verification successful.',
    'your_account_has_been_blocked' => 'Your account has been deleted, please contact us.',
    'account_not_found' => 'Account not found!',
    'your_login_info_incorrect' => 'Your login information is incorrect!',
    'welcome_back' => 'Welcome back!',
    'login_problem' => 'There was a problem starting the session.',
    'profile' => 'Profile',
    'profile_message' => 'You can edit your profile from this page.',
    'sessions' => 'Sessions',
    'sessions_message' => 'You can view active sessions from this page.',
    'device' => 'Device',
    'ip' => 'IP',
    'last_action_point' => 'Last Action Point',
    'last_action_date' => 'Last Action Date',
    'action' => 'Action',
    'terminate' => 'Terminate',
    'session_terminated' => 'Session terminated.',
    'session_not_terminated' => 'The session could not be terminated!',
    'signed_out' => 'Signed out.',
    'login_information_updated' => 'Your login information has been updated.',
    'birth_date' => 'Birth Date',
    'update' => 'Update',
    'save_problem' => 'There was a problem saving.',
    'save_success' => 'Successfully saved.',
    'recovery_request_successful' => 'We\'ve sent you the account recovery link, don\'t forget to check your email.',
    'recovery_request_problem' => 'There was a problem sending the account recovery link.',
    'new_password' => 'New Password',
    'change_password' => 'Change Password',
    'account_recovered' => 'The account has been recovered, you can log in with your new password.',
    'account_not_recovered' => 'There was a problem recovering the account.',
    'account_not_verified' => 'Account verification not done.',
    'management' => 'Management',
    'toggle_navigation' => 'Toggle Navigation',
    'dashboard' => 'Dashboard',
    'dashboard_message' => 'The dashboard is the shortest way to see a summary of what\'s going on.',
    'users' => 'Users',
    'users_message' => 'This is the page where you can manage users.',
    'user_roles' => 'User Roles',
    'user_roles_message' => 'This is the page where you can manage user roles.',
    'logs' => 'Logs',
    'logs_message' => 'This is the page where you can review all log records.',
    'settings' => 'Settings',
    'settings_message' => 'You can update all settings from this screen.',
    'view' => 'View',
    'status' => 'Status',
    'all' => 'All',
    'active' => 'Active',
    'passive' => 'Passive',
    'deleted' => 'Deleted',
    'role' => 'Role',
    'created_at' => 'Created',
    'updated_at' => 'Updated',
    'edit' => 'Edit',
    'routes' => 'Routes',
    'add_new' => 'Add New',
    'close' => 'Close',
    'add' => 'Add',
    'user_role_successfully_added' => 'The user role has been successfully added.',
    'user_role_add_problem' => 'There was a problem adding the user role.',
    'user_role_successfully_deleted' => 'User role deleted successfully.',
    'user_role_delete_problem' => 'There was a problem deleting the user role.',
    'user_role_successfully_updated' => 'The user role has been successfully updated.',
    'user_role_update_problem' => 'There was a problem updating the user role.',
    'same_name_alert' => 'There is already another record with the same name.',
    'loading' => 'Loading...',
    'are_you_sure' => 'Are you sure?',
    'record_not_found' => 'Record not found!',
    'delete_role' => 'Delete Role',
    'role_to_transfer_users' => 'Role to Transfer Users',
    'user_role_delete_required_transfer' => 'To be able to delete this role, you must transfer the relevant members!',
    'role_to_delete' => 'Role to Delete',
    'affected_user_count' => 'Number of Users to be Affected',
    'user_role_transfer_problem' => 'Problem occurred while transferring users to new role!',
    'no_change' => 'No change!',
    'copyright' => 'Copyright',
    'all_rights_reserved' => 'All rights reserved.',
    'language' => 'Language',
    'user_successfully_added' => 'The user has been successfully added.',
    'user_add_problem' => 'There was a problem adding the user.',
    'user_successfully_deleted' => 'User deleted successfully.',
    'user_delete_problem' => 'There was a problem deleting the user.',
    'user_successfully_updated' => 'The user has been successfully updated.',
    'user_update_problem' => 'There was a problem updating the user.',
    'user_delete_problem_for_own_account' => 'You cannot delete your own account!',
    'middleware' => 'Middleware',
    'controller' => 'Controller',
    'request' => 'Request',
    'endpoint' => 'Endpoint',
    'user' => 'User',
    'execute_time' => 'Execute Time',
    'block_ip' => 'Block IP',
    'remove_ip_block' => 'Remove IP Block',
    'ip_block_list_not_updated' => 'Failed to update IP block list!',
    'ip_block_list_updated' => 'IP block list updated.',
    'auth_code' => 'Auth Code',
    'ssl' => 'SSL',
    'tls' => 'TLS',
    'server' => 'Server',
    'smtp' => 'SMTP',
    'settings_not_updated' => 'Failed to update settings!',
    'settings_updated' => 'Settings updated.',
    'maintenance_mode' => 'Maintenance Mode',
    'maintenance_mode_desc' => 'Unfortunately, we are currently under maintenance, so we are unable to provide service, you can try again later.',
    'system' => 'System',
    'contents' => 'Contents',
    'content' => 'Content',
    'contents_message' => 'Contents are flexible structures that you can remodel based on your content structures.',
    'services' => 'Services',
    'services_message' => 'This is the page where you can manage the services.',
    'other_services' => 'Other Services',
    'other_services_message' => 'You can manage other services here.',
    'countries' => 'Countries',
    'countries_message' => 'You can edit countries here.',
    'all' => 'All',
    'description' => 'Description',
    'title' => 'Title',
    'icon' => 'Icon',
    'slug' => 'Slug',
    'flag' => 'Flag',
    'image' => 'Image',
    'file_successfully_uploaded' => 'File uploaded successfully!',
    'file_upload_problem' => 'There was a problem uploading the file!',
    'file_not_found' => 'File not found!',
    'file_not_uploaded' => 'The file could not be loaded!',
    'header_image' => 'Header Image',
    'content_successfully_added' => 'Content has been successfully added.',
    'content_add_problem' => 'There was a problem adding content.',
    'modules' => 'Modules',
    'content_successfully_updated' => 'Content successfully edited.',
    'content_update_problem' => 'There was a problem editing the content.',
    'content_successfully_deleted' => 'Content successfully deleted.',
    'content_delete_problem' => 'There was a problem deleting the content.',
    'service_list' => 'View our services on this page.',
    'service_detail' => 'View the corresponding service.',
    'contact' => 'Contact',
    'contact_message' => 'You can find contact forms on this page.',
    'forms' => 'Forms',
    'information_request_form' => 'Information Request Form',
    'gallery' => 'Gallery',
    'gallery_message' => 'This is the page where you can manage the galleries.',
    'images' => 'Images',
    'blog' => 'Blog',
    'blog_message' => 'This is the page where you can manage blog posts.',
    'blog_list' => 'You can find all articles on this page.',
    'blog_detail' => 'View the related article.',
    'categories' => 'Categories',
    'categories_message' => 'This is the page where you can manage categories.',
    'categories_detail' => 'View the post for the category.',
    'color' => 'Color',
    'category' => 'Category',
    'pages' => 'Pages',
    'pages_message' => 'It is the page where you can manage the pages.',
    'page_detail' => 'View the relevant page.',
    'media' => 'Media',
    'media_message' => 'You can manage the media contents here.',
    'module' => 'Module',
    'preview' => 'Preview',
    'extension' => 'Extension',
    'size' => 'Size',
    'general' => 'General',
    'file_successfully_deleted' => 'File successfully deleted.',
    'file_delete_problem' => 'There was a problem deleting the file.',
    'menus' => 'Menus',
    'menus_message' => 'This is the page where you can manage the menus.',
    'other' => 'Other',
    'direct_link' => 'Direct Link',
    'parameter' => 'Parameter',
    'key' => 'Key',
    'basic' => 'Basic',
    'list' => 'List',
    'list_as_dropdown' => 'List as Dropdown',
    'key_is_already_used' => 'The key is already in use.',
    'menu_successfully_added' => 'The menu has been successfully added.',
    'menu_add_problem' => 'There was a problem adding the menu.',
    'menu_successfully_deleted' => 'Menu deleted successfully.',
    'menu_delete_problem' => 'There was a problem deleting the menu.',
    'menu_successfully_updated' => 'The menu has been successfully updated.',
    'menu_update_problem' => 'There was a problem updating the menu.',
    'menu_integrity_problem' => 'There seems to be a problem with the menu data integrity, you should check to make sure there are no missing fields.',
    'sliders' => 'Sliders',
    'sliders_message' => 'You can manage the slider contents here.',
    'link' => 'Link',
    'order' => 'Order',
    'links' => 'Links',
    'phone' => 'Phone',
    'subject' => 'Subject',
    'message' => 'Message',
    'contact_detail' => 'You can reach us by filling out the contact form on this page or by using our contact information.',
    'first_name_last_name' => 'First Name – Last Name',
    'telephone_number' => 'Telephone Number',
    'submit' => 'Submit',
    'form_successfully_added' => 'The form has been successfully submitted.',
    'form_add_problem' => 'There was a problem submitting the form.',
    'form_successfully_deleted' => 'The form has been successfully deleted.',
    'form_delete_problem' => 'There was a problem deleting the form.',
    'form_successfully_updated' => 'The form has been successfully updated.',
    'form_update_problem' => 'There was a problem updating the form.',
    'detail' => 'Detail',
    'pending' => 'Pending',
    'in_action' => 'In Action',
    'completed' => 'Completed',
    'form_received' => 'Form Received',
    'information_request_form_message' => 'This is the page where you can manage information request forms.',
    'last_studied_program' => 'The Last Studied/Graduated School and Program',
    'service' => 'Service',
    'country' => 'Country',
    'note' => 'Note',
    'related_service_to_be_informed' => 'The Education Service You Want to Be Informed',
    'interested_country' => 'The Country of Interest for Education',
    'remove' => 'Remove',
    'subhead' => 'Subhead',
    'home_contents' => 'Homepage Contents',
    'home_contents_message' => 'You can manage homepage contents from this page.',
    'target_blank' => 'Open in New Tab',
    'move' => 'Move',
    'our_services' => 'Our Services',
    'explore_our_services' => 'Explore Our Services',
    'header_images' => 'Header Images',
    'header_images_message' => 'You can manage header images from this page.',
    'file' => 'File',
    'year' => 'Year',
    'group' => 'Group',
    'sub_title' => 'Sub Title',
    'birth_year' => 'Birth Year',
    'death_year' => 'Death Year',
    'filter' => 'Filter',
    'filter_option' => 'Filter Option',
    'filter_options' => 'Filter Options',
    'scroll_to_top' => 'Scroll to Top',
    'career_form' => 'Career',
    'career_form_message' => 'You can manage career forms here.',
    'position' => 'Position',
    'subtitle' => 'Subtitle',
    'about' => 'About',
    'home_images' => 'Home Images',
    'products_title' => 'Products title',
    'news_title' => 'News Title',
    'home_banner_image' => 'Home Banner Image',
    'home_banner_text' => 'Home Banner Text',
    'home_gallery' => 'Home Gallery',
    'news' => 'News',
    'news_message' => 'You can manage news in this page.',
    'slide_content' => 'Slide Content',
    'product_categories' => 'Product Categories',
    'product_categories_message' => 'You can manage product categories in here.',
    'products' => 'Products',
    'products_message' => 'You can manage products in here.',
    'bid_form' => 'Bid',
    'bid_form_message' => 'You can manage bid forms in here.',
    'positions' => 'Positions',
    'positions_message' => 'You can manage positions in here.',
    'career' => 'Career',
    'career_gallery' => 'Career Gallery',
    'link_title' => "Link Title",
    'banners' => 'Banners',
    'banners_message' => 'It is the screen where you can manage banners.',
    'our_products' => 'Our Products',
    'about_us' => 'About Us',
    'read_more' => 'Read More',
    'all_news' => 'All News',
    'go_to_category' => 'Go to Category',
    'product_detail' => 'Product Detail',
    'contact_info' => 'Contact Info',
    'contact_info_message' => 'This is the page where you can update the information on the contact page.',
    'address' => 'Address',
    'fax' => 'Fax',
    'map_link' => 'Map Link',
    'main' => 'Main',
    'departments' => 'Departments',
    'customer_service' => 'Customer Service',
    'show_on_map' => 'Show on Map',
    'write_us' => 'Write Us',
    'clarification_text_check' => 'I have read and accept the <a href="[LINK]">Clarification Text</a>.',
    'follow_us' => 'Follow Us',
    'product_list' => 'You can view our products, examine their details, and create an offer form.',
    'go_back' => 'Go Back',
    'get_offer' => 'Get Offer',
    'bid_form' => 'Bid Form',
    'other_products' => 'Other Products',
    'about_message' => 'About us is the page where you can manage the page content.',
    'header_contents' => 'Header Contents',
    'header_contents_message' => 'It is the area where you can manage the content entered at the top of the pages on the pages under the Corporate.',
    'corporate' => 'Corporate',
    'catalogue' => 'Catalogue',
    'product_catalogue' => 'Product Catalogue',
    'about_side' => 'About - Side',
    'vision' => 'Vision',
    'mission' => 'Mission',
    'slogan' => 'Slogan',
    'widget_title' => 'Widget Title',
    'widget_content' => 'Widget Content',
    'our_vision' => 'Our Vision',
    'our_mission' => 'Our Mission',
    'view_job_detail' => 'View Job Detail',
    'close_the_window' => 'Close the Window',
    'career_form_title' => 'Join Our Family',
    'career_form_desc' => 'Determine the division of work you want to be with us.',
    'quality_policy' => 'Quality Policy',
    'quality_policy_message' => 'This is the page where you can manage the content of the quality policy.',
    'quality_policy_head' => 'We Care About Quality!',
    'quality_policy_desc' => 'You can find our quality policies on this page.',
    'about_us_desc' => 'You can get to know us from this page.',
    'news_list_desc' => 'You can browse the news on this page to be informed about us.',
    'news_detail_desc' => 'You can find the details of the related news on this page.',
    'career_detail_desc' => 'If you want to join us, you can use the career form on this page.',
    'certificates_desc' => 'You can view the certificates we have received on this page.',
    'fairs_desc' => 'You can view the fairs we attended on this page.',
    'quality_policy_foot' => 'We contribute to the protection of <span>natural life.</span>',
    'quality_certificates' => 'Our Quality Certificates',
    'certificates' => 'Certificates',
    'certificates_message' => 'You can manage certificates from this page.',
    'fairs' => 'Fairs',
    'fairs_message' => 'This is the page where you can manage the fair contents.',
    'load_more' => 'Load More',
    'values' => 'Values',
    'our_values' => 'Our Values',
    'our_values_message' => 'You can manage our values section content here.',
    'product_info' => 'Product Info',
    'nutritional_values' => 'Nutritional Values',
    'center' => 'HQ',
    'contact_us' => 'Contact Us',
    'our_values_title' => 'Our Values Section title',
    'vision_title' => 'Vision Section Title',
    'mission_title' => 'Mission Section Title',
    'vision_image' => 'Vision Cover Image',
    'mission_image' => 'Mission Cover Image',
    'all_details' => 'All Details',
    'location' => 'Location',
    'period' => 'Period',
    'career_title' => 'Career Title',
    'career_subtitle' => 'Career Sub Title',
    'career_image' => 'Career Cover',
    'x_open_position' => '[X] Open Positions',
    'open_positions' => 'Open Positions',
    'open_positions_desc' => 'After selecting the job posting, we will direct you to the application form.',
    'upload' => 'Upload',
    'upload_cv' => 'Upload CV',
    'cv' => 'CV',
    'ok' => 'OK',
    'cookie_desc' => 'This site uses cookies to provide its services and analyze traffic.',
    'get_info' => 'Get Info',
    'check_languages' => 'Check Languages',
    'check_languages_message' => 'Dil dosyalarını kontrol edip karşılaştırmasını yapar.',
    'no_missing_definitions' => 'No missing definitions.',
    'documentation' => 'Documentation',
    'licence' => 'Licence',
    'source_code' => 'Source Code',
    'cols' => 'Cols',
    'available_languages' => 'Available Languages',
    'available_folders' => 'Available Folders',
  ],
  'app' => []
];