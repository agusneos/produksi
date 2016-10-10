<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Scan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_scan','record');
    }
    
    function index()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        $this->load->view('v_scan');  
    } 
    
    function create()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $id     = addslashes($_POST['scid']);
        $shid   = addslashes($_POST['shid']);
        $mcid   = addslashes($_POST['mcid']);
        $procid = $this->session->userdata('procid');
        $proc   = $this->session->userdata('proc');
        $nik    = $this->session->userdata('nik');
                           
        echo $this->record->create($id, $procid, $proc, $nik, $shid, $mcid);
        
    }
    
    function machCheck()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $liid   = addslashes($_POST['liid']);
        $mcid   = addslashes($_POST['mcid']);
        $procid = $this->session->userdata('procid');
                           
        echo $this->record->machCheck($procid, $liid, $mcid);
        
    }
    
    function viewupdate(){
        $auth       = new Auth();
        $auth->restrict();
        
        $this->load->view('v_update');  
    }
    
    function cardCheck() {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $scId   = addslashes($_POST['scId']);
        $procid = $this->session->userdata('procid');
                           
        echo $this->record->cardCheck($procid, $scId);
    }
    
    function update()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $scId       = addslashes($_POST['idPros']);
        $afterKg    = addslashes($_POST['afterKg']);
        $grPcs      = addslashes($_POST['grPcs']);
        
        echo $this->record->update($scId, $afterKg, $grPcs);
        
    }

}

/* End of file Scan.php */
/* Location: ./application/controllers/Scan.php */