<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	public $logged_in;

	function __construct()
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('upload');

        if ($this->session->userdata('logged_in')) {
            $this->logged_in = TRUE;
         } else {
            //$this->logged_in = FASLE;
         }
        
        $this->pic_path = realpath(APPPATH . '../uploads/');

        $this->load->model('admin_model');
        
        parent::__construct();
          
    }

// Display the first page of the admin module
    function index()
    {

        $data['log_navbar'] = 'admin/log_header';
        $data['log_content'] = 'admin/v_log';
        $data['log_footer'] = 'admin/log_footer';
        
        
        $this->template->call_log_template($data);
    }

   function log_check(){
      if($this->session->userdata('logged_in') == 0){
          redirect(base_url().'admin');
      }else{
        return "logged_in";
      }
   }


    function logout()
    {
        $sess_log = $this->session->userdata('session_id');
        $log = $this->admin_model->logoutuser($sess_log);

        $this->session->sess_destroy();
        redirect(base_url().'admin');
    }

    function dashboard(){
 
        $this->log_check();

        $email = $this->session->userdata('emp_email');

        $passcheck = $this->admin_model->passcheck($email);

        if($passcheck == 'e10adc3949ba59abbe56e057f20f883e'){
            $passmessage = "Remember to change your password";
        }

        $data['clientnumber'] = $this->getclientnumber();
        $data['ordernumber'] = $this->getordernumber();
        $data['commentnumber'] = $this->getcommentnumber();
        $data['productnumber'] = $this->getproductnumber();

        $data['all_categories'] = $this->allcategories('table');

        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'Overall Statistics';
        $data['admin_navbar'] = 'admin/header';
        $data['admin_sidebar'] = 'admin/sidebar';
        $data['admin_content'] = 'admin/v_admin';
        $data['admin_footer'] = 'admin/footer';
        
        $data['passmessage'] = $passmessage;

        $this->template->call_admin_template($data);
    }

    public function getclientnumber()
    {
          $results = $this->admin_model->clientnumber();

          return $results;

          //echo '<pre>'; print_r($results); echo '</pre>';die;
    }

    public function getproductnumber()
    {
          $results = $this->admin_model->productnumber();

          return $results;

          //echo '<pre>'; print_r($results); echo '</pre>';die;
    }

    public function getcommentnumber()
    {
          $results = $this->admin_model->commentnumber();

          return $results;

          //echo '<pre>'; print_r($results); echo '</pre>';die;
    }

    public function getordernumber()
    {
          $results = $this->admin_model->ordernumber();

          return $results;

          //echo '<pre>'; print_r($results); echo '</pre>';die;
    }

    function validate_member()
    {
        
            $username = $this->input->post('useremail');
            $passw1 = md5($this->input->post('userpassword'));

            $result = $this->admin_model->log_member($username,$passw1);      
            
             //echo '<pre>';print_r($result);echo'</pre>';die;
            switch($result){

                case 'logged_in':
                    
                    switch($this->session->userdata('level_id')){

                        // Level 1 Admin
                        
                        case '1':
                          echo json_encode(array(
                          'level' => 'superadmin',
                          'state' => 'success',
                          'subject' => 'Log Success',
                          'message'=> 'Logged in successfully'
                          ));
                          
                          //redirect(base_url().'superadmin/dashboard');
                        break;

                        // Level 2 Manager

                        case '2':
                        echo json_encode(array(
                          'level' => 'manager',
                          'state' => 'success',
                          'subject' => 'Log Success',
                          'message'=> 'Logged in successfully'
                        ));
                        break;

                        // Level 3 Stock Manager

                        case '3':
                          echo json_encode(array(
                          'level' => 'stockmanager',
                          'state' => 'success',
                          'subject' => 'Log Success',
                          'message'=> 'Logged in successfully'
                          ));
                          
                          //redirect(base_url().'stockmanager/dashboard');
                        break;
                    }

                break;

                case 'incorrect_password':
                   echo json_encode(array(
                    'state' => 'error',
                    'subject' => 'Incorrect Password',
                    'message'=> 'Incorrect username or Password. Please try again...'
                   ));
                break;

                case 'not_activated':
                echo json_encode(array(
                    'state' => 'error',
                    'subject' => 'Not Activated',
                    'message'=> 'Your account is not activated'
                   ));

                    // $data['new_user'] = 'Your account is not activated';

                    // $data['log_navbar'] = 'admin/log_header';
                    // $data['log_content'] = 'admin/v_log';
                    // $data['log_footer'] = 'admin/log_footer';

                    // $this->template->call_log_template($data);
                break;

                default:
                    // echo '';
                break;
            }   
        
    }  

     

    // Display of other pages
    
    function clients()
    {
       $this->log_check();

        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'Clients';
        $data['admin_navbar'] = 'admin/header';
        $data['admin_sidebar'] = 'admin/sidebar';
        $data['admin_content'] = 'admin/clients';
        $data['admin_footer'] = 'admin/footer';
        
        
        $this->template->call_admin_template($data);
    }

     function orders()
    {
       $this->log_check();

        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'Orders';
        $data['admin_navbar'] = 'admin/header';
        $data['admin_sidebar'] = 'admin/sidebar';
        $data['admin_content'] = 'admin/orders';
        $data['admin_footer'] = 'admin/footer';
        
        
        $this->template->call_admin_template($data);
    }

     function comments()
    {
        $this->log_check();

        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'Comments';
        $data['admin_navbar'] = 'admin/header';
        $data['admin_sidebar'] = 'admin/sidebar';
        $data['admin_content'] = 'admin/comments';
        $data['admin_footer'] = 'admin/footer';
        
        
        $this->template->call_admin_template($data);
    }
    
    // Displays the contents page of the categories, in this case opens the category.php file
    function categories()
    {
        $this->log_check();
        //Transfer result to category.php from a function within the admin controller called allcategories()
        $data['all_categories'] = $this->allcategories('table'); 

        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'Category';
        $data['admin_navbar'] = 'admin/header';//header.php file
        $data['admin_sidebar'] = 'admin/sidebar';//sidebar.php file
        $data['admin_content'] = 'admin/category';//category.php file
        $data['admin_footer'] = 'admin/footer';//footer.php file

        
        
        $this->template->call_admin_template($data);
    }


     function employees()
    {
        $this->log_check();
        $data['all_administrators'] = $this->allemployees('table'); 

        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'Employee';
        $data['admin_navbar'] = 'admin/header';
        $data['admin_sidebar'] = 'admin/sidebar';
        $data['admin_content'] = 'admin/administrators';
        $data['admin_footer'] = 'admin/footer';

        
        
        $this->template->call_admin_template($data);
    }
 
    // Displays the contents page of the addcategory, in this case opens the addcategory.php file
    function addcategory()
    {
        
        $this->log_check();
        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'Add Category';
        $data['admin_navbar'] = 'admin/header';//header.php file
        $data['admin_sidebar'] = 'admin/sidebar';//sidebar.php file
        $data['admin_content'] = 'admin/addcategory';//addcategory.php file
        $data['admin_footer'] = 'admin/footer';//footer.php file

        
        
        $this->template->call_admin_template($data);
    }

    function addemployee()
    {
        
        $this->log_check();
        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'Add Employee';
        $data['admin_navbar'] = 'admin/header';//header.php file
        $data['admin_sidebar'] = 'admin/sidebar';
        $data['admin_content'] = 'admin/addadministrator';
        $data['admin_footer'] = 'admin/footer';

        
        
        $this->template->call_admin_template($data);
    }



    // Display of tables for categories and exporting of data
    function allcategories($type)
    {
        $display = '';
        $categories = $this->admin_model->get_all_categories();
        // echo "<pre>";print_r($active_job_groups);die();

        $count = 0;


      // creating arrays for both pdf and excel for data storage and transfer
        $column_data = $row_data = array();

        // display used for table
        $display .= "<tbody>";

        // html_body Used for the pdf
        $html_body = '
        <table class="data-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Category Name</th>
            <th>Category Status</th>
        </tr> 
        </thead>
        <tbody>
        <ol type="a">';

        foreach ($categories as $key => $data) {
            $count++;
                if ($data['Category Status'] == 1) {
                    $state = '<span class="label label-info">Activated</span>';
                    $states = 'Activated';
                } else if ($data['Category Status'] == 0) {
                    $state = '<span class="label label-danger">Deactivated</span>';
                    $states = 'Deactivated';
                }

        switch ($type) {
            case 'table':
                $display .= '<tr>';
                $display .= '<td class="centered">'.$count.'</td>';
                $display .= '<td class="centered">'.$data['Category Name'].'</td>';
                $display .= '<td class="centered">'.$state.'</td>';

                // button below used for viewing the specific category. Goes to admin controller into function called viewcategory(), passing the category id as parameter
                $display .= '<td class="centered"><a data-toggle="tooltip" data-placement="bottom" title="View Profile" href = "'.base_url().'admin/viewcategory/'.$data['Category ID'].'"><i class="fa fa-eye black"></i></a></td>';
                
                // button below used for editing the specific category. Goes to admin controller into function called catupdate(), passing the type of update and the category id as parameter
                $display .= '<td class="centered"><a data-toggle="tooltip" data-placement="bottom" title="Deactivate Profile" href = "'.base_url().'admin/catupdate/catdelete/'.$data['Category ID'].'"><i class="ion-trash-a icon black"></i></td>';
                $display .= '</tr>';

                break;
            
            case 'excel':
               
                 array_push($row_data, array($data['Category ID'], $data['Category Name'], $states)); 

                break;

            case 'pdf':

            //echo'<pre>';print_r($categories);echo'</pre>';die();
           
                $html_body .= '<tr>';
                $html_body .= '<td>'.$data['Category ID'].'</td>';
                $html_body .= '<td>'.$data['Category Name'].'</td>';
                $html_body .= '<td>'.$states.'</td>';
                $html_body .= "</tr></ol>";

                break;
               }
            }
        
        
        if($type == 'excel'){

            $excel_data = array();
            $excel_data = array('doc_creator' => 'Mirad Jewelries ', 'doc_title' => 'Category Excel Report', 'file_name' => 'Category Report', 'excel_topic' => 'Category');
            $column_data = array('Category ID','Category Name','Category Status');
            $excel_data['column_data'] = $column_data;
            $excel_data['row_data'] = $row_data;

              //echo'<pre>';print_r($excel_data);echo'</pre>';die();

            $this->export->create_excel($excel_data);

        }elseif($type == 'pdf'){
            
            $html_body .= '</tbody></table>';
            $pdf_data = array("pdf_title" => "Category PDF Report", 'pdf_html_body' => $html_body, 'pdf_view_option' => 'download', 'file_name' => 'Category Report', 'pdf_topic' => 'Category');

            //echo'<pre>';print_r($pdf_data);echo'</pre>';die();

            $this->export->create_pdf($pdf_data);

        }else{

            $display .= "</tbody>";

            //echo'<pre>';print_r($display);echo'</pre>';die();

            return $display;
        }

      }

      function allemployees($type)
    {
        $display = '';
        $administrators = $this->admin_model->get_all_administrators();
        // echo "<pre>";print_r($administrators);die();

        $count = 0;


      // creating arrays for both pdf and excel for data storage and transfer
        $column_data = $row_data = array();

        // display used for table
        $display .= "<tbody>";

        // html_body Used for the pdf
        $html_body = '
        <table class="data-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Occupation</th>
            <th>Date Registered</th>
            <th>Status</th>
        </tr> 
        </thead>
        <tbody>
        <ol type="a">';

        foreach ($administrators as $key => $data) {
            $count++;
                if ($data['Employee Status'] == 1) {
                    $state = '<span class="label label-info">Activated</span>';
                    $states = 'Activated';
                } else if ($data['Employee Status'] == 0) {
                    $state = '<span class="label label-danger">Deactivated</span>';
                    $states = 'Deactivated';
                }

                if ($data['Employee Level'] == 1) {
                    $level = 'System Admin';
                } else if ($data['Employee Level'] == 2) {
                    $level = 'Manager';
                } else if ($data['Employee Level'] == 3) {
                    $level = 'Stock Manager';
                } else if ($data['Employee Level'] == 4) {
                    $level = 'Consultant';
                }

        switch ($type) {
            case 'table':
                $display .= '<tr>';
                $display .= '<td class="centered">'.$count.'</td>';
                $display .= '<td class="centered">'.$data['Employee Name'].'</td>';
                $display .= '<td class="centered">'.$data['Employee Email'].'</td>';
                $display .= '<td class="centered">'.$level.'</td>';
                $display .= '<td class="centered">'.$data['Date Registered'].'</td>';
                $display .= '<td class="centered">'.$state.'</td>';

                // button below used for viewing the specific category. Goes to admin controller into function called viewcategory(), passing the category id as parameter
                $display .= '<td class="centered"><a data-toggle="tooltip" data-placement="bottom" title="View Profile" href = "'.base_url().'admin/viewemployee/'.$data['Employee ID'].'"><i class="fa fa-eye black"></i></a></td>';
                
                // button below used for editing the specific category. Goes to admin controller into function called catupdate(), passing the type of update and the category id as parameter
                $display .= '<td class="centered"><a data-toggle="tooltip" data-placement="bottom" title="Deactivate Profile" href = "'.base_url().'admin/empupdate/empdelete/'.$data['Employee ID'].'"><i class="ion-trash-a icon black"></i></td>';
                $display .= '</tr>';

                break;
            
            case 'excel':
               
                 array_push($row_data, array($data['Employee ID'], $data['Employee Name'], $data['Employee Email'], $level, $data['Date Registered'], $states)); 

                break;

            case 'pdf':

            //echo'<pre>';print_r($categories);echo'</pre>';die();
           
                $html_body .= '<tr>';
                $html_body .= '<td>'.$data['Employee ID'].'</td>';
                $html_body .= '<td>'.$data['Employee Name'].'</td>';
                $html_body .= '<td>'.$data['Employee Email'].'</td>';
                $html_body .= '<td>'.$level.'</td>';
                $html_body .= '<td>'.$data['Date Registered'].'</td>';
                $html_body .= '<td>'.$states.'</td>';
                $html_body .= "</tr></ol>";

                break;
               }
            }
        
        
        if($type == 'excel'){

            $excel_data = array();
            $excel_data = array('doc_creator' => 'Mirad Jewelries ', 'doc_title' => 'Emploee Excel Report', 'file_name' => 'Employee Report', 'excel_topic' => 'Employee');
            $column_data = array('Employee ID','Employee Name','Employee Email','Occupation','Date Registered','Employee Status');
            $excel_data['column_data'] = $column_data;
            $excel_data['row_data'] = $row_data;

              //echo'<pre>';print_r($excel_data);echo'</pre>';die();

            $this->export->create_excel($excel_data);

        }elseif($type == 'pdf'){
            
            $html_body .= '</tbody></table>';
            $pdf_data = array("pdf_title" => "Employee PDF Report", 'pdf_html_body' => $html_body, 'pdf_view_option' => 'download', 'file_name' => 'Employee Report', 'pdf_topic' => 'Employee');

            //echo'<pre>';print_r($pdf_data);echo'</pre>';die();

            $this->export->create_pdf($pdf_data);

        }else{

            $display .= "</tbody>";

            //echo'<pre>';print_r($display);echo'</pre>';die();

            return $display;
        }

      }


      // enables the registration for a new category
      function employeeregistration(){
         
        $this->form_validation->set_rules('employeeemail', 'Employee Email', 'trim|required|xss_clean|is_unique[employees.emp_email]');

        $employeename = $this->input->post('employeename');
        $employeeemail = $this->input->post('employeeemail');
        $employeeoccupation = $this->input->post('employeeoccupation');
        $employeestatus = $this->input->post('employeestatus');


        $path = base_url().'uploads/users/';
               $config['upload_path'] = 'uploads/employees/';
               $config['allowed_types'] = 'jpeg|jpg|png|gif';
               $config['encrypt_name'] = TRUE;
               $this->load->library('upload', $config);
               $this->upload->initialize($config);

            if ( ! $this->upload->do_upload('employeepicture'))
            {
               $error = array('error' => $this->upload->display_errors());

               print_r($error);die;
            }
             else
             {
               
                $data = array('upload_data' => $this->upload->data());
                 foreach ($data as $key => $value) {
                  //print_r($data);die;
                  $path = base_url().'uploads/employees/'.$value['file_name'];
                
                  }



        $insert = $this->admin_model->register_employee($employeename, $employeeemail, $employeeoccupation, $path, $employeestatus);

        return $insert;
        }
    
      }


      function categoryregistration(){
         
        $this->form_validation->set_rules('categoryname', 'Category Name', 'trim|required|xss_clean|is_unique[category.catname]');

        $categoryname = $this->input->post('categoryname');
        $categorystatus = $this->input->post('categorystatus');


        // transfers data into the admin_model.php in the models for admin module into a function called reigister_category() with parameters
        $insert = $this->admin_model->register_category($categoryname, $categorystatus);

        return $insert;
        
    
      }
      


      // enables the editing of the selected category
      public function editcategory()
    {
        $id = $this->input->post('editcategoryid');
        $category_name = $this->input->post('editcategoryname');
        $category_status = $this->input->post('editcategorystatus');

        // transfers data into the admin_model.php in the models for admin module into a function called category_update() with parameters
        $result = $this->admin_model->category_update($id,$category_name,$category_status);
        

        $this->categories();
        
    }

    public function editemployee()
    {
        $id = $this->input->post('editemployeeid');
        $employee_name = $this->input->post('editemployeename');
        $employee_status = $this->input->post('editemployeestatus');

        if($this->input->post('editemployeepassword')){
            $employee_password = md5($this->input->post('editemployeepassword'));
        }else{
            $employee_password = $this->input->post('employeepassword');
        }


        $employee_email = $this->input->post('editemployeeemail');
        $employee_occupation = $this->input->post('editemployeeoccupation');

        
        $result = $this->admin_model->administrator_update($id,$employee_name, $employee_email, $employee_password, $employee_occupation, $employee_status);
        

        $this->employees();
        
    }


    // function that passes the id to be viewed and displays it in the viewcategory file
    function viewcategory($id)
    {
        $this->log_check();
        $userdet = array();

        // uses the id to acquire details from the admin_model.php in a function called categoryprofile() with the id as the parameter
        $results = $this->admin_model->categoryprofile($id);

        foreach ($results as $key => $values) {
            $details['category'][] = $values;  
        }
        
        
        $data['categorydetails'] = $details;//uses result from the foreach above to and passes it into key -> categorydetails to be used as reference


        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'View Category';
        $data['admin_navbar'] = 'admin/header';//header.php file
        $data['admin_sidebar'] = 'admin/sidebar';//sidebar.php file
        $data['admin_content'] = 'admin/viewcategory';//viewcategory.php file
        $data['admin_footer'] = 'admin/footer';//footer.php file

        
        
        $this->template->call_admin_template($data);
 
    }


    function viewemployee($id)
    {
        $this->log_check();
        $userdet = array();

        
        $results = $this->admin_model->administratorprofile($id);

        foreach ($results as $key => $values) {
            $details['employees'][] = $values;  
        }
        
        
        $data['employeedetails'] = $details;


        $data['admin_title'] = 'Manager';
        $data['admin_subtitle'] = 'View Employee';
        $data['admin_navbar'] = 'admin/header';
        $data['admin_sidebar'] = 'admin/sidebar';
        $data['admin_content'] = 'admin/viewadministrator';
        $data['admin_footer'] = 'admin/footer';

        
        
        $this->template->call_admin_template($data);
 
    }

     //function that allows other updates for specific category with $cat_id
      function catupdate($type, $cat_id)
    {
        $update = $this->admin_model->updatecat($type, $cat_id);

        if($update)
        {
            switch ($type) {

                case 'catdelete':
                    $this->categories();
                    break;

                case 'catrestore':
                    
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }


    function empupdate($type, $emp_id)
    {
        $update = $this->admin_model->updateemp($type, $emp_id);

        if($update)
        {
            switch ($type) {

                case 'empdelete':
                    $this->administrators();
                    break;

                case 'emprestore':
                    
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }








	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */